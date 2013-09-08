<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

// The class name must always be the same as the filename (in camel case)
class JFormFieldParentEntity extends JFormFieldList {

	//The field class must know its own type through the variable $type.
	protected $type = 'parententity';

	public function getLabel() {
		// code that returns HTML that will be shown as the label
        return parent::getLabel();
	}

	public function getInput() {
		// code that returns HTML that will be shown as the form field
        return parent::getInput();
	}

    static function getParents($type, $parent = true) {
        $options = array();

        if ($type == 1 && $parent) {  /* root entity */
            $options[] = JHtml::_('select.option', 0, 'No Parent Required');
            return $options;
        }

        if ($parent) {
            $options[] = JHtml::_('select.option', 0, 'Select Parent Entity');
            $type--;
        } else {
            $options[] = JHtml::_('select.option', 0, 'Select an Entity');
        }


        $db = JFactory::getDBO();
        $query = $db->getQuery(true)
            ->select('id, name')
            ->from('#__fleet_entity')
            ->where('entity_type='.(int)$type);
        $db->setQuery((string)$query);
        $parents = $db->loadObjectList();
        if ($parents)
        {
                foreach($parents as $parent)
                {
                        $options[] = JHtml::_('select.option', $parent->id, $parent->name);
                }
        }
        return $options;
    }

    public function getOptions() {
        # The options available are based on what type was selected
        $options = array();
        $type = $this->form->getField('entity_type')->value;

        return array_merge(parent::getOptions(), JFormFieldParentEntity::getParents($type));
    }
}