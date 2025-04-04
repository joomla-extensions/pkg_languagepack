<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_languagepack
 *
 * @copyright   Copyright (C) 2020 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use FOF30\Container\Container;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\ListModel;

/**
 * Export model class.
 *
 * @since  1.0
 */
class LanguagepackModelExport extends ListModel
{
	/**
	 * Maps the API Path
	 *
	 * (which we keep consistent with existing breakdown etc endpoints) to the language pack
	 * component application ID and the corresponding ARS Menu ID for that language for nice SEF URLs.
	 *
	 * @var    Array
	 * @since  1.0
	 */
	public $mapping
		= [
			10 => [
				'application_id' => 1,
				'menu_id'        => 678,
				'filename'       => 'translationlist_1',
				'folder'         => 'details1',
				'target'         => '1.0'
			],
			15 => [
				'application_id' => 2,
				'menu_id'        => 677,
				'filename'       => 'translationlist_1_5',
				'folder'         => 'details1_5',
				'target'         => '1.5'
			],
			25 => [
				'application_id' => 3,
				'menu_id'        => 676,
				'filename'       => 'translationlist',
				// this should have been 'translationlist_2_5'
				'folder'         => 'details',
				// this should have been 'details2_5'
				'target'         => '2.5'
			],
			30 => [
				'application_id' => 4,
				'menu_id'        => 674,
				'filename'       => 'translationlist_3',
				'folder'         => 'details3',
				'target'         => '3.([0123456789]|10)'
			],
			40 => [
				'application_id' => 5,
				'menu_id'        => 675,
				'filename'       => 'translationlist_4',
				'folder'         => 'details4',
				'target'         => '4.[01234]'
			],
			50 => [
				'application_id' => 6,
				'menu_id'        => 1198,
				'filename'       => 'translationlist_5',
				'folder'         => 'details5',
				'target'         => '5.[0123]'
			],	
		];

	/**
	 * Create the export document for a language xml request.
	 *
	 * @param   string  $type  The request TYPE to process
	 *
	 * @return  array|boolean  A array for a successful export or boolean false on an error
	 *
	 * @since  1.0
	 */
	public function collectDataForExportRequest($cmsVersion = null,
		$languageCode = null
	) {
		$cmsVersion   = !empty($cmsVersion) ? $cmsVersion
			: (string) $this->getState($this->getName() . '.cms_version');
		$languageCode = !empty($languageCode) ? $languageCode
			: (string) $this->getState($this->getName() . '.language_code');

		// we must have a CMS version
		if (!$cmsVersion)
		{
			$this->setError(
				Text::_('COM_LANGUAGE_PACK_EXPORT_CMS_VERSION_REQUIRED')
			);

			return false;
		}
		elseif (!isset($this->mapping[$cmsVersion]))
		{
			$this->setError(
				Text::_('COM_LANGUAGE_PACK_EXPORT_CMS_VERSION_NOT_FOUND')
			);

			return false;
		}

		if ($languageCode)
		{
			return $this->getLanguageBreakdown($cmsVersion, $languageCode);
		}

		return $this->getLanguagesByversion($cmsVersion);
	}

