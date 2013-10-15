<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

    ini_set('display_errors', 1);
    error_reporting(E_ALL);

require_once(JPATH_COMPONENT . DS . 'models' . DS . 'baselist.php');
require(JPATH_COMPONENT . DS . 'models' . DS . 'calculator.php');

class Mpg
{
    public function __construct($capacity, $date) {
        $this->capacity = $capacity;
        $this->date = $date;
        $this->full = 0;
        $this->mfull = 0;
        $this->emp = 0;
        $this->memp = 0;
    }

    public function gallons() {
        return ($this->full/100*$this->capacity) - ($this->emp/100*$this->capacity);
    }

    public function mpg() {
        if (!$this->gallons()) {
            return 0;
        }
        return ($this->memp - $this->mfull) / $this->gallons();
    }
}

class FleetMatrixModelReportsList extends FleetMatrixModelBaseList
{
    var $model_key = 'Reports';

    public function __construct() {
        parent::__construct();
        //$this->averages = array();
        $this->calculator = new ScoreCalculator();
    }

	protected function populateState()
	{
		$app = JFactory::getApplication();
        $trend = JRequest::getCmd('trend', 'overall');
        $this->setState(strtolower($this->model_key).'.trend', $trend);

		parent::populateState();
	}

	protected function getListQuery()
	{
        $cmd = $this->getState(strtolower($this->model_key).'.cmd');
        $window = $this->getState(strtolower($this->model_key).'.window');
        $company = JRequest::getInt('company', 0);
        $group = JRequest::getInt('group', 0);
        $vehicle = JRequest::getInt('vehicle', 0);
        $driver = JRequest::getInt('driver', 0);

		// Create a new query object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

        switch ($cmd) {
            case 'vehicle':
            case 'vehicletrend':
                $clause = "distinct s.id as vehicle_id, s.subscription_id, a.name as group_name, ".
                        "a.id as group_id, a.parent_entity_id as company_id, ".
                        "'N/A' as mpg, ".
                        "s.name as vehicle_name, ".
                        "SUM(h.odo_end - h.odo_start) as miles, ".
                        "COUNT(h.subscriber_id) as trip_count, ".
                        "'N/A' as gallons, ".
                        "'N/A' as not_connected, ".
                        "'N/A' as disconnects ";
                $query = $query->select($clause)
                    ->from('#__fleet_subscription as s')
                    ->leftJoin('#__fleet_entity as a on a.id = s.entity_id')
                    ->leftJoin('#__fleet_trip_subscription as e on e.subscription_id = s.id')
                    ->leftJoin('fleet_trip as h on h.id = e.trip_id')
                    ->group('s.id')
                    ->where('s.visible')
                    ->where('UNIX_TIMESTAMP(h.end_date)-UNIX_TIMESTAMP(h.start_date)>60')
                    ;
                break;
            case 'driver':
                $clause = "distinct s.name as vehicle_name, ".
                        "DATE_SUB(h.start_date, INTERVAL 8 HOUR) as trip_start, ".
                        "DATE_SUB(h.end_date, INTERVAL 8 HOUR) as trip_end, ".
                        "h.odo_end - h.odo_start as miles, ".
                        "f.name as assigned_driver,".
                        "d.driver_id, h.id as trip_id, fr.hard_turns_count, fr.hard_turns_scoretype, fr.accel_count, fr.accel_scoretype, fr.decel_count, fr.decel_scoretype"
                        ;
                $query = $query->select($clause)
                    ->from('fleet_trip as h')
                    ->leftJoin('#__fleet_trip_subscription as e on e.trip_id = h.id')
                    ->join('left outer', '#__fleet_subscription as s on e.subscription_id = s.id')
                    ->leftJoin('fleet_trip as g on g.id = e.trip_id')
                    ->leftJoin('#__fleet_trip_driver as d on h.id = d.trip_id')
                    ->leftJoin('#__fleet_driver as b on d.driver_id = b.id')
                    ->leftJoin('#__fleet_driver as f on d.driver_id = f.id')
                    ->leftJoin('#__fleet_entity as a on f.entity_id = a.id')
                    ->leftJoin('fleet_redflag_report fr ON h.id = fr.tripid')
                    ->group('h.id')
                    #->where('c.visible')
                    ->where('f.visible')
                    ->where('UNIX_TIMESTAMP(h.end_date)-UNIX_TIMESTAMP(h.start_date)>60')
                    ->order('h.end_date DESC')
                    ;
                break;
            case 'driverdetail':
            case 'drivertrend':
                $clause = "distinct b.name as driver_name, ".
                        "a.name as group_name, s.id as vehicle_id, ".
                        "a.id as group_id, a.parent_entity_id as company_id, ".
                        "d.driver_id,".
                        "COUNT(h.subscriber_id) as trip_count, ".
                        "'N/A' as mpg, ".
                        "'N/A' as overall, ".
                        "'N/A' as accel_score, ".
                        "'N/A' as decel_score, ".
                        "'N/A' as hard_turns, ".
                        "'N/A' as speed_score, ".
                        "SUM(h.odo_end - h.odo_start) as miles "
                        ;
                $query = $query->select($clause)
                    ->from('fleet_trip as h')
                    ->leftJoin('#__fleet_trip_subscription as e on e.trip_id = h.id')
                    ->join('left outer', '#__fleet_subscription as s on e.subscription_id = s.id')
                    ->leftJoin('#__fleet_trip_driver as d on h.id = d.trip_id')
                    ->leftJoin('#__fleet_driver as b on d.driver_id = b.id')
                    ->leftJoin('#__fleet_entity as a on b.entity_id = a.id')
                    ->group('b.id')
                    #->where('c.visible')
                    ->where('b.visible')
                    ->where('UNIX_TIMESTAMP(h.end_date)-UNIX_TIMESTAMP(h.start_date)>60')
                    ;
                break;
            default:
                $query = $query->select('1=0');
                return $query;
                break;

        }

        # Apply time window if specified
        if ($window) {
            $query = $query->where('h.end_date > DATE_SUB(NOW(), INTERVAL '.$window.' DAY)');
        }
        if ($company) {
            $query = $query->where('a.parent_entity_id = "'.$company.'"');
        } else {
            if (array_key_exists('user_companies', $GLOBALS) && $GLOBALS['user_companies']) {
            $query = $query->where(
                "a.parent_entity_id in (".implode(',', $GLOBALS['user_companies']).")"
            );
            }
        }
        if ($group) {
            $query = $query->where('a.id = "'.$group.'"');
        } else {
            if (array_key_exists('user_groups', $GLOBALS) && $GLOBALS['user_groups']) {
            $query = $query->where(
                "a.id in (".implode(',', $GLOBALS['user_groups']).")"
            );
            }
        }
        if ($vehicle) {
            $query = $query->where('s.id = "'.$vehicle.'"');
        }
        if ($driver) {
            $query = $query->where('b.id = "'.$driver.'"');
        }

        #var_dump((string)$query);
		return $query;
	}

