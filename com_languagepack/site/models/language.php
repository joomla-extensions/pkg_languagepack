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
use Joomla\CMS\MVC\Model\ItemModel;

/**
 * Language pack item model class.
 *
 * @since  1.0
 */
class LanguagepackModelLanguage extends ItemModel
{
	/**
	 * Model context string.
	 *
	 * @var		string
	 * @since   1.0
	 */
	protected $_context = 'com_languagepack.language';

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
		// TODO: Respect the menu type language code if set from the menu
		$this->setState('language_id', Factory::getApplication()->input->getInt('langid'));

		parent::populateState($ordering, $direction);
	}

	/**
	 * Method to get newsfeed data.
	 *
	 * @param   integer  $pk  The id of the newsfeed.
	 *
	 * @return  mixed  Menu item data object on success, false on failure.
	 *
	 * @since   1.6
	 */
	public function &getItem($pk = null)
	{
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('language_id');

		if ($this->_item === null)
		{
			$this->_item = array();
		}

		if (!isset($this->_item[$pk]))
		{
			$db = $this->getDbo();
			$query = $db->getQuery(true)
				->select('*')
				->from($db->quoteName('#__languagepack_languages'))
				->where($db->quoteName('id') . ' = ' . (int) $pk);

			$db->setQuery($query);

			$data = $db->loadObject();

			if (empty($data))
			{
				throw new RuntimeException(Text::_('COM_LANGUAGE_PACK_ERROR_LANGUAGE_NOT_FOUND'), 404);
			}

			$this->_item[$pk] = $data;
		}

		return $this->_item[$pk];
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
			->select($db->quoteName('b.name'))
			->from($db->quoteName('#__languagepack_languages', 'a'))
			->where($db->quoteName('a.id') . ' = ' . $this->getState('language_id'))
			->join(
				'INNER',
				$db->quoteName('#__languagepack_applications', 'b') . ' ON ' . $db->quoteName('a.application_id') . ' = ' . $db->quoteName('b.id')
			);

		$db->setQuery($query);

		return $db->loadResult();
	}
}
