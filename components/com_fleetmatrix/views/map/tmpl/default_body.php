<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
$db = JFactory::getDBO();
$end_date = JRequest::getVar('date', '');
$trip_id = JRequest::getInt('trip', 0);


/*$queryreprot = mysql_query("Select * from fleet_redflag_report where tripid=".$_REQUEST['trip']);
$resultreport = mysql_fetch_assoc($queryreprot);
*/
//print_r($resultreport);
/*$query2 = $db->getQuery(true)
    ->select('*')
        ->from('fleet_redflag_report as a')
        ->where('a.tripid='.$_REQUEST['trip']) ;
$db->setQuery($query2);
$driverdetail = $db->loadObjectList();
foreach($driverdetail as $item){
print_r($item);
}*/
//print_r($driverdetail);

 $query2 = "Select * from fleet_redflag where trip_id=".$_REQUEST['trip'];
$db->setQuery($query2);

$driverdetail = $db->loadObjectList();
/*foreach($driverdetail as $item){
echo $item->id;
echo "<br/>";
echo $item->type;
echo "<br/>"; 
}*/
// $driverdetail->hard_turns_hard_count;
//hard_turns
//decel
//accel;

$lataccel  = array();
$longaccel =   array();
 $lat = array();
 $long  =  array();
$latbrake =   array();
$longbrake  =  array();
$accel_middle = '';
$brake_middle = '';
$turn_middle = '';


