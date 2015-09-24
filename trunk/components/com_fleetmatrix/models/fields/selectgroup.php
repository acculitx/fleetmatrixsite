<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

// The class name must always be the same as the filename (in camel case)
class JFormFieldSelectGroup extends JFormFieldList {

	//The field class must know its own type through the variable $type.
	protected $type = 'selectgroup';

	public function getLabel() {
            // code that returns HTML that will be shown as the label
            return parent::getLabel();
	}

	public function getInput() {
            // code that returns HTML that will be shown as the form field
            return parent::getInput();
	}

    static function getCompany($group, $remove_first=true) {
        $options = array();

        $options[] = JHtml::_('select.option', 0, 'Select a Company');

        $db = JFactory::getDBO();
        $query = $db->getQuery(true)
            ->select('a.id, a.name')
            ->from('#__fleet_entity as h')
            ->leftJoin('#__fleet_entity as a on a.id = h.parent_entity_id')
            ->where('h.entity_type=3');
        if ($group) {
            $query = $query->where('h.id='.$group[0]);
        }
        $db->setQuery((string)$query);
        
//        echo "kelvin_com_2: ".(string)$query;
                
        $rows = $db->loadObjectList();
        
//        echo "kelvin_com_2: ".print_r($rows);
        
        if ($rows)
        {
            if (sizeof($rows) == 1 && $remove_first) {
                $options = array();
            }
            foreach($rows as $row)
            {
                $options[] = JHtml::_('select.option', $row->id, $row->name);
            }
        }
        return $options;
    }

    static function getGroups($parent=NULL) {
        $options = array();

        $options[] = JHtml::_('select.option', 0, 'Select a Group');

        $db = JFactory::getDBO();
        $query = $db->getQuery(true)
            ->select('id, name')
            ->from('#__fleet_entity as h')
            ->where('h.entity_type=3');
        if ($parent) {
            $query = $query->where('h.parent_entity_id="'.$parent.'"');
        } else {
            if ($GLOBALS['user_groups']) {
                $query = $query->where(
                        "id in (".implode(',', $GLOBALS['user_groups']).")"
                );
            }
        }
        $db->setQuery((string)$query);
//        echo "kelvin_group";
//        echo (string)$query;
        
        $groups = $db->loadObjectList();
        if ($groups)
        {
            if (sizeof($groups) == 1) {
                $options = array();
            }
            foreach($groups as $group)
            {
                    $options[] = JHtml::_('select.option', $group->id, $group->name);
            }
        }
        return $options;
    }
    
    static function getVehicleGroup($vehicle, $remove_first=true) {
        $options = array();

        $options[] = JHtml::_('select.option', 0, 'Select a Group');

        $db = JFactory::getDBO();
        $query = $db->getQuery(true)
            ->select('h.id, h.name')
            ->from('#__fleet_entity as h')
            ->leftJoin('#__fleet_subscription as a on a.entity_id = h.id')
            ;
        if ($vehicle) {
            $query = $query->where('a.id='.$vehicle);
        }
        $db->setQuery((string)$query);
        $rows = $db->loadObjectList();
        if ($rows)
        {
            if (sizeof($rows) == 1 && $remove_first) {
                $options = array();
            }
            foreach($rows as $row)
            {
                $options[] = JHtml::_('select.option', $row->id, $row->name);
            }
        }
        return $options;
    }

    static function getDriverGroup($driver, $remove_first=true) {
        $options = array();

        $options[] = JHtml::_('select.option', 0, 'Select a Group');

        $db = JFactory::getDBO();
        $query = $db->getQuery(true)
            ->select('h.id, h.name')
            ->from('#__fleet_entity as h')
            ->leftJoin('#__fleet_driver as a on a.entity_id = h.id')
            ;
        if ($driver) {
            $query = $query->where('a.id='.$driver);
        }
        $db->setQuery((string)$query);
        $rows = $db->loadObjectList();
        if ($rows)
        {
            if (sizeof($rows) == 1 && $remove_first) {
                $options = array();
            }
            foreach($rows as $row)
            {
                $options[] = JHtml::_('select.option', $row->id, $row->name);
            }
        }
        return $options;
    }

    static function getVehicles($parent=NULL) {
        $options = array();

        $options[] = JHtml::_('select.option', 0, 'Select a Vehicle');

        $db = JFactory::getDBO();
        $query = $db->getQuery(true)
            ->select('h.id, h.name')
            ->from('#__fleet_subscription as h')
            ->where('h.visible');
        $company = JRequest::getInt('company', 0);
        if ($parent) {
            $query = $query->where('h.entity_id="'.$parent.'"');
        } else {
            if ($GLOBALS['user_groups']) {
            $query = $query->where(
                "entity_id in (".implode(',', $GLOBALS['user_groups']).")"
            );
            } elseif ($company) {
                $query = $query->leftJoin('#__fleet_entity as b on h.entity_id = b.id')
                    ->where('b.parent_entity_id = '.$company);
            }
        }
        $db->setQuery((string)$query);
        $groups = $db->loadObjectList();
        if ($groups)
        {
            if (sizeof($groups) == 1) {
                $options = array();
            }
            foreach($groups as $group)
            {
                    $options[] = JHtml::_('select.option', $group->id, $group->name);
            }
        }
        return $options;
    }

    static function getDrivers($parent=NULL) {
        $options = array();

        $options[] = JHtml::_('select.option', 0, 'Select a Driver');

        $db = JFactory::getDBO();
        $query = $db->getQuery(true)
            ->select('h.id, h.name')
            ->from('#__fleet_driver as h')
            ->where('h.visible');
        $company = JRequest::getInt('company', 0);
        if ($parent) {
            $query = $query->where('h.entity_id="'.$parent.'"');
        } else {
            if ($GLOBALS['user_groups']) {
            $query = $query->where(
                "entity_id in (".implode(',', $GLOBALS['user_groups']).")"
            );
            } elseif ($company) {
                $query = $query->leftJoin('#__fleet_entity as b on h.entity_id = b.id')
                    ->where('b.parent_entity_id = '.$company);
            }
        }
        $db->setQuery((string)$query);
        $groups = $db->loadObjectList();
        if ($groups)
        {
            if (sizeof($groups) == 1) {
                $options = array();
            }
            foreach($groups as $group)
            {
                    $options[] = JHtml::_('select.option', $group->id, $group->name);
            }
        }
        return $options;
    }

    static function getDriver($parent=NULL) {
        $options = array();

        $options[] = JHtml::_('select.option', 0, 'Select a Driver');

        $db = JFactory::getDBO();
        $query = $db->getQuery(true)
            ->select('id, name')
            ->from('#__fleet_driver as h')
            ->where('h.visible');
        if ($parent) {
            $query = $query->where('h.entity_id="'.$parent.'"');
        } else
        if (!$group && $GLOBALS['user_groups']) {
            $query = $query->where(
                "h.entity_id in (".implode(',', $GLOBALS['user_groups']).")"
            );
        }
        $db->setQuery((string)$query);
        $groups = $db->loadObjectList();
        if ($groups)
        {
            if (sizeof($groups) == 1) {
                $options = array();
            }
            foreach($groups as $group)
            {
                    $options[] = JHtml::_('select.option', $group->id, $group->name);
            }
        }
        return $options;
    }

    public function getOptions() {
        # The options available are based on what type was selected
        $options = array();

        return array_merge(parent::getOptions(), JFormFieldSelectGroup::getGroups());
    }
}
