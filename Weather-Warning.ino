#include <WiFi.h>
#include <HTTPClient.h>
#include <DHTesp.h>
#include <IPAddress.h>

String lastOctet = "111" // last octet of local ip address of device hosting site
const char* ssid = SECRET_SSID; // name of network to connect ESP to
const char* password = SECRET_PASSWORD; // password of network to connect ESP to

String serverName; // url where the data will get posted at

String apiKeyValue = "mF8d0cge2"; // database api key for validation
String sensorName = "DHT11"; // sensor name to display
String sensorLocation = "Classroom"; // location of station to display

DHTesp dht; // sensor object
int dhtPin = 13; // sensor pin

unsigned long lastTime = 0; // flag for last time data was sent
unsigned long timerDelay = 30000; // delay between sending of data

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
  
  String prefixString = "http://";
  uint8_t firstOctet = WiFi.localIP()[0];
  uint8_t secondOctet = WiFi.localIP()[1];
  uint8_t thirdOctet = WiFi.localIP()[2];
  String suffixString = "/esp-post-data.php";
  String completeUrl = prefixString  + String(firstOctet) + "." + String(secondOctet) + "." + String(thirdOctet) + "." + fourthOctet + suffixString;
  serverName = completeUrl;

  Serial.println("Timer set to " + String(timerDelay / 1000) + " seconds (timerDelay variable), it will take 30 seconds before publishing the first reading.");

  dht.setup(dhtPin, DHTesp::DHT11);
}

void loop() {
  if ((millis() - lastTime) > timerDelay) {
    if (WiFi.status() == WL_CONNECTED) {
      WiFiClient client;
      HTTPClient http;

flag:
      TempAndHumidity newValues = dht.getTempAndHumidity();
      if (dht.getStatus() != 0) {
        goto flag;
      }

      http.begin(client, serverName);

      http.addHeader("Content-Type", "application/x-www-form-urlencoded");

      newValues.temperature = newValues.temperature * 9/5 + 32;
      String httpRequestData = "api_key=" + apiKeyValue + "&sensor=" + sensorName
                               + "&location=" + sensorLocation + "&value1=" + newValues.temperature
                               + "&value2=" + newValues.humidity + "";
      Serial.print("httpRequestData: ");
      Serial.println(httpRequestData);

      int httpResponseCode = http.POST(httpRequestData);

      if (httpResponseCode > 0) {
        Serial.print("HTTP Response code: ");
        Serial.println(httpResponseCode);
      } else {
        Serial.print("Error code: ");
        Serial.println(httpResponseCode);
      }
      http.end();
    } else {
      Serial.println("WiFi Disconnected");
    }
    lastTime = millis();
  }
}
