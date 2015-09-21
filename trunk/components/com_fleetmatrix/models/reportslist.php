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
//     	$config['filter_fields'] = array(
//     			'Driver Name', 'driver_name',
//     			'Idle Time (minutes)', 'idle_time',
//     			'Miles Driven', 'miles'
//     	);
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
		$windowtwo = $this->getState(strtolower($this->model_key).'.windowtwo');
		// $windowtwo = JRequest::getInt('windowtwo', 0);
        $company = JRequest::getInt('company', 0);
        $group = JRequest::getInt('group', 0);
        $vehicle = JRequest::getInt('vehicle', 0);
        $driver = JRequest::getInt('driver', 0);
        $diffDays = JRequest::getInt('diffDays', 0);

        // Create a new query object.
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        switch ($cmd) {
            case 'vehicle':
            	$clause = "distinct s.id as vehicle_id, s.name as vehicle_name, a.name as group_name, ".
            			"SUM(h.odo_end - h.odo_start) as miles, ".
            			"COUNT(h.subscriber_id) as trip_count, ".
            			"SUM(h.gallon_consumed) as gallon_consumed";
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
            case 'vehicletrend':
                $clause = "distinct s.id as vehicle_id, s.subscription_id, s.driver_id, a.name as group_name, ".
                        "a.id as group_id, a.parent_entity_id as company_id, ".
                        "'N/A' as mpg, ".
                        "s.name as vehicle_name, ".
                        "SUM(h.odo_end - h.odo_start) as miles, ".
                        "COUNT(h.subscriber_id) as trip_count, ".
                        "'N/A' as gallons, ".
                        "'N/A' as not_connected, ".
                        "'N/A' as disconnects ";
                $query = $query->select($clause)
                    ->from('fleet_trip as h')
                    ->leftJoin('#__fleet_trip_subscription as e on e.trip_id = h.id')
                    ->join('left outer', '#__fleet_subscription as s on e.subscription_id = s.id')
                    ->leftJoin('#__fleet_trip_driver as d on h.id = d.trip_id')
                    ->leftJoin('#__fleet_driver as b on d.driver_id = b.id')
                    ->leftJoin('#__fleet_entity as a on b.entity_id = a.id')
                    ->group('b.id')
                    ->where('b.visible')
                    ->where('UNIX_TIMESTAMP(h.end_date)-UNIX_TIMESTAMP(h.start_date)>60')
                    ;
                break;
            case 'driver':
                $clause = "distinct s.name as vehicle_name, ".
                        "DATE_ADD(h.start_date, INTERVAL h.time_zone HOUR) as trip_start, ".
//                         "DATE_ADD(h.end_date, INTERVAL h.time_zone HOUR) as trip_end, ".
                        "h.odo_end - h.odo_start as miles, ".
                        "b.name as assigned_driver,".
                        "a.name as group_name, ".
                        "aa.name as company_name, ".
            			"redflag.hard_turns_hard_count as turns_hard,".
            			"redflag.hard_turns_severe_count as turns_severe,".
            			"redflag.accel_hard_count as accel_hard,".
            			"redflag.accel_severe_count as accel_severe,".
            			"redflag.decel_hard_count as decel_hard,".
            			"redflag.decel_severe_count as decel_severe,".
                        "sScore.hard_count as speed_hard,".
                        "sScore.severe_count as speed_severe,".
                        "d.driver_id, h.id as trip_id,  ".
                        "idle.idle_time"
                		;
            $query = $query->select($clause)
                    ->from('fleet_trip as h')
                    ->leftJoin('#__fleet_trip_subscription as e on e.trip_id = h.id')
                    ->join('left outer', '#__fleet_subscription as s on e.subscription_id = s.id')
                    ->leftJoin('#__fleet_trip_driver as d on h.id = d.trip_id')
                    ->leftJoin('#__fleet_driver as b on d.driver_id = b.id')
                    ->leftJoin('#__fleet_entity as a on b.entity_id = a.id')
                    ->leftJoin('fleet_idletime as idle ON h.id = idle.trip_id')
                    ->leftJoin('#__fleet_entity as aa on a.parent_entity_id = aa.id')
                    ->leftJoin('fleet_redflag_report as redflag ON h.id = redflag.tripid')
                    ->leftJoin('fleet_redflag_speed_report as sScore ON h.id = sScore.tripid ')
                    ->group('h.id')
                    #->where('c.visible')
                    ->where('b.visible')
                    ->where('UNIX_TIMESTAMP(h.end_date)-UNIX_TIMESTAMP(h.start_date)>60')
                    ->order('h.end_date DESC')
                    ;
//                 echo ($query);
                break;
            case 'tendency':
            case 'drivertrend':
                $clause = "distinct b.name as driver_name, ".
                        "a.name as group_name, s.id as vehicle_id, ".
                        "a.id as group_id, a.parent_entity_id as company_id, ".
                        "d.driver_id,".
                        "COUNT(h.subscriber_id) as trip_count, ".
                        "'N/A' as mpg, ".
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
            case 'idletime':
            	$clause = "distinct b.name as driver_name, ".
            			"SUM(idle.idle_time) as idle_time,".
            			"COUNT(h.subscriber_id) as trip_count, ".
            			"SUM(h.odo_end - h.odo_start) as miles "
            			;
            	$query = $query->select($clause)
            			->from('fleet_trip as h')
            			->leftJoin('#__fleet_trip_subscription as e on e.trip_id = h.id')
            			->join('left outer', '#__fleet_subscription as s on e.subscription_id = s.id')
            			->leftJoin('#__fleet_trip_driver as d on h.id = d.trip_id')
            			->leftJoin('#__fleet_driver as b on d.driver_id = b.id')
            			->leftJoin('#__fleet_entity as a on b.entity_id = a.id')
            			->leftJoin('fleet_idletime as idle ON h.id = idle.trip_id')
            			->group('b.id')
            			->where('b.visible')
            			->where('UNIX_TIMESTAMP(h.end_date)-UNIX_TIMESTAMP(h.start_date)>60')
            		;
//             	echo ($query);
            	break;
            case 'vigilance':
            	$clause = "distinct b.name as driver_name, ".
              			"a.name as group_name, ".
              			"aa.name as company_name, ".
            			"SUM(redflag.hard_turns_hard_count) as turns_hard,".
            			"SUM(redflag.hard_turns_severe_count) as turns_severe,".
            			"SUM(redflag.accel_hard_count) as accel_hard,".
            			"SUM(redflag.accel_severe_count) as accel_severe,".
            			"SUM(redflag.decel_hard_count) as decel_hard,".
            			"SUM(redflag.decel_severe_count) as decel_severe,".
            			"SUM(speed.hard_count) as speed_hard,".
            			"SUM(speed.severe_count) as speed_severe,".
            			"COUNT(h.subscriber_id) as trip_count, ".
            			"SUM(h.odo_end - h.odo_start) as miles "
            			;
          	$query = $query->select($clause)
            			->from('fleet_trip as h')
            			->leftJoin('#__fleet_trip_subscription as e on e.trip_id = h.id')
            			->join('left outer', '#__fleet_subscription as s on e.subscription_id = s.id')
            			->leftJoin('#__fleet_trip_driver as d on h.id = d.trip_id')
            			->leftJoin('#__fleet_driver as b on d.driver_id = b.id')
            			->leftJoin('#__fleet_entity as a on a.id = b.entity_id')
            			->leftJoin('#__fleet_entity as aa on a.parent_entity_id = aa.id')
            			->leftJoin('fleet_redflag_report as redflag ON h.id = redflag.tripid')
            			->leftJoin('fleet_redflag_speed_report as speed ON h.id = speed.tripid ')
            			->group('b.id')
            			->where('b.visible')
            			->where('UNIX_TIMESTAMP(h.end_date)-UNIX_TIMESTAMP(h.start_date)>60')
            		;
            	break;
				
            	case 'totalscore':
            		$clause = "distinct b.name as driver_name, ".
              				"b.id as driver_id, ".
            				"a.name as group_name, ".
            				"aa.name as company_name"
            		;
            		$query = $query->select($clause)
            		->from('fleet_trip as h')
            		->leftJoin('#__fleet_trip_subscription as e on e.trip_id = h.id')
            		->join('left outer', '#__fleet_subscription as s on e.subscription_id = s.id')
            		->leftJoin('#__fleet_trip_driver as d on h.id = d.trip_id')
            		->leftJoin('#__fleet_driver as b on d.driver_id = b.id')
            		->leftJoin('#__fleet_entity as a on a.id = b.entity_id')
            		->leftJoin('#__fleet_entity as aa on a.parent_entity_id = aa.id')
            		->group('d.driver_id')
            		->where('b.visible')
            		;
            	break;
            	
            	case 'severelist':
            		$clause = "distinct b.name as driver_name, ".
            				"b.id as driver_id, ".
            				"a.name as group_name, ".
            				"aa.name as company_name"
            		;
            		$query = $query->select($clause)
            		->from('fleet_trip as h')
            		->leftJoin('#__fleet_trip_subscription as e on e.trip_id = h.id')
            		->join('left outer', '#__fleet_subscription as s on e.subscription_id = s.id')
            		->leftJoin('#__fleet_trip_driver as d on h.id = d.trip_id')
            		->leftJoin('#__fleet_driver as b on d.driver_id = b.id')
            		->leftJoin('#__fleet_entity as a on a.id = b.entity_id')
            		->leftJoin('#__fleet_entity as aa on a.parent_entity_id = aa.id')
            		->group('d.driver_id')
            		->where('b.visible')
            		;
            	break;
            	 
		case 'driverfuelreport':
                    $clause = "distinct b.name as driver_name, ".
                                    "a.name as group_name, ".
                                    "aa.name as company_name, ".
                                    "SUM(redflag.hard_turns_hard_count) as turns_hard,".
                                    "SUM(redflag.accel_hard_count) as accel_hard,".
                                    "SUM(redflag.decel_hard_count) as decel_hard,".
                                    "SUM(speed.hard_count) as speed_hard,".
                                    "COUNT(h.subscriber_id) as trip_count, ".
                                                    "SUM(h.gallon_consumed) as gallon_consumed, ".
                                                    "idle.idle_time, ".
                                    "SUM(h.odo_end - h.odo_start) as miles "
                                    ;
                    $query = $query->select($clause)
                                    ->from('fleet_trip as h')
                                    ->leftJoin('#__fleet_trip_subscription as e on e.trip_id = h.id')
                                    ->join('left outer', '#__fleet_subscription as s on e.subscription_id = s.id')
                                    ->leftJoin('#__fleet_trip_driver as d on h.id = d.trip_id')
                                                     ->leftJoin('fleet_idletime as idle ON h.id = idle.trip_id')
                                    ->leftJoin('#__fleet_driver as b on d.driver_id = b.id')
                                    ->leftJoin('#__fleet_entity as a on a.id = b.entity_id')
                                    ->leftJoin('#__fleet_entity as aa on a.parent_entity_id = aa.id')
                                    ->leftJoin('fleet_redflag_report as redflag ON h.id = redflag.tripid')
                                    ->leftJoin('fleet_redflag_speed_report as speed ON h.id = speed.tripid ')
                                    ->group('b.id')
                                    ->where('b.visible')
                                    ->where('UNIX_TIMESTAMP(h.end_date)-UNIX_TIMESTAMP(h.start_date)>60');
            	break;
                case 'vigilancetrend':
                    $clause = "AVG(redflag.hard_turns_hard) as turns_hard, ".
              			"AVG(redflag.hard_turns_severe) as turns_severe, ".
              			"AVG(redflag.accel_hard) as accel_hard, ".
            			"AVG(redflag.accel_severe) as accel_severe,".
            			"AVG(redflag.decel_hard) as decel_hard,".
            			"AVG(redflag.decel_severe) as decel_severe,".
						"redflag.trip_id as tripid,".
            			"AVG(redflag.speed_hard)  as speed_hard,".
            			"AVG(redflag.speed_severe)  as speed_severe,".
						"redflag.window  as windowtwo,".
						"redflag.date  as date,".
						"b.name as driver_name, ".
            			"h.id "
            			;
						
                    $query = $query->select($clause)
            			->from('fleet_trip as h')
            			->join('','fleet_vigilance_windowScore as redflag ON h.id =  redflag.trip_id')
						->leftJoin('#__fleet_trip_driver as d on h.id = d.trip_id')
            			->leftJoin('#__fleet_driver as b on d.driver_id = b.id')
						->leftJoin('#__fleet_entity as a on a.id = b.entity_id')
						->leftJoin('#__fleet_entity as aa on a.parent_entity_id = aa.id')
						->group('DATE(redflag.date)')
						->order('redflag.date ASC');
            	break;
				
            default:
                $query = $query->select('1=0');
                return $query;
                break;

        }

        # Apply time window if specified
        if ($cmd == 'totalscore') { # only totalscore has implemented diffDays
            $totalDiffDays = (int) $diffDays + 7;
            $query = $query->where('h.end_date > DATE_SUB(NOW(), INTERVAL ' . $totalDiffDays . ' DAY)');
        } else {
            if ($window) {
                $query = $query->where('h.end_date > DATE_SUB(NOW(), INTERVAL '.$window.' DAY)');
            } else {
                $query = $query->where('h.end_date > DATE_SUB(NOW(), INTERVAL 1 DAY)');
            }
            if ($windowtwo) {
                $query = $query->where('redflag.`date` > DATE_SUB(NOW(), INTERVAL '.$windowtwo.' DAY)');
            }
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

//        var_dump((string)$query);
//         echo $query;
		return $query;
	}

    public function getItems() {
        $items = parent::getItems();

        $cmd = $this->getState(strtolower($this->model_key).'.cmd');
        $window = $this->getState(strtolower($this->model_key).'.window');
        $windowtwo = $this->getState(strtolower($this->model_key).'.windowtwo');
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
        $idletime = JRequest::getInt('idletime', 0);
        $diffDays = JRequest::getInt('diffDays', 0);

        switch ($cmd) {
            case 'vehicle':
                foreach ($items as &$item) {
                    $ret = $this->getVehicleFuelUsage($item, $window);
                    #var_dump($ret);
//                     $item->gallons = $ret[0];
//                     $item->mpg = $ret[1];
                    $item->disconnects = $this->getDisconnects($item, $window);
                    $item->not_connected = $this->not_connected($item, $window);
                }
                break;
            case 'tendency':
                foreach ($items as &$item) {
                    #$item->gallons = $this->getVehicleFuelUsage($item, $window);
                    #$item->mpg = $this->calcMPG($item);
                    $item->accel_score = $this->calculator->getDriverAccelScore($item, $window, 'accel');
                    $item->decel_score = $this->calculator->getDriverAccelScore($item, $window, 'decel');
                    $item->hard_turns = $this->calculator->getDriverAccelScore($item, $window, 'hard_turns');
//                     $item->speed_score = $this->calculator->getDriverSpeedScore($item, $window);
//                     $item->overall_score = $this->calculator->getOverall($item);
                }
//                echo "<pre>"; print_r($items);
                break;
            case 'totalscore':
                foreach ($items as &$item) {
                    $item->total_score = $this->calculator->getDriverTotalScore($item, 'totalScore', $diffDays);
                    $item->aggressive_score = $this->calculator->getDriverTotalScore($item, 'aggressiveScore', $diffDays);
                    $item->distraction_score = $this->calculator->getDriverTotalScore($item, 'distractionScore', $diffDays);
                    
                    $item->company_group_name = $this->calculator->getCompanyGroupName($company, $group);
                    $company_group_score = $this->calculator->getCompanyGroupTotalScore($item, $diffDays, $company, $group, '7');
                    $item->company_group_total_score = count($company_group_score) > 0 ? $company_group_score[0]->total_score : 0;
                    $item->company_group_aggressive_score = count($company_group_score) > 0 ? $company_group_score[0]->aggressive_score : 0;
                    $item->company_group_distraction_score = count($company_group_score) > 0 ? $company_group_score[0]->distraction_score : 0;
                }
                break;
            case 'vehicletrend':
                foreach ($items as &$item) {
                    $item->mpg = $this->calculator->getMpgArray($item, $window);
                }
//                 echo "<pre>"; echo "before reduce to average"; print_r($items);
//                 $this->reduce_to_scope($items, 'mpg');
//                 $items = $this->reduce_to_average($items, $company, $group, 0, $vehicle, 'vehicle_name');
//                 $items = $this->reduce_to_average($items, $company, $group, $driver, 0);
//                 echo "<pre>"; echo "after reduce to average"; print_r($items);
//                 $items = $this->reduce_to_average($items, $company, $group, $driver, 0);
//                 echo "<pre>"; print_r($items);
                break;
               
            case 'drivertrend':
                #TODO: remove when drop downs are complete
//             	echo "<pre>"; print_r($items);
                foreach ($items as &$item) {
                    #$item->gallons = $this->getVehicleFuelUsage($item, $window);
                    #$item->mpg = $this->calcMPG($item);
                    if ($trend == "accel" || $trend == "overall" || $trend=="all") {
                        $item->accel_score = $this->calculator->getDriverAccelArray($item, $window, 'accel');
// 						echo "<pre>"; print_r($item->accel_score);
                    }
                    if ($trend == 'decel' || $trend == 'overall' || $trend=="all") {
                        $item->decel_score = $this->calculator->getDriverAccelArray($item, $window, 'decel');
						//echo "<pre>"; print_r($item->decel_score);
                    }
                    if ($trend == 'hard_turns' || $trend == 'overall' || $trend=="all") {
                        $item->hard_turns = $this->calculator->getDriverAccelArray($item, $window, 'hard_turns');
						//echo "<pre>"; print_r($item->hard_turns);
                    }
//                     if ($trend == 'speed' || $trend == 'overall' || $trend=="all") {
					
//                         $item->speed_score = $this->calculator->getDriverSpeedArray($item, $window);
// 						//echo "<pre>"; print_r($item->speed_score);
//                     }
                }
                
                if ($trend == 'overall' || $trend == 'all') {
                    foreach (array('accel', 'decel', 'hard_turns') as $context) {
//                         $this->reduce_to_scope($items, $context);
                        $this->cap_to_zero($items, $context);
                    }
                    $this->calculator->getOverallArray($items);
                } else {
//                 	echo "in else"; echo "<pre>"; print_r($items);
//                     $this->reduce_to_scope($items);
                }
//                 echo "after else"; echo "<pre>"; print_r($items);
//                 $items = $this->reduce_to_average($items, $company, $group, $driver, 0);
// 				echo "after reduce to average"; echo "<pre>"; print_r($items);
				
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
                $ret[] = new Score($d, $fuel[$d]);
            } else {
                $ret[] = new Score($d, -1);
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
//         echo "v is: "; echo "<pre>"; print_r($v);
        $firsts = array();
        $lasts = array();
        foreach($v as &$values) {
            $first = NULL;
            $last = sizeof($v[0])-1;
            // find the first date with non-zero score
//             for ($x=0; $x<$last; $x++) {
//                 if ($values[$x]->value != -1) {
//                     $first = $x;
//                     break;
//                 }
//             }

//             echo "first is: "; echo "<pre>"; print_r($first);
            // find the last date with non-zero score
//             for ($x=$last; $x>$first; $x--) {
//                 if ($values[$x]->value != -1) {
//                     $last = $x;
//                     break;
//                 }
//             }
//             echo "last is: "; echo "<pre>"; print_r($last);
			// loops thru the values array
//             $prev = 0;
//             foreach ($values as &$vv) {
//                 if ($vv->value == -1) {
//                     $vv->value = $prev;
//                 }
//                 $prev = $vv->value;
//             }
//             if (!is_null($first)) {
//                 $firsts[] = $first;
//                 $lasts[] = $last;
//             }
//             echo "values is: "; echo "<pre>"; print_r($values);
//             echo "first array is: "; echo "<pre>"; print_r($firsts);
//             echo "last array is: "; echo "<pre>"; print_r($lasts);
        }
        #var_dump($firsts);
        if (sizeof($firsts)) {
            $first = min($firsts);
            $last = max($lasts);
			
            for ($x=0; $x<sizeof($v); $x++) {
//             	echo "x is: "; echo "<pre>"; print_r($x);
//             	echo "items is: "; echo "<pre>"; print_r($items);
                $items[$x]->$context = array_slice($v[$x], $first, $last-$first);
//                 echo "items is: "; echo "<pre>"; print_r($items);
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
        if ($company) {
        } else
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
