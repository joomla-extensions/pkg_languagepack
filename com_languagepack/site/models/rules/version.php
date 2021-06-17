<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_languagepack
 *
 * @copyright   Copyright (C) 2021 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Form\Form;
use Joomla\CMS\Form\FormRule;
use Joomla\Registry\Registry;

/**
 * Joomla Version validation
 *
 * @since 1.0.3
 */
class LanguagepackFormRuleVersion extends FormRule
{
	/**
	 * Method to test that an valid version value was added.
	 *
	 * @param   \SimpleXMLElement  $element  The SimpleXMLElement object representing the `<field>` tag for the form field object.
	 * @param   mixed              $value    The form field value to validate.
	 * @param   string             $group    The field name group control value. This acts as an array container for the field.
	 *                                       For example if the field has name="foo" and the group value is set to "bar" then the
	 *                                       full field name would end up being "bar[foo]".
	 * @param   Registry           $input    An optional Registry object with the entire data set to validate against the entire form.
	 * @param   Form               $form     The form object for which the field is being tested.
	 *
	 * @return  boolean  True if the value is valid version, false otherwise.
	 *
	 */
	public function test(\SimpleXMLElement $element, $value, $group = null, Registry $input = null, Form $form = null)
	{
		// Check if the field is required.
		$required = ((string) $element['required'] == 'true' || (string) $element['required'] == 'required');

		// If the value is empty and the field is not required return True.
		if (($value === '' || $value === null) && ! $required)
		{
			return true;
		}

		/**
		 * Fail if the string contains whitespace
		 * TODO: Potentially pass if at the start/end of the string but trim it in the model?
		 */
		if (preg_match('/\s/', $value))
		{
			return false;
		}

		/**
		 * Not accepted:
		 * 4.0.0-rc
		 * 4.0.0-
		 * 4.0
		 * 4
		 *
		 * Accepted:
		 * 4.0.0
		 * 4.0.0-rc11
		 * 4.0.0-beta1
		 * 3.9.29
		 */
		if(preg_match('/^((\d+).(\d+)(.(\d+))(-rc[0-9]{1,3})?(-beta[0-9]{1,3})?(-alpha[0-9]{1,3})?)$/', $value))
		{
			return true;
		}

		return false;
	}
}
