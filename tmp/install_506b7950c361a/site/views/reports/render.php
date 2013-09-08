<?php
defined('_JEXEC') or die;

ini_set('display_errors', 1);
error_reporting(E_ALL);

include JPATH_BASE . "/modules/fleet/libchart/libchart/classes/libchart.php";

function renderLineChart($value_arrays, $labels=NULL, $title='', $xlabels=NULL) {
    $chart = new LineChart(1000, 400);

    $dataSet = new XYSeriesDataSet();

    for ($va=0; $va<sizeof($value_arrays); $va++) {
        $value_array = $value_arrays[$va];
        $ds = new XYDataSet();
        if (sizeof($value_array)) {
            for ($x=0; $x<sizeof($value_array); $x++) {
                if ($xlabels) {
                    $label = $xlabels[$x];
                } else {
                    $label = $x + 1;
                }
                $ds->addPoint(new Point($label, $value_array[$x]));
            }
        } else {
            // need at least one point or will except
            $ds->addPoint(new Point(1, 0));
        }
        if ($labels) {
            $label = $labels[$va];
        } else {
            $label = $va;
        }
        $dataSet->addSerie($label, $ds);
    }

    $chart->setDataSet($dataSet);

    $chart->setTitle($title);

    $name = tempnam('/tmp', '.png');
    $chart->render($name);
    $image = base64_encode(file_get_contents($name));
    unlink($name);

    echo '<div style="text-align: center"><img src="data:image/png;base64,';
    echo $image;
    echo '"></div>';
}
