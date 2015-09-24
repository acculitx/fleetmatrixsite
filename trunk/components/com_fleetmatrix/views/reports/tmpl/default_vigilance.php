<?php
 $document = JFactory::getDocument();
 $document->addScript('sorttable.js');
 
?>


<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

// load tooltip behavior
JHtml::_('behavior.tooltip');



?>

<h2>Reports - Driver Vigilance Report (Events) </h2>
<br />
<div class="tempbg">
<form action="<?php echo JRoute::_($this->getRoute()); ?>" id="adminForm" method="post" name="adminForm">
	<table id="vigilance_table" class="adminlist sortable">
		<thead><?php echo $this->loadTemplate('vigilance_head');?></thead>
		<tfoot><?php echo $this->loadTemplate('vigilance_foot');?></tfoot>
		<tbody><?php echo $this->loadTemplate('vigilance_body');?></tbody>
	</table>
</form>
</div>
<br />

