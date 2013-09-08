<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

    ini_set('display_errors', 1);
    error_reporting(E_ALL);

require_once(JPATH_COMPONENT . DS . 'views' . DS . 'reports' . DS . 'render.php');

$values = array();
$names = array();
$xlabels = array();

foreach($this->items as $i => $item) {
    $a = array();
    foreach($item->mpg as $score) {
        $a[] = $score->value;
        if ($i == 0) {
            $xlabels[] = $score->date;
        }
    }

    $values[] = $a;
    $names[] = $item->vehicle_name;
}
if (!sizeof($values)) {
    $values[] = array(0);
}


renderLineChart($values, $names, "Trends over time", $xlabels);
?>
