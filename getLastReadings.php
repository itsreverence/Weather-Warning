<?php
include_once('esp-database.php');

$last_reading = getLastReadings();

if ($last_reading) {
    $last_reading_temp = $last_reading["value1"];
    $last_reading_humi = $last_reading["value2"];

    $readings = array(
        'temperature' => $last_reading_temp,
        'humidity' => $last_reading_humi
    );

    header('Content-Type: application/json');
    echo json_encode($readings);
    exit();
} else {
    header('HTTP/1.1 500 Internal Server Error');
    $error = array('error' => 'Failed to retrieve last readings from the database.');
    echo json_encode($error);
    exit();
}
?>