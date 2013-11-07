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

<h2>Reports - Driver Trip Report</h2>
<br />

<form action="<?php echo JRoute::_($this->getRoute()); ?>" method="post" name="adminForm">

	<table class="adminlist sortable">
		<thead><?php echo $this->loadTemplate('driver_head');?></thead>
		<tfoot><?php echo $this->loadTemplate('driver_foot');?></tfoot>
		<tbody><?php echo $this->loadTemplate('driver_body');?></tbody>
	</table>
</form>

