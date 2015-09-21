<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted Access' );
$db = JFactory::getDBO();
$end_date = JRequest::getVar ('date', '');
$trip_id = JRequest::getInt ('trip', 0);

// get driver id
$dqry = "select td.driver_id as driver_id from giqwm_fleet_trip_driver as td where td.trip_id = " . $_REQUEST ['trip'];
$db->setQuery ($dqry);
$driver_id = $db->loadObject();

// get previous trip
$ptqry = "select td.trip_id as trip_id from giqwm_fleet_trip_driver as td"
        . " where td.driver_id =" .$driver_id->driver_id. " and td.trip_id < " . $_REQUEST ['trip'] 
        . " order by td.trip_id desc limit 1";
$db->setQuery ($ptqry);
$prev_trip = $db->loadObject();
$prev_trip_id = $prev_trip ? $prev_trip->trip_id : NULL;
//echo var_dump($prev_trip_id);

// get next trip
$ntqry = "select td.trip_id as trip_id from giqwm_fleet_trip_driver as td"
        . " where td.driver_id =" .$driver_id->driver_id. " and td.trip_id > " . $_REQUEST ['trip'] 
        . " order by td.trip_id asc limit 1";
$db->setQuery ($ntqry);
$next_trip = $db->loadObject();
$next_trip_id = $next_trip ? $next_trip->trip_id : NULL;
//echo var_dump($next_trip_id);

// get all redflag events
$qry = "Select * from fleet_redflag where trip_id=" . $_REQUEST ['trip'];
$db->setQuery ($qry);
$redflag_events = $db->loadObjectList();

$accel_decel_turns_markers = '';
$speed_markers = '';

foreach($redflag_events as $i => $item) {
    switch ($item->type) {
        case 'hard_turns': $type = 'Turn'; break;
        case 'accel': $type = 'Accel'; break;
        case 'decel': $type = 'Brake'; break;
    }
    $accel_decel_turns_markers .= get_marker($item, $type);
}

// get all the speed redflag events
$qry = "select * from fleet_redflag_speed a where tripid = ".$_REQUEST ['trip'];
$db->setQuery ($qry);
$speed_redflag_events = $db->loadObjectList();
//echo var_dump($speed_redflag_events);
foreach($speed_redflag_events as $i => $e) {
    $speed_markers .= get_marker($e, 'Speed');
}

function get_gps_marker($gps, $type = ''){
	$marker = '';
	
        $lat_degrees = substr($gps->latitude, 0, 2);
        $lat_minutes = substr($gps->latitude, 2);
        $lat_minutes /=  60;
        $lat = $lat_degrees + $lat_minutes;

        $pos = strpos ($gps->longitude, '.');
        if ($pos == 4) {
                $lon_degrees = substr($gps->longitude, 0, 2);
                $lon_minutes = substr($gps->longitude, 2);
        }
        elseif ($pos == 5) {
                $lon_degrees = substr($gps->longitude, 0, 3);
                $lon_minutes = substr($gps->longitude, 3);
        }
        else {
            return;
        }
        $lon_minutes /= 60;
        $lon = $lon_degrees + $lon_minutes;

        if ($gps->lat_dir == 'S') {
                $lat = '-' . $lat;
        }

        if ($gps->lon_dir == 'W') {
                $lon = '-' . $lon;
        }

        if ($lat != 0) {
                $marker .= '
                <Placemark>
                        <styleUrl>#startStyle</styleUrl>
                    <name>'.$type.' Hard / '.$type.' Severe #__TRIPID__</name>
                    <description>' . $gps->date . '</description>
                    <Point>
                        <coordinates>'.$lon.','.$lat.'</coordinates>
                    </Point>
                        <StyleMap>
                                <Pair>
                                        <Style>
                                                <IconStyle>
                                                        <Icon>
                                                                <href>'.JURI::root().'images/'.strtolower($type).'.png</href>
                                                        </Icon>
                                                </IconStyle>
                                        </Style>
                                </Pair>
                        </StyleMap>
                </Placemark>';
        }
	return $marker;
}

