<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_languagepack
 *
 * @copyright   Copyright (C) 2020 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
?>
<form action="<?php echo Route::_('index.php?option=com_languagepack&layout=edit&id=' . (int) $this->item->id); ?>"
      method="post" name="adminForm" id="adminForm">
    <div class="form-horizontal">
        <fieldset class="adminform">
            <legend><?php echo Text::_('COM_LANGUAGEPACK_LANGUAGE_DETAILS'); ?></legend>
            <div class="row-fluid">
                <div class="span6">
					<?php
					foreach($this->form->getFieldset() as $field) {
						echo $field->renderField();
					}
					?>
                </div>
            </div>
        </fieldset>
    </div>
    <input type="hidden" name="task" value="language.edit" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
