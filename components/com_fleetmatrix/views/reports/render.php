<?php
ob_start();
function renderLineChart($value_arrays, $labels=NULL, $title, $xlabels=NULL) {
//print_r($value_arrays);

//print_r($xlabels);
//exit;
global $chartmapvlaue;

if($value_arrays[0][0] != 0 || $value_arrays[0][0] != ''){
 for ($va=0; $va<sizeof($value_arrays[0]); $va++) {

    $value= $value_arrays[0][$va];
	
    $labels = $xlabels[$va];
	$datess = $labels;
	//$datess = explode("-",$labels);
	if(count($datess) != 3){
	$datess[0];
	$dates = strtotime($datess[0]);
	 $labels = date('Y-m-d', $dates);
	}
	$cur_date = $datess;
	$_SESSION['title'] = $title;
	$datess1 = explode("-",$datess);
	$datess2 = $datess1[1]."-".$datess1[2];
	$reqDate = 1;
		
		/*$hard_turns = $value+0.5;
		$accel_hard =  $value+$value/7;*/
		$hard_turns1 = rand(4,4.5);
		$accel_hard2 = rand(4,4.5);
		
		$hard_turnsPoints = rand(1,100);
		$accel_hardPoints = rand(1,100);
		
		$hard_turns = 	$hard_turns1.".".$hard_turnsPoints;
		$accel_hard =	$accel_hard2.".".$accel_hardPoints;
		
		
		//echo $Abc;
		if($_SESSION['title'] == "all"){
		$value1= $value_arrays[1][$va];
		$value2= $value_arrays[2][$va]; 
		$value3= $value_arrays[3][$va]; 
		$chartmapvlaue .= '{"year":"'. $datess2.'" , "hard_turns":'.round($value3, 2).',"accel":"'.round($value1, 2).'","decel":"'.round($value2, 2).'"},';
		}
		else{
			$chartmapvlaue .= '{"date":"'. $datess2.'" , "duration":'.round($value, 2).'},';
		}
    }
 $chartmapvlaue =  substr($chartmapvlaue,0,-1);
 return $chartmapvlaue;


}
	
}


?>
		