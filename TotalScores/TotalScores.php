<?php
$mysql_host     = "localhost";
$mysql_user = "webserver";
$mysql_password = "fleetmatrixdbpassword";
//$mysql_user     = "root";
//$mysql_password = "sectrends";
$mysql_database = "fleetmatrix_test";

// Connecting, selecting database
$link = mysql_connect($mysql_host, $mysql_user, $mysql_password) or die('Could not connect: ' . mysql_error());
mysql_select_db($mysql_database) or die('Could not select database');
mysql_set_charset('utf8', $link);

// Read query params.
$company = $_GET['c'];
$group   = $_GET['g'];
$driver  = $_GET['d'];
$slice   = $_GET['s'];
$t0      = $_GET['t0'];
$t1      = $_GET['t1'];

// Test values.
//$company = "29";
//$group = "*";
//$group = "49";
//$driver = "*";
// $company = "*";
//$t0 = "2015-04-01";
//$t1 = "2015-09-01";

// Define data source.
$table             = "fleet_daily_total_score";
$aggregate_columns = array(
    "totalScore",
    "aggressiveScore",
  "distractionScore"
);
$date_column       = "date";
$timeslice         = "%Y-%m";

$request_columns = "";
$where           = "";
$array_groupby   = array();
$array_alias     = array();
$alias_columns   = "";

if (isset($t0)) {
  $where .= " AND `date` >= \"$t0\" ";
}

if (isset($t1)) {
  $where .= " AND `date` <= \"$t1\" ";
}

if (isset($driver)) {
  $request_columns .= " driver.id as driver_id, driver.name as driver_name, ";
  
  array_push($array_alias, "driver_id", "driver_name");
  array_push($array_groupby, "driver.id", "driver.name");
  
  if ($driver != "*") {
    $where .= " AND driver.id=$driver ";
  }
}

if (isset($group)) {
  $request_columns .= " dgroup.id as group_id, dgroup.name as group_name, ";
  
  if (count($array_alias) == 0)
    array_push($array_alias, "group_id", "group_name");
  
  array_push($array_groupby, "dgroup.id", "dgroup.name");
  
  if ($group != "*") {
    $where .= " AND dgroup.id=$group ";
  }
}

if (isset($company)) {
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


function getDates($table, $date_column, $timeslice, $where)
{
  $query = "
select distinct DATE_FORMAT($date_column, \"$timeslice\") as date
from $table
     LEFT JOIN giqwm_fleet_driver as driver on $table.driver_id = driver.id
     LEFT JOIN giqwm_fleet_entity as dgroup on dgroup.id = driver.entity_id
     LEFT JOIN giqwm_fleet_entity as company on dgroup.parent_entity_id = company.id
     where driver.visible $where
order by DATE_FORMAT($date_column, \"$timeslice\")
";
  
  // Performing SQL query
  file_put_contents("/tmp/mysqllog.txt", $query . "\n", FILE_APPEND);
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

/* ----
echo "request_columns=[$request_columns], sizeof=" . sizeof($request_columns);
echo "groupby_columns=[$groupby_columns], sizeof=" . sizeof($groupby_columns);
echo "alias_columns=[$alias_columns], sizeof=" . sizeof($alias_columns);
---  */

$query    = "";

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
    $columns .= "MAX(CASE WHEN time_period = \"$column\" THEN $aggcol END) as \"$column\"";
  }
  $subquery .= $columns;
  $subquery .= ", \"$aggcol\" ";

  $subquery .= " from
    (select $request_columns
      DATE_FORMAT($date_column, \"$timeslice\") as time_period,
      avg($aggcol) as $aggcol
     from $table
     LEFT JOIN giqwm_fleet_driver as driver on $table.driver_id = driver.id
     LEFT JOIN giqwm_fleet_entity as dgroup on dgroup.id = driver.entity_id
     LEFT JOIN giqwm_fleet_entity as company on dgroup.parent_entity_id = company.id
     where driver.visible $where
     group by time_period
     ";
  if ($groupby_columns != "")
    $subquery .= " , $groupby_columns ";
  $subquery .= "
    ) as table_$aggcol ";
  if ($alias_columns != "")
    $subquery .= " group by $alias_columns ";
  
  if ($query != "") {
    $query .= " UNION \n";
  }
  $query .= $subquery;
  
  $subquery = "";
  $columns  = "";
}


$finalquery = "select ";
//if ($alias_columns != "")
//  $finalquery .= " $alias_columns, ";
$finalquery .= " union_query.* from ($query) as union_query ";
if ($alias_columns != "")
  $finalquery .= " order by $alias_columns ";

#echo $query;
##return;


// Perform SQL query
file_put_contents("/tmp/mysqllog.txt", $finalquery . "\n", FILE_APPEND);
$result = mysql_query($finalquery) or die('Query failed: ' . mysql_error());

#echo "<pre>";

#echo ($query);

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