    public function getItems() {
        $items = parent::getItems();

        $cmd = $this->getState(strtolower($this->model_key).'.cmd');
        $window = $this->getState(strtolower($this->model_key).'.window');
        $trend = $this->getState(strtolower($this->model_key).'.trend');
        $company = JRequest::getInt('company', 0);
        if (!$company && sizeof($GLOBALS['user_companies'])==1) {
            $company = (int)$GLOBALS['user_companies'];
        }
        $group = JRequest::getInt('group', 0);
        if (!$group && sizeof($GLOBALS['user_groups'])==1) {
            $group = (int)$GLOBALS['user_groups'];
        }
        $vehicle = JRequest::getInt('vehicle', 0);
        $driver = JRequest::getInt('driver', 0);

        switch ($cmd) {
            case 'vehicle':
                foreach ($items as &$item) {
                    $ret = $this->getVehicleFuelUsage($item, $window);
                    #var_dump($ret);
                    $item->gallons = $ret[0];
                    $item->mpg = $ret[1];
                    $item->disconnects = $this->getDisconnects($item, $window);
                    $item->not_connected = $this->not_connected($item, $window);
                }
                break;
            case 'driverdetail':
                foreach ($items as &$item) {
                    #$item->gallons = $this->getVehicleFuelUsage($item, $window);
                    #$item->mpg = $this->calcMPG($item);
                    $item->accel_score = $this->calculator->getDriverAccelScore($item, $window, 'accel');
                    $item->decel_score = $this->calculator->getDriverAccelScore($item, $window, 'decel');
                    $item->hard_turns = $this->calculator->getDriverAccelScore($item, $window, 'hard_turns');
                    $item->speed_score = $this->calculator->getDriverSpeedScore($item, $window);
                    $item->overall_score = $this->calculator->getOverall($item);
                }
                break;
            case 'vehicletrend':
                foreach ($items as &$item) {
                    $item->mpg = $this->getMpgArray($item, $window);
                }
                $this->reduce_to_scope($items, 'mpg');
                $items = $this->reduce_to_average($items, $company, $group, 0, $vehicle, 'vehicle_name');
                break;
            case 'drivertrend':
                #TODO: remove when drop downs are complete
                foreach ($items as &$item) {
                    #$item->gallons = $this->getVehicleFuelUsage($item, $window);
                    #$item->mpg = $this->calcMPG($item);
                    if ($trend == "accel" || $trend == "overall" || $trend=="all") {
                        $item->accel_score = $this->calculator->getDriverAccelArray($item, $window, 'accel');
                    }
                    if ($trend == 'decel' || $trend == 'overall' || $trend=="all") {
                        $item->decel_score = $this->calculator->getDriverAccelArray($item, $window, 'decel');
                    }
                    if ($trend == 'hard_turns' || $trend == 'overall' || $trend=="all") {
                        $item->hard_turns = $this->calculator->getDriverAccelArray($item, $window, 'hard_turns');
                    }
                    if ($trend == 'speed' || $trend == 'overall' || $trend=="all") {
                        $item->speed_score = $this->calculator->getDriverSpeedArray($item, $window);
                    }
                }
                if ($trend == 'overall' || $trend == 'all') {
                    foreach (array('accel', 'decel', 'hard_turns', 'speed') as $context) {
                        $this->reduce_to_scope($items, $context);
                        $this->cap_to_zero($items, $context);
                    }
                    $this->calculator->getOverallArray($items);
                } else {
                    $this->reduce_to_scope($items);
                }
                $items = $this->reduce_to_average($items, $company, $group, $driver, 0);
                break;
        }

        return $items;
    }

