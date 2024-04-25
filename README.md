# Weather Warning

![Weather Warning Application](https://github.com/itsreverence/Weather-Warning/blob/41a11db3189814f0c62d3d5b316a7274cb38f814/preview.png)

Weather Warning is a web application that retrieves real-time sensor data from an ESP32 Wrover and DHT11 sensor including temperature and humidity and displays it in an easily digestible manner.

## Requirements

- [ESP 32 Wrover Ultimate Starter Kit](https://www.amazon.com/FREENOVE-Ultimate-ESP32-WROVER-Included-Compatible/dp/B0CJJJ7BCY/ref=sr_1_3?sr=8-3)
- Computer

## Getting Started

1. Download the latest version of Docker Desktop from [here](https://www.docker.com/products/docker-desktop/) and follow the instructions in the installer.
2. Clone the repo into your editor of choice using [this link](https://github.com/itsreverence/Weather-Warning.git).
3. Configure all the variables for the network you will be using in the file `arduino_secrets.h.sample` and rename it to `arduino_secrets.h`.
4. Open `Weather-Warning.ino` and put the last octet of your local IP address which can be found [like this](https://geekflare.com/find-ip-address-of-windows-linux-mac-and-website/) in the `lastOctet` variable.
5. Make sure that you are connected to the same network on your host device that you will be connecting the ESP to.
6. Navigate to a terminal inside the main folder of the repo and run: `docker-compose up --build` to build and start the application.
7. Download the latest version of Arduino IDE from [here](https://www.arduino.cc/en/software), and connect your ESP32 via USB.
8. Open Arduino IDE and go to File > Preferences, and add [this URL](https://dl.espressif.com/dl/package_esp32_index.json) to the Additional Board Managers URLs.
9. Select the port your ESP32 is connected to and select the ESP32 Wrover board then flash the sketch onto the ESP32.
10. Whenever you want to end the program you can just escape from the task using Control/Option and C key and run `docker-compose down` to stop the program and `docker-compose up` to start it again.
