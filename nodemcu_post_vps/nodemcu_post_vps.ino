
// Robo India Tutorial 
// Simple code upload the tempeature and humidity data using thingspeak.com
// Hardware: NodeMCU,DHT11

#include <Wire.h>
#include <LiquidCrystal_I2C.h>
#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include "DHT.h"
#define DHTPIN 2
#define DHTTYPE DHT11
DHT dht(DHTPIN, DHTTYPE);
LiquidCrystal_I2C lcd(0x27,16,2);

const char *ssid =  "SFR-f613";     // replace with your wifi ssid and wpa2 key
const char *pass =  "cq76ivk5r65t";
const char* server = "http://51.75.126.70/iot/dht11/store_temp.php";

WiFiClient client;
HTTPClient http;
 
void setup() 
{
       Serial.begin(115200);
       delay(10);
       dht.begin();

       lcd.init(); // initialisation de l'afficheur
       lcd.backlight();
       lcd.setCursor(0, 0);
       lcd.print("DHT11 VPS");
       lcd.setCursor(0, 1);
       lcd.print("v20181220");

       pinMode(D5,OUTPUT);
       pinMode(D6,OUTPUT);
       pinMode(D7,OUTPUT);
 
       Serial.println("Connecting to ");
       Serial.println(ssid);
 
 
       WiFi.begin(ssid, pass);
 
      while (WiFi.status() != WL_CONNECTED) 
     {
            delay(500);
            Serial.print(".");
     }
      Serial.println("");
      Serial.println("WiFi connected");
 
}
 
void loop() 
{

      //Relevés de température et humidité en flottants
      float h = dht.readHumidity();
      float t = dht.readTemperature();

      //Vérification de la validité des relevés
      if (isnan(h) || isnan(t)) 
      {
        Serial.println("Failed to read from DHT sensor!");
        return;
      }      
      else {

      //Conversion en entiers
      int hum = (int) h;
      int temp = (int) t;

      //Requête post vers vps : envoi json des relevés
      http.begin(server); 
      http.addHeader("Content-Type", "application/json");
      String data = "{\"temperature\":\""+ String(temp) +"\",\"humidite\":\"" + String(hum)+"\"}"; 
      int httpCode = http.POST(data);
      http.end();

      //Affichage lcd
      lcd.setCursor(0, 0);
      lcd.print("  Temp: ");
      lcd.print(temp);
      lcd.print(" *C ");
      lcd.setCursor(0, 1);
      lcd.print("  Humi: ");
      lcd.print(hum);
      lcd.print(" %  ");

      //Allumage led
      if (t>20) {digitalWrite(D7, 1);}
      else if (t>16) {digitalWrite(D6, 1);}
      else {digitalWrite(D5, 1);}

      //Affichage bus série
      Serial.print("Temperature: ");
      Serial.print(temp);
      Serial.print(" degrees Celcius, Humidity: ");
      Serial.print(hum);
      Serial.println("%. Send to VPS.");
      }
      Serial.println("Waiting...");
  
  // mise à jour des données toutes les heures
  delay(3600000);
}
