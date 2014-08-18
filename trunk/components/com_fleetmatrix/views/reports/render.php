<?php
ob_start ();
function renderLineChart($value_arrays, $labels = NULL, $title, $xlabels = NULL) {
// 	echo "value array is: "; echo "<pre>"; print_r($value_arrays);
// 	echo "labels is: "; echo "<pre>"; print_r($labels);
// 	echo "title is: "; echo "<pre>"; print_r($title);
// 	echo "xlabels is: "; echo "<pre>"; print_r($xlabels);
	// exit;
	global $chartmapvlaue;
	
	$average_value_array = array();
	$driverSize = sizeof($labels);
	
	// in case 'all drivers' is selected, we compute the average first
	if ($title != 'all') {
		for ($i=0; $i < sizeof($value_arrays[0]); $i++) { // for each date
			$sum = 0;
			for ($j=0; $j < $driverSize ; $j++) { // for each driver
				$sum = $sum + $value_arrays[$j][$i];
			}
			$average_value_array[0][] = $sum/$driverSize;
		}
	} else {
		$average_value_array = $value_arrays;
	}
	
	if ($average_value_array [0] [0] != '') {
		for($va = 0; $va < sizeof ( $average_value_array [0] ); $va ++) {
			
			$value = $average_value_array [0] [$va];
			
			$labels = $xlabels [$va];
			$datess = $labels;
			// $datess = explode("-",$labels);
			if (count ( $datess ) != 3) {
				$datess [0];
				$dates = strtotime ( $datess [0] );
				$labels = date ( 'Y-m-d', $dates );
			}
			$cur_date = $datess;
			$_SESSION ['title'] = $title;
			$datess1 = explode ( "-", $datess );
			$datess2 = $datess1 [1] . "-" . $datess1 [2];
			$reqDate = 1;
			
			/*
			 * $hard_turns = $value+0.5; $accel_hard = $value+$value/7;
			 */
			$hard_turns1 = rand ( 4, 4.5 );
			$accel_hard2 = rand ( 4, 4.5 );
			
			$hard_turnsPoints = rand ( 1, 100 );
			$accel_hardPoints = rand ( 1, 100 );
			
			$hard_turns = $hard_turns1 . "." . $hard_turnsPoints;
			$accel_hard = $accel_hard2 . "." . $accel_hardPoints;
			
			// echo $Abc;
			if ($_SESSION ['title'] == "all") {
				$value1 = $value_arrays [1] [$va]; // accel
				$value2 = $value_arrays [2] [$va]; // decel
				$value3 = $value_arrays [3] [$va]; // turns
				$chartmapvlaue .= '{"year":"' . $datess2 . '" , "hard_turns":' . round ( $value3, 2 ) . ',"accel":"' . round ( $value1, 2 ) . '","decel":"' . round ( $value2, 2 ) . '"},';
			} else {
				$chartmapvlaue .= '{"date":"' . $datess2 . '" , "duration":' . round ( $value, 2 ) . '},';
			}
		}
		$chartmapvlaue = substr ( $chartmapvlaue, 0, - 1 );
		return $chartmapvlaue;
	}
}

?>
		