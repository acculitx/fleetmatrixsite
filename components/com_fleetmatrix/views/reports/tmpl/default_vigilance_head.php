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




<!--<div style="height:30px; width:71%; background-color:#FCE700; padding-left:283px;">--------------------------------Event counts per 100 miles --------------------------------</div>

-->

<div style="height:30px; width:auto; background-color:#FCE700; padding-left:400px;">--------------------------------Event counts per 100 miles --------------------------------</div>

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
		<?php echo JText::_('Trips'); ?>
	</th>
</tr>
<!--
<tr style="background-color:#FCE700;">
    <td style="background-color:#FCE700;">&nbsp;
	
	</td>
    <td style="background-color:#FCE700;">&nbsp;

	</td >
    <td style="background-color:#FCE700;" >&nbsp;
		
	</td>
	
	<td colspan="8" style = "text-align:center; background-color:#FCE700;" >
	
	</td>
    <td style="background-color:#FCE700;">&nbsp;

	</td>
    <td style="background-color:#FCE700;">&nbsp;
	
	</td>
</tr>-->
