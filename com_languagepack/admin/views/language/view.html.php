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
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * HTML Languages View class for the Language pack component
 *
 * @since  1.0
 */
class LanguagepackViewLanguage extends HtmlView
{
	/**
	 * List of languages
	 *
	 * @var  \Joomla\CMS\Object\Object
	 */
	protected $item;

	/**
	 * Pagination Object
	 *
	 * @var  \Joomla\CMS\Form\Form
	 */
	protected $form;

	/**
	 * Execute and display a template script.
	 *
	 * @param string $tpl The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 */
	public function display($tpl = null)
	{
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			// TODO: Improve this get all errors and pipe them all in maybe a custom exception
			throw new \RuntimeException($errors);
		}

		// Set the toolbar
		$this->addToolBar();

		// Display the template
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function addToolBar()
	{
		$input = Factory::getApplication()->input;

		// Hide Joomla Administrator Main menu
		$input->set('hidemainmenu', true);

		$isNew = ($this->item->id == 0);

		if ($isNew)
		{
			$title = Text::_('COM_LANGUAGEPACK_LANGUAGE_NEW');
		}
		else
		{
			$title = Text::_('COM_LANGUAGEPACK_LANGUAGE_EDIT');
		}

		ToolbarHelper::title($title, 'languagepack');
		ToolbarHelper::save('language.save');
		ToolbarHelper::cancel(
			'language.cancel',
			$isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE'
		);
	}
}
