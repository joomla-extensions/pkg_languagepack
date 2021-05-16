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
use Joomla\CMS\Language\Text;
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
		$app = Factory::getApplication();
		$this->setState('params', $app->getParams());
		$this->setState('application_id', $app->input->getInt('application_id'));

		parent::populateState($ordering, $direction);

		// Override the list model to show all languages in the frontend
		$this->setState('list.limit', 0);
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

		$query = $db->getQuery(true)
			->select('a.*')
			->from($db->quoteName('#__languagepack_languages', 'a'))
			->rightJoin($db->quoteName('#__languagepack_applications', 'b') . ' ON ' . $db->quoteName('b.id') . ' = ' . $db->quoteName('a.application_id'))
			->where($db->quoteName('a.application_id') . ' = ' . $this->getState('application_id'))
			->order(
				$db->escape($db->quoteName('a.' . $this->getState('list.ordering', 'name'))) . ' ' . $db->escape($this->getState('list.direction', 'ASC'))
			);

		$user = Factory::getUser();

		if (!$user->authorise('core.edit', 'com_languagepack'))
		{
			$query->where('a.state = ' . 1);
			$query->where('b.state = ' . 1);
		}

		return $query;
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
	 * Method to get any extra info for rendering on the page.
	 *
	 * @return  string[][]
	 *
	 * @since   1.0
	 */
	public function getExtraInfo()
	{
		// TODO: Store in DB so the component isn't specific to Joomla applications
		if ($this->getState('application_id') === 4)
		{
			return [
				[
					'title' => Text::_('COM_LANGUAGE_PACK_JOOMLA_VERSION_3_0_EXTRA_INFO_0_TITLE'),
					'body' => Text::_('COM_LANGUAGE_PACK_JOOMLA_VERSION_3_0_EXTRA_INFO_0_BODY'),
				],
				[
					'title' => Text::_('COM_LANGUAGE_PACK_JOOMLA_VERSION_3_0_EXTRA_INFO_1_TITLE'),
					'body' => Text::_('COM_LANGUAGE_PACK_JOOMLA_VERSION_3_0_EXTRA_INFO_1_BODY'),
				],
				[
					'title' => Text::_('COM_LANGUAGE_PACK_JOOMLA_VERSION_3_0_EXTRA_INFO_2_TITLE'),
					'body' => Text::_('COM_LANGUAGE_PACK_JOOMLA_VERSION_3_0_EXTRA_INFO_2_BODY'),
				],
				[
					'title' => Text::_('COM_LANGUAGE_PACK_JOOMLA_VERSION_3_0_EXTRA_INFO_3_TITLE'),
					'body' => Text::_('COM_LANGUAGE_PACK_JOOMLA_VERSION_3_0_EXTRA_INFO_3_BODY'),
				],
				[
					'title' => Text::_('COM_LANGUAGE_PACK_JOOMLA_VERSION_3_0_EXTRA_INFO_4_TITLE'),
					'body' => Text::_('COM_LANGUAGE_PACK_JOOMLA_VERSION_3_0_EXTRA_INFO_4_BODY'),
				],
			];
		}
		elseif ($this->getState('application_id') === 3)
		{
			return [
				[
					'title' => Text::_('COM_LANGUAGE_PACK_JOOMLA_VERSION_2_5_EXTRA_INFO_0_TITLE'),
					'body' => Text::_('COM_LANGUAGE_PACK_JOOMLA_VERSION_2_5_EXTRA_INFO_0_BODY'),
				],
				[
					'title' => Text::_('COM_LANGUAGE_PACK_JOOMLA_VERSION_2_5_EXTRA_INFO_1_TITLE'),
					'body' => Text::_('COM_LANGUAGE_PACK_JOOMLA_VERSION_2_5_EXTRA_INFO_1_BODY'),
				],
				[
					'title' => Text::_('COM_LANGUAGE_PACK_JOOMLA_VERSION_2_5_EXTRA_INFO_2_TITLE'),
					'body' => Text::_('COM_LANGUAGE_PACK_JOOMLA_VERSION_2_5_EXTRA_INFO_2_BODY'),
				],
				[
					'title' => Text::_('COM_LANGUAGE_PACK_JOOMLA_VERSION_2_5_EXTRA_INFO_3_TITLE'),
					'body' => Text::_('COM_LANGUAGE_PACK_JOOMLA_VERSION_2_5_EXTRA_INFO_3_BODY'),
				],
				[
					'title' => Text::_('COM_LANGUAGE_PACK_JOOMLA_VERSION_2_5_EXTRA_INFO_4_TITLE'),
					'body' => Text::_('COM_LANGUAGE_PACK_JOOMLA_VERSION_2_5_EXTRA_INFO_4_BODY'),
				],
			];
		}
		else
		{
			return [];
		}
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
