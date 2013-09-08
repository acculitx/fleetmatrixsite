<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

include(JPATH_COMPONENT . DS . 'models' . DS . 'search_controls.php');
?>
<tr>
	<th width="80">
        <?php echo JText::_('Vehicle Name'); ?>
    </th>
    <th>
		<?php echo JText::_('Group Name'); ?>
	</th>
	<th>
		<?php echo JText::_('MPG'); ?>
	</th>
	<th>
		<?php echo JText::_('Miles Driven'); ?>
	</th>
    <th>
        <?php echo JText::_('Number of Trips'); ?>
    </th>
    <th>
        <?php echo JText::_('Gallons Consumed'); ?>
    </th>
    <th>
        <?php echo JText::_('Time not connected'); ?>
    </th>
    <th>
        <?php echo JText::_('Number of disconnects'); ?>
    </th>
</tr>