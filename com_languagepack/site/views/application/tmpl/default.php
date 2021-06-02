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

// Ensure jQuery exists TODO: Use native JS
HTMLHelper::_('jquery.framework');

$document = Factory::getDocument();
$document->addScriptDeclaration('
	jQuery(document).ready(function ($){
		$(\'#language_picker\').change(function(){
			$(\'.language-block\').hide();
			if ($(this).val() === \'ALL\'){
				$(\'.language-block\').show();
			} else {
				$(\'.language-block.\' + $(this).val()).show();
			}
		});
	});
');

$lang = Factory::getLanguage();
$languages = LanguageHelper::getLanguages('lang_code');
$languageCode = $languages[ $lang->getTag() ]->sef;
?>

<div class="languages">
	<h1>
		<?php echo Text::sprintf('COM_LANGUAGE_PACK_TRANSLATIONS_AVAILABLE_IN', Text::_($this->applicationName)) ?>
	</h1>

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
        <label for="language_picker"><?php echo Text::sprintf('COM_LANGUAGE_PACK_CHOOSE_TRANSLATION_LABEL'); ?></label>
        <select id="language_picker">
            <option value="ALL"><?php echo Text::sprintf('COM_LANGUAGE_PACK_VIEW_ALL_TRANSLATIONS'); ?></option>
            <?php foreach ($this->languages as $language): ?>
                <option value="<?php echo $language->lang_code;?>"><?php echo $language->name;?></option>
            <?php endforeach;?>
        </select>

        <script type="text/javascript">
        </script>
	<?php endif; ?>

	<?php if (!empty($this->languages)) : ?>
	<?php foreach ($this->languages as $language): ?>
	<div class="items-row cols-1 row-0 row-fluid clearfix language-block <?php echo $language->lang_code; ?>">
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
							<?php echo Text::sprintf('COM_LANGUAGE_PACK_APPLICATION_TEAM_COORDINATOR', $language->coordinator); ?>
						<?php endif; ?>
					<br>
					<?php if (!empty($language->coordinator_email)) : ?>
						<?php echo Text::sprintf('COM_LANGUAGE_PACK_CONTACT_EMAIL', $language->coordinator_email); ?>
					<?php endif; ?>
					<br>
					<?php echo !empty($language->website) ? ' ' . Text::sprintf('COM_LANGUAGE_PACK_CONTACT_WEBSITE', '<a href="' . $language->website . '">' . $language->website . '</a>') . ' ' : ''; ?>
					</p>
				</div>

				<div class="language-buttons">
						<a class="btn btn-success" href="<?php echo Route::_('index.php?option=com_ars&view=Releases&category_id=' . $language->ars_category . (($this->arsMenuId === 0) ? '' : '&Itemid=' . $this->arsMenuId)); ?>"><?php echo Text::sprintf('COM_LANGUAGE_PACK_APPLICATION_DOWNLOAD'); ?></a>
					<?php if (!$language->application_lock && !$language->lock && in_array($language->group_id, Factory::getUser()->getAuthorisedGroups())): ?>
						<a class="btn btn-warning" href="<?php echo Route::_('index.php?option=com_languagepack&task=release.add&langid=' . $language->id . '&application_id=' . $this->applicationId); ?>"><?php echo Text::sprintf('COM_LANGUAGE_PACK_LANGUAGE_CREATE_A_RELEASE'); ?></a>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
	<?php endforeach;?>
	<?php endif; ?>
</div>
