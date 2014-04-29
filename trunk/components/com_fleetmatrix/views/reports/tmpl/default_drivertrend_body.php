<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

    ini_set('display_errors', 1);
    error_reporting(E_ALL);

require_once(JPATH_COMPONENT . DS . 'views' . DS . 'reports' . DS . 'render.php');

$values = array();
$names = array();
$xlabels = array();

$driver = JRequest::getInt('driver', 0);
$trend = JRequest::getCmd('trend', $driver?'all':'overall');

if ($trend == 'hard_turns') {
    $context = $trend;
} else {
    $context = $trend . '_score';
}

$items = $this->items;
if ($trend == 'all' && $this->items) {
    $items = array(
        'overall_score' => array_shift(array_values($this->items))->overall_score,
        'accel_score' => array_shift(array_values($this->items))->accel_score,
        'decel_score' => array_shift(array_values($this->items))->decel_score,
        'hard_turns' => array_shift(array_values($this->items))->hard_turns,
        'speed_score' => array_shift(array_values($this->items))->speed_score,
    );
}

foreach($items as $i => $item) {
    if ($trend=='all') {
        $names[] = ucfirst(str_replace('hard_turns', 'Turns', str_replace('_score','',$i)));
        $a = array();
        foreach($item as $score) {
            $a[] = (is_null($score->value)) ? 0 : $score->value;
            $xl = '';
            if ($i == 'overall') {
                $xl = $score->date;
            }
            if (!$xl && $score->date) {
                $xl = $score->date;
            }
            $xlabels[] = $xl;
        }
        $values[] = $a;
    } else {
        $a = array();
        foreach($item->$context as $score) {
            $a[] = (is_null($score->value)) ? 0 : $score->value;
            $xl = '';
            if ($i == 0) {
                $xl = $score->date;
            }
            if (!$xl && $score->date) {
                $xl = $score->date;
            }
            $xlabels[] = $xl;
        }

        $values[] = $a;
        $names[] = $item->driver_name;
    }
}
if (!sizeof($values)) {
    $values[] = array(0);
}

