<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_languagepack
 *
 * @copyright   Copyright (C) 2020 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * HTML Languages View class for the Language pack component
 *
 * @since  1.0
 */
class LanguagepackViewLanguages extends HtmlView
{
	/**
	 * List of languages
	 *
	 * @var  array
	 */
	protected $languages = array();

	/**
	 * Pagination Object
	 *
	 * @var  \Joomla\CMS\Pagination\Pagination
	 */
	protected $pagination;

	/**
	 * Form object for search filters
	 *
	 * @var  \Joomla\CMS\Form\Form
	 */
	public $filterForm;

	/**
	 * The active search filters
	 *
	 * @var  array
	 */
	public $activeFilters;

	/**
	 * Execute and display a template script.
	 *
	 * @param string $tpl The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 */
	public function display($tpl = null)
	{
		/** @var \LanguagepackModelLanguages $model */
		$model = $this->getModel();
		$this->languages  = $model->getItems();
		$this->pagination = $model->getPagination();
		$this->filterForm    = $model->getFilterForm();
		$this->activeFilters = $model->getActiveFilters();

		if ($this->applications === false)
		{
			// TODO: Improve this get all errors and pipe them all in maybe a custom exception
			throw new \RuntimeException($model->getError());
		}

		$this->addToolbar();

		return parent::display($tpl);
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
		ToolbarHelper::title(Text::_('COM_LANGUAGEPACK_LANGUAGES_VIEW'));
		ToolbarHelper::addNew('language.add');
		ToolbarHelper::editList('language.edit');
		ToolbarHelper::deleteList('', 'language.delete');
	}
}
