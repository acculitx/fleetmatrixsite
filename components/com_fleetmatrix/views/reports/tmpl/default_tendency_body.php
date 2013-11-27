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
			<?php echo $item->group_name; ?>
		</td>
		<td>
			<?php echo is_numeric($item->accel_score) ? number_format($item->accel_score, 2) : $item->accel_score; ?>
		</td>
		<td>
			<?php echo is_numeric($item->decel_score) ? number_format($item->decel_score, 2) : $item->decel_score; ?>
		</td>
		<td>
			<?php echo is_numeric($item->hard_turns) ? number_format($item->hard_turns, 2) : $item->hard_turns; ?>
		</td>
        <td>
            <?php echo number_format($item->miles); ?>
        </td>
		<td>
			<?php echo $item->trip_count; ?>
		</td>
	</tr>
<?php endforeach; ?>
