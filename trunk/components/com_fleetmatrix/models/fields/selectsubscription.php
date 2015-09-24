<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

// The class name must always be the same as the filename (in camel case)
class JFormFieldSelectSubscription extends JFormFieldList {

	//The field class must know its own type through the variable $type.
	protected $type = 'selectsubscription';

	public function getLabel() {
            // code that returns HTML that will be shown as the label
            return parent::getLabel();
	}

	public function getInput() {
            // code that returns HTML that will be shown as the form field
            return parent::getInput();
	}

    static function getSubscriptionByGroup($group = '-1') {
        $options = array();
        $options[] = JHtml::_('select.option', 0, 'Select a subscription');
        
        $db = JFactory::getDBO();
        $query = $db->getQuery(true)
            ->select('subscription_id,name')
            ->from('#__fleet_subscription as s');
        
        if ($group && $group != '-1') {
            $query = $query->where('s.entity_id='.$group);
        }
            
        $db->setQuery((string)$query);
        
//        echo "kelvin_subscription";
//        echo (string)$query;
        
        $subscriptionList = $db->loadObjectList();
        
        if ($subscriptionList) {
            if (sizeof($subscriptionList) == 1) {
                $options = array();
            }
            foreach($subscriptionList as $subscription)
            {
                $options[] = JHtml::_('select.option', $subscription->subscription_id, $subscription->subscription_id.'-'.$subscription->name);
            }
        }
        
        return $options;
    }

    public function getOptions() {
        # The options available are based on what type was selected
        $options = array();

        return array_merge(parent::getOptions(), JFormFieldSelectSubscription::getSubscriptionByGroup());
    }
}