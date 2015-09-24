<?php

header("content-type:application/json");

$status = 'failure';
$driverErrorMessage = ''; // file level
$subErrorMessage = ''; // file level
$driverErrorList = array(); // line level
$subErroList = array(); // line level

//$LOG_FILE = ('D:\wamp21\www\batchfileloadlog.txt');
$LOG_FILE = ('/tmp/batchfileload.txt');
unlink($LOG_FILE);
$ERROR_MESSAGE = 'Note: the inputs before this have executed. Only this input and the ones after have not. Please adjust your input accordingly.';

if (!$link = mysqli_connect('205.251.141.114', 'webserver', 'fleetmatrixdbpassword'))  {
// if (!$link = mysqli_connect('localhost', 'root', 'root'))  {
    error_log('Could not connect to mysql'.PHP_EOL, 3, $LOG_FILE);
    $jsonResponse = constructJson($status, $driverErrorMessage, $subErrorMessage, $driverErrorList, $subErroList);
    echo json_encode($jsonResponse);
    mysqli_close($link);
    exit();
}

if (!mysqli_select_db($link, 'fleetmatrix_test')) {
    error_log('Could not select database'.PHP_EOL, 3, $LOG_FILE);
    $jsonResponse = constructJson($status, $driverErrorMessage, $subErrorMessage, $driverErrorList, $subErroList);
    echo json_encode($jsonResponse);
    mysqli_close($link);
    exit();
}



$driverList = array();
$subList = array();
/* get the content of the submitted files */
if (isset($_POST['driverList']) && isset($_POST['subList'])) { // both are specified
    $driverList = $_POST['driverList'];
    $subList = $_POST['subList'];
} 
else {
    error_log('missing a fil'.PHP_EOL, 3, $LOG_FILE);
    $jsonResponse = constructJson($status, $driverErrorMessage, $subErrorMessage, $driverErrorList, $subErroList);
    echo json_encode($jsonResponse);
    mysqli_close($link);
    exit();
}

/* scan driver create file content and check for potential errors */
scanDriverCreateFile($link, $driverList, $driverErrorMessage, $driverErrorList, $LOG_FILE);

/* scan sub create file content and check for potential errors */
scanSubCreateFile($link, $subList, $subErrorMessage, $subErroList, $LOG_FILE);

/* process the files if there is no errors */
if (empty($driverErrorMessage) && empty($subErrorMessage) && count($driverErrorList) == 0 && count($subErroList) == 0) {
    processDriverCreateFile($link, $driverList, $driverErrorMessage, $driverErrorList, $LOG_FILE, $ERROR_MESSAGE);
    processSubCreateFile($link, $subList, $subErrorMessage, $subErroList, $LOG_FILE, $ERROR_MESSAGE);
}

$jsonResponse = constructJson($status, $driverErrorMessage, $subErrorMessage, $driverErrorList, $subErroList);
echo json_encode($jsonResponse);
mysqli_close($link);
exit();


