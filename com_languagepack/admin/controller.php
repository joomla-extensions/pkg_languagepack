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
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;

/**
 * Language Pack master display controller.
 *
 * @since  1.0.0
 */
class LanguagepackController extends BaseController
{
	/**
	 * The default view for the display method.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $default_view = 'languages';

	/**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached.
	 * @param   boolean  $urlparams  An array of safe URL parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  static  This object to support chaining.
	 *
	 * @since   1.0
	 */
	public function display($cachable = true, $urlparams = array())
	{
		$urlparams += array('langid' => 'INT', 'application_id' => 'INT');

		return parent::display($cachable, $urlparams);
	}
}
