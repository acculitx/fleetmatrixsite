<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

// load tooltip behavior
JHtml::_('behavior.tooltip');

?>

<h2>Reports - Driver Trend Report</h2>
<br />

<form action="<?php echo JRoute::_($this->getRoute()); ?>" method="post" name="adminForm">

	<table class="adminlist">
		<thead><?php echo $this->loadTemplate('drivertrend_head');?></thead>
		<tfoot><?php echo $this->loadTemplate('drivertrend_foot');?></tfoot>
		<tbody><?php echo $this->loadTemplate('drivertrend_body');?></tbody>
	</table>
</form>

<br />

