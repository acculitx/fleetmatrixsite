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

<h2>Reports - Idle Time Report</h2>
<br />

<form action="<?php echo JRoute::_($this->getRoute()); ?>" id="adminForm" method="post" name="adminForm">
	<table id="idletime_table" class="adminlist sortable">
		<thead><?php echo $this->loadTemplate('idletime_head');?></thead>
		<tfoot><?php echo $this->loadTemplate('idletime_foot');?></tfoot>
		<tbody><?php echo $this->loadTemplate('idletime_body');?></tbody>
	</table>
</form>

<br />

