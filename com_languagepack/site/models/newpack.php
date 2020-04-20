<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_languagepack
 *
 * @copyright   Copyright (C) 2020 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use FOF30\Container\Container;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
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
		// TODO: Some sort of access check on the usergroup of the user

		// Ensure the maintainer ID is the current user id
		$data['maintainer_id'] = Factory::getUser()->id;

		$languageTable = $this->getTable('Language');
		$loadResult = $languageTable->load($data['langId']);

		if (!$loadResult)
		{
			$this->setError($languageTable->getError());

			return false;
		}

		// Assemble data for generating the ARS release and the ZIP
		$joomlaVersion = $data['joomla_version'];
		$releaseVersion = $data['language_pack_version'];
		$completeVersion = $joomlaVersion . '.' . $releaseVersion;
		$languageName = $languageTable->name;
		$languageCode = $languageTable->lang_code;
		$dateNow = new Date;

		if (!$this->generateZipToS3($languageTable, $joomlaVersion))
		{
			$this->setError(Text::_('COM_LANGUAGE_FAILED_TO_GENERATE_ZIP'));

			return false;
		}

		$arsContainer = Container::getInstance('com_ars');

		/** @var \Akeeba\ReleaseSystem\Site\Model\Items $itemsModel */
		$itemsModel = $arsContainer->factory->model('Items');

		/** @var \Akeeba\ReleaseSystem\Site\Model\Releases $releasesModel */
		$releasesModel = $arsContainer->factory->model('Releases');

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
			'environments' => [(string) $languageTable->ars_environment],
			'created'      => $dateNow->toSql(),
			'access'       => '1',
		];

		// Skip loading if it exists
		if ($releasesModel->load(['category_id' => $arsReleaseData['category_id'], 'version' => $arsReleaseData['version']]))
		{
			$this->setError(Text::_('COM_LANGUAGE_ARS_RELEASE_ALREADY_EXISTS'));

			return false;
		}

		// Fail saving the item if it already exists in ARS
		if ($itemsModel->load(['title' => $arsItemData['title']]))
		{
			$this->setError(Text::_('COM_LANGUAGE_ARS_RELEASE_ITEM_ALREADY_EXISTS'));

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

		// TODO: Catch exception
		$itemsModel->save($arsItemData);

		return $success;
	}

	/**
	 * Method to generate the translation zip and push it to S3.
	 *
	 * @param   \LanguagepackTableLanguage  $languageTable  The language table for the current release.
	 * @param   string                      $releaseName    The release name
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.0
	 */
	private function generateZipToS3(\LanguagepackTableLanguage $languageTable, $releaseName)
	{
		// TODO: Factory to do the integration based on the source_id of the language
		//       here we'll generate the zip + upload to S3
		return true;
	}
}
