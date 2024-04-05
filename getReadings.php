<?php
include_once('esp-database.php');

$readings_count = isset($_GET["readingsCount"]) ? intval($_GET["readingsCount"]) : 20;

$result = getAllReadings($readings_count);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo '<tr>
                <td>' . $row["id"] . '</td>
                <td>' . $row["sensor"] . '</td>
                <td>' . $row["location"] . '</td>
                <td>' . $row["value1"] . '</td>
                <td>' . $row["value2"] . '</td>
                <td>' . $row["reading_time"] . '</td>
              </tr>';
    }
    $result->free();
}
?>
