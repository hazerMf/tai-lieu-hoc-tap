#include <SD.h>
#include <SPI.h>
#include <Wire.h>
#include <Adafruit_GFX.h>
#include <Adafruit_SH110X.h>

// Display settings
#define SCREEN_WIDTH 128
#define SCREEN_HEIGHT 64
#define SDA_PIN 21
#define SCL_PIN 22

// Try different CS pins - uncomment one at a time to test
#define SD_CS 5    // Currently testing GPIO 5
// #define SD_CS 33   // Try this if 5 doesn't work
// #define SD_CS 4    // Or this
// #define SD_CS 15   // Or this

#define SD_MISO 13
#define SD_MOSI 14
#define SD_SCK 27

// Create display object for SH1106
Adafruit_SH1106G display = Adafruit_SH1106G(SCREEN_WIDTH, SCREEN_HEIGHT, &Wire, -1);

void setup() {
  Serial.begin(115200);
  delay(1000);
  
  // Initialize I2C for display
  Wire.begin(SDA_PIN, SCL_PIN);
  
  // Initialize display
  if(!display.begin(0x3C, true)) {
    Serial.println("Display failed!");
    while(1);
  }
  
  display.clearDisplay();
  display.setTextSize(1);
  display.setTextColor(SH110X_WHITE);
  display.setCursor(0,0);
  display.println("SD Card Test");
  display.println("");
  display.print("CS: GPIO ");
  display.println(SD_CS);
  display.display();
  delay(2000);
  
  display.clearDisplay();
  display.setCursor(0,0);
  display.println("Initializing SPI...");
  display.display();
  
  SPI.begin(SD_SCK, SD_MISO, SD_MOSI, SD_CS);
  delay(500);
  
  display.println("Testing SD card...");
  display.display();
  
  if (!SD.begin(SD_CS)) {
    display.clearDisplay();
    display.setCursor(0,0);
    display.println("SD CARD FAILED!");
    display.println("");
    display.println("Check:");
    display.println("- Card inserted?");
    display.println("- Wiring OK?");
    display.println("- FAT32 format?");
    display.println("");
    display.print("CS pin: ");
    display.println(SD_CS);
    display.display();
    while(1);
  }
  
  display.clearDisplay();
  display.setCursor(0,0);
  display.println("SD Card OK!");
  display.println("");
  
  uint8_t cardType = SD.cardType();
  if (cardType == CARD_NONE) {
    display.println("No card detected");
    display.display();
    while(1);
  }
  
  display.print("Type: ");
  if (cardType == CARD_MMC) {
    display.println("MMC");
  } else if (cardType == CARD_SD) {
    display.println("SDSC");
  } else if (cardType == CARD_SDHC) {
    display.println("SDHC");
  } else {
    display.println("UNKNOWN");
  }
  
  uint64_t cardSize = SD.cardSize() / (1024 * 1024);
  display.print("Size: ");
  display.print(cardSize);
  display.println(" MB");
  display.println("");
  
  // Count files
  int fileCount = 0;
  File root = SD.open("/");
  File file = root.openNextFile();
  while (file) {
    if (!file.isDirectory()) {
      fileCount++;
    }
    file.close();
    file = root.openNextFile();
  }
  root.close();
  
  display.print("Files: ");
  display.println(fileCount);
  display.println("");
  display.println("Test Complete!");
  display.display();
}

void loop() {
  // Nothing here - test runs once in setup
}