    protected function getVehicleFuelUsage($item, $window, &$arr=NULL) {
        $consumed = 0;

        $clause = "a.fuel_capacity, b.level, h.id as trip_id, ".
            " DATE(b.date) as date, ".
            "DATE(h.start_date) as start, DATE(h.end_date) as end, ".
            "h.odo_start as miles_start, ".
            "h.odo_end as miles_end";
		$db = JFactory::getDBO();
		$query = $db->getQuery(true)
            ->select($clause)
            ->from('fleet_trip as h')
            ->leftJoin('#__fleet_trip_subscription as e on e.trip_id = h.id')
            ->leftJoin('#__fleet_subscription as a on e.subscription_id = a.id')
            ->leftJoin('fleet_fuel_level as b on h.id = b.trip_id')
            //->group('h.id')
            ->order('b.date')
            ->where('a.id="'.$item->vehicle_id.'"')
            ->where('UNIX_TIMESTAMP(h.end_date)-UNIX_TIMESTAMP(h.start_date)>60')
            ;

        if ($window) {
            $query = $query->where('h.end_date > DATE_SUB(NOW(), INTERVAL '.$window.' DAY)');
        }

        #var_dump((string)$query);

        $db->setQuery($query);

        $levels = $db->loadObjectList();
        $this->remove_invalid($levels);
        $indexes = $this->refuel_indexes($levels);
#var_dump($indexes);

        $mpgs = array();
        for ($x=1; $x<=sizeof($indexes)-2; $x++) {
            $v = $indexes[$x];
            $capacity = $levels[$v]->fuel_capacity;
            $date = $levels[$v]->date;
            $m = new Mpg($capacity, $date);
            $full = ($levels[$v]->level + $levels[$v+1]->level + $levels[$v+2]->level) / 3;
            #var_dump($levels[$v]->miles_start);
            #var_dump($levels[$v]->miles_end);
            #var_dump($levels[$v]->level);
            #echo "S <br>";
            $m->full = $full;
            $m->mfull = $levels[$v]->miles_start;
            $v = $indexes[$x+1];
            $mpgs[] = $m;
            $empty = ($levels[$v-3]->level + $levels[$v-2]->level + $levels[$v-1]->level) / 3;
            $mpgs[$x-1]->emp = $empty;
            $mpgs[$x-1]->memp = $levels[$v-1]->miles_end;
            #var_dump($levels[$v-1]->miles_start);
            #var_dump($levels[$v-1]->miles_end);
            #var_dump($levels[$v-1]->level);
            #echo "E <br>";
        }

        $consumed = 0;
        $mpg = -1;
        #var_dump($mpgs);
        foreach ($mpgs as $m) {
            if (!property_exists($m, 'emp')) { continue; }
            if (!property_exists($m, 'full')) { continue; }
            if ($m->emp > $m->full) { continue; }

            #echo $m->full."/".$m->mfull.", ".$m->emp."/".$m->memp." ".$m->date."<br>";
            $consumed += $m->gallons();
            #var_dump($consumed);
            #var_dump($m->mpg());
            if ($mpg == -1) {
                $mpg = $m->mpg();
            } else {
                $mpg += $m->mpg();
                $mpg /= 2;
            }
            #var_dump($mpg);
            #echo "<br>";
            if ($arr !== NULL && $m->gallons()) {
                if (!array_key_exists($m->date, $arr)) {
                    $arr[$m->date] = $m->mpg();
                } else {
                    $val = $arr[$m->date];
                    $val += $m->mpg();
                    $val /= 2;
                    $arr[$m->date] = $val;
                }
            }
        }

        if (!$consumed) { $consumed = 'N/A'; }
        if ($mpg == -1) { $mpg = 'N/A'; }
        return array($consumed, $mpg);
    }

