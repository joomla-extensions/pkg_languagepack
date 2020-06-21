<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_languagepacks
 *
 * @copyright   Copyright (C) 2020 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/** @var  $this  LanguagepackViewApplications */

use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
?>

<div class="languages">
	<h1>
		<?php echo Text::sprintf('COM_LANGUAGE_PACK_APPLICATIONS') ?>
	</h1>
	<?php if (!empty($this->applications)) : ?>
	<ul>
		<?php foreach ($this->applications as $application): ?>
		<li>
			<a href="<?php echo Route::_('index.php?option=com_languagepack&view=application&application_id=' . $application->id); ?>">
				<?php echo Text::_($application->name); ?>
			</a>
			<?php echo Text::_($application->description); ?>
		</li>
		<?php endforeach; ?>
	</ul>
	<?php endif; ?>
	<p><?php echo Text::_('COM_LANGUAGE_PACK_APPLICATIONS_INFO'); ?></p>
	<p><?php echo Text::_('COM_LANGUAGE_PACK_HOW_TO_INSTALL'); ?></p>
</div>
