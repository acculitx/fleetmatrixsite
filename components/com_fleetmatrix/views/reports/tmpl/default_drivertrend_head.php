<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

$driver = JRequest::getInt('driver', 0);
$trend = JRequest::getCmd('trend', $driver?'all':'overall');

?>

<select name="trend" id="trend">
    <option value="overall" <?php if($trend=='overall') {echo "selected";} ?>>Overall Score</option>
    <option value="accel" <?php if($trend=='accel') {echo "selected";} ?>>Acceleration</option>
    <option value="decel" <?php if($trend=='decel') {echo "selected";} ?>>Deceleration</option>
    <option value="hard_turns" <?php if($trend=='hard_turns') {echo "selected";} ?>>Hard Turns</option>
    <option value="speed" <?php if($trend=='speed') {echo "selected";} ?>>Speed by Street</option>
    <option value="all" <?php if ($trend=='all') { echo "selected"; } ?>>All Scores</option>
</select>

<?php
include(JPATH_COMPONENT . DS . 'models' . DS . 'driver_search_controls.php');
?>
