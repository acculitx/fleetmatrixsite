<?php

// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted access' );

ini_set ( 'display_errors', 1 );
error_reporting ( E_ALL );

if (! class_exists ( 'AverageItem' )) {
	class AverageItem {
	}
}

if (! class_exists ( 'Score' )) {
	class Score {
		public function __construct($date, $value) {
			$this->date = $date;
			$this->value = $value;
		}
	}
}
function doAverage($avg, $a, $avtype) {
	if ($avtype != 'speed') {
		return $a;
	}
	$average = $avg [0];
	$max = $avg [1];
	$min = $avg [2];
	
	if ($a < $min) {
		$a = $min;
	}
	if ($a > $max) {
		$a = $max;
	}
	
	if ($a > $average) {
		$step = ($max - $average) / 5;
		$a = (($a - $average) / $step) + 5;
		if ($a > 10) {
			$a = 10;
		}
	} else if ($a < $average) {
		$step = ($average - $min) / 5;
		$a = 5 - (($average - $a) / $step);
		if ($a < 0) {
			$a = 0;
		}
	} else {
		$a = 5;
	}
	
	/*
	 * if ($a < $average) { $a = ($a-$min)*5/($average-$min); } else { $denom = ($max - $average) + 5; if ($denom) { $a = ($a-$average)*5/$denom; } } if ($a > 15) { $a = 15; } if ($a < 0) { $a = 0; }
	 */
	
	return $a;
}

if (! class_exists ( 'BinTotals' )) {
	class BinTotals {
		public function __construct($avg /*$avg=array(5,9999,0)*/) {
			$this->average = $avg [0];
			$this->max = $avg [1];
			$this->min = $avg [2];
			$this->total = 0;
			$this->bin_total = array ();
			$this->severity = array ();
			for($x = 1; $x <= 31; $x ++) {
				$this->bin_total [$x] = 0;
				$this->severity [$x] = 0;
			}
		}
		public function addRow($row) {
			$this->total += ( int ) $row->bin_count;
			$this->bin_total [( int ) $row->bin_num] += $row->bin_count;
			$this->severity [( int ) $row->bin_num] = $row->severity;
		}
		public function process_rows() {
			$debug = JRequest::getCmd ( 'debug', '0' );
			if (! $this->total) {
				return - 1;
			}
			$a = 0;
			for($x = 1; $x <= 31; $x ++) {
				$count = $this->bin_total [$x];
				if ($count) {
					$val = ((600 * $count) / $this->total) * $this->severity [$x];
					$a += $val;
					// printf('%s: ((600 * %s / %s) * %5.2f = %s',$x, $count, $this->total, $this->severity[$x], $val);
					// echo "<br>";
				}
			}
			if ($a < $this->min) {
				$a = $this->min;
			}
			if ($a > $this->max) {
				$a = $this->max;
			}
			
			if ($a > $this->average) {
				$step = ($this->max - $this->average) / 5;
				$a = (($a - $this->average) / $step) + 5;
				if ($a > 10) {
					$a = 10;
				}
			} else if ($a < $this->average) {
				$step = ($this->average - $this->min) / 5;
				$a = 5 - (($this->average - $a) / $step);
				if ($a < 0) {
					$a = 0;
				}
			} else {
				$a = 5;
			}
			// echo "<br>";
			
			// var_dump($this->min);
			// var_dump($this->max);
			// var_dump($this->average);
			// var_dump($a);
			// echo "<hr>";
			return $a;
		}
	}
}

