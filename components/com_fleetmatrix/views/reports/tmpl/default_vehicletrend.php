<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

// load tooltip behavior
JHtml::_('behavior.tooltip');

?>

<h2>Reports - Vehicle MPG Trend Report</h2>
<br />
<div class="tempbg">
<form action="<?php echo JRoute::_($this->getRoute()); ?>" method="post" name="adminForm">

	<table class="adminlist">
		<thead><?php echo $this->loadTemplate('vehicletrend_head');?></thead>
		<tfoot><?php echo $this->loadTemplate('vehicletrend_foot');?></tfoot>
		<tbody><?php echo $this->loadTemplate('vehicletrend_body');?></tbody>
	</table>
</form>
</div>
<br />
