<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');


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
$db = JFactory::getDBO();
$end_date = JRequest::getVar('date', '');
$trip_id = JRequest::getInt('trip', 0);

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
    $endpoints = str_replace(
        '__END__',
        implode('', array_slice($item, -1, 1)),
        $endpoints
    );
    $kml .= $endpoints;

    $query = $db->getQuery(true)
        ->select('id, DATE_SUB(start_date, INTERVAL 8 HOUR) as start_date, DATE_SUB(end_date, INTERVAL 8 HOUR) as end_date')
        ->from('fleet_trip as a')
        ->where('a.id='.$trip)
        ;
    $db->setQuery($query);
    $row = $db->loadObject();

    $kml = str_replace('__TRIPID__', $row->id, $kml);
    $kml = str_replace('__TRIPSTART__', $row->start_date, $kml);
    $kml = str_replace('__TRIPEND__', $row->end_date, $kml);
    if ($row->end_date) {
        $end_date = $row->end_date;
    }
}


$kml .= $kml_foot;

$id = JRequest::getInt('trip');
file_put_contents('cache/map'.$id.'.kml', $kml);

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
