<?php
$mysql_host = "localhost";
$mysql_user = "webserver";
$mysql_password = "fleetmatrixdbpassword";
$mysql_database = "fleetmatrix_test";

// Connecting, selecting database
$link = mysql_connect($mysql_host, $mysql_user, $mysql_password)
    or die('Could not connect: ' . mysql_error());
mysql_select_db($mysql_database) or die('Could not select database');
mysql_set_charset('utf8',$link);

$company = $_GET['c'];
$group = $_GET['g'];
$driver = $_GET['d'];
$slice = $_GET['s'];
$t0 = $_GET['t0'];
$t1 = $_GET['t1'];

$columns = "";
$where = "";
$groupby = "";

if (isset($driver)) {
  $columns .= " driver.id as driver_id, driver.name as driver_name, ";
  $groupby .= " driver.id, driver.name, ";
  if ($driver != "*") {
    $where .= " AND driver.id=$driver ";
  }
}

if (isset($group)) {
  $columns .= " dgroup.id as group_id, dgroup.name as group_name, ";
  $groupby .= " dgroup.id, dgroup.name, ";
  if ($group != "*") {
    $where .= " AND dgroup.id=$group ";
  }
}

if (isset($company)) {
  $columns .= " company.id as company_id, company.name as company_name, ";
  $groupby .= " company.id, company.name, ";
  if ($company != "*") {
    $where .= " AND company.id=$company "; 
  }
}

if (isset($t0)) {
  $where .= " AND `date` >= \"$t0\" ";
}

if (isset($t1)) {
  $where .= " AND `date` <= \"$t1\" ";
}

$query = "
SELECT 
  $columns
  DATE_FORMAT(`date`,'%Y-%v') as month,
  round(avg(accel),2) as accel,
  round(avg(decel),2) as decel,
  round(avg(hard_turns),2) as hard_turns
FROM fleet_moving_daily_score as score
LEFT JOIN giqwm_fleet_driver as driver on score.driver_id = driver.id
LEFT JOIN giqwm_fleet_entity as dgroup on dgroup.id = driver.entity_id
LEFT JOIN giqwm_fleet_entity as company on dgroup.parent_entity_id = company.id
WHERE driver.visible AND window=365
$where
GROUP BY $groupby month
order by $groupby month
";


// Performing SQL query
file_put_contents ("/tmp/mysqllog.txt", $query . "\n", FILE_APPEND);
$result = mysql_query($query) or die('Query failed: ' . mysql_error());

#echo "<pre>";

#echo ($query);

// First line has the headers.
$num_fields = mysql_num_fields($result);
for($i = 0; $i < $num_fields; $i++) {
    $field_info = mysql_fetch_field($result, $i);
    echo "{$field_info->name}";
    if ($i < $num_fields - 1) {
       echo "\t";
    }
}
echo "\n";

// Print rows.
while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
    $firstCol = 1;
    foreach ($line as $col_value) {
        if (!$firstCol) { echo "\t"; }
        echo "$col_value";
        $firstCol = 0;
    }
    echo "\n";
}

// Free resultset
mysql_free_result($result);

// Closing connection
mysql_close($link);
?>

