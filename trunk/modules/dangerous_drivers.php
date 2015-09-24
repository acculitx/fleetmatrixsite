<?php
defined('_JEXEC') or die;

    ini_set('display_errors', 1);
    error_reporting(E_ALL);



function sort_items_by_totalScore($a, $b) {
	if ($a->total_score > $b->total_score) 
        return -1;
    else if ($b->total_score > $a->total_score) 
        return 1;
    else
    	return 0;
}

function convertToJSON($items) {
	global $chartmapvlaue;
	
	for($va = 0; $va < sizeof ( $items ); $va ++) {
		$item = $items[$va];
		$chartmapvlaue .= '{"driver_name":"' . $item->driver_name . 
				'" , "total_score":' . $item->total_score . 
				',"aggressive_score":"' . $item->aggressive_score . 
				'","distraction_score":"' . $item->distraction_score . '"},';
	}
	return $chartmapvlaue;
}

require(JPATH_BASE.DS."components".DS."com_fleetmatrix".DS."models".DS."calculator.php");
$calculator = new ScoreCalculator();

        $user =& JFactory::getUser();
        $db =& JFactory::getDBo();
        if ($user) {
            $query = $db->getQuery(true)
                ->select('entity_type, entity_id')
                ->from('#__fleet_user')
                ->where('id = "'.$user->id.'"')
                ;
            $db->setQuery($query);
            $row = $db->loadObject();
            if ($row) {
            $query = $db->getQuery(true)
                ->select('id')
                ->from("#__fleet_entity")
                ->where('parent_entity_id = '.$row->entity_id)
                ;
            $db->setQuery($query);
            $results = $db->loadResultArray();
            switch ($row->entity_type) {
                case 1: // reseller
                    foreach($results as $id) {
                        $GLOBALS['user_companies'][] = $id;
                        $query = $db->getQuery(true)
                            ->select('id')
                            ->from("#__fleet_entity")
                            ->where('parent_entity_id = '.$id)
                            ;
                        $db->setQuery($query);
                        $groups = $db->loadResultArray();
                        foreach($groups as $gid) {
                            $GLOBALS['user_groups'][] = $gid;
                        }
                    }
                    break;
                case 2: // company
                    $GLOBALS['user_companies'][] = $row->entity_id;
                    foreach($results as $id) {
                        $GLOBALS['user_groups'][] = $id;
                    }
                    break;
                case 3: // group
                    $GLOBALS['user_groups'][] = $row->entity_id;
                    break;
                default: // ?? shouldn't happen
                    break;
            }
            }
        }

$db =& JFactory::getDBO();
$query = $db->getQuery(true);

$clause = "distinct b.name as driver_name, ".
        "a.name as group_name, s.id as vehicle_id, ".
        "a.id as group_id, a.parent_entity_id as company_id, ".
        "d.driver_id, ".
        "COUNT(h.subscriber_id) as trip_count, ".
        "'N/A' as mpg, ".
        "'N/A' as overall, ".
        "'N/A' as accel_score, ".
        "'N/A' as decel_score, ".
        "'N/A' as hard_turns, ".
        "'N/A' as speed_score, ".
        "'N/A' as total_score, ".
        "'N/A' as aggressive_score, ".
        "'N/A' as distraction_score, ".
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
    ->where('h.odo_end > h.odo_start');

if (array_key_exists('user_companies', $GLOBALS) && $GLOBALS['user_companies']) {
    $query = $query->where(
        "a.parent_entity_id in (".implode(',', $GLOBALS['user_companies']).")"
    );
}
if (array_key_exists('user_groups', $GLOBALS) && $GLOBALS['user_groups']) {
    $query = $query->where(
        "a.id in (".implode(',', $GLOBALS['user_groups']).")"
    );
}
$db->setQuery($query);
$rows = $db->loadObjectList();

