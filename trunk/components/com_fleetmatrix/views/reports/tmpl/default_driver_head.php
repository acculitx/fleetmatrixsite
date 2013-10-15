<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

include(JPATH_COMPONENT . DS . 'models' . DS . 'driver_search_controls.php');
?>

<script src="/colorbox/colorbox/jquery.colorbox-min.js"></script>

<link rel="stylesheet" href="/colorbox/example1/colorbox.css" />
<tr style="background: #f7f7f7; border: none;">
	<td colspan="3" style="text-align: center; color: red;">Red Flags</td>
</tr>
<tr>
	<th style="font-size: 10px;">
        <?php echo JText::_('Brake'); ?>
    </th>
	<th style="font-size: 10px;">
        <?php echo JText::_('Accel'); ?>
    </th>
	<th style="font-size: 10px;">
        <?php echo JText::_('Turns'); ?>
    </th>
    <th>
        &nbsp;
    </th>
	<th style="width: 200px;">
        <?php echo JText::_('Vehicle Name'); ?>
    </th>
    <th style="width: 150px;">
		<?php echo JText::_('Trip Start'); ?>
	</th>
	<th style="width: 150px;">
		<?php echo JText::_('Trip End'); ?>
	</th>
	<th>
		<?php echo JText::_('Miles Driven'); ?>
	</th>
    <th>
        <?php echo JText::_('Assigned Driver'); ?>
    </th>
</tr>