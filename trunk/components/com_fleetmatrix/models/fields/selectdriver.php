<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

// The class name must always be the same as the filename (in camel case)
class JFormFieldSelectDriver extends JFormFieldList {

	//The field class must know its own type through the variable $type.
	protected $type = 'selectdriver';

	public function getLabel() {
            // code that returns HTML that will be shown as the label
            return parent::getLabel();
	}

	public function getInput() {
            // code that returns HTML that will be shown as the form field
            return parent::getInput();
	}

    static function getAllDriversByCompanyOrGroup() {
        $options = array();
        $options[] = JHtml::_('select.option', 0, 'Select a driver');
        
        $db = JFactory::getDBO();
        
        $query = $db->getQuery(true)
            ->select('id, name')
            ->from('#__fleet_driver as d');
        
        // check if user is at group level
        if ($GLOBALS['user_groups']) {
            $query = $query->where(
                        "d.entity_id in (".implode(',', $GLOBALS['user_groups']).")"
                );
        }
        
        $db->setQuery((string)$query);
//        echo "kelvin_driver";
//        echo (string)$query;
//        echo print_r($GLOBALS['user_groups']);
        
        $drivers = $db->loadObjectList();
        if ($drivers)
        {
            if (sizeof($drivers) == 1) {
                $options = array();
            }
            foreach($drivers as $driver)
            {
                    $options[] = JHtml::_('select.option', $driver->id, $driver->name);
            }
        }
        return $options;
    }

    public function getOptions() {
        # The options available are based on what type was selected
        $options = array();

        return array_merge(parent::getOptions(), JFormFieldSelectDriver::getAllDriversByCompanyOrGroup());
    }
}