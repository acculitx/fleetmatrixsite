<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

// The class name must always be the same as the filename (in camel case)
class JFormFieldSelectCompany extends JFormFieldList {

	//The field class must know its own type through the variable $type.
	protected $type = 'selectcompany';

	public function getLabel() {
		// code that returns HTML that will be shown as the label
        return parent::getLabel();
	}

	public function getInput() {
		// code that returns HTML that will be shown as the form field
        return parent::getInput();
	}

    static function getCompanies() {
        $options = array();

        $options[] = JHtml::_('select.option', 0, 'Select a Company');

        $db = JFactory::getDBO();
        $query = $db->getQuery(true)
            ->select('id, name')
            ->from('#__fleet_entity as h')
            ->where('h.entity_type=2');
        $db->setQuery((string)$query);
        $companies = $db->loadObjectList();
        if ($companies)
        {
                foreach($companies as $company)
                {
                        $options[] = JHtml::_('select.option', $company->id, $company->name);
                }
        }
        return $options;
    }

    public function getOptions() {
        # The options available are based on what type was selected
        $options = array();

        return array_merge(parent::getOptions(), JFormFieldSelectCompany::getCompanies());
    }
}