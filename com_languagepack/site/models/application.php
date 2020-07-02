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
use Joomla\CMS\MVC\Model\ListModel;

/**
 * Language pack list model class.
 *
 * @since  1.0
 */
class LanguagepackModelApplication extends ListModel
{
	/**
	 * Method to auto-populate the model state.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// TODO: Respect the menu type if this is a language page
		$this->setState('application_id', Factory::getApplication()->input->getInt('application_id'));

		parent::populateState($ordering, $direction);
	}

	/**
	 * Method to get a \JDatabaseQuery object for retrieving the data set from a database.
	 *
	 * @return  \JDatabaseQuery  A \JDatabaseQuery object to retrieve the data set.
	 *
	 * @since   1.6
	 */
	protected function getListQuery()
	{
		$db = $this->getDbo();

		return $db->getQuery(true)
			->select('*')
			->from($db->quoteName('#__languagepack_languages'))
			->where($db->quoteName('application_id') . ' = ' . $this->getState('application_id'));
	}

	/**
	 * Method to get an application name for the active application id.
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	public function getApplicationName()
	{
		$db = $this->getDbo();

		$query = $db->getQuery(true)
			->select($db->quoteName('name'))
			->from($db->quoteName('#__languagepack_applications'))
			->where($db->quoteName('id') . ' = ' . $this->getState('application_id'));

		$db->setQuery($query);

		return $db->loadResult();
	}

	/**
	 * Method to get an application name for the active application id.
	 *
	 * @return  integer|null
	 *
	 * @since   1.0
	 */
	public function getApplicationId()
	{
		return $this->getState('application_id');
	}
}