	/**
	 * Get the breakdown of a language.
	 *
	 * @param   string  $cmsVersion    The request CMS Version to retrieve
	 * @param   string  $languageCode  The request Language Code to retrieve
	 *
	 * @return  array|boolean  A array for a successful export or boolean false on an error
	 *
	 * @since  1.0
	 */
	protected function getLanguageBreakdown($cmsVersion, $languageCode)
	{
		$db           = \JFactory::getDbo();
		$arsContainer = Container::getInstance('com_ars');

		$query = $db->getQuery(true)
			->select($db->quoteName('a.ars_category'))
			->from($db->quoteName('#__languagepack_languages', 'a'))
			->rightJoin(
				$db->quoteName('#__languagepack_applications', 'b') . ' ON '
				. $db->quoteName('b.id') . ' = ' . $db->quoteName(
					'a.application_id'
				)
			)
			->where(
				$db->quoteName('a.application_id') . ' = '
				. (int) $this->mapping[$cmsVersion]['application_id']
			)
			->where(
				$db->quoteName('a.lang_code') . ' = ' . $db->quote(
					$languageCode
				)
			)
			->where('a.state = ' . 1)
			->where('b.state = ' . 1);

		$db->setQuery($query);
		$arsCategoryId = $db->loadResult();

		if (!$arsCategoryId)
		{
			return false;
		}

		/** @var \Akeeba\ReleaseSystem\Admin\Model\Releases $model */
		$model = $arsContainer->factory->model('Releases')->tmpInstance();

		/** @var \FOF30\Model\DataModel\Collection $releases */
		$releases = $model->reset(true)
			->category_id(['value' => $arsCategoryId, 'method' => 'exact'])
			->published(1)
			->access_user($arsContainer->platform->getUser()->id)
			->with(['items', 'category'])
			->get(true);

		$totalReleases = $releases->count();
		$versions      = [];
		$rootURL       = rtrim(\JUri::root(), '/');
		$target        = $this->mapping[$cmsVersion]['target'];

		if ($totalReleases)
		{
			/** @var \Akeeba\ReleaseSystem\Admin\Model\Releases $release */
			foreach ($releases as $release)
			{
				$version = [
					'name'        => $release->category->title, // not ideal
					'description' => strip_tags($release->description),
					'version'     => $release->version,
					'element'     => 'pkg_' . $languageCode,
					'type'        => 'package',
					'target'      => $target,
					'downloads'   => [],
				];

				foreach ($release->items as $item)
				{
					if ($item->type !== 'file')
					{
						continue;
					}

					$downloadUrl = \JRoute::link(
						'site',
						'index.php?option=com_ars&view=Item&task=download&id='
						. $item->id . '&Itemid='
						. $this->mapping[$cmsVersion]['menu_id']
					);

					// We use the same structure for response as in the CMS signatures endpoint. Yay for API Consistency!
					$version['downloads'][] = [
						'url'    => $rootURL . $downloadUrl,
						'type'   => 'full',
						'format' => 'zip',
						// the type and format values not found in the item object
						// we could pull this from the filename, but not ideal?
						'md5'    => $item->md5,
						'sha1'   => $item->sha1,
						'sha256' => $item->sha256,
						'sha384' => $item->sha384,
						'sha512' => $item->sha512
					];
				}

				$versions[] = $version;
			}

			$returnedData = [
				'total'    => $totalReleases,
				'versions' => $versions,
			];

			return $returnedData;
		}

		return false;
	}

	/**
	 * Get the breakdown of a language.
	 *
	 * @param   string  $cmsVersion  The request CMS Version to retrieve
	 *
	 * @return  array|boolean  A array for a successful export or boolean false on an error
	 *
	 * @since  1.0
	 */
	protected function getLanguagesByversion($cmsVersion)
	{
		$model = \JModelLegacy::getInstance(
			'Application', 'LanguagepackModel', array('ignore_request' => true)
		);
		$model->setState(
			'application_id', $this->mapping[$cmsVersion]['application_id']
		);
		$items        = $model->getItems();
		$results      = [];
		$arsContainer = Container::getInstance('com_ars');

		/** @var \Akeeba\ReleaseSystem\Admin\Model\Releases $model */
		$model = $arsContainer->factory->model('Releases')->tmpInstance();

		$model->reset(true)
			->published(1)
			->latest(true)
			->access_user($arsContainer->platform->getUser()->id)
			->with(['items', 'category']);

		/** @var \FOF30\Model\DataModel\Collection $releases */
		$releases = $model->get(true)->filter(
			function ($item) {
				return \Akeeba\ReleaseSystem\Site\Helper\Filter::filterItem(
					$item, true
				);
			}
		);

		$categoryLatest = [];

		if ($releases->count())
		{
			/** @var \Akeeba\ReleaseSystem\Admin\Model\Releases $release */
			foreach ($releases as $release)
			{
				$categoryLatest[$release->category->id] = $release->getData();
			}
		}

		foreach ($items as $item)
		{
			// If we don't have an available release on ARS don't return it...
			// In the future we may want to make this access level driven
			if (!array_key_exists($item->ars_category, $categoryLatest))
			{
				continue;
			}

			$result = [
				'name'          => $item->name,
				'languageCode'  => $item->lang_code,
				'element'       => 'pkg_' . $item->lang_code,
				'latestVersion' => $categoryLatest[$item->ars_category]['version'],
				'url'           => 'https://update.joomla.org/language/'
					. $this->mapping[$cmsVersion]['folder'] . '/'
					. $item->lang_code . '_details.xml'
			];

			$results[] = $result;
		}

		$returnedData = [
			'total'     => count($results),
			'languages' => $results,
		];

		return $returnedData;
	}

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
		$app = Factory::getApplication();
		$this->setState('params', $app->getParams());
		$this->setState(
			$this->getName() . '.cms_version',
			$app->input->getInt('cms_version')
		);
		$this->setState(
			$this->getName() . '.language_code',
			$app->input->getCmd('language_code', null)
		);

		parent::populateState($ordering, $direction);

		// Override the list model to show all languages in the frontend
		$this->setState('list.limit', 0);
	}
}
