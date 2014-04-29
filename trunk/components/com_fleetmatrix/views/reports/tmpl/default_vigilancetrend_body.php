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


foreach($this->items as $i => $item) {
//echo "<pre >";
//print_r($item);


$trip_id .= "'".$item->id."', ";

if($item->accel_hard == '') {
$accel_hard .= "0, ";
} else {
$accel_hard .= $item->accel_hard.", ";
}


if($item->accel_severe == '') {
$accel_severe .= "0, ";
} else {
$accel_severe .= $item->accel_severe.", ";
}


if($item->decel_hard == '') {
$decel_hard .= "0, ";
} else {
$decel_hard .= $item->decel_hard.", ";
}


if($item->decel_severe == '') {
$decel_severe .= "0, ";
} else {
$decel_severe .= $item->decel_severe.", ";
}


if($item->turns_hard == '') {
$turns_hard .= "0, ";
} else {
$turns_hard .= $item->turns_hard.", ";
}

if($item->turns_severe == '') {
$turns_severe .= "0, ";
} else {
$turns_severe .= $item->turns_severe.", ";
}

if($item->speed_hard == '') {
$speed_hard .= "0, ";
} else {
$speed_hard .= $item->speed_hard.", ";
}


if($item->speed_severe == '') {
$speed_severe .= "0, ";
} else {
$speed_severe .= $item->speed_severe.", ";
}

    
}

 $trip_id = substr(trim($trip_id),0,-1);

$accel_hard = substr(trim($accel_hard),0,-1);
 $accel_severe = substr(trim($accel_severe),0,-1);

 $decel_hard = substr(trim($decel_hard),0,-1);
 $decel_severe = substr(trim($decel_severe),0,-1);
 $turns_hard = substr(trim($turns_hard),0,-1);
 $turns_severe = substr(trim($turns_severe),0,-1);

 $speed_hard = substr(trim($speed_hard),0,-1);
 $speed_severe = substr(trim($speed_severe),0,-1);


//echo $accel_hard;

//echo $accel_severe;
//echo $decel_hard;

?>
<!--
        <script src="modules/jquery-1.8.2.min.js" type="text/javascript"></script>-->
<script src="http://code.highcharts.com/highcharts.js"></script>
<script src="http://code.highcharts.com/modules/exporting.js"></script>

<div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>



<script>

jQuery(function () {
        jQuery('#container').highcharts({
            title: {
                text: 'Reports - Driver Vigilance Report',
                x: -20 //center
            },
            subtitle: {
               // text: 'Source: WorldClimate.com',
                x: -20
            },
            xAxis: {
                categories: [<?php echo $trip_id; ?>]
            },
            yAxis: {
                title: {
                   // text: 'Temperature (°C)'
                },
                plotLines: [{
                    value: 0,
                    width: 1,
                    color: '#808080'
                }]
            },
            tooltip: {
                valueSuffix: '°C'
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle',
                borderWidth: 0
            },
            series: [{
                name: 'Accel hard',
                data: [<?php echo $accel_hard; ?>]
            }, {
                name: 'Accel severe',
                data: [<?php echo $accel_severe; ?>]
            }, {
                name: 'Decel hard',
                data: [<?php echo $decel_hard; ?>]
            }, {
                name: 'Decel severe',
                data: [<?php echo $decel_severe; ?>]
            }, {
                name: 'Turns hard',
                data: [<?php echo $turns_hard; ?>]
            }, {
                name: 'Turns severe',
                data: [<?php echo $turns_severe; ?>]
            }, {
                name: 'Speed hard',
                data: [<?php echo $speed_hard; ?>]
            }, {
                name: 'Speed severe',
                data: [<?php echo $speed_severe; ?>]
            }
			
			/*{
                name: 'Accel hard',
                data: [7.0, 6.9, 9.5, 14.5, 18.2, 21.5, 25.2, 26.5, 23.3, 18.3, 13.9, 9.6]
            }*/
			
			]
        });
    });
    


</script>

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
