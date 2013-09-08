<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>
<?php foreach($this->items as $i => $item): ?>
	<tr class="row<?php echo $i % 2; ?>">
		<td>
			<?php echo $item->id; ?>
		</td>
		<td>
			<?php echo $item->name; ?>
		</td>
		<td>
			<?php echo $item->visible ? 'Yes' : 'No'; ?>
		</td>
		<td>
			<?php echo $item->company; ?>
		</td>
		<td>
			<?php echo $item->driver_group; ?>
		</td>
        <td>
            <a href="<?php echo JRoute::_($this->getRoute().'&id='.$item->id); ?>">Edit</a>
        </td>
	</tr>
<?php endforeach; ?>
