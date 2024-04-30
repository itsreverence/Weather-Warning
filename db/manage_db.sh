#!/bin/bash

if [ "${CLEAR_DB,,}" = "true" ]; then
  echo "Clearing the database..."
  mysql -h db -P 3306 -u "$MYSQL_USER" -p"$MYSQL_PASSWORD" -e "DROP DATABASE IF EXISTS \`$MYSQL_DATABASE\`; CREATE DATABASE \`$MYSQL_DATABASE\`;"
elif [ "${MOVE_DB,,}" = "true" ]; then
  echo "Moving old data to a separate table..."

  # Get the current timestamp
  timestamp=$(date +"%Y%m%d_%H%M%S")

  # Get the start and end timestamps from the SensorData table
  start_time=$(mysql -h db -P 3306 -u "$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE" -e "SELECT MIN(reading_time) FROM SensorData;" -s -N)
  end_time=$(mysql -h db -P 3306 -u "$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE" -e "SELECT MAX(reading_time) FROM SensorData;" -s -N)

  # Format the start and end timestamps
  start_time_formatted=$(date -d "$start_time" +"%Y%m%d_%H%M%S")
  end_time_formatted=$(date -d "$end_time" +"%Y%m%d_%H%M%S")

  # Create the old data table with the timestamp or interval in the name
  mysql -h db -P 3306 -u "$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE" -e "
    CREATE TABLE IF NOT EXISTS SensorData_${start_time_formatted}_to_${end_time_formatted} LIKE SensorData;
    INSERT INTO SensorData_${start_time_formatted}_to_${end_time_formatted} SELECT * FROM SensorData;
    TRUNCATE TABLE SensorData;
  "
else
  echo "Keeping the existing database data."
fi

echo "Running init.sql..."
mysql -h db -P 3306 -u "$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE" < /docker-entrypoint-initdb.d/init.sql

if [ "${DELETE_OLD_TABLES,,}" = "true" ]; then
  echo "Deleting old sensor data tables..."
  tables=$(mysql -h db -P 3306 -u "$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE" -e "SHOW TABLES LIKE 'SensorData\_%';" -s -N)
  for table in $tables; do
    mysql -h db -P 3306 -u "$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE" -e "DROP TABLE \`$table\`;"
  done
fi