    protected function getMpgArray($item, $window) {
        $fuel = array();
        $this->getVehicleFuelUsage($item, $window, $fuel);

        $now = new DateTime();
        $start = clone $now;
        $start->sub(new DateInterval('P'.$window.'D'));

        $range = new DatePeriod($start, new DateInterval('P1D'), $now);

        # fill missing dates with dummy value
        $ret = array();
        foreach ($range as $d) {
            $date = $d->format('M d');
            $d = $d->format('Y-m-d');
            if (array_key_exists($d, $fuel)) {
                $ret[] = new Score($date, $fuel[$d]);
            } else {
                $ret[] = new Score($date, -1);
            }
        }

        return $ret;
    }


    protected function calcMPG($item) {
        if (!$item->gallons) {
            // no divide by zero
            return "N/A";
        }

        return $item->miles / $item->gallons;
    }

    protected function cap_to_zero(&$items, $trend=NULL) {
        /*
         * In overall we want to only reduce to largest time frame, rest of
         * values have to be moved away from -1
         */
        if (!sizeof($items)) {
            return;
        }

        if (!$trend) {
            $trend = $this->getState(strtolower($this->model_key).'.trend');
        }

        if ($trend == 'hard_turns' || $trend == 'mpg') {
            $context = $trend;
        } else {
            $context = $trend . '_score';
        }
        $v = array();
        foreach ($items as $item) {
            $v[] = $item->$context;
        }
        foreach($v as &$values) {
            $last = 5;
            for ($x=0; $x<sizeof($values); $x++) {
               if ($values[$x]->value == -1) {
                    $values[$x]->value = $last;
               }
               //$last = $values[$x]->value;
            }
        }
    }

    protected function reduce_to_scope(&$items, $trend=NULL) {
        if (!sizeof($items)) {
            return;
        }

        if (!$trend) {
            $trend = $this->getState(strtolower($this->model_key).'.trend');
        }

        if ($trend == 'hard_turns' || $trend == 'mpg') {
            $context = $trend;
        } else {
            $context = $trend . '_score';
        }
        $v = array();
        foreach ($items as $item) {
            $v[] = $item->$context;
        }
        #var_dump($v);
        $firsts = array();
        $lasts = array();
        foreach($v as &$values) {
            $first = NULL;
            $last = sizeof($v[0])-1;
            for ($x=0; $x<$last; $x++) {
                if ($values[$x]->value != -1) {
                    $first = $x;
                    break;
                }
            }
            for ($x=$last; $x>$first; $x--) {
                if ($values[$x]->value != -1) {
                    $last = $x;
                    break;
                }
            }
            $prev = 0;
            foreach ($values as &$vv) {
                if ($vv->value == -1) {
                    $vv->value = $prev;
                }
                $prev = $vv->value;
            }
            if (!is_null($first)) {
                $firsts[] = $first;
                $lasts[] = $last;
            }
        }
        #var_dump($firsts);
        if (sizeof($firsts)) {
            $first = min($firsts);
            $last = max($lasts);

            for ($x=0; $x<sizeof($v); $x++) {
                $items[$x]->$context = array_slice($v[$x], $first, $last-$first);
            }
        }
    }

    protected function refuel_indexes($levels) {
        if (sizeof($levels)<6) { return array(); }

        $refuels = array();
        for ($x=2; $x<sizeof($levels)-3; $x++) {
            $avg = ($levels[$x-2]->level + $levels[$x-1]->level + $levels[$x]->level) / 3;
            #var_dump($avg);
            #echo "<br>";
            if ($levels[$x]->level > $levels[$x-1]->level + 30 &&
                $levels[$x]->trip_id != $levels[$x-1]->trip_id) {
                #var_dump($levels[$x-1]->level);
                #var_dump($levels[$x]->level);
                $refuels[] = $x;
            }
        }

        return $refuels;
    }

