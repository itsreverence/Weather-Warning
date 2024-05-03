#!/bin/bash

if [ "${CLEAR_DB,,}" = "true" ]; then
  echo "Clearing the database..."
  mysql -h db -P 3306 -u "$MYSQL_USER" -p"$MYSQL_PASSWORD" -e "DROP DATABASE IF EXISTS \`$MYSQL_DATABASE\`; CREATE DATABASE \`$MYSQL_DATABASE\`;"
else
  # Check if the SensorData table exists and has data
  table_exists=$(mysql -h db -P 3306 -u "$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE" -e "SHOW TABLES LIKE 'SensorData';" -s -N)
  if [ -n "$table_exists" ]; then
    row_count=$(mysql -h db -P 3306 -u "$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE" -e "SELECT COUNT(*) FROM SensorData;" -s -N)
    if [ "$row_count" -gt 0 ]; then
      if [ "${MOVE_DB,,}" = "true" ]; then
        echo "Moving old data to a separate table..."

        # Get the current timestamp
        timestamp=$(date +"%Y%m%d_%H%M%S")

        # Get the start and end timestamps from the SensorData table
        start_time=$(mysql -h db -P 3306 -u "$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE" -e "SELECT MIN(reading_time) FROM SensorData;" -s -N)
        end_time=$(mysql -h db -P 3306 -u "$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE" -e "SELECT MAX(reading_time) FROM SensorData;" -s -N)

        # Format the start and end timestamps
        start_time_formatted=$(date -d "$start_time" +"%Y%m%d_%H%M%S")

        # Create the new table name
        if [ "$start_time" = "$end_time" ]; then
           new_table_name="SensorData_${start_time_formatted}_to_${start_time_formatted}"
        else
          end_time_formatted=$(date -d "$end_time" +"%Y%m%d_%H%M%S")
          new_table_name="SensorData_${start_time_formatted}_to_${end_time_formatted}"
        fi

        # Create the old data table with the timestamp or interval in the name
        mysql -h db -P 3306 -u "$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE" -e "
          CREATE TABLE IF NOT EXISTS ${new_table_name} LIKE SensorData;
          INSERT INTO ${new_table_name} SELECT * FROM SensorData;
          TRUNCATE TABLE SensorData;
        "
      else
        echo "Keeping the existing database data."
      fi
    else
      echo "SensorData table is empty, no data to move."
    fi
  else
    echo "SensorData table does not exist."
  fi
fi

echo "Running init.sql..."
mysql -h db -P 3306 -u "$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE" < /docker-entrypoint-initdb.d/init.sql