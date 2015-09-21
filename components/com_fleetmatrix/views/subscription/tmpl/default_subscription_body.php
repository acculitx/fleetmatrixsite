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
            <?php echo $item->entity; ?>
        </td>
		<td>
			<?php echo $item->name; ?>
		</td>
        <td>
            <?php echo $item->vin; ?>
        </td>
        <td>
            <?php echo $item->match; ?>
        </td>
        <td>
            <?php echo $item->date_range; ?>
        </td>
        <td>
            <?php echo $item->serial; ?>
        </td>
        <td>
            <?php echo $item->weight; ?>
        </td>
        <td>
            <?php echo $item->driver; ?>
        </td>
		<td>
			<?php echo $item->visible ? 'Yes' : 'No'; ?>
		</td>
        <td>
            <a href="<?php echo JRoute::_($this->getRoute().'&id='.$item->id); ?>">Edit</a>
        </td>
	</tr>
<?php endforeach; ?>
