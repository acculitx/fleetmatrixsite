<?php
 $document = JFactory::getDocument();
 $document->addScript('sorttable.js');
?>

<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

//$trend = "accel";
?>

 

<tr>
    <th title="Click to sort by this column">
		<?php echo JText::_('Driver Name'); ?>
	</th>
	<th title="Click to sort by this column">
		<?php echo JText::_('Total Score'); ?>
	</th>
	<th title="Click to sort by this column">
		<?php echo JText::_('Aggressive Score'); ?>
	</th>
	<th title="Click to sort by this column">
		<?php echo JText::_('Distraction Score'); ?>
	</th>
	<th title="Click to sort by this column">
		<?php echo JText::_('Severe Events'); ?>
	</th>
</tr>