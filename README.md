![image](https://github.com/itsreverence/Weather-Warning/assets/158013050/775a5f82-37df-4129-9920-ba8db9c97b2f)


<h1>Weather Warning</h1>

Weather Warning is a web application that retrieves real-time sensor data from an ESP32 Wrover and DHT11 sensor including temperature and humidity and displays it in a easily digestible manner.


<h1>Requirements</h1>
- <a href="https://www.amazon.com/FREENOVE-Ultimate-ESP32-WROVER-Included-Compatible/dp/B0CJJJ7BCY/ref=sr_1_3?sr=8-3">ESP 32 Wrover Ultimate Starter Kit</a></br>
- Computer


<h1>Getting Started</h1>
1. Download the latest version of Docker Desktop from <a href="https://www.docker.com/products/docker-desktop/">here</a> and follow the instructions in the installer.</br>
2. Clone the repo into your editor of choice using this link <a href="https://github.com/itsreverence/Weather-Warning.git">here</a>.</br>
3. Configure all the variables for the network you will be using in the file `arduino_secrets.h.sample` and rename it to `arduino_secrets.h`.
4. Open `Weather-Warning.ino` and put the last octet of your local ip address which can be found <a href="https://geekflare.com/find-ip-address-of-windows-linux-mac-and-website/">like this</a> in the `lastOctet` variable.</br>
5. Make sure that you are connected to the same network on your host device that you will be connecting the ESP to.
6. Navigate to a terminal inside the main folder of the repo and run: `docker-compose up --build` to build and start the application.
7. Download the latest version of Arduino IDE from <a href="https://geekflare.com/find-ip-address-of-windows-linux-mac-and-website/">here</a>, and connect your ESP32 via USB.</br>
8. Open Arduino IDE and go to File, Preferences, and add <a href="https://geekflare.com/find-ip-address-of-windows-linux-mac-and-website/">this URL</a> to the additional board managers entry.</br>
9. Select your the port your ESP32 is connected to and select the ESP 32 Wrover board then flash the sketch onto the ESP32.
10. Whenever you want to end the program you can just escape from the task using Control/Option and C key and run `docker-compose down` to stop the program and `docker-compose up` to start it again.