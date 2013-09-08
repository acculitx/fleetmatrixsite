<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>
<?php foreach($this->items as $i => $item): ?>
	<tr class="row<?php echo $i % 2; ?>">
        <td>
            <input type="checkbox" class="cb" name="cid[]" value="<?php echo $item->id; ?>" />
        </td>
		<td>
			<?php echo $item->id; ?>
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
