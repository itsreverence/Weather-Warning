# Weather Warning

[![Build Status](https://img.shields.io/badge/build-testing-brightgreen)](https://github.com/itsreverence/weather-watch/tree/main)
[![License](https://img.shields.io/badge/License-AGPL-yellow.svg)](https://github.com/itsreverence/weather-watch/blob/main/LICENSE) 

A web application to display real-time temperature and humidity data from an ESP32 Wrover and DHT11 sensor.

![Weather Warning Application Preview](https://github.com/itsreverence/weather-watch/blob/main/preview.png)

## Key Features

* Real-time temperature and humidity monitoring
* User-friendly web interface
* Built using Docker for easy deployment

## Requirements

* **Hardware:**
    * [ESP 32 Wrover Ultimate Starter Kit](https://www.amazon.com/FREENOVE-Ultimate-ESP32-WROVER-Included-Compatible/dp/B0CJJJ7BCY/ref=sr_1_3?sr=8-3) 
* **Software:**
    * [Docker Desktop](https://www.docker.com/products/docker-desktop/)
    * [Arduino IDE](https://www.arduino.cc/en/software)

## Getting Started

**Important:** Your host device and the ESP32 need to be connected to the same network.

1. **Install Docker Desktop:** Download Docker Desktop from [here](https://www.docker.com/products/docker-desktop/) then install and start it.
2. **Clone the repository:** `git clone https://github.com/itsreverence/weather-watch.git`
3. **Configure ESP32:**
    * Edit `arduino_secrets.h.sample` with your network details and rename it to `arduino_secrets.h`.
    * Update the `lastOctet` variable in `weather-watch.ino` with the last octet of your local IP address.
4. **Build and run the Docker image (first time):** `docker-compose up --build`
5. **Install Arduino IDE and ESP32 board support:**
   * Download Arduino IDE from here [here](https://www.arduino.cc/en/software) 
   * Add this link [here](https://dl.espressif.com/dl/package_esp32_index.json) to your Additional Board Manager URL's in File > Preferences
6. **Flash ESP32:** Connect the ESP32, select the correct port and board, and upload the `weather-watch.ino` sketch.
7. **Access the web interface:** Open the app [here](http://localhost/esp-weather-station.php) in your browser. 

## Stopping and Restarting 

* **Stopping:** Press Ctrl/Option + C to terminate, or use `docker-compose down`.
* **Restarting:** Run `docker-compose up` 
