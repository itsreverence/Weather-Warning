<?php
$servername = getenv('MYSQL_SERVER');
$dbname = getenv('MYSQL_DATABASE');
$username = getenv('MYSQL_USER');
$password = getenv('MYSQL_PASSWORD');

function insertReading($sensor, $location, $value1, $value2, $table = null)
{
    global $servername, $username, $password, $dbname;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Use the table if no table is provided
    if (empty($table)) {
        $table = getenv('MYSQL_TABLE');
    }

    $sql = "INSERT INTO $table (sensor, location, value1, value2) VALUES ('" . $sensor . "', '" . $location . "', '" . $value1 . "', '" . $value2 . "')";

    if ($conn->query($sql) === TRUE) {
        return "New record created successfully";
    } else {
        return "Error: " . $sql . "<br>" . $conn->error;
    }
    $conn->close();
}
function getAllReadings($limit, $table)
{
    global $servername, $username, $password, $dbname;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT id, sensor, location, value1, value2, reading_time FROM $table ORDER BY reading_time DESC LIMIT " . $limit;
    if ($result = $conn->query($sql)) {
        return $result;
    } else {
        return false;
    }
    $conn->close();
}

function getLastReadings($table)
{
    global $servername, $username, $password, $dbname;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT id, sensor, location, value1, value2, reading_time FROM $table ORDER BY reading_time DESC LIMIT 1";
    if ($result = $conn->query($sql)) {
        return $result->fetch_assoc();
    } else {
        return false;
    }
    $conn->close();
}

function minReading($limit, $value, $table)
{
    global $servername, $username, $password, $dbname;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT MIN($value) AS min_amount FROM (SELECT $value FROM $table ORDER BY reading_time DESC LIMIT $limit) AS min";
    if ($result = $conn->query($sql)) {
        return $result->fetch_assoc();
    } else {
        return false;
    }
    $conn->close();
}

function maxReading($limit, $value, $table)
{
    global $servername, $username, $password, $dbname;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT MAX($value) AS max_amount FROM (SELECT $value FROM $table ORDER BY reading_time DESC LIMIT $limit) AS max";
    if ($result = $conn->query($sql)) {
        return $result->fetch_assoc();
    } else {
        return false;
    }
    $conn->close();
}

function avgReading($limit, $value, $table)
{
    global $servername, $username, $password, $dbname;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT AVG($value) AS avg_amount FROM (SELECT $value FROM $table ORDER BY reading_time DESC LIMIT $limit) AS avg";
    if ($result = $conn->query($sql)) {
        return $result->fetch_assoc();
    } else {
        return false;
    }
    $conn->close();
}

function getAvailableTables()
{
    global $servername, $username, $password, $dbname;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $tablePrefix = getenv('MYSQL_TABLE');
    $sql = "SHOW TABLES LIKE '$tablePrefix%'";
    $result = $conn->query($sql);

    $tables = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_row()) {
            $tables[] = $row[0];
        }
    }

    // Add the main table if it's not already in the array
    if (!in_array($tablePrefix, $tables)) {
        $tables[] = $tablePrefix;
    }

    $conn->close();
    return $tables;
}
