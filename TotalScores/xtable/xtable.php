<?php

include 'db.php';

$conn  = dbInit();
$query = composeQuery();
#echo "<pre>";
#echo ($query);
executeQuery($query);
dbDone($conn);


function param($name, $defaultValue)
{
  $p = isset($_GET[$name]) ? $_GET[$name] : null;
  if (!$p || empty($p))
    $p = $defaultValue;
  return $p;
}

function composeQuery()
{
  $table     = param('table', 'trips');
  $start_row = param('start_row', 0);
  $row_count = param('row_count', 10);
  
  $sort       = "";
  $sortParams = param("sort", Array());
  foreach ($sortParams as $sortSpec) {
    if ($sort == "")
      $sort = " ORDER BY ";
    else
      $sort .= ", ";
    $sort .= rawurldecode($sortSpec);
  }
  
  $t0 = param("t0", "");
  if ($t0 == "")
    $t0 = "DATE_SUB(NOW(), INTERVAL 1 MONTH)";
  else
    $t0 = "'" . $t0 . "'";
  $t1 = param("t1", "");
  if ($t1 == "")
    $t1 = "NOW()";
  else
    $t1 = "'" . $t1 . "'";
  $where = " WHERE Date between $t0 AND $t1 ";
  
  $whereParams = param("where", Array());
  foreach ($whereParams as $whereSpec) {
    if ($where == "")
      $where = " WHERE ";
    else
      $where .= " AND ";
    $where .= rawurldecode($whereSpec);
  }
  
  
  $query = "SELECT * FROM $table $where $sort LIMIT $start_row, $row_count";
  return $query;
}

function executeQuery($query)
{
  // Perform SQL query
  // file_put_contents("/tmp/mysqllog.txt", $query . "\n", FILE_APPEND);
  $result = mysql_query($query) or die('Query failed: ' . mysql_error());
  
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
}

?>
