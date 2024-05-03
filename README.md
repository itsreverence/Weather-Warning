<p align="center">
  <img src="https://github.com/itsreverence/weather-watch/blob/main/assets/preview.png" alt="Weather Watch Website Preview">
</p>
<h1 align="center">
  <b>Weather Watch</b>
</h1>

<b>A web application that allows for analyzation of weather data from the past and real-time from a DHT11 sensor on-board an ESP32 Wrover.</b>

[![Build Status](https://img.shields.io/badge/build-testing-brightgreen)](https://github.com/itsreverence/weather-watch/tree/main)
[![License](https://img.shields.io/badge/License-AGPL-yellow.svg)](https://github.com/itsreverence/weather-watch/blob/main/LICENSE) 

---

## Key Features

* Real-time temperature and humidity monitoring
* Review history of weather data
* User-friendly web interface
* Built using Docker for easy deployment

## Requirements

* **Hardware:**
    * [ESP 32 Wrover Ultimate Starter Kit](https://www.amazon.com/FREENOVE-Ultimate-ESP32-WROVER-Included-Compatible/dp/B0CJJJ7BCY/ref=sr_1_3?sr=8-3)
    * Computer
* **Software:**
    * [Docker Desktop](https://www.docker.com/products/docker-desktop/)
    * [Arduino IDE](https://www.arduino.cc/en/software)

---

## Getting Started
1. Complete the hardware setup which can be found [here](#Hardware-Setup) if you have not already.
2. Complete the software setup which can be found [here](#Software-Setup) if you have not already.
3. Connect your host pc to the same network your ESP32 will connect to.
4. Build the software if you have not before or just start it if you have, the corresponding commands to do so can be found [here](#Operating-Software).
5. Start the ESP32 by plugging it into your computer or providing another power source to run it.
6. Access the main webpage of the software [here](http://localhost/esp-weather-station.php).

---

## Hardware Setup
* **Setup ESP32:**
     * Arrange 4 M/M Jumpers, DHT11 Sensor, and 10 kΩ Transistor as displayed in the photo below.
![Hardware Diagram](https://github.com/itsreverence/weather-watch/blob/main/assets/hardware.png)
     * Ensure that your all your connections are properly made as showcased in the photo below.
![Hardware Schematic](https://github.com/itsreverence/weather-watch/blob/main/assets/schematic.png)

## Software Setup
1. **Prepare Docker Desktop:** Download Docker Desktop from [here](https://www.docker.com/products/docker-desktop/) then install and start it.
2. **Prepare Arduino IDE:**
      * Download Arduino IDE from [here](https://www.arduino.cc/en/software) then install and start it.
      * Add this [link](https://dl.espressif.com/dl/package_esp32_index.json) to the Addition Board Manager URL's in File > Preferences.
3. **Clone the repository:** Run `git clone https://github.com/itsreverence/weather-watch.git` and open the directory in your IDE of choice.
4. **Prepare ESP32:**
    * Configure the following variables:
      * Set `SECRET_SSID` in `arduino_secrets.h.sample` to the name of the network your ESP32 and host computer will connect to.
      * Set `SECRET_PASSWORD` in `arduino_secrets.h.sample` to the password of the network your ESP32 and host computer will connect to.
      * Set `SECRET_LASTOCTET` in `arduino_secrets.h.sample` to the last octet of the local IPv4 address of your host computer on the chosen network.
      * Set `SECRET_APIKEY` in `arduino_secrets.h.sample` to the API key that is set in the .env file of the program.
      * Set `sensorName` in `weather-watch.ino` to the name you want displayed for the sensor that is collecting the data.
      * Set `sensorLocation` in `weather-watch.ino` to the location you want displayed that the sensor will be collecting data from.
      * Set `timerDelay` in `weather-watch.ino` to the delay you want between each time data is sent in milliseconds.
    * Rename the `arduino_secrets.h.sample` file to `arduino_secrets.h`.
    * Plug the ESP32 into your computer, open Arduino IDE, select the port that your ESP32 is on, select ESP32 Wrover Module as the board, and upload `weather-watch.ino` to the ESP32.
5. **Prepare Docker**
   * Configure the following variables:
      * Set `TIMEZONE` in `.env.sample` to the timezone that you want timestamps stored in.
      * Set `KEY` in `.env.sample` to the API key that you want to be checked with data posts that are received.
      * Set `SERVERNAME` in `.env.sample` to the name that you want the database server to be called.
      * Set `DATABASE` in `.env.sample` to the the name that you want the database to be called.
      * Set `USER` in `.env.sample` to the username you want for the login to the database.
      * Set `PASSWORD` in `.env.sample` to the password you want for the login to the database.
      * Set `ALLOWEMPTYPASSWORD` in `.env.sample` to 1 to allow empty passwords or 0 to not allow them.
      * Set `CLEAR_DB` in `.env.sample` to true to clear all the tables in the database on startup or false to leave them.
      * Set `MOVE_DB` in `.env.sample` to true to move all the data from the main table to tables named after the data intervals on startup or false to leave the data.
      * Rename the `.env.sample` file to `.env`.

---

## Operating Software
* **Building:** Run `docker-compose up --build`
* **Stopping:** Run `docker-compose down`
* **Starting:** Run `docker-compose up`
* Access the main webpage [here](http://localhost/esp-weather-station.php)
* Access the database [here](http://localhost:8001)

---

## Troubleshooting
* Invalid End of Line Sequence / Line Seperator:
  * Error:
    * `manage_db-1   | /manage_db.sh: line 2: $'\r': command not found`
    * `manage_db-1   | /manage_db.sh: line 52: syntax error: unexpected end of file`
  * Solution:
    * Open `manage_db.sh` in your text editor or IDE of choice in make sure the line seperator is set to LF.

---

# License
[![License](https://www.gnu.org/graphics/agplv3-155x51.png)](LICENSE)   
Ultroid is licensed under [GNU Affero General Public License](https://www.gnu.org/licenses/agpl-3.0.en.html) v3 or later.

---

# Credit
* [Rui Santos](https://randomnerdtutorials.com/about) for the [original program](https://github.com/RuiSantosdotme/Cloud-Weather-Station-ESP32-ESP8266).

> Made by [@itsreverence](https://github.com/itsreverence), [@carlosgothub](https://github.com/carlosgothub), [@Comiserif](https://github.com/Comiserif), and [@limon-nawaj](https://github.com/limon-nawaj)    