    protected function remove_invalid(&$levels) {
        $last = 0;
        foreach ($levels as $k => $level) {
            $l = $level->level;
            $c = '';
            if (!(float)$l || (float)$l < $last - 15) {
                unset($levels[$k]);
                $c = 'X';
            }
            #printf('%s %s %s %s %s %s<br>', $level->trip_id, $level->level, $level->miles_start, $level->miles_end, $level->date, $c);
            $last = $l;
        }
        $levels = array_values($levels);
    }

    protected function reduce_to_average($items, $company, $group, $driver, $vehicle, $n='driver_name') {
        $new_items = array();
        if (sizeof($items)<1) {
            return $items;
        }

        if ($vehicle) {
            $nn = $n;
            $g = 0;
        } else
        if ($driver) {
            $nn = $n;
            $g = 0;
        } else
        if ($group) {
            $nn = $n;
            $g = str_replace('_name', '_id', $n);
        } else
        if ($company) {
            $nn = 'group_name';
            $g = 'group_id';
        } else {
            $nn = 0;
            $g = 0;
        }


        foreach (array('mpg', 'overall_score', 'accel_score', 'decel_score', 'hard_turns', 'speed_score') as $field) {
            if (!property_exists($items[0], $field)) {
                continue;
            }
            
            $total_items = count($items);
            
            $i=0;
            foreach($items as $item) {
            	$i++;
                if ($g) {
                    $k = $item->$g;
                } else if ($nn) {
                    $k = $item->$nn;
                } else {
                    $k = 'All';
                }
                if (!array_key_exists($k, $new_items)) {
                    $a = new AverageItem();
                    $a->scores = array();
                    if (property_exists($item, $nn)) {
                        $a->$n = $item->$nn;
                    } else {
                        $a->$n = $k;
                    }
                } else {
                    $a = $new_items[$k];
                }
                if (!is_array($items[0]->$field)) {
                    $a->scores[$field] = $items[0]->$field;
                    continue;
                }
                if (!array_key_exists($field, $a->scores)) {
                    $a->scores[$field] = array();
                }
                $dates = $a->scores[$field];
                foreach($item->$field as $val) {
                    if (!array_key_exists($val->date, $dates)) {
                        $dates[$val->date] = $val;
                        $dates[$val->date]->total_trip = 0;
                    } else {
                    	$dates[$val->date]->value += $val->value;
                    }
                    if($val->value != 0) 
                    	$dates[$val->date]->total_trip += 1;
                    if($i == $total_items && $dates[$val->date]->total_trip !=0 )
                    	$dates[$val->date]->value = $dates[$val->date]->value/$dates[$val->date]->total_trip;
                    //if($i == $total_items)
                    	//$dates[$val->date]->value = $dates[$val->date]->value/$total_items;
                }
                $a->scores[$field] = $dates;
                $new_items[$k] = $a;
            }
        }

        foreach ($new_items as &$item) {
            foreach ($item->scores as $key => $score) {
                $item->$key = $score;
            }
        }
        return $new_items;
    }

    protected function getDisconnects($item, $window) {

        $clause = "count(b.disconnect) as disconnects";
        $db = JFactory::getDBO();
        $query = $db->getQuery(true)
            ->select($clause)
            ->from('#__fleet_subscription as a')
            ->leftJoin('fleet_disconnect as b on (a.serial = b.subscription_id or a.id = b.subscription_id)')
            ->where('a.id="'.$item->vehicle_id.'"')
            ->where('b.disconnect = 1')
            ;

        if ($window) {
            $query = $query->where('b.date > DATE_SUB(NOW(), INTERVAL '.$window.' DAY)');
        }

        #var_dump((string)$query);
        $db->setQuery($query);
        return $db->loadResult();
    }

    protected function not_connected($item, $window) {
        $clause = "disconnect, date";
        $db = JFactory::getDBO();
        $query = $db->getQuery(true)
            ->select($clause)
            ->from('#__fleet_subscription as a')
            ->leftJoin('fleet_disconnect as b on (a.serial = b.subscription_id or a.id = b.subscription_id)')
            ->where('a.id="'.$item->vehicle_id.'"')
            ->order('b.date')
            ;

        if ($window) {
            $query = $query->where('b.date > DATE_SUB(NOW(), INTERVAL '.$window.' DAY)');
        }

        #var_dump((string)$query);
        $db->setQuery($query);
        $total = new DateTime('00:00');
        $start = 0;
        foreach ($db->loadObjectList() as $row) {
            if (!$row->disconnect && $start) {
                $d1 = new DateTime($start);
                $d2 = new DateTime($row->date);
                $total->add($d1->diff($d2));
                $start = 0;
            } else {
                $start = $row->date;
            }
        }
        return $total->format("H:i:s");
    }
}

?>
