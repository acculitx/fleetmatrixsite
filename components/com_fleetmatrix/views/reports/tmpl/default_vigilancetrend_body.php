<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');



$trip_id = '';
$accel_hard = '';
$accel_severe = '';
$decel_hard = '';
$decel_severe = '';
$turns_hard = '';
$turns_severe = '';
$speed_hard = '';
$speed_severe = '';

global $chartmapvlaue;
$_SESSION['lastdays'] = '';

function dateDiff ($d1, $d2) {
// Return the number of days between the two dates:

  return round(abs(strtotime($d1)-strtotime($d2))/86400);

}  // end function dateDiff

 $total = count($this->items);

foreach($this->items as $i => $item) {
 $time =  strtotime($item->date);
  //echo $item->date;
 
  $days = dateDiff(date('Y-m-d'), date("Y-m-d",$time));




 $ltday = (int)$_SESSION['lastdays']-(int)$days ;
 
$forlop = $_SESSION['lastdays'];
if($ltday <= 1){


$_SESSION['lastdays'] = $days;
//$trip_id = $item->id."-window-".$item->windowtwo;

//$timestamp = strtotime($item->date);
// $trip_id = date("M-d", $timestamp);

 $trip_id = $days;
 // echo "<pre>";print_r($trip_id);

if($item->accel_hard == '') {
$accel_hard = 0;
} else {
$accel_hard = round($item->accel_hard, 2);
$_SESSION['accelhard'] = round($item->accel_hard, 2);
}


if($item->accel_severe == '') {
$accel_severe = 0;
} else {
$accel_severe = round($item->accel_severe, 2);
$_SESSION['accelsevere'] = round($item->accel_severe, 2);
}


if($item->decel_hard == '') {
$decel_hard = 0;
} else {
$decel_hard = round($item->decel_hard, 2);
$_SESSION['decelhard'] = round($item->decel_hard, 2);
}


if($item->decel_severe == '') {
$decel_severe = 0;
} else {
$decel_severe = round($item->decel_severe, 2);
$_SESSION['decelsevere'] = round($item->decel_severe, 2);
}


if($item->turns_hard == '') {
$turns_hard = 0;
} else {
$turns_hard = round($item->turns_hard, 2);
$_SESSION['turnshard'] = round($item->turns_hard, 2);
}

if($item->turns_severe == '') {
$turns_severe = 0;
} else {
$turns_severe = round($item->turns_severe, 2);
$_SESSION['turnssevere'] = round($item->turns_severe, 2);
}

if($item->speed_hard == '') {
$speed_hard = 0;
} else {
$speed_hard = round($item->speed_hard, 2);
$_SESSION['speedhard'] = round($item->speed_hard, 2);
}


if($item->speed_severe == '') {
$speed_severe = 0;
} else {
$speed_severe = round($item->speed_severe, 2);
$_SESSION['speedsevere'] = round($item->speed_severe, 2);
}

    $crnt_date = explode("-",$item->date);
	$req_date = $crnt_date[1]."/".$crnt_date[2];
	$req_date1 = explode(" ",$req_date);
	$reqDate = $req_date1[0];
	$chartmapvlaue .= '{"year":"'. $reqDate.'" , "Accel hard":"'.$accel_hard.'" , "Accel Severe":"'.$accel_severe.'", "Decel Hard":"'.$decel_hard.'", "Decel severe":"'.$decel_severe.'", "Turns hard":"'.$turns_hard.'", "Turns severe":"'.$turns_severe.'", "Speed hard":"'.$speed_hard.'", "Speed severe":"'.$speed_severe.'"},';
	
	
	if($total-1 == $i){

	
	for($k=$days-1; $k>0; $k--){

 $m = $k;
$_SESSION['lastdays'] = $k;
  $trip_id = $k;
$accel_hard = $_SESSION['accelhard'];
$accel_severe = $_SESSION['accelsevere'];
$decel_hard = $_SESSION['decelhard'];
$decel_severe = $_SESSION['decelsevere'];
$turns_hard = $_SESSION['turnshard'];
$turns_severe = $_SESSION['turnssevere'];
$speed_hard = $_SESSION['speedhard'];
$speed_severe = $_SESSION['speedsevere'];
   $crnt_date = explode("-",$item->date);
	$req_date = $crnt_date[1]."/".$crnt_date[2];
	$req_date1 = explode(" ",$req_date);
	$reqDate = $req_date1[0];
   $chartmapvlaue .= '{"year":"'. $reqDate.'" , "Accel hard":"'.$accel_hard.'" , "Accel Severe":"'.$accel_severe.'", "Decel Hard":"'.$decel_hard.'", "Decel severe":"'.$decel_severe.'", "Turns hard":"'.$turns_hard.'", "Turns severe":"'.$turns_severe.'", "Speed hard":"'.$speed_hard.'", "Speed severe":"'.$speed_severe.'"},';
   }
	
	}
	
} else {


for($k=$forlop-1; $k>$days; $k--){

 $m = $k;
if($m-1 != $days){
$_SESSION['lastdays'] = $k;
  $trip_id = $k;
$accel_hard = $_SESSION['accelhard'];
$accel_severe = $_SESSION['accelsevere'];
$decel_hard = $_SESSION['decelhard'];
$decel_severe = $_SESSION['decelsevere'];
$turns_hard = $_SESSION['turnshard'];
$turns_severe = $_SESSION['turnssevere'];
$speed_hard = $_SESSION['speedhard'];
$speed_severe = $_SESSION['speedsevere'];
	
    $crnt_date = explode("-",$item->date);
	$req_date = $crnt_date[1]."/".$crnt_date[2];
	$req_date1 = explode(" ",$req_date);
	$reqDate = $req_date1[0];
   $chartmapvlaue .= '{"year":"'. $reqDate.'" , "Accel hard":"'.$accel_hard.'" , "Accel Severe":"'.$accel_severe.'", "Decel Hard":"'.$decel_hard.'", "Decel severe":"'.$decel_severe.'", "Turns hard":"'.$turns_hard.'", "Turns severe":"'.$turns_severe.'", "Speed hard":"'.$speed_hard.'", "Speed severe":"'.$speed_severe.'"},';
   } else {
   
  
$_SESSION['lastdays'] = $days;
//$trip_id = $item->id."-window-".$item->windowtwo;

//$timestamp = strtotime($item->date);
// $trip_id = date("M-d", $timestamp);

 $trip_id = "DAY ".$days;

if($item->accel_hard == '') {
$accel_hard = 0;
} else {
$accel_hard = round($item->accel_hard, 2);
$_SESSION['accelhard'] = round($item->accel_hard, 2);
}


if($item->accel_severe == '') {
$accel_severe = 0;
} else {
$accel_severe = round($item->accel_severe, 2);
$_SESSION['accelsevere'] = round($item->accel_severe, 2);
}


if($item->decel_hard == '') {
$decel_hard = 0;
} else {
$decel_hard = round($item->decel_hard, 2);
$_SESSION['decelhard'] = round($item->decel_hard, 2);
}


if($item->decel_severe == '') {
$decel_severe = 0;
} else {
$decel_severe = round($item->decel_severe, 2);
$_SESSION['decelsevere'] = round($item->decel_severe, 2);
}


if($item->turns_hard == '') {
$turns_hard = 0;
} else {
$turns_hard = round($item->turns_hard, 2);
$_SESSION['turnshard'] = round($item->turns_hard, 2);
}

if($item->turns_severe == '') {
$turns_severe = 0;
} else {
$turns_severe = round($item->turns_severe, 2);
$_SESSION['turnssevere'] = round($item->turns_severe, 2);
}

if($item->speed_hard == '') {
$speed_hard = 0;
} else {
$speed_hard = round($item->speed_hard, 2);
$_SESSION['speedhard'] = round($item->speed_hard, 2);
}


if($item->speed_severe == '') {
$speed_severe = 0;
} else {
$speed_severe = round($item->speed_severe, 2);
$_SESSION['speedsevere'] = round($item->speed_severe, 2);
}

    $crnt_date = explode("-",$item->date);
	$req_date = $crnt_date[1]."/".$crnt_date[2];
	$req_date1 = explode(" ",$req_date);
	$reqDate = $req_date1[0];
	$chartmapvlaue .= '{"year":"'. $reqDate.'" , "Accel hard":"'.$accel_hard.'" , "Accel Severe":"'.$accel_severe.'", "Decel Hard":"'.$decel_hard.'", "Decel severe":"'.$decel_severe.'", "Turns hard":"'.$turns_hard.'", "Turns severe":"'.$turns_severe.'", "Speed hard":"'.$speed_hard.'", "Speed severe":"'.$speed_severe.'"},';
	

   
   }
   
   

}


}





}

