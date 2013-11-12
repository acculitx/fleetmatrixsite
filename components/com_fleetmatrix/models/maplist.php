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
        //var_dump($value);
        $deg = (int)($value);
        //var_dump($deg);
        $min = (($value - $deg) * 100) / 60;
        //var_dump($min);

        $ret = sprintf('%6.9f', $deg + $min);
        //var_dump($ret);
        //echo "<hr>";

        return $ret;
    }

    protected function convertGPS($lat, $lat_dir, $lon, $lon_dir)
    {
        $lat /= 100;
        $lon /= 100;

        $latc = $lat_dir == "S" ? '-' : '';
        $lonc = $lon_dir == "W" ? '-' : '';

        return sprintf('%s%s,%s%s', $lonc, $this->degrees2decimal($lon), $latc, $this->degrees2decimal($lat));
    }

    public function getItems() {
        //$items = parent::getItems();
        $result = array();

		$db = JFactory::getDBO();
        $db->setQuery($this->getListQuery());
        $items = &$db->loadObjectList();

        foreach ($items as &$item) {
            //$item->clatitude = $this->decimal2DMS($item->latitude);
            //$item->clongitude = $this->decimal2DMS($item->longitude);
            //var_dump($item);
            $item->coordinates = $this->convertGPS(
                $item->latitude,
                $item->lat_dir,
                $item->longitude,
                $item->lon_dir
            );
            if ($item->coordinates == "0.000000000,0.000000000") {
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
