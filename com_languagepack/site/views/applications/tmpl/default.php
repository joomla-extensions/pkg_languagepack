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

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;

HTMLHelper::_('stylesheet', 'com_languagepack/com_lp_front.css', array('version' => 'auto', 'relative' => true));
?>

<div class="languages">
  <div class="page-header">
	  <h1>
		  <?php echo Text::sprintf('COM_LANGUAGE_PACK_APPLICATIONS') ?>
	  </h1>
  </div>
  <div><p class="applications-info lead"><?php echo Text::sprintf('COM_LANGUAGE_PACK_APPLICATIONS_INFO', $this->numberOfLanguages); ?></p></div>
	<?php if (!empty($this->applications)) : ?>
	<div class="languages-versions">
		<?php foreach ($this->applications as $application): ?>
		<div>
			<div>
				<h2>
					<a href="<?php echo Route::_('index.php?option=com_languagepack&view=application&application_id=' . $application->id); ?>">
						<?php echo Text::_($application->name); ?>
					</a>
				</h2>
				<div>
					<?php echo Text::_($application->description); ?>
				</div>
			</div>
		</div>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>
	<p><?php echo Text::_('COM_LANGUAGE_PACK_HOW_TO_INSTALL'); ?></p>
</div>
