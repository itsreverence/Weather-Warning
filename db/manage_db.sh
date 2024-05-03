#!/bin/bash

TABLE_NAME=${MYSQL_TABLE:-SensorData}

if [ "${CLEAR_DB,,}" = "true" ]; then
    echo "Clearing the database..."
    mysql -h db -P 3306 -u "$MYSQL_USER" -p"$MYSQL_PASSWORD" -e "DROP DATABASE IF EXISTS \`$MYSQL_DATABASE\`; CREATE DATABASE \`$MYSQL_DATABASE\`;"
elif [ "${MOVE_DB,,}" = "true" ]; then
    # Get the list of tables that don't have an underscore followed by a timestamp
    tables_to_move=$(mysql -h db -P 3306 -u "$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE" -e "SHOW TABLES;" | awk '{ print $1 }' | grep -v "_[0-9]\{8\}_[0-9]\{6\}")

    if [ -n "$tables_to_move" ]; then
        echo "Moving old data to separate tables..."
        # Get the current timestamp
        timestamp=$(date +"%Y%m%d_%H%M%S")

        # Get the start and end timestamps from the SensorData table
        start_time=$(mysql -h db -P 3306 -u "$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE" -e "SELECT MIN(reading_time) FROM $TABLE_NAME;" -s -N)
        end_time=$(mysql -h db -P 3306 -u "$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE" -e "SELECT MAX(reading_time) FROM $TABLE_NAME;" -s -N)
        # Format the start and end timestamps
        start_time_formatted=$(date -d "$start_time" +"%Y%m%d_%H%M%S")
        end_time_formatted=$(date -d "$end_time" +"%Y%m%d_%H%M%S")

        for table in $tables_to_move; do
            echo "Moving table: $table"
            # Create the old data table with the timestamp in the name
            mysql -h db -P 3306 -u "$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE" -e "
                CREATE TABLE IF NOT EXISTS ${table}_${start_time_formatted}_to_${end_time_formatted}  LIKE $table;
                INSERT INTO ${table}_${start_time_formatted}_to_${end_time_formatted}  SELECT * FROM $table;
                TRUNCATE TABLE $table;
            "
        done
    else
        echo "No tables to move."
    fi
else
    echo "Keeping the existing database data."
fi

echo "Generating init.sql..."
cat > /docker-entrypoint-initdb.d/init.sql <<EOL
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Create the table if it doesn't exist
CREATE TABLE IF NOT EXISTS \`${TABLE_NAME}\` (
  \`id\` int UNSIGNED NOT NULL AUTO_INCREMENT,
  \`sensor\` varchar(30) NOT NULL,
  \`location\` varchar(30) NOT NULL,
  \`value1\` varchar(10) DEFAULT NULL,
  \`value2\` varchar(10) DEFAULT NULL,
  \`reading_time\` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (\`id\`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

COMMIT;
EOL

echo "Running init.sql..."
mysql -h db -P 3306 -u "$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE" < /docker-entrypoint-initdb.d/init.sql