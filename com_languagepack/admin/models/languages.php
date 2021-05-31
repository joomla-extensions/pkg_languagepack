<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_languagepack
 *
 * @copyright   Copyright (C) 2020 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;

/**
 * Language Pack Languages Model
 *
 * @since  1.0
 */
class LanguagepackModelLanguages extends ListModel
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @since   1.6
	 * @see     JControllerLegacy
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'name', 'a.name',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function populateState($ordering = 'a.id', $direction = 'desc')
	{
		$app = Factory::getApplication();
		$formSubmitted = $app->input->post->get('form_submitted');

		if ($formSubmitted)
		{
			$applicationId = $app->input->post->get('application_id');

			if (!empty($applicationId))
			{
				$this->setState('filter.application_id', $applicationId);
			}
		}

		// List state information.
		parent::populateState($ordering, $direction);
	}

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return      string  An SQL query
	 */
	protected function getListQuery()
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select($db->quoteName('b.name', 'application_name'))
			->select('a.*')
			->from($db->quoteName('#__languagepack_languages', 'a'))
			->innerJoin($db->quoteName('#__languagepack_applications', 'b') . ' ON b.id = a.application_id');

		// DISABLED UNTIL I CAN FIGURE OUT WHY ALL APPLICATIONS SHOWS NONE AND HAS AN EMPTY STRING ARRAY
		// ELEMENT IN THE STATE
//		$applicationId = $this->getState('filter.application_id', []);
//
//		if (count($applicationId))
//		{
//			$applicationIdInts = implode(',', $applicationId);
//			$query->where($db->quoteName('a.application_id') . ' IN (' . $applicationIdInts . ')');
//		}

		// Filter by search in title.
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
				$query->where('(a.name LIKE ' . $search . ' OR a.alias LIKE ' . $search . ')');
			}
		}

		return $query;
	}
}
