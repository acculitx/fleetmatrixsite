<?php
$mysql_host     = "localhost";
$mysql_user     = "webserver";
$mysql_password = "fleetmatrixdbpassword";
$mysql_database = "fleetmatrix_test";

// Connecting, selecting database
$link = mysql_connect($mysql_host, $mysql_user, $mysql_password) or die('Could not connect: ' . mysql_error());
mysql_select_db($mysql_database) or die('Could not select database');
mysql_set_charset('utf8', $link);

function param($name, $defaultValue)
{
  $p = isset($_GET[$name]) ? $_GET[$name] : null;
  if (!$p || empty($p))
    $p = $defaultValue;
  return $p;
}

// Read query params.
$company = param('c', "");
$group   = param('g', "");
$driver  = param('d', "");
$slice   = param('s', "");
$t0      = param('t0', "");
$t1      = param('t1', "");
$df      = param('df', "");
$ds      = param('ds', "");


// Set default source.
if (!$ds)
  $ds = "fleet_moving_daily_score";

// Test values.
//$t0 = " DATE_SUB(NOW(), INTERVAL 1 MONTH) ";
//$company = "29";
//$group = "*";
//$group = "49";
//$driver = "*";
// $company = "*";
//$t0 = "2015-04-01";
//$t1 = "2015-09-01";
//$t0 = " DATE_SUB(NOW(), INTERVAL 6 DAY) ";
//$t1 = " NOW() ";

$c = "*";

// Schemas of SQL tables used in this report.
$table = $ds;
if ($ds == "total") {
  $aggregate_columns = array(
    "Total",
    "Aggressive",
    "Distraction"
  );
} else if ($ds == "vigilance") {
  $table             = "vigilance";
  $aggregate_columns = array(
    "Turn Hard",
    "Turn Severe",
    "Accel Hard",
    "Accel Severe",
    "Brake Hard",
    "Brake Severe"
  );
} else if ($ds == "bellcurve") {
   $table = "bellcurve";
   $aggregate_columns = array(
      Acceleration,
      Deceleration,
      Turns 
  );
} else {
  $table             = "driver_trend";
  $aggregate_columns = array(
      Acceleration,
      Deceleration,
      Turns 
  );
}

// Every table has a date column.
$date_column = "date";

// Default timeslice is daily.
$timeslice = $df;
if (!$timeslice) {
  $timeslice = "%Y-%b-%d";
}

$request_columns = "";
$where           = "";
$array_groupby   = array();
$array_alias     = array();
$alias_columns   = "";

if ($t0 != "") {
  $where .= " AND $date_column >= $t0 ";
}

if ($t1 != "") {
  $where .= " AND $date_column <= $t1 ";
}

// Set up columns for drill down interface (Company -> Group -> Driver)
if ($driver != "") {
  $request_columns .= " driver.id as driver_id, driver.name as driver_name, ";
  
  array_push($array_alias, "driver_id", "driver_name");
  array_push($array_groupby, "driver.id", "driver.name");
  
  if ($driver != "*") {
    $where .= " AND driver.id=$driver ";
  }
}

if ($group != "") {
  $request_columns .= " dgroup.id as group_id, dgroup.name as group_name, ";
  
  if (count($array_alias) == 0)
    array_push($array_alias, "group_id", "group_name");
  
  array_push($array_groupby, "dgroup.id", "dgroup.name");
  
  if ($group != "*") {
    $where .= " AND dgroup.id=$group ";
  }
}

if ($company != "") {
  $request_columns .= " company.id as company_id, company.name as company_name, ";
  
  if (count($array_alias) == 0)
    array_push($array_alias, "company_id", "company_name");
  
  array_push($array_groupby, "company.id", "company.name");
  
  if ($company != "*") {
    $where .= " AND company.id=$company ";
  }
}

$groupby_columns = join($array_groupby, ",");
$alias_columns   = join($array_alias, ",");

// First query:  get the list of dates contained in the date column.
// The query will transpose these dates, making a column for each time slice.
function getDates($table, $date_column, $timeslice, $where)
{
  $query = "
select distinct DATE_FORMAT($date_column, \"$timeslice\") as date_column
from $table
     LEFT JOIN giqwm_fleet_driver as driver on $table.id = driver.id
     LEFT JOIN giqwm_fleet_entity as dgroup on dgroup.id = driver.entity_id
     LEFT JOIN giqwm_fleet_entity as company on dgroup.parent_entity_id = company.id
     where driver.visible $where
order by $date_column
";
  
  //  echo "<pre>" . $query;
  //  return;
  
  // Performing SQL query
  // file_put_contents("/tmp/mysqllog.txt", $query . "\n", FILE_APPEND);
  $result = mysql_query($query) or die('Query failed: ' . mysql_error());
  
  $cols = array();
  while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
    foreach ($line as $column) {
      array_push($cols, $column);
    }
  }
  return $cols;
}

$dateColumns = getDates($table, $date_column, $timeslice, $where);

// Second query:  do the transpose, average over values in each time slice.
$query = "";

# Each aggregate column needs its own transpose query.
foreach ($aggregate_columns as $aggcol) {
  
  $subquery = "";
  $columns  = "";
  $subquery = "select ";
  if ($alias_columns != "")
    $subquery .= " $alias_columns,";
  
  #  $subquery .= " \"$aggcol\" ";
  foreach ($dateColumns as $column) {
    if ($columns != "") {
      $columns .= ",\n";
    }
    $columns .= "MAX(CASE WHEN time_period = \"$column\" THEN round(`$aggcol`,2) END) as \"$column\"";
  }
  $subquery .= $columns;
  $subquery .= ", \"$aggcol\" ";
  
  $subquery .= " from
    (select $request_columns
      DATE_FORMAT($date_column, \"$timeslice\") as time_period,
      avg(`$aggcol`) as `$aggcol`
     from $table
     LEFT JOIN giqwm_fleet_driver as driver on $table.id = driver.id
     LEFT JOIN giqwm_fleet_entity as dgroup on dgroup.id = driver.entity_id
     LEFT JOIN giqwm_fleet_entity as company on dgroup.parent_entity_id = company.id
     where driver.visible $where
     group by time_period
     ";
  if ($groupby_columns != "")
    $subquery .= " , $groupby_columns ";
  $subquery .= "
    ) as `table_$aggcol` ";
  if ($alias_columns != "")
    $subquery .= " group by $alias_columns ";
  
  if ($query != "") {
    $query .= " UNION \n";
  }
  $query .= $subquery;
  
  $subquery = "";
  $columns  = "";
}

// Construct final query from the series.
$finalquery = "select ";
$finalquery .= " union_query.* from ($query) as union_query ";
if ($alias_columns != "")
  $finalquery .= " order by $alias_columns ";

//echo $query;
//return;



// Perform SQL query
//file_put_contents("/tmp/mysqllog.txt", $finalquery . "\n", FILE_APPEND);
$result = mysql_query($finalquery) or die('Query failed: ' . mysql_error());

// First line has the headers.
$num_fields = mysql_num_fields($result);
for ($i = 0; $i < $num_fields; $i++) {
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
    if (!$firstCol) {
      echo "\t";
    }
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
