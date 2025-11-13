#include <Wire.h>
#include <Adafruit_GFX.h>
#include <Adafruit_SH110X.h>

// I2C pins for ESP32
#define SCREEN_WIDTH 128
#define SCREEN_HEIGHT 64
#define SDA_PIN 21
#define SCL_PIN 22

// Create display object for SH1106
Adafruit_SH1106G display = Adafruit_SH1106G(SCREEN_WIDTH, SCREEN_HEIGHT, &Wire, -1);

// Buttons
#define BTN1 34
#define BTN4 35
#define BTN3 36  // VP
#define BTN2 39  // VN

void setup() {
  Serial.begin(115200);

  pinMode(BTN1, INPUT_PULLUP);
  pinMode(BTN2, INPUT_PULLUP);
  pinMode(BTN3, INPUT_PULLUP);
  pinMode(BTN4, INPUT_PULLUP);

  Wire.begin(SDA_PIN, SCL_PIN);

  if(!display.begin(0x3C, true)) {
    Serial.println("SH1106 allocation failed");
    while(1);
  }

  display.clearDisplay();
  display.setTextSize(1);
  display.setTextColor(SH110X_WHITE);
  display.setCursor(0,0);
  display.println("Ready");
  display.display();
}

void loop() {
  display.clearDisplay();
  display.setCursor(0,0);
  bool pressed = false;

  if(digitalRead(BTN1)) { 
    display.println("BTN1 pressed"); 
    Serial.println("BTN1 pressed");
    pressed = true; 
  }
  if(digitalRead(BTN2)) { 
    display.println("BTN2 pressed"); 
    Serial.println("BTN2 pressed");
    pressed = true; 
  }
  if(digitalRead(BTN3)) { 
    display.println("BTN3 pressed"); 
    Serial.println("BTN3 pressed");
    pressed = true; 
  }
  if(digitalRead(BTN4)) { 
    display.println("BTN4 pressed"); 
    Serial.println("BTN4 pressed");
    pressed = true; 
  }

  if(!pressed) {
    display.println("No button pressed");
  }

  display.display();
  delay(100);
}