function get_marker($item, $type){
    $db = JFactory::getDBO();
    if ($type != 'Speed') {
        // get the previous minute with second set to 00
        $start = substr($item->starttime, 0, - 2) . "00";
        $end = substr($item->endtime, 0, - 2) . "00";

        // get the next minute with second set to 00
        $date_array = explode (':', $end);
        $next = $date_array[1] + 1;
        $end = $date_array[0] . ':' . $next . ":" . $date_array[2];          
    }

    $qry = $type == 'Speed' ?
            "Select * from fleet_gps where trip_id=" . $_REQUEST['trip'] . " and date = '$item->date'" :
            "Select * from fleet_gps where trip_id=" . $_REQUEST['trip'] . " and date between '$start' and '$end'";
    $db->setQuery($qry);
    $gps_result = $db->loadObjectList();

    if ($gps_result != NULL && count($gps_result) > 0) {
        $gps = $gps_result[0];
//            echo var_dump($gps);
        return get_gps_marker($gps, $type);
    }
}

$kml_head = <<<KML_HEAD
<kml xmlns="http://www.opengis.net/kml/2.2">
<Style id="trackStyle0">
   <LineStyle>
       <color>FF000000</color>
       <width>3</width>
   </LineStyle>
</Style>
 <Style id="trackStyle1">
   <LineStyle>
       <color>FF14B400</color>
       <width>3</width>
   </LineStyle>
</Style>
<Style id="trackStyle2">
   <LineStyle>
       <color>FF1400D2</color>
       <width>3</width>
   </LineStyle>
</Style>
<Style id="trackStyle3">
   <LineStyle>
       <color>FF14E7FF</color>
       <width>3</width>
   </LineStyle>
</Style>
<Style id="trackStyle4">
   <LineStyle>
       <color>FFF00014</color>
       <width>3</width>
   </LineStyle>
</Style>
<Style id="trackStyle5">
   <LineStyle>
       <color>FF146AFF</color>
       <width>3</width>
   </LineStyle>
</Style>
<Style id="trackStyle6">
   <LineStyle>
       <color>FF781414</color>
       <width>3</width>
   </LineStyle>
</Style>
<Style id="trackStyle7">
   <LineStyle>
       <color>FF7800F0</color>
       <width>3</width>
   </LineStyle>
</Style>
<Style id="trackStyle8">
   <LineStyle>
       <color>FFF0FF14</color>
       <width>3</width>
   </LineStyle>
</Style>
<Style id="trackStyle9">
   <LineStyle>
       <color>FFFF78F0</color>
       <width>3</width>
   </LineStyle>
</Style>
<Style id="startStyle">
    <IconStyle>
        <Icon>
            <href>http://www.google.com/intl/en_us/mapfiles/ms/icons/green-dot.png</href>
        </Icon>
        <BalloonStyle>
            <text>&lt;b&gt;$[name]&lt;/b&gt;&lt;br&gt;$[description]</text>
        </BalloonStyle>
    </IconStyle>
</Style>
<Style id="endStyle">
    <IconStyle>
        <Icon>
            <href>http://www.google.com/intl/en_us/mapfiles/ms/icons/red-dot.png</href>
        </Icon>
        <BalloonStyle>
            <text>&lt;b&gt;$[name]&lt;/b&gt;&lt;br&gt;$[description]</text>
        </BalloonStyle>
    </IconStyle>
</Style>
KML_HEAD;

$kml_placemark = <<<KML_PLACEMARK
<Placemark>
    <styleUrl>#trackStyle__STYLE__</styleUrl>
    <name>Trip #__TRIPID__</name>
    <description></description>
    <LineString>
        <coordinates>
__COORDINATES__
        </coordinates>
    </LineString>
</Placemark>
KML_PLACEMARK;

$kml_endpoints = <<<KML_ENDPOINTS
<Placemark>
    <styleUrl>#startStyle</styleUrl>
    <name>Start of Trip #__TRIPID__</name>
    <description>__TRIPSTART__</description>
    <Point>
        <coordinates>
__START__
        </coordinates>
    </Point>
</Placemark>

$accel_decel_turns_markers
$speed_markers

