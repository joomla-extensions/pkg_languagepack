<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_languagepacks
 *
 * @copyright   Copyright (C) 2020 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/** @var  $this  LanguagepackViewLanguages */

use Joomla\CMS\Language\Text;
?>

<div class="languages">
    <h1>
        <?php echo Text::sprintf('COM_LANGUAGE_PACK_TRANSLATIONS_AVAILABLE_IN', $this->joomlaVersion) ?>
    </h1>
    <?php if (!empty($this->languages)) : ?>
    <ul>
        <?php foreach ($this->languages as $language): ?>
        <li id="lang-<?php echo $language->lang_code ?>">
            <p><?php echo $language->name; ?></p>
        </li>
        <?php endforeach; ?>
    </ul>
	<?php endif; ?>
</div>
