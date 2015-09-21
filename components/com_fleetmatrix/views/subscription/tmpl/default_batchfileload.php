<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

// load tooltip behavior
JHtml::_('behavior.tooltip');

include(JPATH_COMPONENT . DS . 'models' . DS . 'batchFileLoadJS.php');

?>

<h2>Subscription - Batch File Load</h2>
<br />

        <?php echo $this->loadTemplate('batchfileload_body');?>

<br />