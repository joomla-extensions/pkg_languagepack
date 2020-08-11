<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_languagepack
 *
 * @copyright   Copyright (C) 2020 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Akeeba\ReleaseSystem\Admin\Helper\AmazonS3;
use Akeeba\ReleaseSystem\Site\Model\Categories;
use FOF30\Container\Container;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;

/**
 * Language pack item model class.
 *
 * @since  1.0
 */
class LanguagepackModelNewpack extends AdminModel
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $text_prefix = 'COM_LANGUAGE_PACK';

	/**
	 * Returns a Table object, always creating it.
	 *
	 * @param   string  $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  Table    A database object
	 */
	public function getTable($type = 'Release', $prefix = 'LanguagepackTable', $config = array())
	{
		return Table::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  Form|boolean  A JForm object on success, false on failure
	 *
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_languagepack.release', 'release', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @since   1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$app  = \JFactory::getApplication();
		$data = $app->getUserState('com_languagepack.edit.newpack.data', array());

		if (empty($data))
		{
			/** @var \Joomla\CMS\Object\CMSObject $data */
			$data = $this->getItem();

			$data->set('langId', $this->getState('language_id'));
		}

		$this->preprocessData('com_languagepack.newpack', $data);

		return $data;
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication();
		$this->setState('language_id', $app->input->getInt('langid'));

		parent::populateState();
	}

	/**
	 * Method to allow derived classes to preprocess the form.
	 *
	 * @param   \JForm  $form   A \JForm object.
	 * @param   mixed   $data   The data expected for the form.
	 * @param   string  $group  The name of the plugin group to import (defaults to "content").
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @throws  \Exception if there is an error in the form event.
	 */
	protected function preprocessForm(\JForm $form, $data, $group = 'content')
	{
		parent::preprocessForm($form, $data, $group);

		$languageTable = $this->getTable('Language');
		$loadResult = $languageTable->load($data->langId);

		if ($loadResult && (int) $languageTable->source_id === 3)
		{
			$addform = new \SimpleXMLElement('<form />');
			$fieldset = $addform->addChild('fieldset');
			$fieldset->addAttribute('name', 'file_upload');

			$uploadField = $fieldset->addChild('field');
			$uploadField->addAttribute('name', 'language_file');
			$uploadField->addAttribute('type', 'file');
			$uploadField->addAttribute('label', 'COM_LANGUAGEPACK_RELEASE_UPLOAD_PACK');
			$uploadField->addAttribute('accept', 'application/zip');

			$form->load($addform, false);
		}
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.0
	 */
	public function save($data)
	{
		// Ensure the maintainer ID is the current user id
		$data['maintainer_id'] = Factory::getUser()->id;

		$languageTable = $this->getTable('Language');
		$langLoadResult = $languageTable->load($data['langId']);

		if (!$langLoadResult)
		{
			$this->setError($languageTable->getError());

			return false;
		}

		// TODO: Should we try and move this to the allowSave method in the controller (it's complex because of the
		//       dependency on loading the language table data first)
		if (!in_array($languageTable->group_id, Factory::getUser()->getAuthorisedGroups()))
		{
			$this->setError(Text::_('JGLOBAL_AUTH_ACCESS_DENIED'));

			return false;
		}

		$applicationTable = $this->getTable('Application');
		$appLoadResult = $applicationTable->load($languageTable->application_id);

		if (!$appLoadResult)
		{
			$this->setError($applicationTable->getError());

			return false;
		}

		// Assemble data for generating the ARS release and the ZIP
		$joomlaVersion = $data['joomla_version'];
		$releaseVersion = $data['language_pack_version'];
		$languageName = $languageTable->name;
		$languageCode = $languageTable->lang_code;
		$zipName      = $languageCode . '_joomla_lang_full_' . $joomlaVersion . 'v' . $releaseVersion . '.zip';
		$dateNow = new Date;

		$arsContainer = Container::getInstance('com_ars');

		/** @var \Akeeba\ReleaseSystem\Site\Model\Categories $itemsModel */
		$categoriesModel = $arsContainer->factory->model('Categories');

		if (!$categoriesModel->load(['id' => $languageTable->ars_category]))
		{
			$this->setError(Text::_('COM_LANGUAGEPACK_CATEGORY_NOT_FOUND'));

			return false;
		}

		if (!$this->generateZipToS3($languageTable, $categoriesModel, $zipName))
		{
			// No need to set an error message here as it will be handled inside the function - just bail instead
			return false;
		}

		/** @var \Akeeba\ReleaseSystem\Site\Model\Items $itemsModel */
		$itemsModel = $arsContainer->factory->model('Items');

		/** @var \Akeeba\ReleaseSystem\Site\Model\Releases $releasesModel */
		$releasesModel   = $arsContainer->factory->model('Releases');
		$completeVersion = $joomlaVersion . '.' . $releaseVersion;

		$arsReleaseData = [
			'category_id' => $languageTable->ars_category,
			'version'     => $completeVersion,
			'alias'       => str_replace('.', '-', $completeVersion),
			'maturity'    => 'stable',
			'description' => '<p>This is the ' . $languageName . ' Language Pack for Joomla! ' . $joomlaVersion . '</p>',
			'created'     => $dateNow->toSql(),
			'access'      => '1',
		];

		// Build Item Data (omitting the release ID which will be added after creation)
		$arsItemData = [
			'title'        => 'Joomla! ' . $joomlaVersion . ' ' . $languageName . ' ' . $languageCode . ' Language Pack',
			'description'  => '<p>This is the full ' . $languageName . ' Language Pack for Joomla! ' . $joomlaVersion . '</p>',
			'type'         => 'file',
			'filename'     => $languageCode . '_joomla_lang_full_' . $joomlaVersion . 'v' . $releaseVersion . '.zip',
			'environments' => [(string) $applicationTable->ars_environment],
			'created'      => $dateNow->toSql(),
			'access'       => '1',
		];

		// Skip loading if it exists
		if ($releasesModel->load(['category_id' => $arsReleaseData['category_id'], 'version' => $arsReleaseData['version']]))
		{
			$this->setError(Text::_('COM_LANGUAGEPACK_ARS_RELEASE_ALREADY_EXISTS'));

			return false;
		}

		// Fail saving the item if it already exists in ARS
		if ($itemsModel->load(['title' => $arsItemData['title']]))
		{
			$this->setError(Text::_('COM_LANGUAGEPACK_ARS_RELEASE_ITEM_ALREADY_EXISTS'));

			return false;
		}

		// Save the data to the DB first then save to ARS
		$success = parent::save($data);

		try
		{
			$releaseData = $releasesModel->save($arsReleaseData);
		}
		catch (Exception $e)
		{
			// TODO: Rollback our DB Save action
			$this->setError($e->getMessage());

			return false;
		}

		// Add the release ID to the item and save
		$arsItemData['release_id'] = $releaseData->getId();

		try
		{
			$itemsModel->save($arsItemData);
		}
		catch (Exception $e)
		{
			// TODO: Rollback our DB Save action and the item creation
			$this->setError($e->getMessage());

			return false;
		}

		return $success;
	}

	/**
	 * Method to generate the translation zip and push it to S3.
	 *
	 * @param   \LanguagepackTableLanguage     $languageTable    The language table for the current release.
	 * @param   Categories                     $categoriesModel  The ARS Category Model for the release
	 * @param   string                         $zipName          The name of the file we want to place in S3
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.0
	 */
	private function generateZipToS3(\LanguagepackTableLanguage $languageTable, Categories $categoriesModel, $zipName)
	{
		if ((int) $languageTable->source_id === 3)
		{
			// We need to specify a raw type here as we are recieving a zip with PHP Files in which fails the normal file
			// security checks
			$fileUpload = Factory::getApplication()->input->files->get('jform', null, 'RAW')['language_file'];
			$filename = \JFile::makeSafe($fileUpload['name']);

			if (!strtolower(\JFile::getExt($filename)) === 'zip')
			{
				// Wrong file extension - bail
				$this->setError('COM_LANGUAGEPACK_ERROR_FILE_NOT_A_ZIP');

				return false;
			}
		}
		else
		{
			$this->setError('COM_LANGUAGEPACK_ERROR_RETRIEVING_TRANSLATED_FILE');

			return false;
		}

		// TODO: Add any security checks on the zip?

		$s3 = AmazonS3::getInstance();

		$success = $s3->putObject($fileUpload['tmp_name'], $categoriesModel->directory . '/' . $zipName);

		if (!$success)
		{
			// Pretty message to the poor translation team. Log the real error.
			$this->setError('COM_LANGUAGEPACK_ERROR_UPLOADING_TO_REMOTE_STORAGE');
			Log::add($s3->getError(), Log::ERROR, 'com-languagepack');

			return false;
		}

		return true;
	}
}
