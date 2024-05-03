<?php
include_once('esp-database.php');

$readings_count = isset($_GET["readingsCount"]) ? intval($_GET["readingsCount"]) : 20;
$selected_table = isset($_GET["selectedTable"]) ? $_GET["selectedTable"] : getAvailableTables()[0];

$result = getAllReadings($readings_count, $selected_table);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo '<tr>
                <td>' . $row["id"] . '</td>
                <td>' . $row["sensor"] . '</td>
                <td>' . $row["location"] . '</td>
                <td>' . $row["value1"] . ' °F</td>
                <td>' . $row["value2"] . ' %</td>
                <td>' . date("m/d/Y g:i:s A", strtotime($row["reading_time"])) . '</td>
              </tr>';
    }
    $result->free();
}
?>