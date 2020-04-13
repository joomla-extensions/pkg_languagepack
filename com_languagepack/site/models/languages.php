<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_languagepack
 *
 * @copyright   Copyright (C) 2020 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;

/**
 * Language pack list model class.
 *
 * @since  1.0
 */
class LanguagepackModelLanguages extends ListModel
{
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
			->select($db->quoteName('*'))
			->from($db->quoteName('#__languagepack_languages'));
	}
}
