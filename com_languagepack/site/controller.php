<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_languagepack
 *
 * @copyright   Copyright (C) 2020 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;

/**
 * Language Pack master display controller.
 *
 * @since  1.0.0
 */
class LanguagepackController extends BaseController
{
	/**
	 * The application object
	 *
	 * @var  \Joomla\CMS\Application\CMSApplication
	 */
	protected $app;

	/**
	 * The default view for the display method.
	 *
	 * @var    string
	 * @since  3.0
	 */
	protected $default_view = 'languages';
}
