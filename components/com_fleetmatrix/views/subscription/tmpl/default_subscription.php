<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

// load tooltip behavior
JHtml::_('behavior.tooltip');

?>

<form action="<?php echo JRoute::_($this->getRoute()); ?>" method="post" name="adminForm">

	<table class="adminlist">
		<thead><?php echo $this->loadTemplate('subscription_head');?></thead>
		<tfoot><?php echo $this->loadTemplate('subscription_foot');?></tfoot>
		<tbody><?php echo $this->loadTemplate('subscription_body');?></tbody>
	</table>
</form>
