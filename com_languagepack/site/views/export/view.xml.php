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
			echo $this->renderXmlError(['No Data Found']);

			return;
		}

		$cmsVersion   = $model->getState(
			$model->getName() . '.cms_version'
		);
		$languageCode = $model->getState(
			$model->getName() . '.language_code'
		);

		// This document should always be downloaded
		$this->document->setDownload(true);

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
			$xmlLang->addAttribute('element', $language['languageCode']);
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
				$xmlDownLoad->downloadurl->addAttribute(
					'md5', $download['md5']
				);
				$xmlDownLoad->downloadurl->addAttribute(
					'sha1', $download['sha1']
				);
				$xmlDownLoad->downloadurl->addAttribute(
					'sha256', $download['sha256']
				);
				$xmlDownLoad->downloadurl->addAttribute(
					'sha384', $download['sha384']
				);
				$xmlDownLoad->downloadurl->addAttribute(
					'sha512', $download['sha512']
				);
			}

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
