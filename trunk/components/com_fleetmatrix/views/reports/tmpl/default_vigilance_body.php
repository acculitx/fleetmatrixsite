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
<?php endforeach; ?>
