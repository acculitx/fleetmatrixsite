
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

<h2>Reports - Vehicle Report</h2>
<br />

<form action="<?php echo JRoute::_($this->getRoute()); ?>" method="post" name="adminForm">

	<table class="adminlist sortable">
		<thead><?php echo $this->loadTemplate('vehicle_head');?></thead>
		<tfoot><?php echo $this->loadTemplate('vehicle_foot');?></tfoot>
		<tbody><?php echo $this->loadTemplate('vehicle_body');?></tbody>
	</table>
</form>