$chartmapvlaue =  substr($chartmapvlaue,0,-1);


//echo "<pre>"; print_r($chartmapvlaue); echo "</pre>";



?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>amCharts examples</title>
       <link rel="stylesheet" href="style.css" type="text/css">
      <script src="modules/amcharts/amcharts.js" type="text/javascript"></script>
        <script src="modules/amcharts/serial.js" type="text/javascript"></script>
		
        
        <script type="text/javascript">
            var chart;
            
            var chartData = [ <?php echo $chartmapvlaue; ?>];
            
            
            AmCharts.ready(function () {
                // SERIAL CHART
                chart = new AmCharts.AmSerialChart();
                chart.dataProvider = chartData;
				  chart.pathToImages = "<?php  echo JURI::root(); ?>/images/";
                chart.categoryField = "year";
                chart.startDuration = 0.0;
                chart.balloon.color = "#000000";
				
			
					
            
                // AXES
                // category
                var categoryAxis = chart.categoryAxis;
                categoryAxis.fillAlpha = 1;
                categoryAxis.fillColor = "#FAFAFA";
                categoryAxis.gridAlpha = 0;
                categoryAxis.axisAlpha = 0;

               // categoryAxis.gridPosition = "start";
               // categoryAxis.position = "top";
            
                // value
                var valueAxis = new AmCharts.ValueAxis();
                valueAxis.title = "Event counter / 100 Miles";
                valueAxis.dashLength = 5;
                valueAxis.axisAlpha = 0;
                valueAxis.minimum = 1;
                /*valueAxis.maximum =100;*/
                valueAxis.integersOnly = true;
                valueAxis.gridCount = 10;
                /*valueAxis.reversed = true;*/ // this line makes the value axis reversed
				valueAxis.reversed = false;
                chart.addValueAxis(valueAxis);
            
                // GRAPHS
                // Italy graph						            		
                var graph = new AmCharts.AmGraph();
			    graph.type = "smoothedLine"; // this line makes the graph smoothed line.
                graph.title = "Accel hard";
                graph.valueField = "Accel hard";
               // graph.hidden = true; // this line makes the graph initially hidden           
                graph.balloonText = "Accel hard : [[value]]";
               // graph.lineAlpha = 1;
             //   graph.bullet = "round";
                chart.addGraph(graph);
            
                // Germany graph
                var graph = new AmCharts.AmGraph();
				                graph.type = "smoothedLine"; // this line makes the graph smoothed line.
                graph.title = "Accel Severe";
                graph.valueField = "Accel Severe";
                graph.balloonText = "Accel Severe : [[value]]";
              //  graph.bullet = "round";
                chart.addGraph(graph);
            
                // United Kingdom graph
                var graph = new AmCharts.AmGraph();
				                graph.type = "smoothedLine"; // this line makes the graph smoothed line.
                graph.title = "Decel Hard";
                graph.valueField = "Decel Hard";
                graph.balloonText = "Decel Hard : [[value]]";
              //  graph.bullet = "round";
                chart.addGraph(graph);
				
				
				                // United Kingdom graph
                var graph = new AmCharts.AmGraph();
				                graph.type = "smoothedLine"; // this line makes the graph smoothed line.
                graph.title = "Decel severe";
                graph.valueField = "Decel severe";
                graph.balloonText = "Decel severe : [[value]]";
               // graph.bullet = "round";
                chart.addGraph(graph);
                
				                // United Kingdom graph
                var graph = new AmCharts.AmGraph();
				                graph.type = "smoothedLine"; // this line makes the graph smoothed line.
                graph.title = "Turns hard";
                graph.valueField = "Turns hard";
                graph.balloonText = "Turns hard : [[value]]";
               // graph.bullet = "round";
                chart.addGraph(graph);
                
					                // United Kingdom graph
                var graph = new AmCharts.AmGraph();
				                graph.type = "smoothedLine"; // this line makes the graph smoothed line.
                graph.title = "Speed hard";
                graph.valueField = "Speed hard";
                graph.balloonText = "Speed hard : [[value]]";
               // graph.bullet = "round";
                chart.addGraph(graph);
				
							                // United Kingdom graph
                var graph = new AmCharts.AmGraph();
				                graph.type = "smoothedLine"; // this line makes the graph smoothed line.
                graph.title = "Speed severe";
                graph.valueField = "Speed severe";
                graph.balloonText = "Speed severe : [[value]]";
                //graph.bullet = "round";
                chart.addGraph(graph);
				
				
				
                // CURSOR
                var chartCursor = new AmCharts.ChartCursor();
                chartCursor.cursorPosition = "mouse";
                chartCursor.zoomable = false;
                chartCursor.cursorAlpha = 0;
                chart.addChartCursor(chartCursor);                
            
			
					
				  var chartScrollbar = new AmCharts.ChartScrollbar();
                chart.addChartScrollbar(chartScrollbar);
           
		   
		   
                // LEGEND
                var legend = new AmCharts.AmLegend();
                legend.useGraphSettings = true;
                chart.addLegend(legend);
            
                // WRITE
                chart.write("chartdiv");
            });
        </script>
    </head>
    
    <body>
        <div id="chartdiv" style="width: 100%; height: 600px;"></div>
    </body>

