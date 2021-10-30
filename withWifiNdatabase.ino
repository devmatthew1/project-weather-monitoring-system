#include <ESP8266WiFi.h>
 
#include <ESP8266HTTPClient.h>
#include <WiFiClient.h>
WiFiClient client;
#include <Wire.h>
#include "DHT.h"
#include <Adafruit_ADS1X15.h>
#include <SFE_BMP180.h>

// Adafruit_ADS1115 ads;  /* Use this for the 16-bit version */
Adafruit_ADS1015 ads;     /* Use thi for the 12-bit version https://api.thingspeak.com/channels/1460851/feeds.json?api_key=ZSZB04KKFNYSCTHZ */

SFE_BMP180 bmp;
double T, P;
char status;
DHT dht(D5, DHT11);


 /* thingspeak

String apiKey = "ZSZB04KKFNYSCTHZ";
const char *ssid =  "Surelay";
const char *pass =  "daniel53";
const char* server = "api.thingspeak.com";
 
 */


const char* ssid = "Surelay";
const char* password = "daniel53";

unsigned long lastTime = 0;
// Timer set to 10 minutes (600000)
//unsigned long timerDelay = 600000;
// Set timer to 30 seconds (30000)
unsigned long timerDelay = 30000;

//Your Domain name with URL path or IP address with path
const char* serverName = "http://weatherapp4finalyear.000webhostapp.com/esp-post-data.php";

// Keep this API Key value to be compatible with the PHP code provided in the project page.
// If you change the apiKeyValue value, the PHP file /esp-post-data.php also needs to have the same key
String apiKeyValue = "ZSZB04KKFNYSCTHZ";
String sensorLocation = "Office";


void setup(void) 
{
  Wire.begin();
  
  dht.begin();
  delay(10);
  bmp.begin();
  Serial.begin(115200);

  // The ADC input range (or gain) can be changed via the following
  // functions, but be careful never to exceed VDD +0.3V max, or to
  // exceed the upper and lower limits if you adjust the input range!
  // Setting these values incorrectly may destroy your ADC!
  //                                                                ADS1015  ADS1115
  //                                                                -------  -------
  // ads.setGain(GAIN_TWOTHIRDS);  // 2/3x gain +/- 6.144V  1 bit = 3mV      0.1875mV (default)    // activate this if you are using a 5V sensor, this one should  be used with Arduino boards
     ads.setGain(GAIN_ONE);        // 1x gain   +/- 4.096V  1 bit = 2mV      0.125mV               // As the sensor is powered up using 3.3V, this one should be used with 3.3v controller boards
  // ads.setGain(GAIN_TWO);        // 2x gain   +/- 2.048V  1 bit = 1mV      0.0625mV
  // ads.setGain(GAIN_FOUR);       // 4x gain   +/- 1.024V  1 bit = 0.5mV    0.03125mV
  // ads.setGain(GAIN_EIGHT);      // 8x gain   +/- 0.512V  1 bit = 0.25mV   0.015625mV
  // ads.setGain(GAIN_SIXTEEN);    // 16x gain  +/- 0.256V  1 bit = 0.125mV  0.0078125mV
  
  ads.begin();
  WiFi.begin(ssid, password);
  
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  
  Serial.println("");
  Serial.println("WiFi connected");
}


