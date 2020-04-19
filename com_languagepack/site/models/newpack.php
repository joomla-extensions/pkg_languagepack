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
			$data = $this->getItem();
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
		$this->setState('language_id', $app->input->getInt('lang'));

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
		// Ensure the maintainer ID is the current user id
		$data['maintainer_id'] = Factory::getUser()->id;

		// TODO: Factory to do the integration based on the source_id of the language
		//       here we'll generate the zip + upload to S3

		$arsContainer = Container::getInstance('com_ars');

		/** @var \Akeeba\ReleaseSystem\Site\Model\Items $itemsModel */
		$itemsModel = $arsContainer->factory->model('Items');

		/** @var \Akeeba\ReleaseSystem\Site\Model\Releases $releasesModel */
		$releasesModel = $arsContainer->factory->model('Releases');

		// Assemble data for generating the ARS release
		// TODO: Don't hardcode
		$joomlaVersion = '3.9.16';
		$releaseVersion = '1';
		$completeVersion = $joomlaVersion . '.' . $releaseVersion;
		$languageName = 'French';
		$languageCode = 'fr-FR';
		$dateNow = new Date;

		// TODO: Grab category from the language
		$arsReleaseData = [
			'category_id' => '1',
			'version'     => $completeVersion,
			'alias'       => str_replace('.', '-', $completeVersion),
			'maturity'    => 'stable',
			'description' => '<p>This is the ' . $languageName . ' Language Pack for Joomla! ' . $joomlaVersion . '</p>',
			'created'     => $dateNow->toSql(),
		];

		// Build Item Data (omitting the release ID which will be added after creation)
		// TODO: Environment data join in the language table? This is currently hardcoded - needs to be mapped to:
		// Environment 1: Joomla 1.5 translations
		// Environment 2: Joomla 2.5 translations
		// Environment 3: Joomla 3.x translations
		// Currently no environment for Joomla 4.x translations
		$arsItemData = [
			'title'        => 'Joomla! ' . $joomlaVersion . ' ' . $languageName . ' ' . $languageCode . ' Language Pack',
			'description'  => '<p>This is the full ' . $languageName . ' Language Pack for Joomla! ' . $joomlaVersion . '</p>',
			'type'         => 'file',
			'filename'     => $languageCode . '_joomla_lang_full_' . $joomlaVersion . 'v' . $releaseVersion . '.zip',
			'environments' => ['2'],
			'created'      => $dateNow->toSql(),
		];

		// Skip loading if it exists
		if ($releasesModel->load(['category_id' => $arsReleaseData['category_id'], 'version' => $arsReleaseData['version']]))
		{
			return false;
		}

		// Fail saving the item if it already exists in ARS
		if ($itemsModel->load(['title' => $arsItemData['title']]))
		{
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
}
