<?php
    include_once('esp-database.php');
    if (isset($_GET["readingsCount"])){
      $data = $_GET["readingsCount"];
      $data = trim($data);
      $data = stripslashes($data);
      $data = htmlspecialchars($data);
      $readings_count = $_GET["readingsCount"];
    }
    // default readings count set to 20
    else {
      $readings_count = 20;
    }

    $last_reading = getLastReadings();
    $last_reading_temp = $last_reading["value1"];
    $last_reading_humi = $last_reading["value2"];
    $last_reading_time = $last_reading["reading_time"];

    // Uncomment to set timezone to - 1 hour (you can change 1 to any number)
    //$last_reading_time = date("Y-m-d H:i:s", strtotime("$last_reading_time - 1 hours"));
    // Uncomment to set timezone to + 7 hours (you can change 7 to any number)
    //$last_reading_time = date("Y-m-d H:i:s", strtotime("$last_reading_time + 7 hours"));

    $min_temp = minReading($readings_count, 'value1');
    $max_temp = maxReading($readings_count, 'value1');
    $avg_temp = avgReading($readings_count, 'value1');

    $min_humi = minReading($readings_count, 'value2');
    $max_humi = maxReading($readings_count, 'value2');
    $avg_humi = avgReading($readings_count, 'value2');
?>

<!DOCTYPE html>
<html>
    <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">

        <link rel="stylesheet" type="text/css" href="esp-style.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        

    </head>
    <header class="header">
        <h1>ESP Weather Station</h1>
        <form method="get">
            <input type="number" name="readingsCount" min="1" placeholder="Number of readings (<?php echo $readings_count; ?>)">
            <input type="submit" value="UPDATE">
        </form>
    </header>
<body background="#F5EEE6">
    <p>Last reading: <?php echo $last_reading_time; ?></p>
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
		            <th colspan="3">Temperature <?php echo $readings_count; ?> readings</th>
	            </tr>
		        <tr>
		            <td>Min</td>
                    <td>Max</td>
                    <td>Average</td>
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
                    <th colspan="3">Humidity <?php echo $readings_count; ?> readings</th>
                </tr>
                <tr>
                    <td>Min</td>
                    <td>Max</td>
                    <td>Average</td>
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
            <th>Value 1</th>
            <th>Value 2</th>
            <th>Timestamp</th>
        </tr>
        <tbody id="tableBody">';

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

    echo '</tbody></table>';
?>

<script>
    function getAllReadings() {
        $("#tableBody").load("getReadings.php?readingsCount=<?php echo $readings_count; ?>");
    }

    function updateLastReadings() {
        $.ajax({
            url: "getLastReadings.php",
            dataType: "json",
            success: function(data) {
                console.log("Received data:", data); // Debugging line

                if (data.error) {
                    console.error("Error fetching last readings:", data.error);
                } else {
                    var temp = data.temperature;
                    var humi = data.humidity;

                    console.log("Temperature:", temp); // Debugging line
                    console.log("Humidity:", humi); // Debugging line

                    setTemperature(temp);
                    setHumidity(humi);
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
        var minHumi = 0;
        var maxHumi = 100;

        var newVal = scaleValue(curVal, [minHumi, maxHumi], [0, 180]);
        $('.gauge--2 .semi-circle--mask').attr({
            style: '-webkit-transform: rotate(' + newVal + 'deg);' +
                '-moz-transform: rotate(' + newVal + 'deg);' +
                'transform: rotate(' + newVal + 'deg);'
        });
        $("#humi").text(curVal + ' %');
    }

    function scaleValue(value, from, to) {
        var scale = (to[1] - to[0]) / (from[1] - from[0]);
        var capped = Math.min(from[1], Math.max(from[0], value)) - from[0];
        return ~~(capped * scale + to[0]);
    }

    function updateReadingsStats() {
        $.ajax({
            url: "getReadingsStats.php?readingsCount=<?php echo $readings_count; ?>",
            dataType: "json",
            success: function(data) {
                $("#min_temp").text(data.temperature.min + " °C");
                $("#max_temp").text(data.temperature.max + " °C");
                $("#avg_temp").text(data.temperature.avg + " °C");
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
    });
</script>
</body>
</html>