if (! class_exists ( 'ScoreCalculator' )) {
	class ScoreCalculator {
		public function __construct() {
			$this->averages = array ();
		}
		protected function apply_average($a, $avg) {
			$average = ( float ) ($avg [0]);
			$max = ( float ) ($avg [1]);
			$min = ( float ) ($avg [2]);
			if ($a < $min) {
				$a = $min;
			}
			if ($a > $max) {
				$a = $max;
			}
			
			if ($a > $average) {
				$step = ($max - $average) / 5;
				$a = (($a - $average) / $step) + 5;
				if ($a > 10) {
					$a = 10;
				}
			} else if ($a < $average) {
				$step = ($average - $min) / 5;
				$a = 5 - (($average - $a) / $step);
				if ($a < 0) {
					$a = 0;
				}
			} else {
				$a = 5;
			}
			return $a;
		}
		public function getOverall($item) {
			$value = 0;
			$count = 0;
			if (is_numeric ( $item->accel_score )) {
				$value += $item->accel_score * .2;
				$count ++;
			}
			if (is_numeric ( $item->decel_score )) {
				$value += $item->decel_score * .3;
				$count ++;
			}
			if (is_numeric ( $item->hard_turns )) {
				$value += $item->hard_turns * .2;
				$count ++;
			}
			if (is_numeric ( $item->speed_score )) {
				$value += $item->speed_score * .3;
				$count ++;
			}
			
			if (! $count) {
				return "N/A";
			}
			
			// $value /= $count;
			
			return $value;
		}
		public function getOverallArray(&$items) {
			foreach ( $items as &$item ) {
				$item->overall_score = array ();
				for($x = 0; $x < sizeof ( $item->accel_score ); $x ++) {
					$value = 0;
					$accel = $item->accel_score [$x]->value * .2;
					$date = $item->accel_score [$x]->date;
					$decel = $item->decel_score [$x]->value * .3;
					$turns = $item->hard_turns [$x]->value * .2;
					// if (sizeof($item->speed_score)>$x) {
					// $speed = $item->speed_score[$x]->value * .3;
					// } else {
					// $speed = 0;
					// }
					$value = $accel + $decel + $turns;
					$item->overall_score [] = new Score ( $date, $value );
				}
			}
		}
		public function getAverages($avtype) {
			if (! array_key_exists ( $avtype, $this->averages )) {
				$db = JFactory::getDBO ();
				$query = $db->getQuery ( true )->select ( 'top as max, bot as min, mid as average' )->from ( 'fleet_percentile' )->where ( 'type="' . $avtype . '"' )->order ( 'id DESC' );
				$query = (( string ) $query) . ' LIMIT 1';
				// var_dump($query);
				$db->setQuery ( $query );
				$row = $db->loadObject ();
				$this->averages [$avtype] = array (
						$row->average,
						$row->max,
						$row->min 
				);
			}
			return $this->averages [$avtype];
		}
		
		public function getDriverTotalScore($item, $avtype, $diffDays, $window = '7') {
                    $db = JFactory::getDBO ();

                    $clause = 'avg(distinct ' . $avtype .')';

                    $table = 'fleet_daily_total_score as b';

                    $totalDiffDays = (int) $diffDays + (int) $window;

                    $query = $db->getQuery ( true )->select ( $clause )->from ( $table );
                    if (property_exists ( $item, 'driver_id' )) {
                            $query->where ( 'b.driver_id=' . $item->driver_id );
                            $query->where('b.date > DATE_SUB(NOW(), INTERVAL '. $totalDiffDays .' DAY)');
                    } else { // should never be reached
                            $query = $query->leftJoin ( '#__fleet_trip_subscription as e on e.subscription_id = b.subscription_id' )
                            ->leftJoin ( '#__fleet_subscription as a on e.subscription_id = a.id' )
                            ->where ( 'a.id="' . $item->vehicle_id . '"' );
                    }

                    $query = (( string ) $query) . ' group by b.driver_id';

                    $db->setQuery ( $query );

                    $result = $db->loadResult ();

                    // if the driver doesn't have any score prior to specified date
                    // here we return -1 to indicate it's n/a
                    // back to dangerous_drivers, we will set it back to 0 and render n/a
                    if (is_null ( $result )) {
                            $result = -1;
                    }

//                    echo $query;
                    // echo $lastScore;

                    return $result;
		}
		
                public function getCompanyGroupTotalScore($item, $diffDays, $company, $group, $window = '7') {
                    $db = JFactory::getDBO ();

                    $clause = 'avg(b.totalScore) as total_score, avg(b.aggressiveScore) as aggressive_score, avg(b.distractionScore) as distraction_score';

                    $table = 'fleet_entity_daily_total_score as b';

                    $totalDiffDays = (int) $diffDays + (int) $window;

                    $query = $db->getQuery ( true )->select ( $clause )->from ( $table );
                    $query->where('b.date > DATE_SUB(NOW(), INTERVAL '. $totalDiffDays .' DAY)');
                    
                    if ($group) {
                        $query->where ( 'b.entity_id=' . $group);
                        $query = (( string ) $query) . ' group by b.entity_id';
                    }
                    elseif ($company) {
                        $query->where ( 'b.entity_id=' . $company);
                        $query = (( string ) $query) . ' group by b.entity_id';
                    }
                    
                    $db->setQuery ( $query );

                    $result = $db->loadObjectList();

                    // if the driver doesn't have any score prior to specified date
                    // here we return -1 to indicate it's n/a
                    // back to dangerous_drivers, we will set it back to 0 and render n/a
                    if (is_null ( $result )) {
                        $result = -1;
                    }

//                    echo $query;
                    // echo $lastScore;

                    return $result;
		}
                
                public function getCompanyGroupName($company, $group) {
                    if (!$company && !$group) {
                        return 'All Companies';
                    }
                    
                    $db = JFactory::getDBO ();

                    $query = 'select distinct e.name 
                                from fleet_entity_daily_total_score as b, giqwm_fleet_entity as e
                                where b.entity_id = e.id';
                    if ($group) {
                        $query = (( string ) $query) . ' and e.id=' . $group;
                    }
                    elseif ($company) {
                        $query = (( string ) $query) . ' and e.id=' . $company;
                    }
                    
                    $db->setQuery ( $query );

                    $result = $db->loadResult ();

                    // if the driver doesn't have any score prior to specified date
                    // here we return -1 to indicate it's n/a
                    // back to dangerous_drivers, we will set it back to 0 and render n/a
                    if (is_null ( $result )) {
                        $result = -1;
                        return $result;
                    }
                    
                    if (!$group) {
                        $result = $result . ' (All Groups)';
                    }
//                    echo $query;
                    // echo $lastScore;

                    return $result;
		}
                
		public function getDriverADTScore($driver_id) {
			$db = JFactory::getDBO ();
			$select = 'DATE_ADD(redflag.starttime, INTERVAL fleet_trip.time_zone HOUR) as starttime, redflag.type as eventtype, redflag.trip_id as trip_id';
			$from = '#__fleet_trip_driver as trip_driver, fleet_redflag as redflag, fleet_trip';
				
			$query = $db->getQuery ( true ) -> select ( $select ) -> from ( $from );
			$query = $query->where ( 'trip_driver.driver_id=' . $driver_id );
			$query = $query->where ( 'trip_driver.trip_id = redflag.trip_id');
			$query = $query->where ( 'trip_driver.trip_id = fleet_trip.id');
			$query = $query->where ( 'redflag.scoretype=1');
			$query = $query->order ( 'redflag.starttime' . ' DESC' );
			
			$db->setQuery ( $query );
			
			$result = $db->loadObjectList ();
			
// 			echo $query;
			
			return $result;
		}
		
		public function getDriverAllSpeedScore($driver_id) {
			$db = JFactory::getDBO ();
			$select = 'DATE_ADD(speed.date, INTERVAL fleet_trip.time_zone HOUR) as starttime, speed.tripid as trip_id, "speed" as eventtype';
			$from = '#__fleet_trip_driver as trip_driver, fleet_redflag_speed as speed, fleet_trip';
		
			$query = $db->getQuery ( true ) -> select ( $select ) -> from ( $from );
			$query = $query->where ( 'trip_driver.driver_id=' . $driver_id );
			$query = $query->where ( 'trip_driver.trip_id = speed.tripid');
			$query = $query->where ( 'trip_driver.trip_id = fleet_trip.id');
			$query = $query->where ( 'speed.scoretype=2');
			$query = $query->order ( 'speed.date' . ' DESC' );
				
			$db->setQuery ( $query );
				
			$result = $db->loadObjectList ();
				
// 			echo $query;
			// echo $lastScore;
				
			return $result;
		}
		
		public function getDriverAccelScore($item, $window, $avtype = 'accel') {
			$db = JFactory::getDBO ();
			$averages = $this->getAverages ( $avtype );
			if ($window) {
				$table = 'fleet_moving_daily_score as b';
			} else {
				$table = 'fleet_daily_score as b';
			}
                        
			
			$query = $db->getQuery ( true )->select ( sprintf ( "sum(IF(%s > 10, 10, %s))/count(%s)", $avtype, $avtype, $avtype ) )->from ( $table );
			if (property_exists ( $item, 'driver_id' )) {
				/*
				 * $query = $query->leftJoin('#__fleet_trip_driver as d on b.driver_id = d.driver_id') ->where('d.driver_id='.$item->driver_id) ;
				 */
				$query = $query->where ( 'b.driver_id=' . $item->driver_id );
			} else {
				$query = $query->leftJoin ( '#__fleet_trip_subscription as e on e.subscription_id = b.subscription_id' )->leftJoin ( '#__fleet_subscription as a on e.subscription_id = a.id' )->where ( 'a.id="' . $item->vehicle_id . '"' );
			}
			if ($window) {
				$query = $query->where ( 'b.date > DATE_SUB(NOW(), INTERVAL ' . $window . ' DAY)' );
                                $adjustedWindow = $this->adjustWindow($window);
				$query = $query->where ( 'window = ' . $adjustedWindow );
			}
			
			$debug = JRequest::getCmd ( 'debug', '0' );
			if ($debug == '1') {
				var_dump ( ( string ) $query );
			}
			$db->setQuery ( $query );
			
//                        echo $query;
                        
			$result = $db->loadResult ();
			
			$result = doAverage ( $averages, $result, $avtype );
			
			return $result;
		}
                
                /**
                 * if window is not specified through drop down list (not 7,30,90,180)
                 * then we set window = 7. As fleet_moving_daily_score only has pre-computed score for
                 * window 7,30,90,180
                 * 
                 * @param type $window
                 */
                public function adjustWindow($window) {
                    $windowMap = array("7", "30", "90", "180", "365" );
                    if (!in_array($window, $windowMap)) {
                        $window = "7";
                    }
                    return $window;
                }
                
		public function getLastValidScore($item, $window, $avtype) {
			$dbb = JFactory::getDBO ();
			// $averages = $this->getAverages($avtype);
			
			$clause = 'distinct ' . $avtype;
			$table = 'fleet_moving_daily_score as b';
			
			$query = $dbb->getQuery ( true )->select ( $clause )->from ( $table );
			if (property_exists ( $item, 'driver_id' )) {
				$query = $query->where ( 'b.driver_id=' . $item->driver_id );
			} else {
				$query = $query->leftJoin ( '#__fleet_trip_subscription as e on e.subscription_id = b.subscription_id' )->leftJoin ( '#__fleet_subscription as a on e.subscription_id = a.id' )->where ( 'a.id="' . $item->vehicle_id . '"' );
			}
			
			if ($window) {
				$query = $query->where ( 'b.date < DATE_SUB(NOW(), INTERVAL ' . $window . ' DAY)' );
				$query = $query->where ( 'window = ' . $window );
			}
			
			$query = $query->order ( $avtype . ' DESC' );
			$query = (( string ) $query) . ' LIMIT 1';
			
			$dbb->setQuery ( $query );
			
			$lastScore = $dbb->loadResult ();
			
			// if the driver doesn't have any score prior to specified date
			// if (mysql_num_rows($lastScore) == 0) {
			if (is_null ( $lastScore )) {
				$lastScore = 5;
			}
			
			// echo $query;
			// echo $lastScore;
			
			return $lastScore;
		}
		public function getDriverAccelArray($item, $window, $avtype = 'accel') {
			$db = JFactory::getDBO ();
			$averages = $this->getAverages ( $avtype );
			
			if ($window) {
				$table = 'fleet_moving_daily_score as b';
			} else {
				$table = 'fleet_daily_score as b';
			}
			$clause = 'distinct DATE_FORMAT(date, "%Y-%m-%d") as date, ' . $avtype;
			
			if ($window < 2) {
				$table = 'fleet_moving_hourly_score as b';
				$clause = 'distinct DATE_FORMAT(date, "%k") as date, ' . $avtype;
			}
			
			$query = $db->getQuery ( true )->select ( $clause )->from ( $table );
			if (property_exists ( $item, 'driver_id' )) {
				/*
				 * $query = $query->leftJoin('#__fleet_trip_driver as d on b.driver_id = d.driver_id') ->where('d.driver_id='.$item->driver_id) ;
				 */
				$query = $query->where ( 'b.driver_id=' . $item->driver_id );
			} else {
				$query = $query->leftJoin ( '#__fleet_trip_subscription as e on e.subscription_id = b.subscription_id' )->leftJoin ( '#__fleet_subscription as a on e.subscription_id = a.id' )->where ( 'a.id="' . $item->vehicle_id . '"' );
			}
			if ($window) {
				$query = $query->where ( 'b.date > DATE_SUB(NOW(), INTERVAL ' . $window . ' DAY)' );
				$query = $query->where ( 'window = ' . $window );
			}
			
			$debug = JRequest::getCmd ( 'debug', '0' );
			if ($debug == '1') {
				var_dump ( ( string ) $query );
			}
			$db->setQuery ( $query );
			
			$scores = array ();
			
			$now = new DateTime ();
			$start = clone $now;
			if (( int ) $window == 1) {
				$start->sub ( new DateInterval ( 'P' . $window . 'D' ) );
				$range = new DatePeriod ( $start, new DateInterval ( 'PT1H' ), $now );
				$format = 'H';
			} else {
				$start->sub ( new DateInterval ( 'P' . $window . 'D' ) );
				$range = new DatePeriod ( $start, new DateInterval ( 'P1D' ), $now );
				$format = 'Y-m-d';
			}
			$now = $now->format ( $format );
			
			// echo $query;
			
			foreach ( $db->loadObjectList () as $row ) {
				$scores [$row->date] = new Score ( $row->date, doAverage ( $averages, $row->$avtype, $avtype ) );
			}
			
			$ret = array ();
			$lastValidScore = - 1;
			
			foreach ( $range as $d ) {
				$d = $d->format ( $format );
				
				if (array_key_exists ( $d, $scores )) {
					// cap value at 10
					if ($scores [$d]->value > 10)
						$value = 10;
					else
						$value = $scores [$d]->value;
					
					$ret [] = new Score ( $d, $value );
					$lastValidScore = $scores [$d]->value;
				} else {
					// check if this is day 1
					if ($lastValidScore == - 1)
						$lastValidScore = $this->getLastValidScore ( $item, $window, $avtype );
						// cap value at 10
					if ($lastValidScore > 10)
						$lastValidScore = 10;
					$ret [] = new Score ( $d, $lastValidScore );
				}
			}
			
			// echo "score array is: "; echo "<pre>"; print_r($ret);
			return $ret;
		}
		public function getDriverSpeedScore($item, $window, &$arr = NULL) {
			return $this->getDriverAccelScore ( $item, $window, 'speed' );
			
			$averages = $this->getAverages ( 'speed' );
			
			$clause = "DISTINCT SUM(b.score)/count(b.score) as score, h.id as trip_id, " . " DATE_FORMAT(b.date, '%Y-%m-%d') as date, " . "DATE(h.start_date) as start, DATE(h.end_date) as end ";
			if (( int ) $window == 1) {
				$clause = "DISTINCT SUM(b.score)/count(b.score) as score, h.id as trip_id, " . " DATE_FORMAT(b.date, '%k') as date, " . "DATE(h.start_date) as start, DATE(h.end_date) as end ";
			}
			$db = JFactory::getDBO ();
			$query = $db->getQuery ( true )->select ( $clause )->from ( 'fleet_trip as h' )->leftJoin ( '#__fleet_trip_subscription as e on e.trip_id = h.id' )->leftJoin ( '#__fleet_subscription as a on e.subscription_id = a.id' )->leftJoin ( 'fleet_gps as b on h.id = b.trip_id' )->group ( 'h.id' )->order ( 'b.date' )->where ( "UNIX_TIMESTAMP(h.end_date)-UNIX_TIMESTAMP(h.start_date)>60" )->where ( 'a.id="' . $item->vehicle_id . '"' );
			
			if (property_exists ( $item, 'driver_id' )) {
				$query = $query->leftJoin ( '#__fleet_trip_driver as d on h.id = d.trip_id' )->where ( 'd.driver_id=' . $item->driver_id );
				// ->group('d.driver_id');
			} else {
				$query = $query->leftJoin ( '#__fleet_trip_subscription as e on e.trip_id = h.id' )->leftJoin ( '#__fleet_subscription as a on e.subscription_id = a.id' )->where ( 'a.id="' . $item->vehicle_id . '"' )->group ( 'a.id, b.bin_' . $column );
			}
			if ($window) {
				$query = $query->where ( 'h.end_date > DATE_SUB(NOW(), INTERVAL ' . $window . ' DAY)' );
			}
			
			$debug = JRequest::getCmd ( 'debug', '0' );
			if ($debug == '1') {
				var_dump ( ( string ) $query );
			}
			$db->setQuery ( $query );
			
			$rows = $db->loadObjectList ();
			$sum = 0;
			$count = 0;
			foreach ( $rows as $row ) {
				$sum += $row->score;
				$date = $row->date;
				if (( int ) $window == 1) {
					$date = ( int ) $date;
				}
				$count ++;
				if ($arr !== NULL) {
					if (! array_key_exists ( $row->date, $arr )) {
						$arr [$row->date] = $row->score;
					} else {
						$val = $arr [$row->date];
						$val += $row->score;
						$val /= 2;
						$arr [$row->date] = $this->apply_average ( $val, $averages );
					}
				}
			}
			if (! $count) {
				// $v = 5;
				return 0;
			} else
				$v = $sum / $count;
			return $this->apply_average ( $v, $averages );
		}
		public function getDriverSpeedArray($item, $window) {
			return $this->getDriverAccelArray ( $item, $window, 'speed' );
			
			$scores = array ();
			$this->getDriverSpeedScore ( $item, $window, $scores );
			
			$now = new DateTime ();
			$start = clone $now;
			$start->sub ( new DateInterval ( 'P' . $window . 'D' ) );
			if (( int ) $window == 1) {
				$range = new DatePeriod ( $start, new DateInterval ( 'PT1H' ), $now );
				$format = 'H';
				$dformat = 'H';
			} else {
				$range = new DatePeriod ( $start, new DateInterval ( 'P1D' ), $now );
				$format = 'Y-m-d';
				$dformat = 'M d';
			}
			
			// fill missing dates with dummy value
			$ret = array ();
			foreach ( $range as $d ) {
				$date = $d->format ( $dformat );
				$d = $d->format ( $format );
				if (array_key_exists ( $d, $scores )) {
					$ret [] = new Score ( $date, $scores [$d] );
				} else {
					$ret [] = new Score ( $date, - 1 );
				}
			}
			
			return $ret;
		}
		public function getMpgArray($item, $window) {
			$db = JFactory::getDBO ();
			
			$table = 'fleet_mpg_dailyValue as b';
			$clause = 'distinct DATE_FORMAT(date, "%Y-%m-%d") as date, mpg_value';
			
			$query = $db->getQuery ( true )->select ( $clause )->from ( $table );
			$query = $query->where ( 'b.driver_id=' . $item->driver_id );
			
			if ($window) {
				$query = $query->where ( 'b.date > DATE_SUB(NOW(), INTERVAL ' . $window . ' DAY)' );
			}
			
			$debug = JRequest::getCmd ( 'debug', '0' );
			if ($debug == '1') {
				var_dump ( ( string ) $query );
			}
			$db->setQuery ( $query );
			
			$scores = array ();
			
			$now = new DateTime ();
			$start = clone $now;
			
			$start->sub ( new DateInterval ( 'P' . $window . 'D' ) );
			$range = new DatePeriod ( $start, new DateInterval ( 'P1D' ), $now );
			$format = 'Y-m-d';
			
			$now = $now->format ( $format );
			
// 			echo $query;
			
			foreach ( $db->loadObjectList () as $row ) {
// 				echo "row date is: "; echo "<pre>"; print_r($row->date);
// 				echo "row mpg value is: "; echo "<pre>"; print_r($row->mpg_value);
				$scores [$row->date] = new Score ( $row->date, $row->mpg_value );
			}
			
			$ret = array ();
			$lastValidScore = - 1;
			
// 			echo "score array is: "; echo "<pre>"; print_r($scores);
			foreach ( $range as $d ) {
				$d = $d->format ( $format );
				
				if (array_key_exists ( $d, $scores )) {
					$ret [] = new Score ( $d, $scores [$d]->value );
					$lastValidScore = $scores [$d]->value;
				} else {
					// check if this is day 1
					if ($lastValidScore == - 1)
						$lastValidScore = $this->getLastValidMpgScore ( $item, $window );
					
					$ret [] = new Score ( $d, $lastValidScore );
				}
			}
			
// 			echo "score array is: "; echo "<pre>"; print_r($ret);
			return $ret;
		}
		public function getLastValidMpgScore($item, $window) {
			$dbb = JFactory::getDBO ();
			// $averages = $this->getAverages($avtype);
				
			$clause = 'distinct mpg_value';
			$table = 'fleet_mpg_dailyValue as b';
				
			$query = $dbb->getQuery ( true )->select ( $clause )->from ( $table );
			$query = $query->where ( 'b.driver_id=' . $item->driver_id );
				
			if ($window) {
				$query = $query->where ( 'b.date < DATE_SUB(NOW(), INTERVAL ' . $window . ' DAY)' );
			}
				
			$query = $query->order ( 'mpg_value DESC' );
			$query = (( string ) $query) . ' LIMIT 1';
				
			$dbb->setQuery ( $query );
				
			$lastScore = $dbb->loadResult ();
				
			// if the driver doesn't have any score prior to specified date
			// if (mysql_num_rows($lastScore) == 0) {
			if (is_null ( $lastScore )) {
				$lastScore = 0;
			}
				
			// echo $query;
			// echo $lastScore;
				
			return $lastScore;
		}

    }
}
?>