// Database code starts
void loop() {

  int16_t adc0, adc1, adc2, adc3;

  adc0 = ads.readADC_SingleEnded(0);
  adc1 = ads.readADC_SingleEnded(1);
  //adc2 = ads.readADC_SingleEnded(2);
  //adc3 = ads.readADC_SingleEnded(3);

  int moisture_percentage;
 
  moisture_percentage = ( 100.00 - ( ((adc1)/1023.00) * 100.00 ) );

  //DHT11 sensor
  float h = dht.readHumidity();
  float t = dht.readTemperature();
/*
  if (isnan(h) || isnan(t)) {
    Serial.println("Failed to read from DHT sensor!");
    return;
  }
*/
    //BMP180 sensor
  status =  bmp.startTemperature();
  if (status != 0) {
    delay(status);
    status = bmp.getTemperature(T);

    status = bmp.startPressure(3);// 0 to 3
    if (status != 0) {
      delay(status);
      status = bmp.getPressure(P, T);
      if (status != 0) {

      }
    }
  }
   
  
  //Send an HTTP POST request every 10 minutes
  if ((millis() - lastTime) > timerDelay) {
    //Check WiFi connection status
    if(WiFi.status()== WL_CONNECTED){
      WiFiClient client;
      HTTPClient http;

      // Your Domain name with URL path or IP address with path
      http.begin(client, serverName);

      // Specify content-type header
      http.addHeader("Content-Type", "application/x-www-form-urlencoded");

      Serial.println(adc1);
       
      // Prepare your HTTP POST request data
      String httpRequestData = "api_key=" + apiKeyValue + "&location=" + sensorLocation + "&value1=" + String(moisture_percentage)
                            + "&value2=" + String(h) + "&value3=" + String(t) + "&value4=" + String(P, 2) + "&value5=" + String(adc0) + "";
      Serial.print("httpRequestData: ");
      Serial.println(httpRequestData);

      // You can comment the httpRequestData variable above
      // then, use the httpRequestData variable below (for testing purposes without the BME280 sensor)
      //String httpRequestData = "api_key=tPmAT5Ab3j7F9&sensor=BME280&location=Office&value1=24.75&value2=49.54&value3=1005.14";

      // Send HTTP POST request
      int httpResponseCode = http.POST(httpRequestData);

      // If you need an HTTP request with a content type: text/plain
      //http.addHeader("Content-Type", "text/plain");
      //int httpResponseCode = http.POST("Hello, World!");

      // If you need an HTTP request with a content type: application/json, use the following:
      //http.addHeader("Content-Type", "application/json");
      //int httpResponseCode = http.POST("{\"value1\":\"19\",\"value2\":\"67\",\"value3\":\"78\"}");

      if (httpResponseCode>0) {
        Serial.print("HTTP Response code: ");
        Serial.println(httpResponseCode);
      }
      else {
        Serial.print("Error code: ");
        Serial.println(httpResponseCode);
      }
      // Free resources
      http.end();
    }
    else {
      Serial.println("WiFi Disconnected");
    }
    lastTime = millis();
  }
}
// Database code ends

/* real thingspeak loop
 
void loop(void) 
{
  int16_t adc0, adc1, adc2, adc3;

  adc0 = ads.readADC_SingleEnded(0);
  adc1 = ads.readADC_SingleEnded(1);
  //adc2 = ads.readADC_SingleEnded(2);
  //adc3 = ads.readADC_SingleEnded(3);

  int moisture_percentage;
 
  moisture_percentage = ( 100.00 - ( ((adc1)/1023.00) * 100.00 ) );

  //DHT11 sensor
  float h = dht.readHumidity();
  float t = dht.readTemperature();

  if (isnan(h) || isnan(t)) {
    Serial.println("Failed to read from DHT sensor!");
    return;
  }

  //BMP180 sensor
  status =  bmp.startTemperature();
  if (status != 0) {
    delay(status);
    status = bmp.getTemperature(T);

    status = bmp.startPressure(3);// 0 to 3
    if (status != 0) {
      delay(status);
      status = bmp.getPressure(P, T);
      if (status != 0) {

      }
    }
  }
   
  //Serial.print("AIN1: "); Serial.println(adc1);
  //Serial.print("AIN2: "); Serial.println(adc2);
 // Serial.print("AIN3: "); Serial.println(adc3);

    if (client.connect(server, 80)) {
    String postStr = apiKey;
    postStr += "&field1=";
    postStr += String(t);
    postStr += "&field2=";
    postStr += String(h);
    postStr += "&field3=";
    postStr += String(P, 2);
    postStr += "&field4=";
    postStr += String(adc0);
    postStr += "&field5=";
    postStr += String(moisture_percentage);
    postStr += "\r\n\r\n\r\n\r\n\r\n";

    client.print("POST /update HTTP/1.1\n");
    client.print("Host: api.thingspeak.com\n");
    client.print("Connection: close\n");
    client.print("X-THINGSPEAKAPIKEY: " + apiKey + "\n");
    client.print("Content-Type: application/x-www-form-urlencoded\n");
    client.print("Content-Length: ");
    client.print(postStr.length());
    client.print("\n\n\n\n\n");
    client.print(postStr);

// Serial monitor part
    if (adc0 <=700) {
    
    Serial.println("It's Dark Outside; Lights status: ON");
    }
    else {
      
    Serial.println("It's Bright Outside; Lights status: OFF");
    }

    Serial.print("Light intensity: ");
    Serial.println(adc0);
    Serial.print("\n");
    
    Serial.print("Temperature: ");
    Serial.println(t);
    Serial.print("Humidity: ");
    Serial.println(h);
    Serial.print("\n");
    
    Serial.print("Soil Moisture(in Percentage) = ");
    Serial.print(moisture_percentage);
    Serial.println("%");
    Serial.print("\n");

    Serial.print("Pressure: ");
    Serial.print(P, 2);
    Serial.println("mb");
    Serial.print("\n");
  

  }
 
  delay(1000);
}
 */
