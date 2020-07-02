<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_languagepacks
 *
 * @copyright   Copyright (C) 2020 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/** @var  $this  LanguagepackViewApplication */

use Joomla\CMS\Factory;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$lang = Factory::getLanguage();
$languages = LanguageHelper::getLanguages('lang_code');
$languageCode = $languages[ $lang->getTag() ]->sef;
?>

<div class="languages">
    <h1>
	    <?php echo Text::sprintf('COM_LANGUAGE_PACK_TRANSLATIONS_AVAILABLE_IN', Text::_($this->applicationName)) ?>
    </h1>
    <?php if (!empty($this->languages)) : ?>
    <p>
    <?php $i = 1; // Counter so we don't show a pipe after the last language ?>
    <?php foreach ($this->languages as $language): ?>
        <a href="#<?php echo $language->lang_code ?>>"><?php echo $language->name ?></a><?php if (count($this->languages) !== $i): ?> &#124; <?php endif; ?>
        <?php $i++; ?>
    <?php endforeach;?>
    </p>

	<?php foreach ($this->languages as $language): ?>
    <div class="items-row cols-1 row-0 row-fluid clearfix">
        <div class="span12">
            <div class="item column-1" itemprop="blogPost" itemscope="" itemtype="https://schema.org/BlogPosting">
                <div class="page-header">
                    <h2 itemprop="name">
	                    <?php echo $language->name ?> Translation for <?php echo Text::_($this->applicationName); ?>
                    </h2>
                </div>
                <p><a name="<?php echo $language->lang_code ?>"></a><span class="contentheading"><?php echo $language->name ?> Translation</span></p>
                <p>Language: <?php echo $language->name ?> (<?php echo $language->lang_code ?>)</p>
                <!-- TODO: This needs the Itemid in the URL to to work properly -->
                <p>Download Language Pack:<br> <a href="<?php echo Route::_('index.php?option=com_ars&view=Releases&category=' . $language->ars_category . '&lang=' . $languageCode); ?>"> here</a></p>
	            <?php if (in_array($language->group_id, Factory::getUser()->getAuthorisedGroups())): ?>
                    <a class="btn btn-warning" href="<?php echo Route::_('index.php?option=com_languagepack&task=release.add&langid=' . $language->id . '&application_id=' . $this->applicationId); ?>"><?php echo Text::sprintf('COM_LANGUAGE_PACK_LANGUAGE_CREATE_A_RELEASE'); ?></a>
	            <?php endif; ?>
            </div>
        </div>
    </div>
	<?php endforeach;?>
	<?php endif; ?>
</div>
