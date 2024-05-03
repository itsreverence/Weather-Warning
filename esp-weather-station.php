<?php
include_once('esp-database.php');
if (isset($_GET["readingsCount"]) && isset($_GET["selectedTable"])) {
    $data = $_GET["readingsCount"];
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $readings_count = $_GET["readingsCount"];
    $selected_table = $_GET["selectedTable"];
}
// default readings count set to 20 and default table to the first available table
else {
    $readings_count = 20;
    $availableTables = getAvailableTables();
    $defaultTableName = getenv('MYSQL_TABLE');
    $selected_table = in_array($defaultTableName, $availableTables) ? $defaultTableName : $availableTables[0];
}

$last_reading = getLastReadings($selected_table);
if ($last_reading) {
    $last_reading_temp = $last_reading["value1"];
    $last_reading_humi = $last_reading["value2"];
    $last_reading_time = date("F jS, Y g:i:s A", strtotime($last_reading["reading_time"]));
} else {
    $last_reading_temp = "--";
    $last_reading_humi = "--";
    $last_reading_time = "No readings available";
}

$min_temp = minReading($readings_count, 'value1', $selected_table);
$max_temp = maxReading($readings_count, 'value1', $selected_table);
$avg_temp = avgReading($readings_count, 'value1', $selected_table);

$min_humi = minReading($readings_count, 'value2', $selected_table);
$max_humi = maxReading($readings_count, 'value2', $selected_table);
$avg_humi = avgReading($readings_count, 'value2', $selected_table);

