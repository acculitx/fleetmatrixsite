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
		<thead><?php echo $this->loadTemplate('driverdetail_head');?></thead>
		<tfoot><?php echo $this->loadTemplate('driverdetail_foot');?></tfoot>
		<tbody><?php echo $this->loadTemplate('driverdetail_body');?></tbody>
	</table>
</form>

<br />

