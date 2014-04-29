<?php

function renderLineChart($value_arrays, $labels=NULL, $title='', $xlabels=NULL) {
//print_r($value_arrays);

//print_r($xlabels);
//exit;
global $chartmapvlaue;

if($value_arrays[0][0] != 0 || $value_arrays[0][0] != ''){
 for ($va=0; $va<sizeof($value_arrays[0]); $va++) {

   $value= $value_arrays[0][$va];
    $labels = $xlabels[$va];
/*	$datess = explode("-",$labels);
	if(count($datess) != 3){
	$datess[0];
	$dates = strtotime($datess[0]);
	 $labels = date('Y-m-d', $dates);
	}*/
	
	$chartmapvlaue .= '{"date":"'. $labels.'" , "duration":'.$value.'},';
    }
 $chartmapvlaue =  substr($chartmapvlaue,0,-1);
return $chartmapvlaue;
}
	
}


?>
		