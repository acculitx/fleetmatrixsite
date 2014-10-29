<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

require(JPATH_COMPONENT . DS . 'models' . DS . 'baselist.php');

class FleetMatrixModelMapList extends FleetMatrixModelBaseList
{
    var $fields = "a.*";
    var $table_name = "fleet_trip";

	protected function getListQuery()
	{
		$db = JFactory::getDBO();
    	$id = JRequest::getInt('trip', 0);
        $date = JRequest::getVar('date', '');

        // find the subscriber id and date
        $query = $db->getQuery(true)
            ->select('subscription_id, DATE_ADD(end_date, INTERVAL a.time_zone HOUR) as end_date')
            ->from('fleet_trip as a')
            ->leftJoin('#__fleet_trip_subscription as b on a.id = b.trip_id')
            ->where('a.id='.$id)
            ;
        $db->setQuery($query);
        $row = $db->loadObject();

        if (!$date) {
            $date = $row->end_date;
        }

        // now find relevant trip ids
        // find the subscriber id and date
        $query = $db->getQuery(true)
            ->select('id')
            ->from('fleet_trip as a')
            ->leftJoin('#__fleet_trip_subscription as b on a.id = b.trip_id')
            ->where('subscription_id='.$row->subscription_id)
            ->where('DATE_ADD(end_date, INTERVAL a.time_zone HOUR)="'.$date.'"')
            ;
        //var_dump((string)$query);
        $db->setQuery($query);
        $trips = $db->loadResultArray();
        if (!$trips) {
            $trips = array(0);
        }

		$query = $db->getQuery(true)
            ->select($this->fields)
            ->from($this->table_name.' as h')
     		->leftJoin('fleet_gps as a ON h.id = a.trip_id')
            ->where('h.id in ('.implode(',', $trips).')')
            ;
        //var_dump((string)$query);
		return $query;
	}
	
    public function getItems() {
        $result = array();

		$db = JFactory::getDBO();
        $db->setQuery($this->getListQuery());
        $items = &$db->loadObjectList();

        foreach ($items as &$item) {        	
        	$lat_dd = $item->lat_dd;
        	$lon_dd = $item->lon_dd;
        	
        	$item->coordinates = sprintf('%s,%s', $lon_dd, $lat_dd);
        	
            if ($item->coordinates == "0.00000,0.00000") {
                continue;
            }
            
            if (array_key_exists($item->trip_id, $result)) {
                $result[$item->trip_id][] = $item->coordinates;
            } else {
                $result[$item->trip_id] = array($item->coordinates);
            }
        }
        return $result;
    }

}
