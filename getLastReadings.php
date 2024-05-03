<?php
include_once('esp-database.php');

$selected_table = isset($_GET["selectedTable"]) ? $_GET["selectedTable"] : getAvailableTables()[0];

$last_reading = getLastReadings($selected_table);

if ($last_reading) {
    $last_reading_temp = $last_reading["value1"];
    $last_reading_humi = $last_reading["value2"];
    $reading_time = date("m/d/Y g:i:s A", strtotime($last_reading["reading_time"]));

    $readings = array(
        'temperature' => $last_reading_temp,
        'humidity' => $last_reading_humi,
        'readingTime' => $reading_time
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