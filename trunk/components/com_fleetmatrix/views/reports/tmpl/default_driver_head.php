<?php
 $document = JFactory::getDocument();
 $document->addScript('sorttable.js');
?>
<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

include(JPATH_COMPONENT . DS . 'models' . DS . 'driver_search_controls.php');
?>

<script src="/colorbox/colorbox/jquery.colorbox-min.js"></script>

<link rel="stylesheet" href="/colorbox/example1/colorbox.css" />
 
<tr>
    <th title="Click to sort by this column">
		<?php echo JText::_('Driver Name'); ?>
	</th>
    <th title="Click to sort by this column">
		<?php echo JText::_('Company Name'); ?>
	</th>
    <th title="Click to sort by this column">
		<?php echo JText::_('Group Name'); ?>
	</th>
	<th title="Click to sort by this column">
		<?php echo JText::_('Trip Start'); ?>
	</th>
	<th title="Click to sort by this column">
		<?php echo JText::_('Accel Hard'); ?>
	</th>
	<th title="Click to sort by this column">
		<?php echo JText::_('Accel Severe'); ?>
	</th>
	<th title="Click to sort by this column">
		<?php echo JText::_('Brake Hard'); ?>
	</th>
	<th title="Click to sort by this column">
		<?php echo JText::_('Brake Severe'); ?>
	</th>
	<th title="Click to sort by this column">
		<?php echo JText::_('Turn Hard'); ?>
	</th>
	<th title="Click to sort by this column">
		<?php echo JText::_('Turn Severe'); ?>
	</th>
	<th title="Click to sort by this column">
		<?php echo JText::_('Speed Hard'); ?>
	</th>
	<th title="Click to sort by this column">
		<?php echo JText::_('Speed Severe'); ?>
	</th>
	<th title="Click to sort by this column">
		<?php echo JText::_('Miles Driven'); ?>
	</th>
	<th title="Click to sort by this column">
		<?php echo JText::_('Idle Time (min)'); ?>
	</th>
	<th title="Click to sort by this column">
		<?php echo JText::_('Map Link'); ?>
	</th>
</tr>