<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_languagepacks
 *
 * @copyright   Copyright (C) 2020 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Language\Text;

FormHelper::loadFieldClass('list');

/**
 * Provides a list of versions
 *
 * @since  1.0
 */
class LanguagepackFormFieldJoomlaversion extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $type = 'Joomlaversion';

	/**
	 * Cached array of the table data.
	 *
	 * @var    array
	 * @since  1.0
	 */
	protected static $items = null;

	/**
	 * Method to get the field options for versions.
	 *
	 * @return  array  The options the field is going to show.
	 *
	 * @since   1.0
	 */
	public function getOptions()
	{
		if (empty(static::$items))
		{
			$db    = Factory::getDbo();

			$query = $db->getQuery(true)
				->select($db->quoteName(array('id','name'), array('value', 'text')))
				->from($db->quoteName('#__languagepack_jversions'));

			$db->setQuery($query);

			// Load the options then translate the version strings
			$versions = $db->loadAssocList();
			$translatedItems = array();

			foreach ($versions as $version)
			{
				$translatedItem = new \stdClass;
				$translatedItem->text = Text::_($version['text']);
				$translatedItem->value = $version['value'];

				$translatedItems[] = $translatedItem;
			}

			static::$items = $translatedItems;
		}

		return array_merge(parent::getOptions(), static::$items);
	}
}
