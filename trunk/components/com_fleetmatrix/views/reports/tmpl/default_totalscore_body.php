<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>

<?php foreach($this->items as $i => $item): ?>
	<tr class="row<?php echo $i % 2; ?>">
        <td>
            <?php echo $item->driver_name; ?>
        </td>
		<td>
			<?php echo round($item->total_score, 2); ?>
		</td>
        <td>
            <?php echo round($item->aggressive_score, 2); ?>
        </td>
		<td>
			<?php echo round($item->distraction_score, 2); ?>
		</td>
		<td>
			<a class="Severe Event List" href="component/fleetmatrix?view=reports&driver=<?php echo $item->driver_id; ?>&drivername=<?php echo $item->driver_name; ?>&cmd=severelist"> <?php echo $item->driver_name; ?>'s severe events </a>
		</td>
        
	</tr>
<?php endforeach; ?>
