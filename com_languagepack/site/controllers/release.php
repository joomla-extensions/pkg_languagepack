<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_languagepack
 *
 * @copyright   Copyright (C) 2020 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Filter\InputFilter;
use Joomla\CMS\MVC\Controller\FormController;

/**
 * Pack creation controller class.
 *
 * @since  1.0.0
 */
class LanguagepackControllerRelease extends FormController
{
	/**
	 * The URL view item variable.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $view_item = 'newpack';

	/**
	 * The URL view list variable.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $view_list = 'application';

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  object  The model.
	 *
	 * @since   1.0
	 */
	public function getModel($name = 'newpack', $prefix = '', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, $config);
	}

	/**
	 * Gets the URL arguments to append to a list redirect.
	 *
	 * @return  string  The arguments to append to the redirect URL.
	 *
	 * @since   1.0
	 */
	protected function getRedirectToListAppend()
	{
		$append = parent::getRedirectToListAppend();

		$formInstance = Factory::getApplication()->input->get('jform', [], 'array');
		$filter = InputFilter::getInstance();

		if (array_key_exists('langId', $formInstance))
		{
			$filteredLangId = $filter->clean($formInstance['langId'], 'INT');
			$append .= '&langid=' . $filteredLangId;
		}

		return $append;
	}

	/**
	 * Gets the URL arguments to append to an item redirect.
	 *
	 * @param   integer  $recordId  The primary key id for the item.
	 * @param   string   $urlVar    The name of the URL variable for the id.
	 *
	 * @return  string  The arguments to append to the redirect URL.
	 *
	 * @since   1.0
	 */
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
	{
		// Override the layout to be default
		$this->input->set('layout', 'default');

		$append = parent::getRedirectToItemAppend($recordId, $urlVar);

		// Setup redirect info.
		$langId = $this->input->getInt('langid');
		$filter = InputFilter::getInstance();

		// If we have got here from saving an item then the langid is in the form data
		if (is_null($langId))
		{
			$formData = $this->input->get('jform', [], 'array');

			if ($formData && array_key_exists('language_id', $formData))
			{
				$filter->clean($formData['language_id'], 'INT');
			}
		}

		if (!is_null($langId))
		{
			$append .= '&langid=' . $langId;
		}

		// Setup redirect info.
		$applicationId = $this->input->getInt('application_id');

		if ($applicationId)
		{
			$append .= '&application_id=' . $applicationId;
		}

		return $append;
	}
}
