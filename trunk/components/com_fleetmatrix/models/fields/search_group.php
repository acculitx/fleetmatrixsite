<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

require_once(dirname(__FILE__) . DS . 'select_group.php');

// The class name must always be the same as the filename (in camel case)
class JFormFieldSearchGroup extends JFormFieldSelectGroup {

	//The field class must know its own type through the variable $type.
	protected $type = 'searchgroup';

    static function getGroups($type) {
        $options = parent::getGroups();
        $options[0] = JHtml::_('select.option', 0, 'All Groups');

        return $options;
    }
}