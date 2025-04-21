// Sensor
#include <DHT.h>
#include <DHT_U.h>

#define Type DHT11
#define sensorPin 7

DHT SENSOR_1 (sensorPin, Type);

char sensorPrintoutH[3];
char sensorPrintoutT[3];

// TFT 1.8
#include <TFT.h>  // Arduino LCD library
#include <SPI.h>

#define cs   10
#define dc   9
#define rst  8

// create an instance of the library
TFT TFTscreen = TFT(cs, dc, rst);

// char array to print to the screen
char sensorPrintout[4];

void setup() {
  // Sensor
  SENSOR_1.begin();

  // TFT
  // Put this line at the beginning of every sketch that uses the GLCD:
  TFTscreen.begin();
  TFTscreen.setRotation(3);

  // clear the screen with a black background
  TFTscreen.background(0, 0, 0);
  // write the static text to the screen
  // set the font color to white
  TFTscreen.stroke(255, 255, 255);
  // write the text to the top left corner of the screen
  TFTscreen.setTextSize(1); 
  TFTscreen.text("Luis Display Tech.", 0, 0);

  TFTscreen.text("v1.0", 120, 0);

  Serial.begin(9600);
}

float randInt;
float randomDecimal;
float result;

float randomNumber() {
  randInt = random(0,91);   // a random integer from -90 to 90
  randomDecimal = random(0, 1) / 1.0; // a random decimal number from 0.00 to 0.99
  result = randInt + randomDecimal; // a random decimal number from -90.00 to 90.99

  return result;
}

float humidityPre;
float tempCPre;
//float tempF;
float humidityNew;
float tempCNew;

void printSensorValues(float h, float t) {
  
  // labes
  TFTscreen.stroke(255, 255, 255);
  TFTscreen.setTextSize(1); 
  TFTscreen.stroke(0, 255, 208);
  TFTscreen.text("Humedad", 0, 25);

  TFTscreen.stroke(255, 255, 255);
  TFTscreen.setTextSize(1);
  TFTscreen.stroke(0, 255, 208);
  TFTscreen.text("Temperatura", 80, 25);

  TFTscreen.setTextSize(3);
  // Old Values
  String(humidityPre).toCharArray(sensorPrintoutH, 3);  
  TFTscreen.stroke(0, 0, 0);
  TFTscreen.text(sensorPrintoutH, 0, 40);

  String(tempCPre).toCharArray(sensorPrintoutT, 3);
  TFTscreen.stroke(0, 0, 0);
  TFTscreen.text(sensorPrintoutT, 80, 40);

  // New Values  
  String(h).toCharArray(sensorPrintoutH, 3);  
  TFTscreen.stroke(255, 255, 255);
  TFTscreen.text(sensorPrintoutH, 0, 40);

  String(t).toCharArray(sensorPrintoutT, 3);
  TFTscreen.stroke(255, 255, 255);
  TFTscreen.text(sensorPrintoutT, 80, 40);
}

// Print Column label only once
boolean colNamePrinted = true;

void loop() {
  
  // Read the values from the sensor
  // humidityNew = SENSOR_1.readHumidity();
  // tempCNew = SENSOR_1.readTemperature();

  humidityNew = randomNumber();
  tempCNew = randomNumber();

  // printSensorValues(SENSOR_1.readHumidity(), SENSOR_1.readTemperature());
  printSensorValues(humidityNew, tempCNew);

  // Save values for reprint in black
  humidityPre = humidityNew;
  tempCPre = tempCNew;

  if(colNamePrinted) {
    Serial.print("Hum,Temp\n");
    colNamePrinted = false;
  } else {
    Serial.print(String(humidityNew) + "," + String(tempCNew) + "\n");
  }
  
  // wait for a moment
  delay(2500);
}

