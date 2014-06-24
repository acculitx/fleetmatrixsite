<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

    ini_set('display_errors', 1);
    error_reporting(E_ALL);
	
	

require_once(JPATH_COMPONENT . DS . 'views' . DS . 'reports' . DS . 'render.php');

$values = array();
$names = array();
$xlabels = array();

foreach($this->items as $i => $item) {
    $a = array();
    foreach($item->mpg as $score) {
        $a[] = $score->value;
        if ($i == 0) {
		//echo $score->date;
            $xlabels[] = $score->date;
        }
    }

    $values[] = $a;
    $names[] = $item->vehicle_name;
}
if (!sizeof($values)) {
    $values[] = array(0);
}

if(count($values) > 0  && count($xlabels) > 0){

  $chart123 = renderLineChart($values, $names, "Trends over time", $xlabels);
  
  //echo "<pre>"; print_r($chart123);


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>amCharts examples</title>
<?php /*?>        <link rel="stylesheet" href="style.css" type="text/css">
      <script src="modules/amcharts/amcharts.js" type="text/javascript"></script>
        <script src="modules/amcharts/serial.js" type="text/javascript"></script>
		
		<script type="text/javascript">


            var chartData = [ <?php echo $chart123; ?>
            ];
			
		
            var chart;

            AmCharts.ready(function () {
                 SERIAL CHART
                chart = new AmCharts.AmSerialChart();
                chart.dataProvider = chartData;
                chart.pathToImages = "<?php  echo JURI::root(); ?>/images/";
                chart.categoryField = "date";
                chart.dataDateFormat = "YYYY-MM-DD";

                var balloon = chart.balloon;
                balloon.cornerRadius = 6;
                balloon.adjustBorderColor = false;
                balloon.horizontalPadding = 10;
                balloon.verticalPadding = 10;

                 AXES
                 category axis
                var categoryAxis = chart.categoryAxis;
                categoryAxis.parseDates = true; // as our data is date-based, we set parseDates to true
                categoryAxis.minPeriod = "DD"; // our data is daily, so we set minPeriod to DD
                categoryAxis.autoGridCount = false;
                categoryAxis.gridCount = 50;
                categoryAxis.gridAlpha = 0;
                categoryAxis.gridColor = "#000000";
                categoryAxis.axisColor = "#555555";
                 we want custom date formatting, so we change it in next line
                categoryAxis.dateFormats = [{
                    period: 'DD',
                    format: 'DD'
                }, {
                    period: 'WW',
                    format: 'MMM DD'
                }, {
                    period: 'MM',
                    format: 'MMM'
                }, {
                    period: 'YYYY',
                    format: 'YYYY'
                }];

                 as we have data of different units, we create two different value axes
                 Duration value axis
                var durationAxis = new AmCharts.ValueAxis();
                durationAxis.gridAlpha = 0.05;
                durationAxis.axisAlpha = 0;
                 the following line makes this value axis to convert values to duration
                 it tells the axis what duration unit it should use. mm - minute, hh - hour...
                durationAxis.duration = "mm";
                durationAxis.durationUnits = {
                    DD: "d. ",
                    hh: "h ",
                    mm: "min",
                    ss: ""
                };
                chart.addValueAxis(durationAxis);


                 GRAPHS
                 duration graph
                var durationGraph = new AmCharts.AmGraph();
                durationGraph.title = "duration";
                durationGraph.valueField = "duration";
                durationGraph.type = "line";
                durationGraph.valueAxis = durationAxis; // indicate which axis should be used
                durationGraph.lineColorField = "lineColor";
                durationGraph.fillColorsField = "lineColor";
                durationGraph.fillAlphas = 0.3;
                durationGraph.balloonText = "[[value]]";
                durationGraph.lineThickness = 1;
                durationGraph.legendValueText = "[[value]]";
                durationGraph.bullet = "square";
                durationGraph.bulletBorderThickness = 0;
                durationGraph.bulletBorderAlpha = 0;
                chart.addGraph(durationGraph);

                 CURSOR
                var chartCursor = new AmCharts.ChartCursor();
                chartCursor.zoomable = false;
                chartCursor.categoryBalloonDateFormat = "YYYY MMM DD";
                chartCursor.cursorAlpha = 0;
                chart.addChartCursor(chartCursor);


                var chartScrollbar = new AmCharts.ChartScrollbar();
                chart.addChartScrollbar(chartScrollbar);

                 WRITE
                chart.write("chartdiv");
            });
        </script>
<?php */?>


   <link rel="stylesheet" href="style.css" type="text/css">
      <script src="modules/amcharts/amcharts.js" type="text/javascript"></script>
        <script src="modules/amcharts/serial.js" type="text/javascript"></script>
		
        
        <script type="text/javascript">
            var chart;
            
            var chartData = [ <?php echo $chart123; ?>];
            
            
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
                valueAxis.title = "Vehicle Trend";
                valueAxis.dashLength = 5;
                valueAxis.axisAlpha = 0;
                valueAxis.minimum = 1;
                valueAxis.maximum =100;
                valueAxis.integersOnly = true;
                valueAxis.gridCount = 10;
                valueAxis.reversed = false; // this line makes the value axis reversed
                chart.addValueAxis(valueAxis);
            
                // GRAPHS
                // Italy graph						            		
                var graph = new AmCharts.AmGraph();
			    graph.type = "smoothedLine"; // this line makes the graph smoothed line.
                graph.title = "duration";
                graph.valueField = "duration";
               // graph.hidden = true; // this line makes the graph initially hidden           
                graph.balloonText = "duration [[category]]: [[value]]";
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
		
        <?php
		}
		 else {
		
		echo "No Recored found.";
		}
?>
    </head>

    <body>
	
	
        <div id="chartdiv" style="width:100%; height:400px;"></div>
    </body>

</html>
