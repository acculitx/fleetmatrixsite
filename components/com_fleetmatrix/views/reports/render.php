<?php
defined('_JEXEC') or die;

ini_set('display_errors', 1);
error_reporting(E_ALL);

include JPATH_BASE . "/modules/fleet/libchart/libchart/classes/libchart.php";

function rgb2html($r, $g=-1, $b=-1)
{
    if (is_array($r) && sizeof($r) == 3)
        list($r, $g, $b) = $r;

    $r = intval($r); $g = intval($g);
    $b = intval($b);

    $r = dechex($r<0?0:($r>255?255:$r));
    $g = dechex($g<0?0:($g>255?255:$g));
    $b = dechex($b<0?0:($b>255?255:$b));

    $color = (strlen($r) < 2?'0':'').$r;
    $color .= (strlen($g) < 2?'0':'').$g;
    $color .= (strlen($b) < 2?'0':'').$b;
    return '#'.$color;
}

function reduce_array_elems($a) {
    $length = sizeof($a);
    if ($length<=24) {
        return $a;
    }

    while ($length>24) {
        $length /= 2;
    }

    $skip = (sizeof($a) / $length) -1;

    $c = 0;
    $ret = array();
    for ($x=0; $x<sizeof($a); $x++) {
        if ($c != 0) {
            $ret[] = '';
        } else {
            $ret[] = $a[$x];
        }
        $c += 1;
        if ($c > $skip) {
            $c = 0;
        }
    }

    return $ret;

}

function renderLineChart($value_arrays, $labels=NULL, $title='', $xlabels=NULL) {
    $chart = new LineChart(1000, 400);
    $xlabels = reduce_array_elems($xlabels);

    $dataSet = new XYSeriesDataSet();

    $lbls = array();
    for ($va=0; $va<sizeof($value_arrays); $va++) {
        $value_array = $value_arrays[$va];
        $ds = new XYDataSet();
        if (sizeof($value_array)) {
            for ($x=0; $x<sizeof($value_array); $x++) {
                if ($xlabels) {
                    $label = $xlabels[$x];
                } else {
                    $label = '';//$label = $x + 1;
                }
                $val = $value_array[$x];
                if ($val > 10) { $val = 10; }
                $ds->addPoint(new Point($label, $val));
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
        $lbls[] = $label;
    }

    $chart->setDataSet($dataSet);

    $chart->setTitle($title);

    $name = tempnam('/tmp', '.png');
    $chart->render($name);
    $image = base64_encode(file_get_contents($name));
    unlink($name);

    echo '<div id="grapharea">';
    echo '<div id="graph" style="text-align: center"><img src="data:image/png;base64,';
    echo $image;
    echo '"></div>';

    $cc = 0;
    $colors = array(
    rgb2html(172, 172, 210),
    rgb2html(2, 78, 0),
    rgb2html(148, 170, 36),
    rgb2html(233, 191, 49),
    rgb2html(240, 127, 41),
    rgb2html(243, 63, 34),
    rgb2html(190, 71, 47),
    rgb2html(135, 81, 60),
    rgb2html(128, 78, 162),
    rgb2html(121, 75, 255),
    rgb2html(142, 165, 250),
    rgb2html(162, 254, 239),
    rgb2html(137, 240, 166),
    rgb2html(104, 221, 71),
    rgb2html(98, 174, 35),
    rgb2html(93, 129, 1)
);

    echo '<div class="graphnames">';
    for ($x=0; $x<sizeof($lbls); $x++) {
        printf('<div class="graphblock" style="background-color: %s;"></div>&nbsp;%s<br>', $colors[$cc], $lbls[$x]);
        $cc++;
        if ($cc>=sizeof($colors)) { $cc = 0; }
    }
    echo '</div>';
    echo '</div>';
}
