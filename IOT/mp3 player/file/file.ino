#include <Wire.h>
#include <Adafruit_GFX.h>
#include <Adafruit_SH110X.h>
#include <SD.h>
#include <SPI.h>

// Display settings
#define SCREEN_WIDTH 128
#define SCREEN_HEIGHT 64
#define SDA_PIN 21
#define SCL_PIN 22

// SD Card pins
#define SD_MISO 13
#define SD_MOSI 14
#define SD_SCK 27
#define SD_CS 33

// Buttons
#define BTN1 34  // Up
#define BTN2 39  // Down

// Create display object for SH1106
Adafruit_SH1106G display = Adafruit_SH1106G(SCREEN_WIDTH, SCREEN_HEIGHT, &Wire, -1);

// File browsing variables
String fileList[50];  // Maximum 50 files
int fileCount = 0;
int selectedIndex = 0;
int displayOffset = 0;
const int maxDisplayLines = 7;  // Lines that fit on screen

// Button debounce
unsigned long lastButtonPress = 0;
const unsigned long debounceDelay = 200;

void setup() {
  Serial.begin(115200);

  // Initialize buttons
  pinMode(BTN1, INPUT_PULLUP);
  pinMode(BTN2, INPUT_PULLUP);

  // Initialize I2C for display
  Wire.begin(SDA_PIN, SCL_PIN);

  // Initialize display
  if(!display.begin(0x3C, true)) {
    Serial.println("SH1106 allocation failed");
    while(1);
  }

  display.clearDisplay();
  display.setTextSize(1);
  display.setTextColor(SH110X_WHITE);
  display.setCursor(0,0);
  display.println("Initializing SD...");
  display.display();

  // Initialize SD card
  SPI.begin(SD_SCK, SD_MISO, SD_MOSI, SD_CS);
  if (!SD.begin(SD_CS)) {
    Serial.println("SD Card initialization failed!");
    display.clearDisplay();
    display.setCursor(0,0);
    display.println("SD Card FAILED!");
    display.display();
    while(1);
  }

  Serial.println("SD Card initialized.");
  
  // Scan for MP3 files
  scanMP3Files();

  // Display initial list
  displayFileList();
}

void loop() {
  unsigned long currentTime = millis();

  // Button 1 - Move up
  if (digitalRead(BTN1) == LOW && (currentTime - lastButtonPress > debounceDelay)) {
    lastButtonPress = currentTime;
    if (selectedIndex > 0) {
      selectedIndex--;
      // Adjust display offset if needed
      if (selectedIndex < displayOffset) {
        displayOffset = selectedIndex;
      }
      displayFileList();
      Serial.println("Up - Index: " + String(selectedIndex));
    }
  }

  // Button 2 - Move down
  if (digitalRead(BTN2) == LOW && (currentTime - lastButtonPress > debounceDelay)) {
    lastButtonPress = currentTime;
    if (selectedIndex < fileCount - 1) {
      selectedIndex++;
      // Adjust display offset if needed
      if (selectedIndex >= displayOffset + maxDisplayLines) {
        displayOffset = selectedIndex - maxDisplayLines + 1;
      }
      displayFileList();
      Serial.println("Down - Index: " + String(selectedIndex));
    }
  }

  delay(50);
}

void scanMP3Files() {
  File root = SD.open("/");
  if (!root) {
    Serial.println("Failed to open root directory");
    return;
  }

  fileCount = 0;
  File file = root.openNextFile();
  
  while (file && fileCount < 50) {
    if (!file.isDirectory()) {
      String fileName = String(file.name());
      fileName.toUpperCase();
      if (fileName.endsWith(".MP3")) {
        fileList[fileCount] = String(file.name());
        fileCount++;
        Serial.println("Found: " + String(file.name()));
      }
    }
    file.close();
    file = root.openNextFile();
  }
  
  root.close();
  Serial.println("Total MP3 files found: " + String(fileCount));
}

void displayFileList() {
  display.clearDisplay();
  display.setCursor(0, 0);

  if (fileCount == 0) {
    display.println("No MP3 files found");
    display.display();
    return;
  }

  // Display files
  for (int i = 0; i < maxDisplayLines && (displayOffset + i) < fileCount; i++) {
    int fileIndex = displayOffset + i;
    
    // Highlight selected file
    if (fileIndex == selectedIndex) {
      display.print("> ");
    } else {
      display.print("  ");
    }

    // Truncate filename if too long
    String displayName = fileList[fileIndex];
    if (displayName.length() > 19) {
      displayName = displayName.substring(0, 16) + "...";
    }
    
    display.println(displayName);
  }

  // Show scroll indicator if needed
  if (fileCount > maxDisplayLines) {
    display.setCursor(120, 56);
    display.print(String(selectedIndex + 1) + "/" + String(fileCount));
  }

  display.display();
}
