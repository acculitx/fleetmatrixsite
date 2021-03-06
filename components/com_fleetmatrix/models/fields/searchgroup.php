<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

require_once(dirname(__FILE__) . DS . 'selectgroup.php');

// The class name must always be the same as the filename (in camel case)
class JFormFieldSearchGroup extends JFormFieldSelectGroup {

	//The field class must know its own type through the variable $type.
	protected $type = 'searchgroup';

    static function getVehicles($parent=NULL, $company=NULL) {
        $options = parent::getVehicles($parent, $company);
        if (sizeof($options) > 1) {
            $options[0] = JHtml::_('select.option', 0, 'All Vehicles');
        }

        return $options;
    }

    static function getDrivers($parent=NULL, $company=NULL) {
        $options = parent::getDrivers($parent, $company);
        if (sizeof($options) > 1) {
            $options[0] = JHtml::_('select.option', 0, 'All Drivers');
        }

        return $options;
    }

    static function getGroups($parent=NULL) {
        $options = parent::getGroups($parent);
        if (sizeof($options) > 1) {
            $options[0] = JHtml::_('select.option', 0, 'All Groups');
        }

        return $options;
    }

    static function getCompany($group) {
        $options = parent::getCompany($group, false);
        // the first one is always "select a company"
        // if == 2, than it really means there is only one eligbile company, then go ahead and remove the first generated one
        // so the first one doesn't count, and if there is more than 2, means there's more than one eligible company
        if (sizeof($options) == 2) { 
            unset($options[0]); // remove element
            $options = array_values($options); // re-index the array
        }
        elseif (sizeof($options) > 2) { 
            $options[0] = JHtml::_('select.option', 0, 'All Companies');
        }

        return $options;

    }

    static function getDriver($group) {
        $options = parent::getDriver($group);
        if (sizeof($options) > 1) {
            $options[0] = JHtml::_('select.option', 0, 'All Drivers');
        }

        return $options;
    }

}