$items = array();
//echo $query;
// echo '<pre>'; print_r($query); echo '</pre>';
foreach ($rows as $item) {
//     $item->accel_score = $calculator->getDriverAccelScore($item, 7, 'accel');
//     $item->decel_score = $calculator->getDriverAccelScore($item, 7, 'decel');
//     $item->hard_turns = $calculator->getDriverAccelScore($item, 7, 'hard_turns');
//     $item->speed_score = $calculator->getDriverSpeedScore($item, 7);
    $item->total_score = $calculator->getDriverTotalScore($item, 'totalScore', '7');
    $item->aggressive_score = $calculator->getDriverTotalScore($item, 'aggressiveScore', '7');
    $item->distraction_score = $calculator->getDriverTotalScore($item, 'distractionScore', '7');
    // in case this driver doesn't have any trips before, a new driver
    // or he hasn't had any redflag event counts just yet
    // we still place him at (0,0) on the graph
    if ($item->total_score == -1) {
    	$item->total_score = 0;
    	$item->aggressive_score = 0;
    	$item->distraction_score = 0;
    }
//     $item->overall_score = $calculator->getOverall($item);
    $items[] = $item;
    
//     $json_item = array(
//     	"aggressive_score" => $item->aggressive_score,
//     	"distraction_score" => $item->distraction_score,
//     	"total_score" => $item->total_score,
//    		"driver_name" => $item->driver_name
//     );
//     $json_items[] = json_encode($json_item);
}

usort($items, "sort_items_by_totalScore");
$json_items = convertToJSON($items);
// echo '<pre>'; print_r($json_items); echo '</pre>';
?>




<table border=1 frame="void" rules="rows">
	<tr>
		<th><center><b>Total Score</b></center></th>
		<th><center><b>Driver Name</b></center></th>
	</tr>
<?php
for($x = 0; $x < min ( sizeof ( $items ), 5 ); $x ++) {
	printf ( '<tr><td><center>%5.2f</center></td><td><center>%s</center></td></tr>', $items [$x]->total_score, $items [$x]->driver_name );
}
?>
</table>
 
<style>
h1 {
    text-align: center;
}
</style>


<br />
<h2><center> Aggressive & Distraction Graph </center></h2>
<br />

<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8">
		<title>Aggressive Distraction Score Graph</title>
		<link rel="stylesheet" href="style.css" type="text/css">
	
		<script src="modules/amcharts/amcharts.js" type="text/javascript"></script>
		<script src="modules/amcharts/serial.js" type="text/javascript"></script>
		<script src="modules/amcharts/xy.js" type="text/javascript"></script>
		<script src="modules/amcharts/themes/none.js" type="text/javascript"></script>
	
		<script type="text/javascript">
	
		var chartData = [<?php echo $json_items; ?>];
		
        
		var chart = AmCharts.makeChart("chartdiv", {
			"title": "Aggressive Distraction Graph",
		    "type": "xy",
		    "pathToImages": "http://www.amcharts.com/lib/3/images/",
		    "theme": "none",
		    "dataProvider": chartData,
		    "valueAxes": [{
		        "position":"bottom",
		        "axisAlpha": 0.5,
                "maximum": 10,
                "minimum": 0,
                "title" : "Distraction"

			} , {
                 "position": "left",
                 "axisAlpha": 0.5,
                 "maximum": 10,
                 "minimum": 0,
                 "title" : "Aggressive"
			}],
		    "startDuration": 1.5,
		    "graphs": [{
		    	"title" : "Aggressive Distraction Graph",
		        "balloonText": "x:<b>[[x]]</b> y:<b>[[y]]</b><br>value:<b>[[value]]</b>",
		        "bullet": "circle",
		        "bulletBorderAlpha": 0,
				"bulletAlpha": 0.8,
		        "lineAlpha": 0,
		        "fillAlphas": 0,
		        "valueField": "total_score",
		        "yField": "aggressive_score",
		        "xField": "distraction_score",
		        "maxBulletSize": 7,
		        "minBulletSize": 5,     	
		        "balloonText": "Driver name : [[driver_name]] | Aggressive: [[aggressive_score]] | Distraction : [[distraction_score]] | Total: [[total_score]]"
		    }],
		    "marginRight": 100,
		    "marginTop": 10,
		    "marginLeft": 30,
		    "marginBottom": 10
		});
		
		</script>
		
	</head>
	<body>
		<div id="chartdiv" style="width: 100%; height: 400px;"></div>
	</body>
</html>










