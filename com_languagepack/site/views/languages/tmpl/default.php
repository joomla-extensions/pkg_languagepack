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
	<?php foreach ($this->languages as $language): ?>
	<div id="<?php echo $language->lang_code ?>">
        <h1><?php echo Text::sprintf('COM_LANGUAGE_PACK_NAME', $language->name) ?></h1>
        <!-- Show latest release for each of the major versions here -->
	</div>
	<?php endforeach; ?>
</div>