function scanDriverCreateFile(&$link, &$driverList, &$driverErrorMessage, &$driverErrorList, $LOG_FILE) {
    error_log("Scanning driver files...".PHP_EOL, 3, $LOG_FILE);
    
    if (!$driverList || count($driverList) == 0) {
        $driverErrorMessage = 'Driver file is empty';
        return;
    }
    
    // first, scan all lines and see if it will cause db errors
    foreach($driverList as $line) {
        // skip if line is empty
        if (count($line) == 0) { continue; }
        
        $inputArray = explode(',', $line);
        // check if all arguments are present.
        if (count($inputArray) != 4) {
            $error = constructLineLevelError($line, 'Argument count expected to be 4, but got ' . count($inputArray));
            array_push($driverErrorList, $error);
            continue;
        }
        
        $company = $inputArray[0];
        $group = $inputArray[1];
        $driver = $inputArray[2];
        $driver_license = $inputArray[3];
        
        // check if all required arguments (company, driver) are present
        if (empty($company) || empty($driver)) {
            $error = constructLineLevelError($line, 'Missing required arguments. Either Company or Driver is missing.');
            array_push($driverErrorList, $error);
            continue;
        }
        
        // check if a unique Company exists
        $company_trim = trim($company);
        $query_company = "select * from giqwm_fleet_entity where entity_type = 2 and name = '{$company_trim}'";
        error_log("Executing query".PHP_EOL . $query_company .PHP_EOL, 3, $LOG_FILE);
        
        $result_company = mysqli_query($link, $query_company);
        $num_rows_company = mysqli_num_rows($result_company);
        if ($num_rows_company == 0) {
            $error = constructLineLevelError($line, 'Company does not exist.');
            array_push($driverErrorList, $error);
            continue;
        }
        elseif ($num_rows_company > 1) {
            $error = constructLineLevelError($line, 'More than one company exists.');
            array_push($driverErrorList, $error);
            continue;
        }
        
        // check if the new driver already exist based on its name
        $driver_trim = trim($driver);
        $query_driver = "select * from giqwm_fleet_driver where name = '{$driver_trim}'";
        error_log("Executing query " .PHP_EOL. $query_driver . PHP_EOL, 3, $LOG_FILE);
        
        $result_driver = mysqli_query($link, $query_driver);
        $num_rows_driver = mysqli_num_rows($result_driver);
        if ($num_rows_driver > 0) {
            $error = constructLineLevelError($line, 'Driver name already exists.');
            array_push($driverErrorList, $error);
            continue;
        }
        
        // check if a unique Group exists
        if (!empty($group)) {
            $group_trim = trim($group);
            $query_group = "select * from giqwm_fleet_entity where entity_type = 3 and name = '{$group_trim}'";
            error_log("Executing query " .PHP_EOL. $query_group . PHP_EOL, 3, $LOG_FILE);
            
            $result_group = mysqli_query($link, $query_group);
            $num_rows_group = mysqli_num_rows($result_group);
            if ($num_rows_group == 0) {
                $error = constructLineLevelError($line, 'Group does not exist.');
                array_push($driverErrorList, $error);
                continue;
            }
            elseif ($num_rows_group > 1) {
                $error = constructLineLevelError($line, 'More than one Group exists.');
                array_push($driverErrorList, $error);
                continue;
            }
        }
        
        // check if a unique license already exists
        if (!empty($driver_license)) {
            $query_license = "select * from giqwm_fleet_driver where license = '{$driver_license}'";
            error_log("Executing query " .PHP_EOL. $query_license . PHP_EOL, 3, $LOG_FILE);
            
            $result_license = mysqli_query($link, $query_license);
            $num_rows_license = mysqli_num_rows($result_license);
            if ($num_rows_license > 0) {
                $error = constructLineLevelError($line, 'Driver license already exists.');
                array_push($driverErrorList, $error);
                continue;
            }
        }
    }
    return;
}

function processDriverCreateFile(&$link, &$driverList, &$driverErrorMessage, &$driverErrorList, $LOG_FILE, $ERROR_MESSAGE) {
    error_log("Processing driver creation file... ".PHP_EOL, 3, $LOG_FILE);
    
    foreach($driverList as $line) {
        // skip if line is empty
        if (count($line) == 0) { continue; }
        
        $inputArray = explode(',', $line);
        
        $company = $inputArray[0];
        $group = $inputArray[1];
        $driver = $inputArray[2];
        $driver_license = $inputArray[3];
        
        $company_trim = trim($company);
        $driver_trim = trim($driver);
        
        // first, get the entity id for the given company or group
        $entity_id = 0;
        if (empty($group)) { // no group is given
            $query_company = "select id from giqwm_fleet_entity where entity_type = 2 and name = '{$company_trim}'";
            error_log("Executing query" .PHP_EOL. $query_company . PHP_EOL, 3, $LOG_FILE);
            
            $result_company = mysqli_query($link, $query_company);
            $num_rows_company = mysqli_fetch_row($result_company);
            $entity_id = $num_rows_company[0];
        }
        else { // group is given
            $group_trim = trim($group);
            $query_company = "select id from giqwm_fleet_entity where entity_type = 3 and name = '{$group_trim}'";
            error_log("Executing query" .PHP_EOL. $query_company . PHP_EOL, 3, $LOG_FILE);
            
            $result_company = mysqli_query($link, $query_company);
            $num_rows_company = mysqli_fetch_row($result_company);
            $entity_id = $num_rows_company[0];
        }
        
        // no entity id is returned. Should not happen
        if ($entity_id == 0) {
            $error = constructLineLevelError($line, 'DB failed. No entity id returned.');
            array_push($driverErrorList, $error);
            continue;
        }
        
        // create driver
        if (empty($driver_license)) { // no license is given
            $query = "REPLACE INTO giqwm_fleet_driver (name, entity_id, visible) VALUES ('{$driver_trim}', '{$entity_id}', '1')";
            error_log("Inserting db for Driver creation, query:" .PHP_EOL. $query . PHP_EOL, 3, $LOG_FILE);
            
            if (!mysqli_query($link, $query)) {
                error_log("Failed to insert db for Driver creation, query:" .PHP_EOL. $query . PHP_EOL, 3, $LOG_FILE);
                $error = constructLineLevelError($line, 'DB failed. Failed to insert to db.'. $ERROR_MESSAGE);
                array_push($driverErrorList, $error);
                continue;
            }
        }
        else { // license is given
            $license_trim = trim($driver_license);
            $query = "REPLACE INTO giqwm_fleet_driver (name, entity_id, visible, license) VALUES ('{$driver_trim}', '{$entity_id}', '1', '{$license_trim}')";
            error_log("Inserting db for Driver creation, query:" .PHP_EOL. $query . PHP_EOL, 3, $LOG_FILE);
            
            if (!mysqli_query($link, $query)) {
                error_log("Failed to insert db for Driver creation, query: " .PHP_EOL. $query . PHP_EOL, 3, $LOG_FILE);
                $error = constructLineLevelError($line, 'DB failed. Failed to insert to db.' . $ERROR_MESSAGE);
                array_push($driverErrorList, $error);
                continue;
            }
        }
    }
    return;
}

