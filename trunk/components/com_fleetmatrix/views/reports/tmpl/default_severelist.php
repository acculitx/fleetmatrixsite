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

<h2>Severe Events List</h2>
<br />
<div class="tempbg">
<form action="<?php echo JRoute::_($this->getRoute()); ?>" method="post" name="adminForm">
	<table class="adminlist sortable">
		<thead><?php echo $this->loadTemplate('severelist_head');?></thead>
		<tbody><?php echo $this->loadTemplate('severelist_body');?></tbody>
		<tfoot><?php echo $this->loadTemplate('severelist_foot');?></tfoot>
	</table>
</form>
</div>

<br />