$GLOBALS['graph_max'] = 10;
$GLOBALS['graph_min'] = 0;
$GLOBALS['graph_tics'] = 1;
if(count($values) > 0  && count($xlabels) > 0){
 $chart123 = renderLineChart($values, $names, "Trends over time", $xlabels);



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


            // since v3, chart can accept data in JSON format
            // if your category axis parses dates, you should only
            // set date format of your data (dataDateFormat property of AmSerialChart)
            var chartData = [
			
			 <?php echo $chart123; ?>
            ];
            var chart;

            AmCharts.ready(function () {
                // SERIAL CHART
                chart = new AmCharts.AmSerialChart();
                chart.dataProvider = chartData;
                chart.pathToImages = "<?php echo JURI::root(); ?>/images/";
                chart.categoryField = "date";
                chart.dataDateFormat = "YYYY-MM-DD";

                var balloon = chart.balloon;
                balloon.cornerRadius = 6;
                balloon.adjustBorderColor = false;
                balloon.horizontalPadding = 10;
                balloon.verticalPadding = 10;

                // AXES
                // category axis
                var categoryAxis = chart.categoryAxis;
                categoryAxis.parseDates = true; // as our data is date-based, we set parseDates to true
                categoryAxis.minPeriod = "DD"; // our data is daily, so we set minPeriod to DD
                categoryAxis.autoGridCount = false;
                categoryAxis.gridCount = 50;
                categoryAxis.gridAlpha = 0;
                categoryAxis.gridColor = "#000000";
                categoryAxis.axisColor = "#555555";
                // we want custom date formatting, so we change it in next line
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

                // as we have data of different units, we create two different value axes
                // Duration value axis
                var durationAxis = new AmCharts.ValueAxis();
                durationAxis.gridAlpha = 0.05;
                durationAxis.axisAlpha = 0;
                // the following line makes this value axis to convert values to duration
                // it tells the axis what duration unit it should use. mm - minute, hh - hour...
                durationAxis.duration = "mm";
          /*      durationAxis.durationUnits = {
                    DD: "d. ",
                    hh: "h ",
                    mm: "min",
                    ss: ""
                };*/
                chart.addValueAxis(durationAxis);


                // GRAPHS
                // duration graph
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

                // CURSOR
                var chartCursor = new AmCharts.ChartCursor();
                chartCursor.zoomable = false;
                chartCursor.categoryBalloonDateFormat = "YYYY MMM DD";
                chartCursor.cursorAlpha = 0;
                chart.addChartCursor(chartCursor);


                var chartScrollbar = new AmCharts.ChartScrollbar();
                chart.addChartScrollbar(chartScrollbar);

                // WRITE
                chart.write("chartdiv");
            });
        </script>

        <?php
		} else {
		
		echo "No Recored found.";
		}
		
		
		 /*?><script type="text/javascript">
            var chart;
            var graph;

            var chartData = [ {"year":"2014-02-13" , "value":5},{"year":"2014-02-14" , "value":5},{"year":"2014-02-15" , "value":5},{"year":"2014-02-16" , "value":5},{"year":"2014-02-17" , "value":5},{"year":"2014-02-18" , "value":5},{"year":"2014-02-19" , "value":5},{"year":"2014-02-20" , "value":5},{"year":"2014-02-21" , "value":5},{"year":"2014-02-22" , "value":5},{"year":"2014-02-23" , "value":5},{"year":"2014-02-24" , "value":5},{"year":"2014-02-25" , "value":5},{"year":"2014-02-26" , "value":5},{"year":"2014-02-27" , "value":5},{"year":"2014-02-28" , "value":5},{"year":"2014-03-01" , "value":5},{"year":"2014-03-02" , "value":5},{"year":"2014-03-03" , "value":5},{"year":"2014-03-04" , "value":5},{"year":"2014-03-05" , "value":5},{"year":"2014-03-06" , "value":5},{"year":"2014-03-07" , "value":5},{"year":"2014-03-08" , "value":5},{"year":"2014-03-09" , "value":5},{"year":"2014-03-10" , "value":5},{"year":"2014-03-11" , "value":5},{"year":"2014-03-12" , "value":5}
            ];


            AmCharts.ready(function () {
                // SERIAL CHART
                chart = new AmCharts.AmSerialChart();
                chart.pathToImages = "modules/amcharts/images/";
                chart.dataProvider = chartData;
                chart.marginLeft = 10;
                    chart.categoryField = "date";
                chart.dataDateFormat = "YYYY-MM-DD";

                // listen for "dataUpdated" event (fired when chart is inited) and call zoomChart method when it happens
               // chart.addListener("dataUpdated", zoomChart);

                // AXES
                // category
                var categoryAxis = chart.categoryAxis;
                categoryAxis.parseDates = true; // as our data is date-based, we set parseDates to true
                categoryAxis.minPeriod = "DD"; // our data is yearly, so we set minPeriod to YYYY
                categoryAxis.dashLength = 3;
                categoryAxis.minorGridEnabled = true;
                categoryAxis.minorGridAlpha = 0.1;
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
				
				
		


                // value
                var valueAxis = new AmCharts.ValueAxis();
                valueAxis.axisAlpha = 0;
                valueAxis.inside = true;
                valueAxis.dashLength = 3;
		
                chart.addValueAxis(valueAxis);

                // GRAPH
                graph = new AmCharts.AmGraph();
                graph.type = "smoothedLine"; // this line makes the graph smoothed line.
                graph.lineColor = "#d1655d";
                graph.negativeLineColor = "#637bb6"; // this line makes the graph to change color when it drops below 0
                graph.bullet = "round";
                graph.bulletSize = 8;
                graph.bulletBorderColor = "#FFFFFF";
                graph.bulletBorderAlpha = 1;
                graph.bulletBorderThickness = 2;
                graph.lineThickness = 2;
                graph.valueField = "value";
                graph.balloonText = "[[category]]<br><b><span style='font-size:14px;'>[[value]]</span></b>";
                chart.addGraph(graph);

                // CURSOR
                var chartCursor = new AmCharts.ChartCursor();
                chartCursor.cursorAlpha = 0;
                chartCursor.cursorPosition = "mouse";
                chartCursor.categoryBalloonDateFormat = "YYYY MMM DD";
                chart.addChartCursor(chartCursor);

                // SCROLLBAR
                var chartScrollbar = new AmCharts.ChartScrollbar();
                chart.addChartScrollbar(chartScrollbar);

                chart.creditsPosition = "bottom-right";

                // WRITE
                chart.write("chartdiv");
            });

            // this method is called when chart is first inited as we listen for "dataUpdated" event
            function zoomChart() {
                // different zoom methods can be used - zoomToIndexes, zoomToDates, zoomToCategoryValues
                chart.zoomToDates(new Date(1950, 0), new Date(1951, 0));
            }
        </script><?php */?>
    </head>

    <body>
        <div id="chartdiv" style="width:100%; height:400px;"></div>
    </body>

</html>
