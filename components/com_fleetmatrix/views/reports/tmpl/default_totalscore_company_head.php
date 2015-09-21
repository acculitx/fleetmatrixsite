<?php
 $document = JFactory::getDocument();
 $document->addScript('sorttable.js');
?>

<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

include(JPATH_COMPONENT . DS . 'models' . DS . 'search_controls_7day.php');
include(JPATH_COMPONENT . DS . 'models' . DS . 'calendarCreator.php');

//$trend = "accel";
?>

<tr>
    <th title="Click to sort by this column">
		<?php echo JText::_('Company/Group Name'); ?>
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
</tr>