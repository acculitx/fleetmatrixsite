<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

include(JPATH_COMPONENT . DS . 'models' . DS . 'driver_search_controls.php');
?>

<script src="/colorbox/colorbox/jquery.colorbox-min.js"></script>

<link rel="stylesheet" href="/colorbox/example1/colorbox.css" />
<tr>
    <th>
        &nbsp;
    </th>
	<th>
        <?php echo JText::_('Vehicle Name'); ?>
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