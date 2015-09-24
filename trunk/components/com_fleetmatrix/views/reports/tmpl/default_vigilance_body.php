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
				  else {echo round(($item->accel_hard)* 100/($item->miles), 1);} ?>
		</td>
		<td>
			<?php if ($item->miles == 0) {echo 0;}
				  else {echo round(($item->accel_severe)* 100/($item->miles), 2);} ?>
		</td>
		<td>
			<?php if ($item->miles == 0) {echo 0;}
				  else {echo round(($item->decel_hard)* 100/($item->miles), 1);} ?>
		</td>
		<td>
			<?php if ($item->miles == 0) {echo 0;}
				  else {echo round(($item->decel_severe)* 100/($item->miles), 1);} ?>
		</td>
		<td>
			<?php if ($item->miles == 0) {echo 0;}
				  else {echo round(($item->turns_hard)* 100/($item->miles), 1);} ?>
		</td>
		<td>
			<?php if ($item->miles == 0) {echo 0;}
				  else {echo round(($item->turns_severe)* 100/($item->miles), 1);} ?>
		</td>
		<td>
			<?php if ($item->miles == 0) {echo 0;}
				  else {echo round(($item->speed_hard)* 100/($item->miles), 1);} ?>
		</td>
		<td>
			<?php if ($item->miles == 0) {echo 0;}
				  else {echo round(($item->speed_severe)* 100/($item->miles), 1);} ?>
		</td>
        <td>
            <?php echo number_format($item->miles); ?>
        </td>
		<td>
			<?php echo $item->trip_count; ?>
		</td>
	</tr>
<?php endforeach; ?>
