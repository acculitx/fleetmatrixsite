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

<h2>Reports - Total Score Report</h2>
<br />

<div class="tempbg">
<form action="<?php echo JRoute::_($this->getRoute()); ?>" id="adminForm" method="post" name="adminForm">
	<table id="totalscore_company_table" class="adminlist sortable">
		<thead><?php echo $this->loadTemplate('totalscore_company_head');?></thead>
		<tbody><?php echo $this->loadTemplate('totalscore_company_body');?></tbody>
	</table>
</form>
</div>

<br />

<div class="tempbg">
<form action="<?php echo JRoute::_($this->getRoute()); ?>" id="adminForm" method="post" name="adminForm">
	<table id="totalscore_table" class="adminlist sortable">
		<thead><?php echo $this->loadTemplate('totalscore_head');?></thead>
		<tfoot><?php echo $this->loadTemplate('totalscore_foot');?></tfoot>
		<tbody><?php echo $this->loadTemplate('totalscore_body');?></tbody>
	</table>
</form>
</div>

<br />

