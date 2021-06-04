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
use Joomla\CMS\Language\Text;

/**
 * Export view class
 *
 * @since  1.0
 *
 * @property-read   \Joomla\CMS\Document\XmlDocument $document
 */
class LanguagepackViewExport extends HtmlView
{
	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  string  A XML if successful, otherwise an XML Error.
	 */
	public function display($tpl = null)
	{
		/** @var LanguagepackModelExport $model */
		$model = $this->getModel();

		$exportData = $model->collectDataForExportRequest();

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			echo $this->renderXmlError($errors);

			return;
		}
		elseif (!$exportData || !isset($exportData['total'])
			|| !$exportData['total'])
		{
			echo $this->renderXmlError([Text::_('COM_LANGUAGE_PACK_EXPORT_NO_DATA_FOUND')]);

			return;
		}

		$cmsVersion   = $model->getState(
			$model->getName() . '.cms_version'
		);
		$languageCode = $model->getState(
			$model->getName() . '.language_code'
		);

		// This document should always be downloaded
		// not sure what is the best choice here (lets see)
		// $this->document->setDownload(true);

		// Set the document name
		if ($languageCode)
		{
			$this->document->setName(
				$languageCode . '_details'
			);

			echo $this->renderLanguageAsXml($exportData['versions']);
		}
		else
		{
			$this->document->setName(
				$model->mapping[$cmsVersion]['filename']
			);

			echo $this->renderLanguagesAsXml($exportData['languages']);
		}

	}

	/**
	 * Render the languages as a XML document.
	 *
	 * @param   array  $exportData  The data to be exported.
	 *
	 * @return  string
	 *
	 * @since  1.0
	 */
	protected function renderLanguagesAsXml(array $exportData)
	{
		$export = new SimpleXMLElement(
			'<?xml version="1.0" encoding="utf-8"?><extensionset />'
		);
		$export->addAttribute('name', 'Accredited Joomla! Translations');
		$export->addAttribute(
			'description', 'Accredited Joomla! Translations Updates'
		);

		foreach ($exportData as $language)
		{
			$xmlLang = $export->addChild('extension');
			$xmlLang->addAttribute('name', $language['name']);
			$xmlLang->addAttribute('element', $language['element']);
			$xmlLang->addAttribute('type', 'package');
			$xmlLang->addAttribute('version', $language['latestVersion']);
			$xmlLang->addAttribute('detailsurl', $language['url']);
		}

		$dom = new DOMDocument;
		$dom->loadXML($export->asXML());
		$dom->formatOutput = true;

		return $dom->saveXML();
	}

	/**
	 * Render a language details as a XML document.
	 *
	 * @param   array  $exportData  The data to be exported.
	 *
	 * @return  string
	 *
	 * @since  1.0
	 */
	protected function renderLanguageAsXml(array $exportData)
	{
		$export = new SimpleXMLElement(
			'<?xml version="1.0" encoding="utf-8"?><updates />'
		);

		foreach ($exportData as $update)
		{
			$xmlLang              = $export->addChild('update');
			$xmlLang->name        = $update['name'];
			$xmlLang->description = $update['description'];
			$xmlLang->element     = $update['element'];
			$xmlLang->type        = $update['type'];
			$xmlLang->version     = $update['version'];

			$xmlDownLoad = $xmlLang->addChild('downloads');

			foreach ($update['downloads'] as $download)
			{
				$xmlDownLoad->downloadurl = $download['url'];

				$xmlDownLoad->downloadurl->addAttribute(
					'type', $download['type']
				);
				$xmlDownLoad->downloadurl->addAttribute(
					'format', $download['format']
				);
			}

			// Add the hashes. Joomla treats each Download as being the same file but a different CDN. So we just
			// assume there is one file and use the last file's hash in ARS.
			$xmlLang->sha256 = $download['sha256'];
			$xmlLang->sha384 = $download['sha384'];
			$xmlLang->sha512 = $download['sha512'];

			$xmlPlatform = $xmlLang->addChild('targetplatform');
			$xmlPlatform->addAttribute('name', 'joomla');
			$xmlPlatform->addAttribute('version', $update['target']);
		}

		$dom = new DOMDocument;
		$dom->loadXML($export->asXML());
		$dom->formatOutput = true;

		return $dom->saveXML();
	}

	/**
	 * Render the XML error. (simple)
	 *
	 * @param   array  $messages  The error messages
	 *
	 * @return  string
	 *
	 * @since  1.0
	 */
	protected function renderXmlError(array $messages)
	{
		$errors = new SimpleXMLElement(
			'<?xml version="1.0" encoding="utf-8"?><errors />'
		);

		foreach ($messages as $message)
		{
			$error          = $errors->addChild('error');
			$error->message = $message;
		}

		$dom = new DOMDocument;
		$dom->loadXML($errors->asXML());
		$dom->formatOutput = true;

		return $dom->saveXML();
	}
}
