<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_languagepack
 *
 * @copyright   Copyright (C) 2020 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;

/**
 * Language Pack Languages Model
 *
 * @since  1.0
 */
class LanguagepackModelLanguages extends ListModel
{
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

		return $query;
	}
}
