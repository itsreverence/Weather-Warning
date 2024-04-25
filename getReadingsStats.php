<?php
include_once('esp-database.php');

$readings_count = isset($_GET["readingsCount"]) ? intval($_GET["readingsCount"]) : 20;

$min_temp = minReading($readings_count, 'value1');
$max_temp = maxReading($readings_count, 'value1');
$avg_temp = avgReading($readings_count, 'value1');

$min_humi = minReading($readings_count, 'value2');
$max_humi = maxReading($readings_count, 'value2');
$avg_humi = avgReading($readings_count, 'value2');

$stats = array(
    'temperature' => array(
        'min' => $min_temp['min_amount'],
        'max' => $max_temp['max_amount'],
        'avg' => round($avg_temp['avg_amount'], 2)
    ),
    'humidity' => array(
        'min' => $min_humi['min_amount'],
        'max' => $max_humi['max_amount'],
        'avg' => round($avg_humi['avg_amount'], 2)
    )
);

header('Content-Type: application/json');
echo json_encode($stats);
exit();
?>