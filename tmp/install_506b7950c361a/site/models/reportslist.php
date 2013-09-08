<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

    ini_set('display_errors', 1);
    error_reporting(E_ALL);

require(JPATH_COMPONENT . DS . 'models' . DS . 'baselist.php');

class Score
{
    public function __construct($date, $value) {
        $this->date = $date;
        $this->value = $value;
    }
}

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
        if (!$this->gallons) {
            return 0;
        }
        return ($this->memp - $this->mfull) / $this->gallons();
    }
}

class BinTotals
{
    public function __construct() {
        $this->total = 0;
        $this->bin_total = array();
        $this->severity = array();
        for ($x=1; $x<=31; $x++) {
            $this->bin_total[$x] = 0;
            $this->severity[$x] = 0;
        }
    }

    public function addRow($row) {
        $this->total += (int)$row->bin_count;
        $this->bin_total[(int)$row->bin_num] += $row->bin_count;
        $this->severity[(int)$row->bin_num] = $row->severity;
    }

    public function process_rows() {
        for ($x=1; $x<=31; $x++) {
            $count = $this->bin_total[$x];
            if (!$this->total) {
                $this->bin_total[$x] = 0;
            } else {
                $this->bin_total[$x] = (600 / $this->total) *
                            ($count * $this->severity[$x]) / 32;
            }
        }
        return $this->bin_total;
    }
}

class FleetMatrixModelReportsList extends FleetMatrixModelBaseList
{
    var $model_key = 'Reports';

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

		// Create a new query object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

        switch ($cmd) {
            case 'vehicle':
            case 'vehicletrend':
                $clause = "distinct s.id as vehicle_id, s.subscription_id, a.name as group_name, ".
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
                    ;
                break;
            case 'driver':
                $clause = "distinct s.name as vehicle_name, h.start_date as trip_start, ".
                        "h.end_date as trip_end, ".
                        "h.odo_end - h.odo_start as miles, ".
                        "f.name as assigned_driver,".
                        "d.driver_id "
                        ;
                $query = $query->select($clause)
                    ->from('fleet_trip as h')
                    ->leftJoin('#__fleet_trip_subscription as e on e.trip_id = h.id')
                    ->join('left outer', '#__fleet_subscription as s on e.subscription_id = s.id')
                    ->leftJoin('fleet_trip as b on b.id = e.trip_id')
                    ->leftJoin('#__fleet_trip_driver as d on h.id = d.trip_id')
                    ->leftJoin('#__fleet_driver as f on d.driver_id = f.id')
                    ->leftJoin('#__fleet_entity as a on f.entity_id = a.id')
                    ->group('h.id')
                    #->where('c.visible')
                    ->where('f.visible')
                    ->order('trip_end DESC')
                    ;
                break;
            case 'driverdetail':
            case 'drivertrend':
                $clause = "distinct b.name as driver_name, ".
                        "a.name as group_name, s.id as vehicle_id, ".
                        "d.driver_id, ".
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
        }
        if ($group) {
            $query = $query->where('a.id = "'.$group.'"');
        }
        if ($vehicle) {
            $query = $query->where('s.id = "'.$vehicle.'"');
        }

