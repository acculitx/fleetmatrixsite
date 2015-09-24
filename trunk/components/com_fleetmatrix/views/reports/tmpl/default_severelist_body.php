<?php
defined('_JEXEC') or die('Restricted Access');
?>

<?php    
// require(JPATH_BASE.DS."components".DS."com_fleetmatrix".DS."models".DS."calculator.php");
$calculator = new ScoreCalculator();

$driver_id = JRequest::getInt('driver', 0);
$driver_name = JRequest::getString('drivername');

$accel_decel_turns = $calculator->getDriverADTScore($driver_id);
$speed = $calculator->getDriverAllSpeedScore($driver_id);
$merged_events = array_merge($accel_decel_turns, $speed);

# the following merged adt and speed into one array by date
usort($merged_events, "sort_items_by_totalScore");
function sort_items_by_totalScore($a, $b) {
	if ($a->starttime > $b->starttime)
		return -1;
	else if ($b->starttime > $a->starttime)
		return 1;
	else
		return 0;
}
?>

<?php foreach($merged_events as $i => $item): ?>
	<tr class="row<?php echo $i % 2; ?>">
        <td>
            <?php echo $driver_name; ?>
        </td>
		<td>
			<?php echo $item->starttime; ?>
		</td>
        <td>
            <?php echo $item->eventtype; ?>
        </td>
        
		<td><a class="maplink" href="component/fleetmatrix?view=map&trip=<?php echo $item->trip_id; ?>&tmpl=component">(map)</a></td>
        
	</tr>
<?php endforeach; ?>

<script type="text/javascript">
  jQuery(document).ready(function() {
    jQuery(".maplink").click(
      function(event) {
          
        event.preventDefault();
        var elementURL = jQuery(this).attr("href");
        console.log(elementURL);
        jQuery.colorbox({iframe: true, href: elementURL, innerWidth: 850, innerHeight: 465});
      });
  });
</script>

