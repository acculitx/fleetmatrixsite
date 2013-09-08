<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.helper');

    ini_set('display_errors', 1);
    error_reporting(E_ALL);

// The class name must always be the same as the filename (in camel case)
class JFormFieldPrimaryKey extends JFormField {

    function __construct() {
        parent::__construct();
        var_dump(234);
    }

	//The field class must know its own type through the variable $type.
	protected $type = 'primarykey';

	public function getLabel() {
		// code that returns HTML that will be shown as the label
        var_dump(123);
        return 'BANANAS!';
	}

	public function getInput() {
		// code that returns HTML that will be shown as the form field
        return '<input type="hidden" name="'.
            $this->getFieldName().'" value="'.$this->value.'" />';
	}

}