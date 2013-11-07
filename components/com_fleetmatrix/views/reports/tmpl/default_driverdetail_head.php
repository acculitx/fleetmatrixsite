<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');


include(JPATH_COMPONENT . DS . 'models' . DS . 'search_controls.php');

?>

<tr class = "sortable">
    <th>
        <?php echo JText::_('Driver Name'); ?>
    </th>
	<th>
        <?php echo JText::_('Group Name'); ?>
    </th>
    <th>
        <?php echo JText::_('Overall score'); ?>
    </th>
    <th>
        <?php echo JText::_('Accelerations score'); ?>
    </th>
    <th>
        <?php echo JText::_('Decelerations score'); ?>
    </th>
    <th>
        <?php echo JText::_('Hard turns score'); ?>
    </th>
    <th>
        <?php echo JText::_('Speed by street score'); ?>
    </th>
    <th>
		<?php echo JText::_('Miles Driven'); ?>
	</th>
	<th>
		<?php echo JText::_('Number of trips'); ?>
	</th>
</tr>