function processSubCreateFile(&$link, &$subList, &$subErrorMessage, &$subErroList, $LOG_FILE, $ERROR_MESSAGE) {
    error_log("Processing subscription creation files... ".PHP_EOL, 3, $LOG_FILE);
    
    foreach($subList as $line) {
        // skip if line is empty
        if (count($line) == 0) { continue; }
        
        $inputArray = explode(',', $line);
        
        $reseller = trim($inputArray[0]);
        $company = $inputArray[1];
        $group = $inputArray[2];
        $driver = trim($inputArray[3]);
        $weight = trim($inputArray[4]);
        $friendlyName = trim($inputArray[5]);
        $vin = trim($inputArray[6]);
        $visible = trim($inputArray[7]);
        
        $company_trim = trim($company);
        $driver_trim = trim($driver);
        
        // create a new subscription id based on reseller
        $subscription_id = generateNewSubscription($link, $reseller, $LOG_FILE);
        if (empty($subscription_id)) {
            $error = constructLineLevelError($line, 'Failed to generate new subscription for the reseller.' . $ERROR_MESSAGE);
            array_push($subErroList, $error);
            continue;
        }
        
        // get the entity id for the given company or group
        $entity_id = 0;
        if (empty($group)) { // no group is given
            $query_company = "select id from giqwm_fleet_entity where entity_type = 2 and name = '{$company_trim}'";
            error_log("Executing query" .PHP_EOL. $query_company . PHP_EOL, 3, $LOG_FILE);
            
            $result_company = mysqli_query($link, $query_company);
            $num_rows_company = mysqli_fetch_row($result_company);
            $entity_id = $num_rows_company[0];
        }
        else { // group is given
            $group_trim = trim($group);
            $query_company = "select id from giqwm_fleet_entity where entity_type = 3 and name = '{$group_trim}'";
            error_log("Executing query" .PHP_EOL. $query_company . PHP_EOL, 3, $LOG_FILE);
            
            $result_company = mysqli_query($link, $query_company);
            $num_rows_company = mysqli_fetch_row($result_company);
            $entity_id = $num_rows_company[0];
        }
        
        // no entity id is returned. Should not happen
        if ($entity_id == 0) {
            $error = constructLineLevelError($line, 'DB failed. No entity id returned.' . $ERROR_MESSAGE);
            array_push($subErroList, $error);
            continue;
        }
        
        // get the driver id for the given driver name
        $driver_id = getDriverId($link, $driver, $LOG_FILE);
        if (empty($driver_id)) {
            $error = constructLineLevelError($line, 'Failed to find the driver id for the given driver name.' . $ERROR_MESSAGE);
            array_push($subErroList, $error);
            continue;
        }
        
        // get the weight id for the given weight
        $weight_id = getWeightId($link, $weight);
        if (empty($weight_id)) {
            $error = constructLineLevelError($line, 'Failed to find the weight id for the given weight.' . $ERROR_MESSAGE);
            array_push($subErroList, $error);
            continue;
        }
        
        $visible_value = strtolower($visible) == 'no' ? 0 : 1;
        
        // create subscription
        $query = "REPLACE INTO giqwm_fleet_subscription (entity_id, weight_id, driver_id, vin, name, visible, subscription_id) 
                    VALUES ('{$entity_id}', '{$weight_id}', '{$driver_id}', '{$vin}', '{$friendlyName}', '{$visible_value}', '{$subscription_id}')";

        error_log("Inserting db for Subscription creation, query:" .PHP_EOL. $query . PHP_EOL, 3, $LOG_FILE);
        if (!mysqli_query($link, $query)) {
            error_log("DB failed. Failed to insert to db, query:" .PHP_EOL. $query . PHP_EOL, 3, $LOG_FILE);
            $error = constructLineLevelError($line, 'DB failed. Failed to insert to db.' . $ERROR_MESSAGE);
            array_push($subErroList, $error);
            continue;
        }
        
        // update visible if it's a no
        if ($visible_value == 0) {
            $query_update_visible = "UPDATE giqwm_fleet_driver SET visible = 0";
            error_log("Executing query" .PHP_EOL. $query_update_visible . PHP_EOL, 3, $LOG_FILE);
            
            if (!mysqli_query($link, $query_update_visible)) {
                $error = constructLineLevelError($line, 'DB failed. Failed to update visible for driver.' . $ERROR_MESSAGE);
                array_push($subErroList, $error);
                continue;
            }
        }
    }
    return;
}

