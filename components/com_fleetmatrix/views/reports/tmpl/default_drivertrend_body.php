<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

    ini_set('display_errors', 1);
    error_reporting(E_ALL);

require_once(JPATH_COMPONENT . DS . 'views' . DS . 'reports' . DS . 'render.php');

$values = array();
$names = array();
$xlabels = array();

$driver = JRequest::getInt('driver', 0);
$trend = JRequest::getCmd('trend', $driver?'all':'overall');

if ($trend == 'hard_turns') {
    $context = $trend;
} else {
    $context = $trend . '_score';
}

$items = $this->items;
if ($trend == 'all' && $this->items) {
    $items = array(
        'overall_score' => array_shift(array_values($this->items))->overall_score,
        'accel_score' => array_shift(array_values($this->items))->accel_score,
        'decel_score' => array_shift(array_values($this->items))->decel_score,
        'hard_turns' => array_shift(array_values($this->items))->hard_turns,
        'speed_score' => array_shift(array_values($this->items))->speed_score,
    );
}

foreach($items as $i => $item) {
    if ($trend=='all') {
        $names[] = ucfirst(str_replace('hard_turns', 'Turns', str_replace('_score','',$i)));
        $a = array();
        foreach($item as $score) {
            $a[] = (is_null($score->value)) ? 0 : $score->value;
            $xl = '';
            if ($i == 'overall') {
                $xl = $score->date;
            }
            if (!$xl && $score->date) {
                $xl = $score->date;
            }
            $xlabels[] = $xl;
        }
        $values[] = $a;
    } else {
        $a = array();
        foreach($item->$context as $score) {
            $a[] = (is_null($score->value)) ? 0 : $score->value;
            $xl = '';
            if ($i == 0) {
                $xl = $score->date;
            }
            if (!$xl && $score->date) {
                $xl = $score->date;
            }
            $xlabels[] = $xl;
        }

        $values[] = $a;
        $names[] = $item->driver_name;
    }
}
if (!sizeof($values)) {
    $values[] = array(0);
}

$GLOBALS['graph_max'] = 10;
$GLOBALS['graph_min'] = 0;
$GLOBALS['graph_tics'] = 1;
renderLineChart($values, $names, "Trends over time", $xlabels);
?>
