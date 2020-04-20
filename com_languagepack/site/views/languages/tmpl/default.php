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
use Joomla\CMS\Router\Route;
?>

<div class="languages">
    <h1>
	    <?php echo Text::sprintf('COM_LANGUAGE_PACK_TRANSLATIONS_AVAILABLE_IN', Text::_($this->applicationName)) ?>
    </h1>
    <?php if (!empty($this->languages)) : ?>
    <ul>
        <?php foreach ($this->languages as $language): ?>
        <li id="lang-<?php echo $language->lang_code ?>">
            <a href="<?php echo Route::_('index.php?option=com_languagepack&view=language&langid=' . $language->id); ?>">
		        <?php echo $language->name; ?>
            </a>
        </li>
        <?php endforeach; ?>
    </ul>
	<?php endif; ?>
</div>
