<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>
<?php foreach($this->items as $i => $item): ?>
	<tr class="row<?php echo $i % 2; ?>">
		<td>
			<?php echo $item->subscription_id; ?>
		</td>
		<td>
			<?php echo $item->group_name; ?>
		</td>
		<td>
			<?php echo is_numeric($item->mpg) ? number_format($item->mpg, 1) : $item->mpg; ?>
		</td>
        <td>
            <?php echo number_format($item->miles); ?>
        </td>
        <td>
            <?php echo $item->trip_count; ?>
        </td>
        <td>
            <?php echo is_numeric($item->gallons) ? number_format($item->gallons, 2) : $item->gallons; ?>
        </td>
        <td>
            <?php echo $item->not_connected; ?>
        </td>
        <td>
            <?php echo $item->disconnects; ?>
        </td>
	</tr>
<?php endforeach; ?>
