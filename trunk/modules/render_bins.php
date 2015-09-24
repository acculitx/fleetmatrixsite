<?php
defined('_JEXEC') or die;

ini_set('display_errors', 1);
error_reporting(E_ALL);

include "libchart/libchart/classes/libchart.php";

$chart = new LineChart(650, 200);

$db = JFactory::getDBO();
$query = <<<QUERY
SELECT bin_x, bin_y
FROM fleet_acceleration
WHERE bin_x
AND bin_y
ORDER BY date DESC
LIMIT 3600
QUERY;
$db->setQuery($query);

$serie1 = new XYDataSet();
$serie2 = new XYDataSet();

$xbins = array(
        0,0,0,0,0,0,0,0,0,0,
        0,0,0,0,0,0,0,0,0,0,
        0,0,0,0,0,0,0,0,0,0,
        0
);

$ybins = array(
        0,0,0,0,0,0,0,0,0,0,
        0,0,0,0,0,0,0,0,0,0,
        0,0,0,0,0,0,0,0,0,0,
        0
);

foreach ($db->loadAssocList() as $row) {
    if ($row['bin_x']) {
        $xbins[$row['bin_x']-1]++;
    }
    if ($row['bin_y']) {
        $ybins[$row['bin_y']-1]++;
    }
}

for ($x=0; $x<31; $x++) {
    $serie1->addPoint(new Point($x+1, $xbins[$x]));
    $serie2->addPoint(new Point($x+1, $ybins[$x]));
}

$dataSet = new XYSeriesDataSet();
$dataSet->addSerie("X", $serie1);
$dataSet->addSerie("Y", $serie2);
$chart->setDataSet($dataSet);

$chart->setTitle("3600 Data Points");
//$chart->render("/home/hddev3/public_html/images/render_bins.png");
?>
<div style="text-align: center">
<img src="data:image/png;base64,<?php
$name = tempnam('.', '.png');
$chart->render($name);
echo base64_encode(file_get_contents($name)); 
unlink($name);
?>">
</div>

