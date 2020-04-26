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
	protected $default_view = 'applications';

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

		$vName = $this->input->getCmd('view', $this->default_view);

		if ($vName === 'newpack')
		{
			$cachable = false;

			// Check people aren't trying to create new packs who can't.
			if (!Factory::getUser()->authorise('core.create', 'com_languagepack'))
			{
				throw new \Exception(Text::_('JGLOBAL_AUTH_ACCESS_DENIED'), 403);
			}
		}

		return parent::display($cachable, $urlparams);
	}
}
