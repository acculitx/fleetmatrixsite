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
			<?php if ($item->gallon_consumed == 0) {
					echo 0;
				  }
				  else {
					echo is_numeric($item->gallon_consumed) ? 
						round($item->miles/$item->gallon_consumed, 2) : $item->gallon_consumed; }?>
		</td>
		<td>
			<?php if ($item->miles == 0) {echo 0;}
				  else {echo round(($item->accel_hard)* 100/($item->miles), 1);} ?>
		</td>
	
		<td>
			<?php if ($item->miles == 0) {echo 0;}
				  else {echo round(($item->decel_hard)* 100/($item->miles), 1);} ?>
		</td>

		<td>
			<?php if ($item->miles == 0) {echo 0;}
				  else {echo round(($item->turns_hard)* 100/($item->miles), 1);} ?>
		</td>

		<td>
			<?php if ($item->miles == 0) {echo 0;}
				  else {echo round(($item->speed_hard)* 100/($item->miles), 1);} ?>
		</td>

        <td>
			<?php if ($item->miles == 0) {echo 0;}
				  else {echo round(($item->idle_time)* 100/($item->miles), 1);} ?>
            <?php //echo number_format($item->miles); ?>
        </td>

	</tr>
<?php endforeach; ?>
