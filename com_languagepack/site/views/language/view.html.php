<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_languagepack
 *
 * @copyright   Copyright (C) 2020 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView;

/**
 * HTML Language View class for the Language pack component
 *
 * @since  1.0
 */
class LanguagepackViewLanguage extends HtmlView
{
	/**
	 * List of languages
	 *
	 * @var  \stdClass
	 */
	protected $language;

	/**
	 * The application name we are listing languages for
	 *
	 * @var  string
	 */
	protected $applicationName;

	/**
	 * Execute and display a template script.
	 *
	 * @param string $tpl The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 */
	public function display($tpl = null)
	{
		/** @var \LanguagepackModelLanguage $model */
		$model = $this->getModel();
		$this->applicationName = $model->getApplicationName();
		$this->language = $model->getItem();

		return parent::display($tpl);
	}
}
