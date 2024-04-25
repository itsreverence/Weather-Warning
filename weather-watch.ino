#include <WiFi.h>
#include <HTTPClient.h>
#include <DHTesp.h>
#include <IPAddress.h>
#include "arduino_secrets.h"

// Define variables for network
String lastOctet = "111";
const char* ssid = SECRET_SSID;
const char* password = SECRET_PASSWORD;

// Define variables for database
String serverName;
String apiKeyValue = "mF8d0cge2";
String sensorName = "DHT11";
String sensorLocation = "Classroom";

// Define variables for sensor
DHTesp dht;
int dhtPin = 13;
unsigned long lastTime = 0;
unsigned long timerDelay = 30000;

// Connect to network
void setup() {
  Serial.begin(115200);

  WiFi.begin(ssid, password);
  Serial.println("Connecting");
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("");
  Serial.print("Connected to WiFi network with IP Address: ");
  Serial.println(WiFi.localIP());
  
  // Define address of site to post data to
  serverName = "http://" + String(WiFi.localIP()[0]) + "." + String(WiFi.localIP()[1]) + "." + String(WiFi.localIP()[2]) + "." + lastOctet + "/esp-post-data.php";

  // Notify user of the current delay between posting of data
  Serial.println("Timer set to " + String(timerDelay / 1000) + " seconds, it will take 30 seconds before publishing the first reading.");

  // Initialize sensor to start collecting data
  dht.setup(dhtPin, DHTesp::DHT11);
}

void loop() {
  // Check if it is time to send data again
  if ((millis() - lastTime) > timerDelay) {
    // Check network connection and define wifi/http client variables
    if (WiFi.status() == WL_CONNECTED) {
      WiFiClient client;
      HTTPClient http;


flag:
      // Retrieve temperature and humidity values from sensor
      TempAndHumidity newValues = dht.getTempAndHumidity();
      if (dht.getStatus() != 0) {
        goto flag;
      }

      // Prepare http client to send data
      http.begin(client, serverName);
      http.addHeader("Content-Type", "application/x-www-form-urlencoded");

      // Prepare and log sensor data for post
      newValues.temperature = newValues.temperature * 9/5 + 32;
      String httpRequestData = "api_key=" + apiKeyValue + "&sensor=" + sensorName
                               + "&location=" + sensorLocation + "&value1=" + newValues.temperature
                               + "&value2=" + newValues.humidity + "";
      Serial.print("httpRequestData: ");
      Serial.println(httpRequestData);

      // Post sensor data to site
      int httpResponseCode = http.POST(httpRequestData);

      // Log the response code of the post
      if (httpResponseCode > 0) {
        Serial.print("HTTP Response code: ");
        Serial.println(httpResponseCode);
      } else {
        Serial.print("Error code: ");
        Serial.println(httpResponseCode);
      }
      // Stop the http client
      http.end();
    } else {
      // Log that connection got dropped
      Serial.println("WiFi Disconnected");
    }
    // Set new value for last time data was sent
    lastTime = millis();
  }
}
