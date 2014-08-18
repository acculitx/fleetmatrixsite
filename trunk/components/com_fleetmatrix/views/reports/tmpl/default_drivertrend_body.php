<?php

// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted Access' );

ini_set ( 'display_errors', 1 );
error_reporting ( E_ALL );

require_once (JPATH_COMPONENT . DS . 'views' . DS . 'reports' . DS . 'render.php');

$values = array ();
$names = array ();
$xlabels = array ();

$driver = JRequest::getInt ( 'driver', 0 );
$trend = JRequest::getCmd ( 'trend', $driver ? 'all' : 'overall' );


if ($trend == 'hard_turns') {
	$context = $trend;
} else {
	$context = $trend . '_score';
}

$items = $this->items;
// echo "items: "; echo "<pre>"; print_r(array_shift ( array_values ( $this->items ) ));
// echo "after shift statement items: "; echo "<pre>"; print_r($items);

if ($trend == 'all' && $this->items) {
	$temp_items = array();
	foreach ($this->items as $i => $item ) {
		$temp_items['overall_score'][] = $item->overall_score;
		$temp_items['accel_score'][] = $item->accel_score;
		$temp_items['decel_score'][] = $item->decel_score;
		$temp_items['hard_turns'][] = $item->hard_turns;
	}
	// compute average for each category
	$items = array();
	foreach ($temp_items as $i => $scoreType) { // loop thru each category
// 		for ($k=0; $k < sizeof($scoreType); $k++) { // loop thru score array for all drivers
// 			$scoreArray = $scoreType[$k];
// 		echo "scoreType: "; echo "<pre>"; print_r($scoreType);
			foreach ($scoreType[0] as $date => $score) { // for each date
				$sum = 0;
				for ($j=0; $j < sizeof($scoreType) ; $j++) { // for each driver
					$sum = $sum + $scoreType[$j][$date]->value;
				}
				$average = $sum/sizeof($scoreType); // divided by driver size
				$items[$i][] = new Score($date, $average);
			}
// 		}
	}
	
// 	$items = array (
// 			'overall_score' => array_shift ( array_values ( $this->items ) )->overall_score,
// 			'accel_score' => array_shift ( array_values ( $this->items ) )->accel_score,
// 			'decel_score' => array_shift ( array_values ( $this->items ) )->decel_score,
// 			'hard_turns' => array_shift ( array_values ( $this->items ) )->hard_turns,
// // 			'speed_score' => array_shift ( array_values ( $this->items ) )->speed_score 
// 	);
}

// echo "after if statement items: "; echo "<pre>"; print_r($items);

foreach ( $items as $i => $item ) {
	if ($trend == 'all') {
// 		echo "in trend all case";
		$names [] = ucfirst ( str_replace ( 'hard_turns', 'Turns', str_replace ( '_score', '', $i ) ) );
		$a = array ();
		foreach ( $item as $score ) {
			$a [] = (is_null ( $score->value )) ? 0 : $score->value;
			$xl = '';
			if ($i == 'overall') {
				$xl = $score->date;
			}
			if (! $xl && $score->date) {
				$xl = $score->date;
			}
			$xlabels [] = $xl;
		}
		$values [] = $a;
	} else {
		$a = array ();
		foreach ( $item->$context as $score ) {
			$a [] = (is_null ( $score->value )) ? 0 : $score->value;
			$xl = '';
			if ($i == 0) {
				$xl = $score->date;
			}
			if (! $xl && $score->date) {
				$xl = $score->date;
			}
			$xlabels [] = $xl;
		}
		
		$values [] = $a;
		$names [] = $item->driver_name;
// 		echo "<pre>"; print_r($names);
// 		echo "<pre>"; print_r($values);
// 		echo "<pre>"; print_r($xlabels);
	}
}


