<?php

function dbInit() {
  $mysql_host     = "localhost";
  //$mysql_user = "webserver";
  //$mysql_password = "fleetmatrixdbpassword";
  $mysql_user     = "root";
  $mysql_password = "sectrends";
  $mysql_database = "fleetmatrix_test";

  // Connecting, selecting database
  $connection = mysql_connect($mysql_host, $mysql_user, $mysql_password) or die('Could not connect: ' . mysql_error());
  mysql_select_db($mysql_database) or die('Could not select database');
  mysql_set_charset('utf8', $connection);

  return $connection;
}

function dbDone($connection) {
  // Closing connection
  mysql_close($connection);
}
?>
