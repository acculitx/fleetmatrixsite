<?php

include 'db.php';

$conn = dbInit();
$query = composeQuery();
echo "<pre>";
// echo ($query);
executeQuery($query);
dbDone($conn);


function param($name, $defaultValue) {
  $p = isset($_GET[$name]) ? $_GET[$name] : null; 
  if (!$p || empty($p))
     $p = $defaultValue;
  return $p;
}

function composeQuery() {
  $table = param('table', 'fleet_trip');
  $start = param('start', 0);
  $end = param('end', 10);
  $query = "SELECT * FROM $table LIMIT $start, $end";
  return $query;
}

function executeQuery($query) {
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
