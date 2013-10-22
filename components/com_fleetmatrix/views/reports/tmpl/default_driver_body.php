<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>

<?php foreach($this->items as $i => $item): ?>

	<tr class="row<?php echo $i % 2; ?>">
		<td style="font-weight: bold; font-size: 15px;">
			<?php if($item->decel_scoretype == 0) {
				echo "<span style='color: #f5e100;'>";
			} else if($item->decel_scoretype == 1) {
				echo "<span style='color: #ffa500;'>";
			} else { 
				echo "<span style='color: #ff0000;'>";
			} 
					echo ($item->decel_count == 0)?'':$item->decel_count;
				echo "</span>";
			?>
		</td>
		<td style="font-weight: bold; font-size: 15px;">
			<?php if($item->accel_scoretype == 0) {
				echo "<span style='color: #f5e100;'>";
			} else if($item->accel_scoretype == 1) {
				echo "<span style='color: #ffa500;'>";
			} else { 
				echo "<span style='color: #ff0000;'>";
			} 
					echo ($item->accel_count == 0)?'':$item->accel_count;
				echo "</span>";
			?>
		</td>
		<td style="font-weight: bold; font-size: 15px;">
			<?php if($item->hard_turns_scoretype == 0) {
				echo "<span style='color: #f5e100;'>";
			} else if($item->hard_turns_scoretype == 1) {
				echo "<span style='color: #ffa500;'>";
			} else { 
				echo "<span style='color: #ff0000;'>";
			} 
					echo ($item->hard_turns_count == 0)?'':$item->hard_turns_count;
				echo "</span>";
			?>
		</td>
        <td><a class="map-link" href="/component/fleetmatrix?view=map&trip=<?php echo $item->trip_id; ?>&tmpl=component">Map</a></td>
		<td>
			<?php echo $item->vehicle_name; ?>
		</td>
		<td>
			<?php echo $item->trip_start; ?>
		</td>
		<td>
			<?php echo $item->trip_end; ?>
		</td>
        <td>
            <?php echo number_format($item->miles); ?>
        </td>
        <td>
            <?php echo $item->assigned_driver; ?>
        </td>
        <td>
            <?php echo $item->idle_time; ?>
        </td>
	</tr>
<?php endforeach; ?>

<script type="text/javascript">
  jQuery(document).ready(function() {
    jQuery(".map-link").click(
      function(event) {
        event.preventDefault();
        var elementURL = jQuery(this).attr("href");
        jQuery.colorbox({iframe: true, href: elementURL, innerWidth: 850, innerHeight: 465});
      });
  });
</script>