<Placemark>
    <styleUrl>#endStyle</styleUrl>
    <name>End of Trip #__TRIPID__</name>
    <description>__TRIPEND__</description>
    <Point>
        <coordinates>
__END__
        </coordinates>
    </Point>
</Placemark>
KML_ENDPOINTS;

$kml_foot = <<<KML_FOOT
</kml>
KML_FOOT;

$kml = $kml_head;
$c = 0;
$trip = $trip_id;

foreach ( $this->items as $trip => $item ) {
	$placemark = str_replace ( '__COORDINATES__', implode ( "\n", $item ), $kml_placemark );
	$placemark = str_replace ( '__STYLE__', $c, $placemark );
	$c += 1;
	if ($c > 9) {
		$c = 0;
	}
	$kml .= $placemark;
	
	$endpoints = str_replace ( '__START__', implode ( '', array_slice ( $item, 0, 1 ) ), $kml_endpoints );
	
	$endpoints = str_replace ( '__END__', implode ( '', array_slice ( $item, - 1, 1 ) ), $endpoints );
	$kml .= $endpoints;
	
	$query = $db->getQuery ( true )->select ( 'id, DATE_ADD(start_date, INTERVAL a.time_zone HOUR) as start_date, DATE_ADD(end_date, INTERVAL a.time_zone HOUR) as end_date' )->from ( 'fleet_trip as a' )->where ( 'a.id=' . $trip );
	$db->setQuery ( $query );
	$row = $db->loadObject();
	
	$kml = str_replace ( '__TRIPID__', $row->id, $kml );
	// $kml = str_replace('__TRIPTurn__', $resultreport['hard_turns_starttime'], $kml);
	// $kml = str_replace('__TRIPBRAKE__', $resultreport['decel_starttime'], $kml);
	// $kml = str_replace('__TRIPACCEL__', $resultreport['accel_starttime'], $kml);
	$kml = str_replace ( '__TRIPSTART__', $row->start_date, $kml );
	$kml = str_replace ( '__TRIPEND__', $row->end_date, $kml );
	if ($row->end_date) {
		$end_date = $row->end_date;
	}
}

$kml .= $kml_foot;

$id = JRequest::getInt ( 'trip' );
@file_put_contents ( 'cache/map' . $id . '.kml', $kml );
function dayString($date) {
	$d = new DateTime ( $date );
	return $d->format ( 'Y-m-d' );
}
function todayString() {
	$d = new DateTime ();
	$d->sub ( new DateInterval ( 'PT7H' ) );
	return $d->format ( 'Y-m-d' );
}

$query = $db->getQuery ( true )->select ( 'c.name as driver_name' )->from ( 'fleet_trip as a' )->leftJoin ( '#__fleet_trip_driver as b on b.trip_id = a.id' )->leftJoin ( '#__fleet_driver as c on b.driver_id = c.id' )->where ( 'a.id=' . $trip );
$db->setQuery ( $query );
$driver_name = $db->loadResult ();
?>
<table id="current_date" width="100%">
	<tr>
		<td><?php echo dayString($end_date); ?></td>
		<td><?php echo $driver_name; ?></td>
	</tr>
</table>
<div id="map_canvas" style="width: 800px; height: 400px;"></div>

<?php
function dayStringPlus($date) {
	$d = new DateTime ( $date );
	$d->add ( new DateInterval ( 'P1D' ) );
	return $d->format ( 'Y-m-d' );
}
function dayStringMinus($date) {
	$d = new DateTime ( $date );
	$d->sub ( new DateInterval ( 'P1D' ) );
	return $d->format ( 'Y-m-d' );
}

if ($prev_trip_id) { ?>
    <a class="prev_date" href="/component/fleetmatrix?view=map&trip=<?php echo $prev_trip_id; ?>&tmpl=component"><< PREVIOUS</a>
<?php } else { ?>
        <span class="next_date"><< PREVIOUS</a>
<?php } ?>

<?php if ($next_trip_id) { ?>
        <a class="next_date" href="/component/fleetmatrix?view=map&trip=<?php echo $next_trip_id; ?>&tmpl=component">NEXT >></a>
<?php } else { ?>
        <span class="next_date">NEXT >></a>
<?php } ?>
