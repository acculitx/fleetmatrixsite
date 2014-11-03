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

    protected function decimal2DMS($value) {
        $value = $value / 100;
        $deg = (int)($value);
        $minpart = (60 * ($value - $deg));
        $min = (int)($minpart);
        $sec = round((60 * ($minpart - $min)), 4);
        if ($sec == 60) {
            $min += 1;
            $sec = 0;
        }
        if ($min == 60) {
            $deg += 1;
            $min = 0;
        }
        return sprintf('%02d,%02d,%06.4f', $deg, $min, $sec);
    }

    protected function degrees2decimal($value) {
        $deg = (int)($value);
        $min = (($value - $deg) * 100) / 60;
        $ret = sprintf('%6.9f', $deg + $min);
        return $ret;
    }

    /**
     * convertGPS 
     * takes in lat and lon parameters as DM (degrees, minutes)
     * converts this into DD (decimal degrees) 
     * ex. of cooridates 3113.31750,N 8534.26810,W from fleet_gps table
     * 3113.31750 = 31 degrees 13.3175 minutes
     * converts to 31.222458 dd
     * if lat_dir and lon_dir are S or W, then negate the direction
     * 8534.26810 = 85 degrees 34.2681 minutes
     * converts to -85.571135 dd
     * (http://support.groundspeak.com/index.php?pg=kb.page&id=207)
     * 
     * @param float $lat
     * @param string $lat_dir
     * @param float $lon
     * @param string $lon_dir
     * @return string
     */
    protected function convertGPS($lat, $lat_dir, $lon, $lon_dir)
    {        
        $lon /= 100;
        $lat /= 100;
          
        $lon = $this->degrees2decimal($lon);
        $lat = $this->degrees2decimal($lat);
          
        $lat *= $lat_dir == "S" ? -1 : 1;
        $lon *= $lon_dir == "W" ? -1 : 1;
        return sprintf('%s,%s', $lon, $lat);
    }

    public function getItems() {
        $result = array();

		$db = JFactory::getDBO();
        $db->setQuery($this->getListQuery());
        $items = &$db->loadObjectList();

        foreach ($items as &$item) {
            $item->coordinates = $this->convertGPS(
                $item->latitude,
                $item->lat_dir,
                $item->longitude,
                $item->lon_dir
            );
            
            if ($item->coordinates == "0,0") {
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
