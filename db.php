<?php

function dbInit()
{
  $mysql_host     = "localhost";
  $mysql_user = "webserver";
  $mysql_password = "fleetmatrixdbpassword";
  //$mysql_database = "fleetmatrix_celery";
  $mysql_database = "fleetmatrix_test";
  
  // Connecting, selecting database
  $connection = new PDO("mysql:host=$mysql_host;dbname=$mysql_database;charset=utf8", $mysql_user, $mysql_password);

  //mysql_set_charset('utf8', $connection);

  return $connection;
}

function dbDone($connection)
{
  // Closing connection
//  mysql_close($connection);
}
?>