function formatTableNameToDate($tableName)
{
    $defaultTableName = getenv('MYSQL_TABLE');

    // Check if the table name is just the table prefix
    if ($tableName === $defaultTableName) {
        return 'Current Data';
    }

    // Extracts the date and time parts assuming the format "TablePrefix_YYYYMMDD_HHMMSS_to_YYYYMMDD_HHMMSS"
    $pattern = "/" . preg_quote($defaultTableName, '/') . "_(\\d{8})_(\\d{6})_to_(\\d{8})_(\\d{6})/";
    preg_match($pattern, $tableName, $matches);

    if (count($matches) === 5) { // Checks if the pattern match was successful
        // Parsing start date and time. Ensure there's no line break that could disrupt the string concatenation.
        $startDate = DateTime::createFromFormat('Ymd His', $matches[1] . $matches[2]);
        $endDate = DateTime::createFromFormat('Ymd His', $matches[3] . $matches[4]);

        if ($startDate && $endDate) {
            // Formatting dates to "m/d/Y h:i:s A" e.g., "04/30/2024 6:40:01 PM"
            $formattedStartDate = $startDate->format('m/d/Y g:i:s A');
            $formattedEndDate = $endDate->format('m/d/Y g:i:s A');
            return "$formattedStartDate to $formattedEndDate";
        }
    }

    return $tableName; // Return original name if parsing fails
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="stylesheet" type="text/css" href="esp-style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
</head>

<body background="#F5EEE6">
    <div class="mobile-container">
        <header class="header">
            <h1>Weather Watch</h1>
            <form method="get">
                <div class="table-select">
                    <label for="table-select">Selected Database Table</label>
                    <select id="table-select" name="selectedTable">
                        <?php
                        $availableTables = getAvailableTables();
                        foreach ($availableTables as $table) {
                            $formattedDate = formatTableNameToDate($table);
                            $selected = ($selected_table == $table) ? 'selected' : '';
                            echo "<option value='$table' $selected>$formattedDate</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="readings-input">
                    <input type="number" name="readingsCount" min="1" placeholder="Number Of Readings (<?php echo $readings_count; ?>)">
                    <input type="submit" value="UPDATE">
                </div>
                <p id="lastReadingTime">Last Reading: <?php echo $last_reading_time; ?></p>
            </form>
        </header>
        <section class="content">
            <div class="box gauge--1">
                <h3>TEMPERATURE</h3>
                <div class="mask">
                    <div class="semi-circle"></div>
                    <div class="semi-circle--mask"></div>
                </div>
                <p style="font-size: 30px;" id="temp">--</p>
                <table cellspacing="5" cellpadding="5">
                    <tr>
                        <th colspan="3">Latest <?php echo $readings_count; ?> Readings</th>
                    </tr>
                    <tr>
                        <td>Min</td>
                        <td>Max</td>
                        <td>Avg</td>
                    </tr>
                    <tr>
                        <td id="min_temp">--</td>
                        <td id="max_temp">--</td>
                        <td id="avg_temp">--</td>
                    </tr>
                </table>
            </div>
            <div class="box gauge--2">
                <h3>HUMIDITY</h3>
                <div class="mask">
                    <div class="semi-circle"></div>
                    <div class="semi-circle--mask"></div>
                </div>
                <p style="font-size: 30px;" id="humi">--</p>
                <table cellspacing="5" cellpadding="5">
                    <tr>
                        <th colspan="3">Latest <?php echo $readings_count; ?> Readings</th>
                    </tr>
                    <tr>
                        <td>Min</td>
                        <td>Max</td>
                        <td>Avg</td>
                    </tr>
                    <tr>
                        <td id="min_humi">--</td>
                        <td id="max_humi">--</td>
                        <td id="avg_humi">--</td>
                    </tr>
                </table>
            </div>
        </section>
        <?php
        echo '<h2>View Latest ' . $readings_count . ' Readings</h2>
            <table cellspacing="5" cellpadding="5" id="tableReadings">
                <tr>
                    <th>ID</th>
                    <th>Sensor</th>
                    <th>Location</th>
                    <th>Temperature</th>
                    <th>Humidity</th>
                    <th>Timestamp</th>
                </tr>
                <tbody id="tableBody">';

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

        echo '</tbody></table>';
        ?>

        <script>
            function getAllReadings() {
                $("#tableBody").load("getReadings.php?readingsCount=<?php echo $readings_count; ?>&selectedTable=<?php echo $selected_table; ?>");
            }

            function updateLastReadings() {
                $.ajax({
                    url: "getLastReadings.php?selectedTable=<?php echo $selected_table; ?>",
                    dataType: "json",
                    success: function(data) {
                        console.log("Received data:", data); // Debugging line

                        if (data.error) {
                            console.error("Error fetching last readings:", data.error);
                        } else {
                            var temp = data.temperature;
                            var humi = data.humidity;
                            var readingTime = data.readingTime; // Get the reading time from the response

                            console.log("Temperature:", temp); // Debugging line
                            console.log("Humidity:", humi); // Debugging line

                            setTemperature(temp);
                            setHumidity(humi);
                            setLastReadingTime(readingTime);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log("AJAX request error:", error);
                        console.log("Response:", xhr.responseText);
                    }
                });
            }

            function setTemperature(curVal) {
                var minTemp = 32.0;
                var maxTemp = 122.0;

                var newVal = scaleValue(curVal, [minTemp, maxTemp], [0, 180]);
                $('.gauge--1 .semi-circle--mask').attr({
                    style: '-webkit-transform: rotate(' + newVal + 'deg);' +
                        '-moz-transform: rotate(' + newVal + 'deg);' +
                        'transform: rotate(' + newVal + 'deg);'
                });
                $("#temp").text(curVal + ' ºF');
            }

            function setHumidity(curVal) {
                var minHumi = 20;
                var maxHumi = 80;

                var newVal = scaleValue(curVal, [minHumi, maxHumi], [0, 180]);
                $('.gauge--2 .semi-circle--mask').attr({
                    style: '-webkit-transform: rotate(' + newVal + 'deg);' +
                        '-moz-transform: rotate(' + newVal + 'deg);' +
                        'transform: rotate(' + newVal + 'deg);'
                });
                $("#humi").text(curVal + ' %');
            }

            function setLastReadingTime(readingTime) {
                $("#lastReadingTime").text("Last Reading: " + readingTime);
            }

            function scaleValue(value, from, to) {
                var scale = (to[1] - to[0]) / (from[1] - from[0]);
                var capped = Math.min(from[1], Math.max(from[0], value)) - from[0];
                return ~~(capped * scale + to[0]);
            }

            function updateReadingsStats() {
                $.ajax({
                    url: "getReadingsStats.php?readingsCount=<?php echo $readings_count; ?>&selectedTable=<?php echo $selected_table; ?>",
                    dataType: "json",
                    success: function(data) {
                        $("#min_temp").text(data.temperature.min + " ºF");
                        $("#max_temp").text(data.temperature.max + " ºF");
                        $("#avg_temp").text(data.temperature.avg + " ºF");
                        $("#min_humi").text(data.humidity.min + " %");
                        $("#max_humi").text(data.humidity.max + " %");
                        $("#avg_humi").text(data.humidity.avg + " %");
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX request error:", error);
                    }
                });
            }

            $(document).ready(function() {
                getAllReadings();
                updateLastReadings();
                updateReadingsStats();
                setInterval(getAllReadings, 5000);
                setInterval(updateLastReadings, 5000);
                setInterval(updateReadingsStats, 5000);

                $("#table-select").change(function() {
                    var selectedTable = $(this).val();
                    window.location.href = window.location.pathname + "?readingsCount=<?php echo $readings_count; ?>&selectedTable=" + selectedTable;
                });
            });
        </script>
    </div>
</body>

</html> 