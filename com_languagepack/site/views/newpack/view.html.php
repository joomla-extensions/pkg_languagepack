<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_languagepack
 *
 * @copyright   Copyright (C) 2020 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Form\Form;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Object\CMSObject;

/**
 * HTML New language pack view class for the Language pack component
 *
 * @since  1.0
 */
class LanguagepackViewNewpack extends HtmlView
{
	/**
	 * The form for the create pack form
	 *
	 * @var  Form
	 */
	protected $form;

	/**
	 * The form for the create pack form
	 *
	 * @var  CMSObject
	 */
	protected $item;

	/**
	 * Execute and display a template script.
	 *
	 * @param string $tpl The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 */
	public function display($tpl = null)
	{
		/** @var \LanguagepackModelNewpack $model */
		$model = $this->getModel();
		$this->form = $model->getForm();
		$this->item = $model->getItem();

		return parent::display($tpl);
	}
}