function getDriverId($link, $driver, $LOG_FILE) {
    $driver_trim = trim($driver);
    $query_driver = "select id from giqwm_fleet_driver where name = '{$driver_trim}'";
    error_log("Executing query" .PHP_EOL. $query_driver . PHP_EOL, 3, $LOG_FILE);
    
    if (!$result = mysqli_query($link, $query_driver)) {
        return;
    }
    $driver_result = mysqli_fetch_row($result);
    
    return empty($driver_result) ? '' : $driver_result[0];
}

function getWeightId($link, $weight) {
    $weight_hash = array(
        "1" => array(
            "min" => "0",
            "max" => "5000",
            "compensation_table_number" => "1"
        ),
        "2" => array(
            "min" => "5001",
            "max" => "7000",
            "compensation_table_number" => "2"
        ),
        "3" => array(
            "min" => "7001",
            "max" => "9000",
            "compensation_table_number" => "3"
        ),
        "4" => array(
            "min" => "9001",
            "max" => "11000",
            "compensation_table_number" => "4"
        ),
        "5" => array(
            "min" => "11001",
            "max" => "999999",
            "compensation_table_number" => "5"
        )
    );
    
    foreach ($weight_hash as $key => $value) {
        $min = $value['min'];
        $max = $value['max'];
        if ((int)$weight <= (int)$max) {
            return $key;
        }
    }
    
    return;
}


function generateNewSubscription($link, $reseller, $LOG_FILE) {
    // get the last created (highest number) subscription for the given reseller
    $query = " select subscription_id 
                from giqwm_fleet_subscription 
                where subscription_id like '{$reseller}%' 
                order by subscription_id desc
                limit 1";
    error_log("Executing query" .PHP_EOL. $query . PHP_EOL, 3, $LOG_FILE);
    
    if (!$result = mysqli_query($link, $query)) {
        error_log("Failed to execute query:" .PHP_EOL. $query, 3, $LOG_FILE);
        return $result;
    }
    $last_sub_result = mysqli_fetch_row($result);
    
    // no subscription has been registered with the reseller yet
    if (empty($last_sub_result)) {
        return sprintf('%s-%s', $reseller, '00001');
    }
    
    $last_sub = $last_sub_result[0];
    $inputArray = explode('-', $last_sub);
    $reseller_returned = $inputArray[0];
    if ($reseller_returned != $reseller) {
        return;
    }
    $sub = $inputArray[1];
    $new_sub = sprintf('%05d', (int) $sub + 1);
    return sprintf('%s-%s', $reseller, $new_sub);
}

