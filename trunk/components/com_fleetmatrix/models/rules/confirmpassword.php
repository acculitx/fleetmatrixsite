<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla formrule library
jimport('joomla.form.formrule');

class JFormRuleConfirmpassword extends JFormRule
{
        /**
         * The regular expression.
         *
         * @access      protected
         * @var         string
         * @since       1.6
         */
        protected $regex = '^[^0-9]+$';
    public function test(& $element, $value, $group = null, & $input = null, & $form = null) {
        return false;
        return parent::test($element, $value, $group, $input, $form);
    }

}

?>