<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

include(JPATH_COMPONENT . DS . 'models' . DS . 'search_controls.php');
?>
<tr>
	<th>
        <?php echo JText::_('Trip Id'); ?>
    </th>
    <th>
		<?php echo JText::_('Trip Start'); ?>
	</th>
	<th>
		<?php echo JText::_('Trip End'); ?>
	</th>
	<th>
		<?php echo JText::_('Miles Driven'); ?>
	</th>
    <th>
        <?php echo JText::_('Assigned Driver'); ?>
    </th>
</tr>