foreach($driverdetail as $i => $item){


if($item->type == 'decel'){
$first_date =  substr($item->starttime,0,-2)."00";

 $scond_date =  substr($item->endtime,0,-2)."00";
$date_array = explode(':',$scond_date);
$next_date = $date_array[1]+1;
$scond_date =  $date_array[0].':'.$next_date.":".$date_array[2];



$query3 = "Select * from fleet_gps where trip_id=".$_REQUEST['trip'] ." and date between '$first_date' and '$scond_date'";
$db->setQuery($query3);

$querygps = $db->loadObjectList();





foreach($querygps as $k => $itemss){


 //$resultgps['latitude'];
 $itemss->latitude;
 
 
  $result = substr($itemss->latitude, 0, 2);
 
 $result1 = substr($itemss->latitude, 2);
 $result1 = $result1/60;
  $latbrake[$i] = $result+$result1;
  
  
 
 //$resultgps['longitude'];
 $itemss->longitude;
 
 $pos = strpos( $itemss->longitude, '.');
 if($pos == 4){
  $result2 = substr( $itemss->longitude, 0, 2);
 $result3 = substr( $itemss->longitude, 2);
 } 
 
  if($pos == 5){
  $result2 = substr( $itemss->longitude, 0, 3);
 $result3 = substr( $itemss->longitude, 3);
 } 
 
 
 $result3 = $result3/60;
  $longbrake[$i] = $result2+$result3;
  
  if($itemss->lat_dir!= 'N'){
  $latbrake[$i] = '-'.$latbrake[$i];
  }
  
  
  if($itemss->lon_dir!= 'E'){
    $longbrake[$i] = '-'.$longbrake[$i];
  }
  
//echo  $latbrake." , ". $longbrake."<br/>";

if($latbrake[$i] != 0){
 $brake_middle .= '<Placemark>
    <styleUrl>#startStyle</styleUrl>
    <name>Brake Hard / Brake Severe #__TRIPID__</name>
    <description>'. $itemss->date.'</description>
    <Point>
        <coordinates>
'. $longbrake[$i].','.$latbrake[$i].'
        </coordinates>
    </Point>
	<StyleMap>
<Pair>
<Style>
<IconStyle>
<Icon>
<href>'.JURI::root().'images/brake_icon.jpg</href>
</Icon>

</IconStyle>
</Style>
</Pair>
</StyleMap>
</Placemark>';
}

}









}





if($item->type == 'hard_turns'){
$first_date =  substr($item->starttime,0,-2)."00";

 $scond_date =  substr($item->endtime,0,-2)."00";
$date_array = explode(':',$scond_date);
$next_date = $date_array[1]+1;
$scond_date =  $date_array[0].':'.$next_date.":".$date_array[2];



$query3 = "Select * from fleet_gps where trip_id=".$_REQUEST['trip'] ." and date between '$first_date' and '$scond_date'";
$db->setQuery($query3);

$querygps = $db->loadObjectList();

foreach($querygps as $k => $itemss){


 //$resultgps['latitude'];
 $itemss->latitude;
 
 
  $result = substr($itemss->latitude, 0, 2);
 
 $result1 = substr($itemss->latitude, 2);
 $result1 = $result1/60;
   $lat[$i] = $result+$result1;

  
 
 //$resultgps['longitude'];
 $itemss->longitude;
 
 $pos = strpos( $itemss->longitude, '.');
 if($pos == 4){
  $result2 = substr( $itemss->longitude, 0, 2);
 $result3 = substr( $itemss->longitude, 2);
 } 
 
  if($pos == 5){
  $result2 = substr( $itemss->longitude, 0, 3);
 $result3 = substr( $itemss->longitude, 3);
 } 
 
 
 $result3 = $result3/60;
  $long[$i] = $result2+$result3;
  
  if($itemss->lat_dir!= 'N'){
  $lat[$i] = '-'.$lat[$i];
  }
  
  
  if($itemss->lon_dir!= 'E'){
  $long[$i] = '-'.$long[$i];
  }
  

if($lat[$i] != 0){
  $turn_middle .= '<Placemark>
    <styleUrl>#startStyle</styleUrl>
    <name>Turn Hard / Turn Severe #__TRIPID__</name>
    <description>'. $itemss->date.'</description>
    <Point>
        <coordinates>
'. $long[$i].','.$lat[$i].'
        </coordinates>
    </Point>
<StyleMap>
<Pair>
<Style>
<IconStyle>
<Icon>
<href>'.JURI::root().'images/turn_icon1.png</href>
</Icon>

</IconStyle>
</Style>
</Pair>
</StyleMap>
</Placemark>';
}

}






}




if($item->type == 'accel'){
$first_date =  substr($item->starttime,0,-2)."00";

 $scond_date =  substr($item->endtime,0,-2)."00";
$date_array = explode(':',$scond_date);
$next_date = $date_array[1]+1;
$scond_date =  $date_array[0].':'.$next_date.":".$date_array[2];

$query3 = "Select * from fleet_gps where trip_id=".$_REQUEST['trip'] ." and date between '$first_date' and '$scond_date'";
$db->setQuery($query3);

$querygps = $db->loadObjectList();



//$querygps = mysql_query("Select * from fleet_gps where trip_id=".$_REQUEST['trip'] ." and date between '$first_date' and '$scond_date'");



foreach($querygps as $itemss){


 //$resultgps['latitude'];
 $itemss->latitude;
 
 
  $result = substr($itemss->latitude, 0, 2);
 
 $result1 = substr($itemss->latitude, 2);
 $result1 = $result1/60;
  $lataccel[$i] = $result+$result1;
  
  
 
 //$resultgps['longitude'];
 $itemss->longitude;
 
 $pos = strpos( $itemss->longitude, '.');
 if($pos == 4){
  $result2 = substr( $itemss->longitude, 0, 2);
 $result3 = substr( $itemss->longitude, 2);
 } 
 
  if($pos == 5){
  $result2 = substr( $itemss->longitude, 0, 3);
 $result3 = substr( $itemss->longitude, 3);
 } 
 
 
 $result3 = $result3/60;
  $longaccel[$i] = $result2+$result3;
  
  if($itemss->lat_dir!= 'N'){
  $lataccel[$i] = '-'.$lataccel[$i];
  }
  
  
  if($itemss->lon_dir!= 'E'){
    $longaccel[$i] = '-'.$longaccel[$i];
  }
  

  


if($lataccel[$i] != 0){
 $accel_middle = '<Placemark>
    <styleUrl>#startStyle</styleUrl>
    <name>Accel Hard  / Accel Hard  #__TRIPID__</name>
    <description>'. $itemss->date.'</description>
    <Point>
        <coordinates>
'. $longaccel[$i].','.$lataccel[$i].'
        </coordinates>
    </Point>
	<StyleMap>
<Pair>
<Style>
<IconStyle>
<Icon>
<href>'.JURI::root().'images/accel_icon.jpg</href>
</Icon>

</IconStyle>
</Style>
</Pair>
</StyleMap>
</Placemark>';
}

}




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

$brake_middle

$turn_middle


$accel_middle

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
foreach ($this->items as $trip => $item) {
    //var_dump($item);
    $placemark = str_replace(
        '__COORDINATES__',
        implode("\n", $item),
        $kml_placemark
    );
    $placemark = str_replace(
        '__STYLE__',
        $c,
        $placemark
    );
    $c += 1;
    if ($c > 9) { $c = 0; }
    $kml .= $placemark;

    $endpoints = str_replace(
        '__START__',
        implode('', array_slice($item, 0, 1)),
        $kml_endpoints
    );
	
	
/*	foreach($driverdetail as $j => $itemyy){
	
	if(count($lat) > 0){
	
for($p=0 ; $p < count($lat) ; $p++){

  $endpoints = str_replace(
        '__MIDDLE'.$p.'__',
       $long[$p].','.$lat[$p],
        $endpoints
    );
	}
	

	
	}
	
	
	if(count($latbrake) > 0){
	for($p=0; $p < count($latbrake); $p++){
	   $endpoints = str_replace(
        '__BRAKEMIDDLE'.$j.'__',
       $longbrake[$p].','.$latbrake[$p],
        $endpoints
    );
	}
	}
	
	 
if(count($lataccel)  > 0){
for($p=0; $p < count($lataccel); $p++){
	   $endpoints = str_replace(
        '__ACCELMIDDLE'.$j.'__',
       $longaccel.','.$lataccel,
        $endpoints
    );
	}
	
}

}*/


    $endpoints = str_replace(
        '__END__',
        implode('', array_slice($item, -1, 1)),
        $endpoints
    );
    $kml .= $endpoints;

    $query = $db->getQuery(true)
        ->select('id, DATE_ADD(start_date, INTERVAL a.time_zone HOUR) as start_date, DATE_ADD(end_date, INTERVAL a.time_zone HOUR) as end_date')
        ->from('fleet_trip as a')
        ->where('a.id='.$trip)
        ;
    $db->setQuery($query);
    $row = $db->loadObject();

    $kml = str_replace('__TRIPID__', $row->id, $kml);
	 //$kml = str_replace('__TRIPTurn__', $resultreport['hard_turns_starttime'], $kml);
	// $kml = str_replace('__TRIPBRAKE__', $resultreport['decel_starttime'], $kml);
	 // $kml = str_replace('__TRIPACCEL__', $resultreport['accel_starttime'], $kml);
    $kml = str_replace('__TRIPSTART__', $row->start_date, $kml);
    $kml = str_replace('__TRIPEND__', $row->end_date, $kml);
    if ($row->end_date) {
        $end_date = $row->end_date;
    }
}


$kml .= $kml_foot;

$id = JRequest::getInt('trip');
@file_put_contents('cache/map'.$id.'.kml', $kml);

function dayString($date) {
    $d = new DateTime($date);
    return $d->format('Y-m-d');
}
function todayString() {
    $d = new DateTime();
    $d->sub(new DateInterval('PT7H'));
    return $d->format('Y-m-d');
}

$query = $db->getQuery(true)
    ->select('c.name as driver_name')
        ->from('fleet_trip as a')
        ->leftJoin('#__fleet_trip_driver as b on b.trip_id = a.id')
        ->leftJoin('#__fleet_driver as c on b.driver_id = c.id')
        ->where('a.id='.$trip)
        ;
$db->setQuery($query);
$driver_name = $db->loadResult();


/*$querygps = $db->getQuery(true)
    ->select('id')
        ->from('fleet_gps')
        ->where('trip_id='.$trip)
        ;
$db->setQuery($querygps);
$gps = $db->loadResult();

print_r($gps);*/


?>
<table id="current_date" width="100%"><tr><td><?php echo dayString($end_date); ?></td>
<td><?php echo $driver_name; ?></td></tr></table>

<div id="map_canvas" style="width:800px; height:400px;"></div>

<?php

function dayStringPlus($date) {
    $d = new DateTime($date);
    $d->add(new DateInterval('P1D'));
    return $d->format('Y-m-d');
}

function dayStringMinus($date) {
    $d = new DateTime($date);
    $d->sub(new DateInterval('P1D'));
    return $d->format('Y-m-d');
}

if ($end_date) {
?>
<a class="prev_date" href="/component/fleetmatrix?view=map&trip=<?php echo $trip_id; ?>&date=<?php echo dayStringMinus($end_date); ?>&tmpl=component"><< PREVIOUS</a>
<?php
//var_dump(todayString());
//var_dump(dayString($end_date));
if (todayString() != dayString($end_date)) {
?>
<a class="next_date" href="/component/fleetmatrix?view=map&trip=<?php echo $trip_id; ?>&date=<?php echo dayStringPlus($end_date); ?>&tmpl=component">NEXT >></a>
<?php
} else {
?>
<span class="next_date">NEXT >>></a>
<?php
}
}
?>
