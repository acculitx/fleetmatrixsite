<?php
 $document = JFactory::getDocument();
 $document->addScript('sorttable.js');
?>

<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>

<script src="colorbox/colorbox/jquery.colorbox-min.js"></script>
 
<link rel="stylesheet" href="colorbox/example1/colorbox.css" />
 

<tr>
    <th title="Click to sort by this column">
		<?php echo JText::_('Driver Name'); ?>
	</th>
	<th title="Click to sort by this column">
		<?php echo JText::_('Time Stamp'); ?>
	</th>
	<th title="Click to sort by this column">
		<?php echo JText::_('Event type'); ?>
	</th>
	<th title="Click to sort by this column">
		<?php echo JText::_('Map Link'); ?>
	</th>
</tr>