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

    static function getGroups() {
        $options = array();

        if ($type == 1) {  /* root entity */
            $options[] = JHtml::_('select.option', 0, 'Select a Group');
            return $options;
        }

        $options[] = JHtml::_('select.option', 0, 'Select a Group');

        $type--;

        $db = JFactory::getDBO();
        $query = $db->getQuery(true)
            ->select('id, name')
            ->from('#__fleet_entity as h')
            ->where('h.entity_type_id=2');
        $db->setQuery((string)$query);
        $groups = $db->loadObjectList();
        if ($groups)
        {
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
        $type = $this->form->getField('entity_type')->value;

        return array_merge(parent::getOptions(), JFormFieldParentEntity::getGroups());
    }
}