if (! sizeof ( $values )) {
	$values [] = array (
			0 
	);
}
// echo "<pre>"; print_r($trend);
$title = $trend;
$GLOBALS ['graph_max'] = 10;
$GLOBALS ['graph_min'] = 0;
$GLOBALS ['graph_tics'] = 1;
if (count ( $values ) > 0 && count ( $xlabels ) > 0) {
// 			echo "names: "; echo "<pre>"; print_r($names);
// 			echo "values: "; print_r($values);
// 			echo "xlabels: "; print_r($xlabels);
// 			echo "trend: "; print_r($trend);
	$chart123 = renderLineChart ( $values, $names, $title, $xlabels );
	
// 	echo "Chart123 is following: ";
// 	echo "<pre>"; print_r($chart123); echo "</pre>";
	
	?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Driver Trend</title>
		
		
		
		
<?php 
/*
	       * ?> <link rel="stylesheet" href="style.css" type="text/css"> <script src="modules/amcharts/amcharts.js" type="text/javascript"></script> <script src="modules/amcharts/serial.js" type="text/javascript"></script> <script type="text/javascript"> var chartData = [ <?php echo $chart123; ?> ]; var chart; AmCharts.ready(function () { // SERIAL CHART chart = new AmCharts.AmSerialChart(); chart.dataProvider = chartData; chart.pathToImages = "<?php echo JURI::root(); ?>/images/"; chart.categoryField = "date"; chart.dataDateFormat = "YYYY-MM-DD"; var balloon = chart.balloon; balloon.cornerRadius = 6; balloon.adjustBorderColor = false; balloon.horizontalPadding = 10; balloon.verticalPadding = 10; // AXES // category axis var categoryAxis = chart.categoryAxis; categoryAxis.parseDates = true; // as our data is date-based, we set parseDates to true categoryAxis.minPeriod = "DD"; // our data is daily, so we set minPeriod to DD categoryAxis.autoGridCount = false; categoryAxis.gridCount = 50; categoryAxis.gridAlpha = 0; categoryAxis.gridColor = "#000000"; categoryAxis.axisColor = "#555555"; // we want custom date formatting, so we change it in next line categoryAxis.dateFormats = [{ period: 'DD', format: 'DD' }, { period: 'WW', format: 'MMM DD' }, { period: 'MM', format: 'MMM' }, { period: 'YYYY', format: 'YYYY' }]; // as we have data of different units, we create two different value axes // Duration value axis var durationAxis = new AmCharts.ValueAxis(); durationAxis.gridAlpha = 0.05; durationAxis.axisAlpha = 0; // the following line makes this value axis to convert values to duration // it tells the axis what duration unit it should use. mm - minute, hh - hour... durationAxis.duration = "mm"; chart.addValueAxis(durationAxis); // GRAPHS // duration graph var durationGraph = new AmCharts.AmGraph(); durationGraph.title = "duration"; durationGraph.valueField = "duration"; durationGraph.type = "line"; durationGraph.valueAxis = durationAxis; // indicate which axis should be used durationGraph.lineColorField = "lineColor"; durationGraph.fillColorsField = "lineColor"; durationGraph.fillAlphas = 0.3; durationGraph.balloonText = "[[value]]"; durationGraph.lineThickness = 1; durationGraph.legendValueText = "[[value]]"; durationGraph.bullet = "square"; durationGraph.bulletBorderThickness = 0; durationGraph.bulletBorderAlpha = 0; chart.addGraph(durationGraph); // CURSOR var chartCursor = new AmCharts.ChartCursor(); chartCursor.zoomable = false; chartCursor.categoryBalloonDateFormat = "YYYY MMM DD"; chartCursor.cursorAlpha = 0; chart.addChartCursor(chartCursor); var chartScrollbar = new AmCharts.ChartScrollbar(); chart.addChartScrollbar(chartScrollbar); // WRITE chart.write("chartdiv"); }); </script><?php
	       */
	?>
		
		
		       <link rel="stylesheet" href="style.css" type="text/css">
<script src="modules/amcharts/amcharts.js" type="text/javascript"></script>
<script src="modules/amcharts/serial.js" type="text/javascript"></script>
		
        <?php if($_SESSION['title'] == "all"){ ?>
		<script type="text/javascript">
            var chart;
            
            var chartData = [ <?php echo $chart123; ?>];
            
            
            AmCharts.ready(function () {
                // SERIAL CHART
                chart = new AmCharts.AmSerialChart();
                chart.dataProvider = chartData;
				  chart.pathToImages = "<?php  echo JURI::root(); ?>/images/";
                chart.categoryField = "year";
                chart.startDuration = 0.0;
                chart.balloon.color = "#000000";
//                 chart.height = "80%";
			
					
            
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
                valueAxis.title = "Driver Trend";
                valueAxis.dashLength = 5;
                valueAxis.axisAlpha = 0;
                valueAxis.minimum = -0.5;
                valueAxis.maximum = 10.5;
//                 valueAxis.offset = 20;
                valueAxis.integersOnly = true;
//                 valueAxis.autoGridCount = false;
                valueAxis.gridCount = 10;
                /*valueAxis.reversed = true;*/ // this line makes the value axis reversed
				valueAxis.reversed = false;
                chart.addValueAxis(valueAxis);
            
                // GRAPHS
                // Italy graph						            		
  
                // Germany graph
                var graph = new AmCharts.AmGraph();
				                graph.type = "smoothedLine"; // this line makes the graph smoothed line.
                graph.title = "Acceleration";
                graph.valueField = "accel";
                graph.balloonText = "accel : [[value]]";
              //  graph.bullet = "round";
                chart.addGraph(graph);
            
                // United Kingdom graph
				
				
				                // United Kingdom graph
                var graph = new AmCharts.AmGraph();
				                graph.type = "smoothedLine"; // this line makes the graph smoothed line.
                graph.title = "Deceleration";
                graph.valueField = "decel";
                graph.balloonText = "decel : [[value]]";
               // graph.bullet = "round";
                chart.addGraph(graph);
                

                
					                // United Kingdom graph
                var graph = new AmCharts.AmGraph();
				                graph.type = "smoothedLine"; // this line makes the graph smoothed line.
                graph.title = "Hard Turns";
                graph.valueField = "hard_turns";
                graph.balloonText = "hard_turns : [[value]]";
               // graph.bullet = "round";
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
		<?php }else{ ?>
        <script type="text/javascript">
            var chart;
            
            var chartData = [ <?php echo $chart123; ?>];
            
           // alert(chartData);
            AmCharts.ready(function () {
                // SERIAL CHART
                chart = new AmCharts.AmSerialChart();
                chart.dataProvider = chartData;
				  chart.pathToImages = "<?php  echo JURI::root(); ?>/images/";
                chart.categoryField = "date";
                chart.startDuration = 0.5;
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
                valueAxis.title = "Driver Trend";
                valueAxis.dashLength = 5;
                valueAxis.axisAlpha = 0;
                valueAxis.minimum = 1;
                valueAxis.maximum =10;
                valueAxis.integersOnly = true;
                valueAxis.gridCount = 10;
                /*valueAxis.reversed = true;*/ // this line makes the value axis reversed
				valueAxis.reversed = false;
                chart.addValueAxis(valueAxis);
            
                // GRAPHS
                // Italy graph						            		
                var graph = new AmCharts.AmGraph();
			    graph.type = "smoothedLine"; // this line makes the graph smoothed line.
                graph.title = "<?php if($_SESSION['title'] == "accel"){echo "Acceleration";} else if($_SESSION['title'] == "decel"){echo "Deceleration";} else if($_SESSION['title'] == "hard_turns"){echo "Hard Turns";} else if($_SESSION['title'] == "all"){echo "All Scores";}   ?>";
                graph.valueField = "duration";
               // graph.hidden = true; // this line makes the graph initially hidden           
                graph.balloonText = "[[value]]";
               // graph.lineAlpha = 1;
             //   graph.bullet = "round";
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
		<?php } ?>
        <?php
} else {
	
	echo "No Recored found.";
}

?>
    


<body>
	<div id="chartdiv" style="width: 100%; height: 400px;"></div>
</body>

</html>