        #var_dump((string)$query);
		return $query;
	}

    public function getItems() {
        $items = parent::getItems();

        $cmd = $this->getState(strtolower($this->model_key).'.cmd');
        $window = $this->getState(strtolower($this->model_key).'.window');
        $trend = $this->getState(strtolower($this->model_key).'.trend');

        switch ($cmd) {
            case 'vehicle':
                foreach ($items as &$item) {
                    $ret = $this->getVehicleFuelUsage($item, $window);
                    #var_dump($ret);
                    $item->gallons = $ret[0];
                    $item->mpg = $ret[1];
                }
                break;
            case 'driverdetail':
                foreach ($items as &$item) {
                    #$item->gallons = $this->getVehicleFuelUsage($item, $window);
                    #$item->mpg = $this->calcMPG($item);
                    $item->accel_score = $this->getDriverAccelScore($item, $window, 'yes', 'x');
                    $item->decel_score = $this->getDriverAccelScore($item, $window, 'no', 'x');
                    $item->hard_turns = $this->getDriverAccelScore($item, $window, 'both', 'y');
                    $item->speed_score = $this->getDriverSpeedScore($item, $window);
                    $item->overall = $this->getOverall($item);
                }
                break;
            case 'vehicletrend':
                foreach ($items as &$item) {
                    $item->mpg = $this->getMpgArray($item, $window);
                }
                $this->reduce_to_scope($items, 'mpg');
                break;
            case 'drivertrend':
                #TODO: remove when drop downs are complete
                foreach ($items as &$item) {
                    $item->gallons = $this->getVehicleFuelUsage($item, $window);
                    #$item->mpg = $this->calcMPG($item);
                    if ($trend == "accel" || $trend == "overall") {
                        $item->accel_score = $this->getDriverAccelArray($item, $window, 'yes', 'x');
                    }
                    if ($trend == 'decel' || $trend == 'overall') {
                        $item->decel_score = $this->getDriverAccelArray($item, $window, 'no', 'x');
                    }
                    if ($trend == 'hard_turns' || $trend == 'overall') {
                        $item->hard_turns = $this->getDriverAccelArray($item, $window, 'both', 'y');
                    }
                    if ($trend == 'speed' || $trend == 'overall') {
                        $item->speed_score = $this->getDriverSpeedArray($item, $window);
                    }
                }
                if ($trend == 'overall') {
                    #foreach (array('accel', 'decel', 'hard_turns', 'speed') as $context) {
                    #    $this->reduce_to_scope($items, $context);
                    #}
                    $this->getOverallArray($items);
                } else {
                    $this->reduce_to_scope($items);
                }
                break;
        }

        return $items;
    }

    protected function getOverall($item) {
        $value = 0;
        $count = 0;
        if (is_numeric($item->accel_score)) {
            $value += $item->accel_score * .2;
            $count ++;
        }
        if (is_numeric($item->decel_score)) {
            $value += $item->decel_score * .3;
            $count ++;
        }
        if (is_numeric($item->hard_turns)) {
            $value += $item->hard_turns * .2;
            $count ++;
        }
        if (is_numeric($item->speed_score)) {
            $value += $item->speed_score * .3;
            $count ++;
        }

        if (!$count) { return "N/A"; }

        //$value /= $count;

        return $value;
    }

    protected function getOverallArray(&$items) {
        foreach ($items as &$item) {
            $item->overall_score = array();
            for ($x=0; $x<sizeof($item->accel_score); $x++) {
                $value = 0;
                $accel = $item->accel_score[$x]->value * .2;
                $date = $item->accel_score[$x]->date;
                $decel = $item->decel_score[$x]->value * .3;
                $turns = $item->hard_turns[$x]->value * .2;
                $speed = $item->speed_score[$x]->value * .3;
                $value = $accel + $decel + $turns + $speed;
                $item->overall_score[] = new Score($date, $value);
            }
        }
    }

    protected function getDriverAccelScore($item, $window, $accel, $column='x') {

		$db = JFactory::getDBO();
        $query = $db->getQuery(true)
            ->select("count(b.bin_".$column.") as bin_count, b.bin_".$column." as bin_num, severity")
            ->from("fleet_trip as h")
            ->leftJoin('fleet_acceleration as b on h.id = b.trip_id')
            ->leftJoin('fleet_severity as c on b.bin_x = c.bin')
            ->where('b.bin_'.$column.' is not NULL')
            ->group('b.bin_'.$column)
            ;
        if (property_exists($item, 'driver_id')) {
            $query = $query->leftJoin('#__fleet_trip_driver as d on h.id = d.trip_id')
                ->where('d.driver_id='.$item->driver_id);
                //->group('d.driver_id');
        } else {
            $query = $query->leftJoin('#__fleet_trip_subscription as e on e.trip_id = h.id')
                ->leftJoin('#__fleet_subscription as a on e.subscription_id = a.id')
                ->where('a.id="'.$item->vehicle_id.'"')
                ->group('a.id, b.bin_'.$column)
                ;
        }
        if ($accel == 'yes') {
            $query = $query->where('b.'.$column.' <= 127');
        } else if ($accel == 'no') {
            $query = $query->where('b.'.$column.' > 127');
        }
        if ($window) {
            $query = $query->where('h.end_date > DATE_SUB(NOW(), INTERVAL '.$window.' DAY)');
        }

        #var_dump((string)$query);
        $db->setQuery($query);

        $bt = new BinTotals();
        foreach ($db->loadObjectList() as $row) {
            $bt->addRow($row);
        }
        $result = $bt->process_rows();

        return array_sum($result) / sizeof($result);
    }

    protected function getDriverAccelArray($item, $window, $accel, $column='x') {
		$db = JFactory::getDBO();
        $query = $db->getQuery(true)
            ->select("count(b.bin_".$column.") as bin_count, b.bin_".$column." as bin_num, severity, DATE_FORMAT(b.date, '%Y-%m-%d') as date")
            ->from("fleet_trip as h")
            ->leftJoin('fleet_acceleration as b on h.id = b.trip_id')
            ->leftJoin('fleet_severity as c on b.bin_x = c.bin')
            ->where('b.bin_'.$column.' is not NULL')
            ->group('b.bin_'.$column)
            ;
        if (property_exists($item, 'driver_id')) {
            $query = $query->leftJoin('#__fleet_trip_driver as d on h.id = d.trip_id')
                ->where('d.driver_id='.$item->driver_id);
                //->group('d.driver_id');
        } else {
            $query = $query->leftJoin('#__fleet_trip_subscription as e on e.trip_id = h.id')
                ->leftJoin('#__fleet_subscription as a on e.subscription_id = a.id')
                ->where('a.id="'.$item->vehicle_id.'"')
                ->group('a.id, b.bin_'.$column)
                ;
        }
        if ($accel == 'yes') {
            $query = $query->where('b.'.$column.' <= 127');
        } else if ($accel == 'no') {
            $query = $query->where('b.'.$column.' > 127');
        }
        if ($window) {
            $query = $query->where('h.end_date > DATE_SUB(NOW(), INTERVAL '.$window.' DAY)');
        }

        #var_dump((string)$query);
        $db->setQuery($query);

        $bts = array();

        $now = new DateTime();
        $start = clone $now;
        $start->sub(new DateInterval('P'.$window.'D'));
        $range = new DatePeriod($start, new DateInterval('P1D'), $now);
        $now = $now->format('Y-m-d');
        foreach ($range as $d) {
            //$date = $d->format('M d');
            $d = $d->format('Y-m-d');
            $bts[$d] = new BinTotals();
        }
        if (!array_key_exists($now, $bts)) {
            $bts[$now] = new BinTotals();
        }

        foreach ($db->loadObjectList() as $row) {
            $bts[$row->date]->addRow($row);
        }
        $scores = array();
        foreach ($bts as $date => &$bt) {
            $result = $bt->process_rows();
            $scores[] = new Score($date, array_sum($result)/sizeof($result));
        }

        return $scores;
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
        for ($x=1; $x<sizeof($indexes)-2; $x++) {
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
            $mpgs[$x]->emp = $empty;
            $mpgs[$x]->memp = $levels[$v-3]->miles_end;
            #var_dump($levels[$v-1]->miles_start);
            #var_dump($levels[$v-1]->miles_end);
            #var_dump($levels[$v-1]->level);
            #echo "E <br>";
        }

        $consumed = 0;
        $mpg = -1;
        #var_dump($mpgs);
        foreach ($mpgs as $m) {
            #var_dump($m);
            if (!$m->emp) { continue; }
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

    protected function getDriverSpeedScore($item, $window, &$arr=NULL) {

        $clause = "DISTINCT SUM(b.score)/count(b.score) as score, h.id as trip_id, ".
            " DATE(b.date) as date, ".
            "DATE(h.start_date) as start, DATE(h.end_date) as end ";
		$db = JFactory::getDBO();
		$query = $db->getQuery(true)
            ->select($clause)
            ->from('fleet_trip as h')
            ->leftJoin('#__fleet_trip_subscription as e on e.trip_id = h.id')
            ->leftJoin('#__fleet_subscription as a on e.subscription_id = a.id')
            ->leftJoin('fleet_gps as b on h.id = b.trip_id')
            ->group('h.id')
            ->order('b.date')
            ->where('a.id="'.$item->vehicle_id.'"')
            ;

        if (property_exists($item, 'driver_id')) {
            $query = $query->leftJoin('#__fleet_trip_driver as d on h.id = d.trip_id')
                ->where('d.driver_id='.$item->driver_id);
                //->group('d.driver_id');
        } else {
            $query = $query->leftJoin('#__fleet_trip_subscription as e on e.trip_id = h.id')
                ->leftJoin('#__fleet_subscription as a on e.subscription_id = a.id')
                ->where('a.id="'.$item->vehicle_id.'"')
                ->group('a.id, b.bin_'.$column)
                ;
        }
        if ($window) {
            $query = $query->where('h.end_date > DATE_SUB(NOW(), INTERVAL '.$window.' DAY)');
        }

        $db->setQuery($query);

        $rows = $db->loadObjectList();
        $sum = 0;
        $count = 0;
        foreach ($rows as $row) {
            $sum += $row->score;
            $count++;
            if ($arr !== NULL) {
                if (!array_key_exists($row->date, $arr)) {
                    $arr[$row->date] = $row->score;
                } else {
                    $val = $arr[$row->date];
                    $val += $row->score;
                    $val /= 2;
                    $arr[$row->date] = $val;
                }
            }
        }
        return $sum / $count;
    }

    protected function getDriverSpeedArray($item, $window) {
        $scores = array();
        $this->getDriverSpeedScore($item, $window, $scores);

        $now = new DateTime();
        $start = clone $now;
        $start->sub(new DateInterval('P'.$window.'D'));

        $range = new DatePeriod($start, new DateInterval('P1D'), $now);

        # fill missing dates with dummy value
        $ret = array();
        foreach ($range as $d) {
            $date = $d->format('M d');
            $d = $d->format('Y-m-d');
            if (array_key_exists($d, $scores)) {
                $ret[] = new Score($date, $scores[$d]);
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
            //$avg = ($levels[$x-2]->level + $levels[$x-1]->level + $levels[$x]->level) / 3;
            //var_dump($avg);
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
}

?>
