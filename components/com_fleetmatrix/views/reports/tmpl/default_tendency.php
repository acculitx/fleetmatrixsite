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

<h2>Reports - Driver Tendency Report</h2>
<br />
<div class="tempbg">
<form action="<?php echo JRoute::_($this->getRoute()); ?>" method="post" name="adminForm">

	<table class="adminlist sortable" >
		<thead><?php echo $this->loadTemplate('tendency_head');?></thead>
		<tfoot><?php echo $this->loadTemplate('tendency_foot');?></tfoot>
		<tbody><?php echo $this->loadTemplate('tendency_body');?></tbody>
	</table>
</form>
</div>
<br />

