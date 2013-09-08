<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

$trend = JRequest::getCmd('trend', 'accel');

include(JPATH_COMPONENT . DS . 'models' . DS . 'vehicle_search_controls.php');
?>
