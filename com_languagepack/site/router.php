<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_languagepack
 *
 * @copyright   Copyright (C) 2020 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\Router\RouterViewConfiguration;
use Joomla\CMS\Component\Router\Rules\MenuRules;
use Joomla\CMS\Component\Router\Rules\StandardRules;
use Joomla\CMS\Component\Router\Rules\NomenuRules;
use Joomla\CMS\Factory;
use Joomla\CMS\Menu\SiteMenu;

/**
 * Routing class from com_languagepack
 *
 * @since  1.0
 */
class LanguagepackRouter extends RouterView
{
	/**
	 * The database driver
	 *
	 * @var    \JDatabaseDriver
	 * @since  1.0
	 */
	protected $db;

	/**
	 * Search Component router constructor
	 *
	 * @param   CMSApplication  $app   The application object
	 * @param   SiteMenu        $menu  The menu object to work with
	 */
	public function __construct($app = null, $menu = null)
	{
		$this->db = Factory::getDbo();

		$applications = new RouterViewConfiguration('applications');
		$this->registerView($applications);

		$languages = new RouterViewConfiguration('languages');
		$languages->setKey('id')->setParent($applications, 'application_id');
		$this->registerView($languages);

		$language = new RouterViewConfiguration('language');
		$language->setKey('id');
		$this->registerView($language);

		parent::__construct($app, $menu);

		$this->attachRule(new MenuRules($this));
		$this->attachRule(new StandardRules($this));
		$this->attachRule(new NomenuRules($this));
	}

	/**
	 * Method to get the segment(s) for an application
	 *
	 * @param   string  $id     ID of the application to retrieve the segments for
	 * @param   array   $query  The request that is built right now
	 *
	 * @return  array|string  The segments of this item
	 */
	public function getApplicationSegment($id, $query)
	{
		if (!strpos($id, ':'))
		{
			$dbquery = $this->db->getQuery(true);
			$dbquery->select($this->db->quoteName('alias'))
				->from($this->db->quoteName('#__languagepack_applications'))
				->where('id = ' . $dbquery->q((int) $id));
			$this->db->setQuery($dbquery);

			$id .= ':' . $this->db->loadResult();
		}

		list($void, $segment) = explode(':', $id, 2);

		return array($void => $segment);
	}

	/**
	 * Method to get the segment(s) for an application
	 *
	 * @param   string  $segment  Segment of the contact to retrieve the ID for
	 * @param   array   $query    The request that is parsed right now
	 *
	 * @return  mixed   The id of this item or false
	 */
	public function getApplicationId($segment, $query)
	{
		$query = $this->db->getQuery(true);
		$query->select($this->db->quoteName('id'))
			->from($this->db->quoteName('#__languagepack_applications'))
			->where('alias = ' . $this->db->quote($segment))
			->where('application_id = ' . $this->db->quote($query['application_id']));
		$this->db->setQuery($query);

		return (int) $this->db->loadResult();
	}

	/**
	 * Method to get the segment(s) for a language
	 *
	 * @param   string  $id     ID of the language to retrieve the segments for
	 * @param   array   $query  The request that is built right now
	 *
	 * @return  array|string  The segments of this item
	 */
	public function getLanguagesSegment($id, $query)
	{
		return $this->getLanguageSegment($id, $query);
	}

	/**
	 * Method to get the segment(s) for a language
	 *
	 * @param   string  $segment  Segment of the contact to retrieve the ID for
	 * @param   array   $query    The request that is parsed right now
	 *
	 * @return  mixed   The id of this item or false
	 */
	public function getLanguagesId($segment, $query)
	{
		return $this->getLanguageSegment($segment, $query);
	}

	/**
	 * Method to get the segment(s) for a language
	 *
	 * @param   string  $id     ID of the language to retrieve the segments for
	 * @param   array   $query  The request that is built right now
	 *
	 * @return  array|string  The segments of this item
	 */
	public function getLanguageSegment($id, $query)
	{
		if (!strpos($id, ':'))
		{
			$dbquery = $this->db->getQuery(true);
			$dbquery->select($this->db->quoteName('alias'))
				->from($this->db->quoteName('#__languagepack_languages'))
				->where('id = ' . $this->db->quote((int) $id));
			$this->db->setQuery($dbquery);

			$id .= ':' . $this->db->loadResult();
		}

		list($void, $segment) = explode(':', $id, 2);

		return array($void => $segment);
	}

	/**
	 * Method to get the segment(s) for a language
	 *
	 * @param   string  $segment  Segment of the contact to retrieve the ID for
	 * @param   array   $query    The request that is parsed right now
	 *
	 * @return  mixed   The id of this item or false
	 */
	public function getLanguageId($segment, $query)
	{
		$dbquery = $this->db->getQuery(true);
		$dbquery->select($this->db->quoteName('id'))
			->from($this->db->quoteName('#__languagepack_languages'))
			->where('alias = ' . $this->db->quote($segment))
			->where('application_id = ' . $this->db->quote($query['application_id']));
		$this->db->setQuery($dbquery);

		return (int) $this->db->loadResult();
	}
}
