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
	 * Search Component router constructor
	 *
	 * @param   CMSApplication  $app   The application object
	 * @param   SiteMenu        $menu  The menu object to work with
	 */
	public function __construct($app = null, $menu = null)
	{
		$language = new RouterViewConfiguration('language');
		$language->setKey('id');
		$this->registerView($language);

		parent::__construct($app, $menu);

		$this->attachRule(new MenuRules($this));
		$this->attachRule(new StandardRules($this));
		$this->attachRule(new NomenuRules($this));
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
			$db = Factory::getDbo();
			$dbquery = $db->getQuery(true);
			$dbquery->select($dbquery->qn('alias'))
				->from($dbquery->qn('#__contact_details'))
				->where('id = ' . $dbquery->q((int) $id));
			$db->setQuery($dbquery);

			$id .= ':' . $db->loadResult();
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
		$db = Factory::getDbo();
		$dbquery = $db->getQuery(true);
		$dbquery->select($dbquery->qn('id'))
			->from($dbquery->qn('#__contact_details'))
			->where('alias = ' . $dbquery->q($segment))
			->where('catid = ' . $dbquery->q($query['id']));
		$db->setQuery($dbquery);

		return (int) $db->loadResult();
	}
}
