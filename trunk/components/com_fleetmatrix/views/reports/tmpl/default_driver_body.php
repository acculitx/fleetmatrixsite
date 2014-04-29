<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

?>

<?php foreach($this->items as $i => $item): ?>

	<tr class="row<?php echo $i % 2; ?>">
		<td>
            <?php echo $item->assigned_driver; ?>
        </td>
        <td>
            <?php echo $item->company_name; ?>
        </td>
        <td>
            <?php echo $item->group_name; ?>
        </td>
        <td>
            <?php echo date("m/d/Y h:i:s", strtotime($item->trip_start)); ?>
        </td>
		<td>
			<?php if ($item->accel_hard != NULL) echo $item->accel_hard;
					else echo 0; ?>
		</td>
		<td>
			<?php if ($item->accel_severe != NULL) echo $item->accel_severe;
					else echo 0; ?>
		</td>
		<td>
			<?php if ($item->decel_hard != NULL) echo $item->decel_hard;
					else echo 0; ?>
		</td>
		<td>
			<?php if ($item->decel_severe != NULL) echo $item->decel_severe;
					else echo 0; ?>
		</td>
		<td>
			<?php if ($item->turns_hard != NULL) echo $item->turns_hard;
					else echo 0; ?>
		</td>
		<td>
			<?php if ($item->turns_severe != NULL) echo $item->turns_severe;
					else echo 0; ?>
		</td>
		<td>
			<?php if ($item->speed_hard != NULL) echo $item->speed_hard;
					else echo 0; ?>
		</td>
		<td>
			<?php if ($item->speed_severe != NULL) echo $item->speed_severe;
					else echo 0; ?>
		</td>
        <td>
            <?php echo number_format($item->miles); ?>
        </td>
		<td>
            <?php echo $item->idle_time; ?>
        </td>
        <td><a class="map-link" href="component/fleetmatrix?view=map&trip=<?php echo $item->trip_id; ?>&tmpl=component">(map)</a></td>
        
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