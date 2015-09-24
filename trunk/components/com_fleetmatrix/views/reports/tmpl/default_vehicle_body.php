<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>
<?php foreach($this->items as $i => $item): ?>
	<tr class="row<?php echo $i % 2; ?>">
		<td>
			<?php echo $item->vehicle_name; ?>
		</td>
		<td>
			<?php echo $item->group_name; ?>
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
            <?php echo number_format($item->miles); ?>
        </td>
        <td>
            <?php echo $item->trip_count; ?>
        </td>
        <td>
            <?php echo $item->gallon_consumed; ?>
        </td>
        <td>
            <?php echo $item->not_connected; ?>
        </td>
        <td>
            <?php echo $item->disconnects; ?>
        </td>
	</tr>
<?php endforeach; ?>
