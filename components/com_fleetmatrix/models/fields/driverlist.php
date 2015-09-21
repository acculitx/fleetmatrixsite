<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

// The class name must always be the same as the filename (in camel case)
class JFormFieldEntityType extends JFormFieldList {

	//The field class must know its own type through the variable $type.
	protected $type = 'driverlist';

	public function getLabel() {
		// code that returns HTML that will be shown as the label
        return parent::getLabel();
	}

	public function getInput() {
		// code that returns HTML that will be shown as the form field
        return parent::getInput();
	}

    public function getOptions() {
        # The options available are based on what type was selected
        $options = array();
        $options[] = JHtml::_('select.option', 0, 'Select an Entity Type');

        $db = JFactory::getDBO();
        $query = $db->getQuery(true)
            ->select('id, name')
            ->from('#__fleet_entity_type')
            ->order('id');
        $db->setQuery((string)$query);
        $rows = $db->loadObjectList();
        if ($rows)
        {
                foreach($rows as $row)
                {
                        $options[] = JHtml::_('select.option', $row->id, $row->name);
                }
        }

        return array_merge(parent::getOptions(), $options);
    }
}