function scanSubCreateFile(&$link, &$subList, &$subErrorMessage, &$subErroList, $LOG_FILE) {
    error_log("Scanning subscription craetion files...".PHP_EOL, 3, $LOG_FILE);
    
    if (!$subList || count($subList) == 0) {
        $subErrorMessage = 'Subscription file is empty';
        return;
    }
    
    // first, scan all lines and see if it will cause db errors
    foreach($subList as $line) {
        // skip if line is empty
        if (count($line) == 0) { continue; }
        
        $inputArray = explode(',', $line);
        // check if all arguments are present.
        if (count($inputArray) != 8) {
            $error = constructLineLevelError($line, 'Argument count expected to be 8, but got ' . count($inputArray));
            array_push($subErroList, $error);
            continue;
        }
        
        $reseller = $inputArray[0];
        $company = $inputArray[1];
        $group = $inputArray[2];
        $driver = $inputArray[3];
        $weight = $inputArray[4];
        $friendly_name = $inputArray[5];
        $vin = $inputArray[6];
        $visible = $inputArray[7];
        
        // check if all required arguments (company, driver) are present
        checkIfAllArgumentsExist($line, $subErroList);
        
        // check if a unique Company exists
        $company_trim = trim($company);
        $query_company = "select * from giqwm_fleet_entity where entity_type = 2 and name = '{$company_trim}'";
        error_log("Executing query" .PHP_EOL. $query_company . PHP_EOL, 3, $LOG_FILE);
        
        $result_company = mysqli_query($link, $query_company);
        $num_rows_company = mysqli_num_rows($result_company);
        if ($num_rows_company == 0) {
            $error = constructLineLevelError($line, 'Company does not exist.');
            array_push($subErroList, $error);
            continue;
        }
        elseif ($num_rows_company > 1) {
            $error = constructLineLevelError($line, 'More than one company exists.');
            array_push($subErroList, $error);
            continue;
        }
        
        // check if a unique Group exists
        if (!empty($group)) {
            $group_trim = trim($group);
            $query_group = "select * from giqwm_fleet_entity where entity_type = 3 and name = '{$group_trim}'";
            error_log("Executing query" .PHP_EOL. $query_group . PHP_EOL, 3, $LOG_FILE);
            
            $result_group = mysqli_query($link, $query_group);
            $num_rows_group = mysqli_num_rows($result_group);
            if ($num_rows_group == 0) {
                $error = constructLineLevelError($line, 'Group does not exist.');
                array_push($subErroList, $error);
                continue;
            }
            elseif ($num_rows_group > 1) {
                $error = constructLineLevelError($line, 'More than one Group exists.');
                array_push($subErroList, $error);
                continue;
            }
        }
        
        // check if Weight is a numeric value
        $weight_trim = trim($weight);
        if (!is_numeric($weight)) {
            $error = constructLineLevelError($line, 'Weight must be a numeric value.');
            array_push($subErroList, $error);
            continue;
        }
        
        // check if Visible is yes or no
        $visible_trim = trim($visible);
        if (strtolower($visible_trim) != 'yes' && strtolower($visible_trim) != 'no') {
            $error = constructLineLevelError($line, 'Visible must be either yes or no.');
            array_push($subErroList, $error);
            continue;
        }
    }
    return;
}

function checkIfAllArgumentsExist($line, &$subErroList) {
    $inputArray = explode(',', $line);
    $reseller = $inputArray[0];
    $company = $inputArray[1];
    $group = $inputArray[2];
    $driver = $inputArray[3];
    $weight = $inputArray[4];
    $friendly_name = $inputArray[5];
    $vin = $inputArray[6];
    $visible = $inputArray[7];
    
    if (empty($reseller)) {
        $error = constructLineLevelError($line, 'Missing Reseller.');
        array_push($subErroList, $error);
    }
    elseif (empty($company)) {
        $error = constructLineLevelError($line, 'Missing Company.');
        array_push($subErroList, $error);
    }
    elseif (empty($driver)) {
        $error = constructLineLevelError($line, 'Missing Driver.');
        array_push($subErroList, $error);
    }
    elseif (empty($weight)) {
        $error = constructLineLevelError($line, 'Missing Weight.');
        array_push($subErroList, $error);
    }
    elseif (empty($friendly_name)) {
        $error = constructLineLevelError($line, 'Missing Friendly name.');
        array_push($subErroList, $error);
    }
    elseif (empty($vin)) {
        $error = constructLineLevelError($line, 'Missing VIN.');
        array_push($subErroList, $error);
    }
    elseif (empty($visible)) {
        $error = constructLineLevelError($line, 'Missing Visible.');
        array_push($subErroList, $error);
    }
}

function constructJson(&$status, &$driverErrorMessage, &$subErrorMessage, &$driverErrorList, &$subErroList) {

    if (empty($driverErrorMessage) && empty($subErrorMessage) && count($driverErrorList) == 0 && count($subErroList) == 0) {
        $status = 'success';
    }
    
    $response = array(
        'status' => $status,

        'driverErrorMessage' => $driverErrorMessage,

        'subErrorMessage' => $subErrorMessage,

        'driverErrorList' => $driverErrorList,

        'subErrorList' => $subErroList
    );
    return $response;
}

function constructLineLevelError($inputLine, $error) {
    return array(
        'input' => $inputLine,

        'error' => $error
    );
}


