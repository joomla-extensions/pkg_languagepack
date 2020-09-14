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
					'title' => 'How to Create a Joomla! Translation',
					'body' => "<p>It's easy to translate Joomla! into your language by <a href=\"https://docs.joomla.org/J3.1:Making_a_Language_Pack_for_Joomla\">creating a 3.x language pack</a>.</p>",
				],
				[
					'title' => 'More Information about Translations',
					'body' => "<p>There are a number of valuable resources for adding your language to Joomla! and learning more about the <a href=\"https://volunteers.joomla.org/teams/core-translation-team\">Core Translation Team</a>. You can ask questions and get tips in the <a href=\"https://forum.joomla.org/viewforum.php?f=11\">Translations Forum</a> or the <a href=\"https://forum.joomla.org/viewforum.php?f=617\">Language Forum</a> and there are a wealth of resources in <a href=\"https://forum.joomla.org/viewforum.php?f=511\">The International Zone Forum</a>.</p>",
				],
				[
					'title' => 'Want to propose packs for a language not listed here?',
					'body' => "<p>Please contact the <a href=\"https://volunteers.joomla.org/teams/core-translation-team\">Joomla! Core Translation Coordination Team</a></p>",
				],
				[
					'title' => 'Have an issue with the quality of a Translation or want to collaborate with an existing Team?',
					'body' => 'Please contact the language coordinator stated in the list below.',
				],
				[
					'title' => 'Information about new language functionalities in Joomla! 3.x',
					'body' => "<p><a href=\"https://docs.joomla.org/International_Enhancements_for_Version_1.6\">3.x brings a lot of new functionalities in Joomla!</a><br> <a href=\"https://docs.joomla.org/Language_Switcher_Tutorial_for_Joomla_1.6\">including a simple multilanguage site implementation</a>.</p>",
				],
			];
		}
		elseif ($this->getState('application_id') === 3)
		{
			return [
				[
					'title' => 'How to Create a Joomla! Translation',
					'body' => "<p>It's easy to translate Joomla into your language by <a href=\"https://docs.joomla.org/J2.5:Making_a_Language_Pack_for_Joomla\">creating a 2.5 language pack</a>.</p>",
				],
				[
					'title' => 'More Information about Translations',
					'body' => "<p>There are a number of valuable resources for adding your language to Joomla! and learning more about the <a href=\"https://volunteers.joomla.org/teams/core-translation-team\">Core Translation Team</a>. You can ask questions and get tips in the <a href=\"https://forum.joomla.org/viewforum.php?f=11\">Translations Forum</a> or the <a href=\"https://forum.joomla.org/viewforum.php?f=617\">Language Forum</a> and there are a wealth of resources in <a href=\"https://forum.joomla.org/viewforum.php?f=511\">The International Zone Forum</a>.</p>",
				],
				[
					'title' => 'Want to propose packs for a language not listed here?',
					'body' => "<p>Please contact the <a href=\"https://volunteers.joomla.org/teams/core-translation-team\">Joomla! Core Translation Coordination Team</a></p>",
				],
				[
					'title' => 'Have an issue with the quality of a Translation or want to collaborate with an existing Team?',
					'body' => 'Please contact the language coordinator stated in the list below.',
				],
				[
					'title' => 'Information about new language functionalities in Joomla! 2.5',
					'body' => "<p><a href=\"https://docs.joomla.org/International_Enhancements_for_Version_1.6\">2.5 brings a lot of new functionalities in Joomla!</a><br> <a href=\"https://docs.joomla.org/Language_Switcher_Tutorial_for_Joomla_1.6\">including a simple multilanguage site implementation</a>.</p>",
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
