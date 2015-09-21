<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

require_once(dirname(__FILE__) . DS . 'selectcompany.php');

// The class name must always be the same as the filename (in camel case)
class JFormFieldSearchCompany extends JFormFieldSelectCompany {

	//The field class must know its own type through the variable $type.
	protected $type = 'searchcompany';

    static function getCompanies() {
        $options = parent::getCompanies();
        if (sizeof($options) > 1) {
            $options[0] = JHtml::_('select.option', 0, 'All Companies');
        }

        return $options;
    }

}