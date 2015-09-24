<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

require(JPATH_COMPONENT . DS . 'models' . DS . 'baselist.php');

class FleetMatrixModelSubscriptionList extends FleetMatrixModelBaseList
{
    
    protected function getListQuery() {
        $cmd = $this->getState(strtolower($this->model_key).'.cmd') ;
        // Create a new query object.
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        
        switch ($cmd) {
            case 'subscription':
            	$query = $db->getQuery(true)
                    ->select("h.*, a.name as entity, a.id as entity_id, b.name as driver, b.id as driver_id, c.id as weight_id, CONCAT(c.min, '-', c.max) as weight")
                    ->from('#__fleet_subscription as h')
                    ->leftJoin('#__fleet_entity as a ON h.entity_id=a.id')
                    ->leftJoin('#__fleet_driver as b ON h.driver_id=b.id')
                    ->leftJoin('#__fleet_weight as c ON h.weight_id=c.id');

                if (array_key_exists('user_groups', $GLOBALS) && $GLOBALS['user_groups']) {
                    $query = $query->where(
                        "a.id in (".implode(',', $GLOBALS['user_groups']).")"
                    );
                }
            	break;
            default:
                $query = $query->select('1=0');
                return $query;
        }
        
//        echo $query;
        return $query;
    }


    public function getItems() {
        $db =& JFactory::getDbo();

        $items = parent::getItems();
        
        $cmd = $this->getState(strtolower($this->model_key).'.cmd');
        
        switch ($cmd) {
            case 'subscription':
            	foreach ($items as $item) {
                    $query = $db->getQuery(true)
                        ->select('h.vin')
                        ->from('fleet_trip as h')
                        ->leftJoin('#__fleet_trip_subscription as b on b.trip_id = h.id')
                        ->where('b.subscription_id = '.$item->id)
                        ->order('h.id desc limit 1')
                        ;
                    $db->setQuery($query);
                    //var_dump((string)$query);
                    //var_dump($vin);
                    $vin = $db->loadResult();
                    if (!$vin) {
                        $item->match = "N/A";
                    } else {
                        $item->match = ($db->loadResult() == $item->vin) ? 'Yes' : 'No';
                        //var_dump($item->vin);
                    }
                    $query = $db->getQuery(true)
                        ->select('min(DATE(h.start_date)) as start, max(DATE(h.end_date)) as end')
                        ->from('fleet_trip as h')
                        ->leftJoin('#__fleet_trip_subscription as b on b.trip_id = h.id')
                        ->where('b.subscription_id = '.$item->id)
                        ;
                    $db->setQuery($query);
                    //var_dump((string)$query);
                    $ret = $db->loadObject();
                    if (!$ret) {
                        $item->date_range = 'N/A';
                    } else {
                        $item->date_range = sprintf("%s <br> %s", $ret->start, $ret->end);
                    }
                }
            	break;
            case 'batchfileload':
            	break;
        }
        
        return $items;
    }

}