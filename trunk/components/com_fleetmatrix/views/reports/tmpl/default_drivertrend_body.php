<?php

$items = $this->items;
//print_r($items);


//echo "items: "; echo "<pre>"; print_r($items);
// echo "items: "; echo "<pre>"; print_r(array_shift ( array_values ( $this->items ) ));exit;
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted Access' );

ini_set ( 'display_errors', 1 );
error_reporting ( E_ALL );




require_once (JPATH_COMPONENT . DS . 'views' . DS . 'reports' . DS . 'render.php');




$user =& JFactory::getUser();
$adminid  = $user->get('id');


if (isset($_POST['create_alert']))
 {
	
		
$db = JFactory::getDbo();
$query = $db->getQuery(true);
$query->select('report_time AS report_time');
$query->from($db->quoteName('#__fleet_reports'));
$query->order($db->quoteName('id').' desc');  
$db->setQuery($query);
//echo $query;
$rowalert = $db->loadRow();
$last_alert_time = $rowalert['0'];
$duration='+5 minutes';
$report_time =  date('H:i', strtotime($duration, strtotime($last_alert_time)));

		
		$userid 		= $adminid;
		$trend 			= $_POST['trend'];
		$window 		= $_POST['window'];
		$company 		= $_POST['company'];
		$group 			= $_POST['group'];
		$driver 		= $_POST['driver'];
		$report_name 	= $_POST['report_name'];
		$report_name 	= "Driver Trend";
		$report_data_val 	= $_POST['report_data_val'];
		if(!empty($_POST['companyname'])){
		$companyname =  $_POST['companyname'];
		} else {
			$companyname =  '';
			}
		if(!empty($_POST['groupname'])){
		$groupname =  $_POST['groupname'];
		} else {
			$groupname =  '';
			}
		if(!empty($_POST['drivername'])){
		$drivername =  $_POST['drivername'];
		} else {
			$drivername =  '';
			}		
	
		
		
		//$report_data = $trend." >> ".$window." >> ".$companyname." >> ".$groupname." >> ".$drivername;
		
		//$report_data = "Trend = ".$trend."  "."<br />Time Limit =".$window." >> ".$companyname." >> ".$groupname." >> ".$drivername;
		
		if($trend=="accel"){
			$trenddata = " <br />Trend  = "."Acceleration";
		} else if($trend=="decel"){
			$trenddata = " <br />Trend  = "."Deceleration";
		}
		else if($trend=="hard_turns"){
			$trenddata = " <br />Trend  = "."Hard Turns";
		} else {
			$trenddata = " <br />Trend  = "."All Scores";
			}
		
		if(!empty($window)){
			$window_data = " <br /> Time  = ".$window." Days";
		};
		if(!empty($companyname)){
			$companyname = " <br /> Company name = ".$companyname;
		};
		if(!empty($groupname)){
			$groupname = " <br /> Group name = ".$groupname;
		};
		if(!empty($drivername)){
			$drivername = " <br /> Driver name = ".$drivername;
		};
		if(!empty($report_data_val)){
			 $report_data_val = "'".$report_data_val."'";
		} else {
			$report_data_val = "";
			};
		
		$report_data_stats = $trenddata.$window_data.$companyname.$groupname.$drivername;
		
		$db = JFactory::getDbo();
$query = $db->getQuery(true);
$query->insert('#__fleet_reports');
$query->set("user_id='$userid', 
trend='$trend',
window='$window',
company='$company',
com_group='$group',
driver='$driver',
report_name = '$report_name',
report_time = '$report_time',
report_data_stats = '$report_data_stats'
");
$db->setQuery($query);
$db->query();
//echo $query;




		
/*		mysql_query("INSERT INTO giqwm_fleet_reports 
		SET 
			user_id = $userid,
			trend = '$trend',
			window = $window,
			company = $company,
			com_group = $group,
			driver = $driver,
			report_name = '$report_name',
			report_data_stats = '$report_data_stats'");
		//exit;*/
	
	
}

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

//echo $context;

// echo "after shift statement items: "; echo "<pre>"; print_r($items);

if ($trend == 'all' && $this->items) {
	
	$temp_items = array();
	$item->overall_score;
	foreach ($this->items as $i => $item ) {
		$temp_items['overall_score'][] = $item->overall_score;
		$temp_items['accel_score'][] = $item->accel_score;
		$temp_items['decel_score'][] = $item->decel_score;
		$temp_items['hard_turns'][] = $item->hard_turns;
	}
	// compute average for each category
	$items = array();
	foreach ($temp_items as $i => $scoreType) { // loop thru each category
// 		for ($k=0; $k < sizeof($scoreType); $k++) { // loop thru score array for all dsrivers
// 			$scoreArray = $scoreType[$k];
// 		echo "scoreType: "; echo "<pre>"; print_r($scoreType);
                        for($j=0; $j<sizeof($scoreType[0]); $j++){
                            $sum=0;
                            for($x =0; $x<sizeof($scoreType); $x++){
                                $sum = $sum+ $scoreType[$x][$j]->value;
                            }
                            $date = $scoreType[0][$j]->date;
                            $average = $sum/sizeof($scoreType);
                            $items[$i][$j] =new Score($date, $average);
                            
                        }
	}
	
// 	$items = array (
// 			'overall_score' => array_shift ( array_values ( $this->items ) )->overall_score,
// 			'accel_score' => array_shift ( array_values ( $this->items ) )->accel_score,
// 			'decel_score' => array_shift ( array_values ( $this->items ) )->decel_score,
// 			'hard_turns' => array_shift ( array_values ( $this->items ) )->hard_turns,
// // 			'speed_score' => array_shift ( array_values ( $this->items ) )->speed_score 
// 	);
}

 //echo "after if statement items: "; echo "<pre>"; print_r($items);

foreach ( $items as $i => $item ) {
	//echo 'here to render';
	if ($trend == 'all') {
 		//echo "in trend all case";
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
		//echo 'else case on page';
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
		//echo $a;
		$values [] = $a;
		$names [] = $item->driver_name;
// 		echo "<pre>"; print_r($names);
// 		echo "<pre>"; print_r($values);
// 		echo "<pre>"; print_r($xlabels);
	}
}

//print_r($values);

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
		
		
		
		
 	   <script src="http://canvg.googlecode.com/svn/trunk/canvg.js" type="text/javascript"></script>
	  <script src="http://canvg.googlecode.com/svn/trunk/rgbcolor.js" type="text/javascript"></script>
      <script src="modules/amcharts/amcharts.js" type="text/javascript"></script>
      <script src="modules/amcharts/serial.js" type="text/javascript"></script>
		<script type="text/javascript">
		
		/*
		 * Export.js - AmCharts to PNG
		 * Benjamin Maertz (tetra1337@gmail.com)
		 *
		 * Requires:    rgbcolor.js - http://www.phpied.com/rgb-color-parser-in-javascript/
		 *                 canvg.js - http://code.google.com/p/canvg/
		 *                amcharts.js - http://www.amcharts.com/download
		 */
		
		// Lookup for required libs
		if ( typeof(AmCharts) === 'undefined' || typeof(canvg) === 'undefined' || typeof(RGBColor) === 'undefined' ) {
			throw('Woup smth is wrong you might review that http://www.amcharts.com/forum/viewtopic.php?id=11001');
		}
		
		// Define custom util
		AmCharts.getExport = function(anything) {
			/*
			** PRIVATE FUNCTIONS
			*/
		
		
			// Word around until somebody found out how to cover that
			function removeImages(svg) {
			var startStr    = '<image';
			var stopStr        = '</image>';
			var start        = svg.indexOf(startStr);
			var stop        = svg.indexOf(stopStr);
			var match        = '';
		
			// Recursion
			if ( start != -1 && stop != -1 ) {
				svg = removeImages(svg.slice(0,start) + svg.slice(stop + stopStr.length,svg.length));
			}
			return svg;
			};
		
			// Senseless function to handle any input
			function gatherAnything(anything,inside) {
			switch(String(anything)) {
				case '[object String]':
				if ( document.getElementById(anything) ) {
					anything = inside?document.getElementById(anything):new Array(document.getElementById(anything));
				}
				break;
				case '[object Array]':
				for ( var i=0;i<anything.length;i++) {
					anything[i] = gatherAnything(anything[i],true);
				}
				break;
		
				case '[object XULElement]':
				anything = inside?anything:new Array(anything);
				break;
		
				case '[object HTMLDivElement]':
				anything = inside?anything:new Array(anything);
				break;
		
				default:
				anything = new Array();
				for ( var i=0;i<AmCharts.charts.length;i++) {
					anything.push(AmCharts.charts[i].div);
				}
				break;
			}
			return anything;
			}
		
			/*
			** varibales VARIABLES!!!
			*/
			var chartContainer    = gatherAnything(anything);
			var chartImages        = [];
			var canvgOptions    = {
			ignoreAnimation    :    true,
			ignoreMouse        :    true,
			ignoreClear        :    true,
			ignoreDimensions:    true,
			offsetX            :    0,
			offsetY            :    0
			};
		
			/*
			** Loop, generate, offer
			*/
		
			// Loop through given container
			for(var i1=0;i1<chartContainer.length;i1++) {
			var canvas        = document.createElement('canvas');
			var context        = canvas.getContext('2d');
			var svgs        = chartContainer[i1].getElementsByTagName('svg');
			var image        = new Image();
			var heightCounter = 0;
		
			// Set dimensions, background color to the canvas
			canvas.width    = chartContainer[i1].offsetWidth;
			canvas.height    = chartContainer[i1].offsetHeight;
			context.fillStyle = '#FFFFFF';
			context.fillRect(0,0,canvas.width,canvas.height);
		
			// Loop through all svgs within the container
			for(var i2=0;i2<svgs.length;i2++) {
		
				var wrapper        = svgs[i2].parentNode;
				var clone        = svgs[i2].cloneNode(true);
				var cloneDiv    = document.createElement('div');
				var offsets        = {
				x:    wrapper.style.left.slice(0,-2) || 0,
				y:    wrapper.style.top.slice(0,-2) || 0,
				height:    wrapper.offsetHeight,
				width:    wrapper.offsetWidth
				};
		
				// Remove the style and append the clone to the div to receive the full SVG data
				clone.setAttribute('style','');
				cloneDiv.appendChild(clone);
				innerHTML = removeImages(cloneDiv.innerHTML); // without imagery
		
				// Apply parent offset
				if ( offsets.y == 0 ) {
				offsets.y = heightCounter;
				heightCounter += offsets.height;
				}
		
				canvgOptions.offsetX = offsets.x;
				canvgOptions.offsetY = offsets.y;
		
				// Some magic beyond the scenes...
				canvg(canvas,innerHTML,canvgOptions);
			}
				//console.log(canvas);return false;
			// Get the final data URL and throw that image to the array
		
			image.src = canvas.toDataURL();
			chartImages.push(image);
			}
			// Return DAT IMAGESS!!!!
			return chartImages
		}
		</script>
        <?php if($_SESSION['title'] == "all"){?>
		
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
                valueAxis.minimum = 0;
                <!--valueAxis.maximum =10;-->
                valueAxis.integersOnly = true;
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
		<?php }else{  ?>
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
                valueAxis.minimum = 0;
                /*valueAxis.maximum =10;*/
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
        <script type="text/javascript">
			function exportThis(opt) {
			var items = AmCharts.getExport('chartdiv');
			document.getElementById('button').innerHTML = 'Export';
			
			document.getElementById('not_button').innerHTML = '';
			for ( index in items ) {
			document.getElementById('not_button').appendChild(items[index]);
			
			var someimage = document.getElementById('not_button').firstChild.getAttribute("src");
			//alert(someimage); return false;

			
			var image = new PNG(someimage);
			image.width; // Image width in pixels
			image.height; // Image height in pixels
			var line;
			while(line = image.readLine()){
			for(var x = 0;x < line.length;x++){
			var px = line[x]; // Pixel RGB color as a single numeric value
			// white pixel == 0xFFFFFF
			}
			}
			//alert(document.getElementById('not_button').innerHTML);   
			//var someimage = document.getElementById('not_button').firstChild.getAttribute("src");
			//alert(someimage); 

			//alert(document.getElementById('not_button').appendChild(items[index]));
			}
		}
		
        </script>
        <?php
		} else {
		
		echo "No Recored found.";
		}
		//print_r($_SESSION);
		//print_r($_SESSION['report_data']);
		
	?>
    <body>
	<?php 
	//print_r($_SESSION);
	//$myreportdata = $_SESSION["report_data"];
	//echo $_SESSION['report_data']?>
        <div id="chartdiv_hide" style="width:100%; height:100px;text-align:center"> 
            <div style="height:100px"></div>
            <h1>UNDER CONSTRUCTION</h1> 
        </div><div id="not_button"  style="width:100%; height:400px;"></div>
         <!-- Contact Form --> 


<div id="driveralert" style=" display:none">

	<div class="popup_top"></div>
    <div class="popup_cntr" style="padding:10px 0;">
	
	<div style="width:325px; margin:0 auto;">
	<form method="post" action="" enctype="multipart/form-data" name="myform1" id="driveralert">
 	<?php /*?><input type="hidden" name="user_id" value="<?php echo $_SESSION['id']?>"><?php */
	//echo $myreportdata;
	?>
    <input type="hidden" name="report_data_val" value='<?php echo $myreportdata?>'>
    <input type="hidden" name="report_name" value="<?php echo $report_name?>">
    
    <div id="ContactFormErrorDesc" class="red"></div>	
	<div class="tha_formfieldbox">
 	<input type="hidden" name="trend" value="<?php echo $_REQUEST['trend']?>">
    <div class="formtxt"><b>Trend :</b>  <?php if(!empty($_REQUEST['trend'])) { echo $_REQUEST['trend'];} else { echo "Not Set In Alert";}?></div>
	</div>
    <br />
    <div class="tha_formfieldbox">
    <input type="hidden" name="window" value="<?php echo $_REQUEST['window']?>">
 	<div class="formtxt"><b>Time :</b> <?php if($_REQUEST['window']>0) { echo $_REQUEST['window']." Days";} else { echo "Not Set In Alert";}?></div>
	</div>
    <br />
   
    <div class="tha_formfieldbox">
    <input type="hidden" name="company" value="<?php echo $_REQUEST['company']?>">
 	<div class="formtxt"><b>Company :</b> <?php if($_REQUEST['company']>0) { 
	
					$db = JFactory::getDBO();
					$query = $db->getQuery(true);
$query->select('name');
$query->from($db->quoteName('#__fleet_entity'));
$query->where($db->quoteName('id')." = ".$db->quote($_REQUEST['company']));
$db->setQuery($query);
$cmpname = $db->loadResult();
    echo $cmpname; ?>
    <input type="hidden" name="companyname" value="<?php echo $cmpname?>">
	
	<?php } else { echo "Not Set In Alert";}?>
    
    </div>
	</div>
    <br /> 
    <div class="tha_formfieldbox">
     <input type="hidden" name="group" value="<?php echo $_REQUEST['group']?>">
 	<div class="formtxt"><b>Group :</b> <?php if($_REQUEST['group']>0) { 
					$db = JFactory::getDBO();
					$query = $db->getQuery(true);
	$query->select('name');
	$query->from($db->quoteName('#__fleet_entity'));
	$query->where($db->quoteName('id')." = ".$db->quote($_REQUEST['group']));
	$db->setQuery($query);
	$grpname = $db->loadResult();
	echo $grpname; ?>
<input type="hidden" name="groupname" value="<?php echo $grpname?>">
	<?php }
	 else { echo "Not Set In Alert";}?>
     
     </div>
	</div>
    <br />
    <div class="tha_formfieldbox">
     <input type="hidden" name="driver" value="<?php echo $_REQUEST['driver']?>">
 	<div class="formtxt"><b>Driver :</b> <?php if($_REQUEST['driver']>0) { 
	$db = JFactory::getDBO();
	$query = $db->getQuery(true);
	$query->select('name');
$query->from($db->quoteName('#__fleet_driver'));
$query->where($db->quoteName('id')." = ".$db->quote($_REQUEST['driver']));
$db->setQuery($query);
$drivername = $db->loadResult();
	echo $drivername; ?>
	  <input type="hidden" name="drivername" value="<?php echo $drivername?>">
	<?php }
	else { echo "Not Set In Alert";}?>
    
   
    </div>
	</div>

    <div class="tha_formfieldbox" style="display:none">
    <input type="hidden" name="option" value="<?php echo $_REQUEST['option']?>">
 	<div class="formtxt"><b>Option :</b><?php if($_REQUEST['option']>0) { echo $_REQUEST['option'];} else { echo "Not Set In Alert";}?></div>
	</div>
    <br />
    
   <div class="tha_formfieldbox">
 	<div class="formtxt">&nbsp;</div>
	<div class="right" style="margin-right:25px;">
	
	<input type="submit" name="create_alert" id="create_alert" value="Create Alert" class="tha_blueback2">		
	
	</div>
 </div>
 
 	<div class="clear"></div>

	</form>
	</div>
	
	
		</div>	 
 <div class="popup_bottom"></div> 
  </div>  
<!--End Contact Form Close --> 
</body>
</html>
