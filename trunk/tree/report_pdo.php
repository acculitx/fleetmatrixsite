<?php
$mysql_host = "localhost";
$mysql_user = "webserver";
$mysql_password = "fleetmatrixdbpassword";
$mysql_database = "fleetmatrix_test";

// Connecting, selecting database
$db = new PDO("mysql:host=$mysql_host;dbname=$mysql_database;charset=utf8", $mysql_user, $mysql_password);

$storedProcedure = $_GET["proc"];
if (!isset($storedProcedure)) { $storedProcedure = "get_containers"; }
$parms = '';
if (isset($_GET["params"])) {
    $parms = $_GET["params"];
}

$sql = "CALL $storedProcedure ( $parms );";
//echo $sql;

// Performing SQL query
$result = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

header('Content-type: application/json');
echo json_encode($result);
//echo $sql;
?>
