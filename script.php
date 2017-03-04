<?php

use Task\Classes\User as User;

require_once 'app/start.php';

$handle = fopen($argv[1], "r");
if ($handle) {
    $csv = array_map('str_getcsv', file($argv[1]));
    fclose($handle);
} else {
}
$usersArray = [];
foreach ($csv as $row) {
    $date = $row[0];
    $id = $row[1];
    $userType = $row[2];
    $operationType = $row[3];
    $cash = $row[4];
    $currency = $row[5];
    $inArray = false;
        
    foreach ($usersArray as $user) {
        if ($user->getId() == $id) {
            $inArray = true;
            break;
        }
    }
    if (!$inArray) {
        $usersArray[$id] =  new User($id, $userType);
    }
    
    $operation = [$operationType, $cash, $currency, $date];
    fwrite(STDOUT, $usersArray[$id]->operation($operation)."\n");
}
