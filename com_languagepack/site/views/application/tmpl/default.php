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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

HTMLHelper::_('stylesheet', 'com_languagepack/com_lp_front.css', array('version' => 'auto', 'relative' => true));

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
		<a href="#<?php echo $language->lang_code ?>"><?php echo $language->name ?></a><?php if (count($this->languages) !== $i): ?> &#124; <?php endif; ?>
		<?php $i++; ?>
	<?php endforeach;?>
	</p>
	<?php endif; ?>

	<?php if (!empty($this->extraInfo)) : ?>
	<div class="accordion" id="accordion">
		<?php foreach ($this->extraInfo as $i => $accordionItem) : ?>
		<div class="accordion-group">
			<div class="accordion-heading">
				<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $i; ?>">
					<?php echo $accordionItem['title'] ?>
				</a>
			</div>
			<div id="collapse<?php echo $i; ?>" class="accordion-body collapse<?php echo $i === 0 ? ' in' : '' ?>">
				<div class="accordion-inner">
					<?php echo $accordionItem['body'] ?>
				</div>
			</div>
		</div>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>

	<?php if (!empty($this->languages)) : ?>
	<?php foreach ($this->languages as $language): ?>
	<div class="items-row cols-1 row-0 row-fluid clearfix language-block">
		<div class="span12">
			<div class="item column-1" itemprop="blogPost" itemscope="" itemtype="https://schema.org/BlogPosting">
				<div class="page-header language-definition">
					<h2 itemprop="name">
						<?php echo Text::sprintf('COM_LANGUAGE_PACK_APPLICATION_TRANSLATION_FOR', $language->name, Text::_($this->applicationName)) ?>
					</h2>
				</div>

				<div class="language-information">
					<p><?php echo Text::sprintf('COM_LANGUAGE_PACK_APPLICATION_LANGUAGE', $language->name, $language->lang_code); ?>
					<br>
						<?php if (!empty($language->coordinator_forum_id)) : ?>
							<?php echo Text::sprintf('COM_LANGUAGE_PACK_APPLICATION_TEAM_COORDINATOR', '<a href="https://forum.joomla.org/memberlist.php?mode=viewprofile&u=' . $language->coordinator_forum_id . '">' . $language->coordinator . '</a>'); ?>
						<?php else: ?>
							<?php echo $language->coordinator; ?>
						<?php endif; ?>
					<br>
					<?php if (!empty($language->coordinator_email)) : ?>
						<?php echo Text::sprintf('COM_LANGUAGE_PACK_CONTACT_EMAIL', $language->coordinator_email); ?>
					<?php endif; ?>
					<?php echo !empty($language->website) ? ' ' . Text::sprintf('COM_LANGUAGE_PACK_CONTACT_WEBSITE', '<a href="' . $language->website . '">' . $language->website . '</a>') . ' ' : ''; ?>
					</p>
				</div>

				<!-- TODO: 1. Variable for the ItemId -->
				<div class="language-buttons">
						<a class="btn btn-success" href="<?php echo Route::_('index.php?option=com_ars&view=Releases&category_id=' . $language->ars_category . '&Itemid=720'); ?>"><?php echo Text::sprintf('COM_LANGUAGE_PACK_APPLICATION_DOWNLOAD'); ?></a>
					<?php if (in_array($language->group_id, Factory::getUser()->getAuthorisedGroups())): ?>
						<a class="btn btn-warning" href="<?php echo Route::_('index.php?option=com_languagepack&task=release.add&langid=' . $language->id . '&application_id=' . $this->applicationId); ?>"><?php echo Text::sprintf('COM_LANGUAGE_PACK_LANGUAGE_CREATE_A_RELEASE'); ?></a>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
	<?php endforeach;?>
	<?php endif; ?>
</div>
