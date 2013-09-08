<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>

<?php foreach($this->items as $i => $item): ?>

	<tr class="row<?php echo $i % 2; ?>">
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