</html>

<?php /*foreach($this->items as $i => $item): ?>
	<tr class="row<?php echo $i % 2; ?>">
        <td>
            <?php echo $item->driver_name; ?>
        </td>
        <td>
            <?php echo $item->company_name; ?>
        </td>
        <td>
            <?php echo $item->group_name; ?>
        </td>
		<td>
			<?php if ($item->miles == 0) {echo 0;}
				  else {echo round(($item->accel_hard)/($item->miles) * 100, 1);} ?>
		</td>
		<td>
			<?php if ($item->miles == 0) {echo 0;}
				  else {echo round(($item->accel_severe)/($item->miles) * 100, 2);} ?>
		</td>
		<td>
			<?php if ($item->miles == 0) {echo 0;}
				  else {echo round(($item->decel_hard)/($item->miles) * 100, 1);} ?>
		</td>
		<td>
			<?php if ($item->miles == 0) {echo 0;}
				  else {echo round(($item->decel_severe)/($item->miles) * 100, 1);} ?>
		</td>
		<td>
			<?php if ($item->miles == 0) {echo 0;}
				  else {echo round(($item->turns_hard)/($item->miles) * 100, 1);} ?>
		</td>
		<td>
			<?php if ($item->miles == 0) {echo 0;}
				  else {echo round(($item->turns_severe)/($item->miles) * 100, 1);} ?>
		</td>
		<td>
			<?php if ($item->miles == 0) {echo 0;}
				  else {echo round(($item->speed_hard)/($item->miles) * 100, 1);} ?>
		</td>
		<td>
			<?php if ($item->miles == 0) {echo 0;}
				  else {echo round(($item->speed_severe)/($item->miles) * 100, 1);} ?>
		</td>
        <td>
            <?php echo number_format($item->miles); ?>
        </td>
		<td>
			<?php echo $item->trip_count; ?>
		</td>
	</tr>
<?php endforeach;*/ ?>
