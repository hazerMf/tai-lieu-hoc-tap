#include <Wire.h>
#include <Adafruit_GFX.h>
#include <Adafruit_SH110X.h>
#include <SD.h>
#include <SPI.h>
#include <Audio.h>

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

// Audio pins (PAM8403 - Analog input)
// PAM8403 connections:
// L -> GPIO 25 (DAC1 - Left channel)
// G -> GND
// B -> GPIO 26 (DAC2 - Right channel)
// Note: ESP32 internal DAC outputs analog audio directly
#define I2S_DOUT 25
#define I2S_BCLK 26
#define I2S_LRC 32  // Using GPIO 32, an available pin (not used by screen or SD)

// Buttons
#define BTN_UP 34      // D34 - Up/Previous
#define BTN_DOWN 39    // D39 - Down/Next
#define BTN_SELECT 35  // D35 - Select/Play-Pause
#define BTN_BACK 36    // D36 (VP) - Back

// Create display and audio objects
Adafruit_SH1106G display = Adafruit_SH1106G(SCREEN_WIDTH, SCREEN_HEIGHT, &Wire, -1);
Audio audio;

// Screen modes
enum ScreenMode {
  MODE_BROWSE,
  MODE_PLAYER
};

ScreenMode currentMode = MODE_BROWSE;

// File browsing variables
String fileList[50];  // Maximum 50 files
int fileCount = 0;
int selectedIndex = 0;
int displayOffset = 0;
const int maxDisplayLines = 7;  // Lines that fit on screen

// Player variables
bool isPlaying = false;
int currentSongIndex = 0;
unsigned long scrollPosition = 0;
unsigned long lastScrollTime = 0;
const unsigned long scrollDelay = 300;

// Button debounce
bool btnUpWasPressed = false;
bool btnDownWasPressed = false;
bool btnSelectWasPressed = false;
bool btnBackWasPressed = false;

void setup() {
  Serial.begin(115200);

  // Initialize buttons - Input only pins, no pull-up available
  // Buttons connected to VCC (3.3V), so HIGH when pressed
  pinMode(BTN_UP, INPUT);
  pinMode(BTN_DOWN, INPUT);
  pinMode(BTN_SELECT, INPUT);
  pinMode(BTN_BACK, INPUT);

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
    display.println("");
    display.println("Check CS->GPIO 5");
    display.display();
    while(1);
  }

  Serial.println("SD Card initialized.");
  
  // Initialize Audio with internal DAC
  // For internal DAC mode: BCLK, LRC, DOUT (only DOUT matters for internal DAC)
  audio.setPinout(I2S_BCLK, I2S_LRC, I2S_DOUT);
  audio.setVolume(15); // 0-21
  audio.forceMono(true); // Use mono output on GPIO 25
  
  // Scan for MP3 files
  scanMP3Files();

  // Display initial list
  displayFileList();
}

void loop() {
  audio.loop(); // Keep audio processing
  
  // Read button states - HIGH when pressed (connected to VCC)
  bool btnUpPressed = (digitalRead(BTN_UP) == HIGH);
  bool btnDownPressed = (digitalRead(BTN_DOWN) == HIGH);
  bool btnSelectPressed = (digitalRead(BTN_SELECT) == HIGH);
  bool btnBackPressed = (digitalRead(BTN_BACK) == HIGH);

  if (currentMode == MODE_BROWSE) {
    handleBrowseMode(btnUpPressed, btnDownPressed, btnSelectPressed);
  } else if (currentMode == MODE_PLAYER) {
    handlePlayerMode(btnUpPressed, btnDownPressed, btnSelectPressed, btnBackPressed);
  }

  // Remember button states
  btnUpWasPressed = btnUpPressed;
  btnDownWasPressed = btnDownPressed;
  btnSelectWasPressed = btnSelectPressed;
  btnBackWasPressed = btnBackPressed;

  delay(50);
}

void handleBrowseMode(bool btnUp, bool btnDown, bool btnSelect) {
  // Button Up - Move up in list
  if (btnUp && !btnUpWasPressed) {
    if (selectedIndex > 0) {
      selectedIndex--;
      if (selectedIndex < displayOffset) {
        displayOffset = selectedIndex;
      }
      displayFileList();
      Serial.println("Up - Index: " + String(selectedIndex));
    }
    delay(200);
  }

  // Button Down - Move down in list
  if (btnDown && !btnDownWasPressed) {
    if (selectedIndex < fileCount - 1) {
      selectedIndex++;
      if (selectedIndex >= displayOffset + maxDisplayLines) {
        displayOffset = selectedIndex - maxDisplayLines + 1;
      }
      displayFileList();
      Serial.println("Down - Index: " + String(selectedIndex));
    }
    delay(200);
  }

  // Button Select - Enter player mode
  if (btnSelect && !btnSelectWasPressed) {
    currentSongIndex = selectedIndex;
    currentMode = MODE_PLAYER;
    playSong(currentSongIndex);
    displayPlayerScreen();
    delay(300);
    // Wait for button to be released
    while(digitalRead(BTN_SELECT) == HIGH) {
      delay(10);
    }
  }
}

void handlePlayerMode(bool btnUp, bool btnDown, bool btnSelect, bool btnBack) {
  // Button Up - Previous song
  if (btnUp && !btnUpWasPressed) {
    if (currentSongIndex > 0) {
      currentSongIndex--;
      playSong(currentSongIndex);
      displayPlayerScreen();
    }
    delay(200);
  }

  // Button Down - Next song
  if (btnDown && !btnDownWasPressed) {
    if (currentSongIndex < fileCount - 1) {
      currentSongIndex++;
      playSong(currentSongIndex);
      displayPlayerScreen();
    }
    delay(200);
  }

  // Button Select - Play/Pause
  if (btnSelect && !btnSelectWasPressed) {
    if (isPlaying) {
      audio.pauseResume();
      isPlaying = false;
    } else {
      audio.pauseResume();
      isPlaying = true;
    }
    displayPlayerScreen();
    delay(200);
  }

  // Button Back - Return to browse mode
  if (btnBack && !btnBackWasPressed) {
    audio.stopSong();
    isPlaying = false;
    currentMode = MODE_BROWSE;
    selectedIndex = currentSongIndex; // Set browse cursor to current song
    // Adjust display offset
    if (selectedIndex >= displayOffset + maxDisplayLines) {
      displayOffset = selectedIndex - maxDisplayLines + 1;
    } else if (selectedIndex < displayOffset) {
      displayOffset = selectedIndex;
    }
    displayFileList();
    delay(300); // Longer delay to ensure button release
    // Wait for button to be released
    while(digitalRead(BTN_BACK) == HIGH) {
      delay(10);
    }
  }

  // Update player display (for scrolling and progress)
  static unsigned long lastUpdate = 0;
  if (millis() - lastUpdate > 500) {
    displayPlayerScreen();
    lastUpdate = millis();
  }
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

void playSong(int index) {
  if (index < 0 || index >= fileCount) return;
  
  String filePath = "/" + fileList[index];
  audio.connecttoFS(SD, filePath.c_str());
  isPlaying = true;
  scrollPosition = 0;
  lastScrollTime = millis();
  
  Serial.println("Playing: " + fileList[index]);
}

void displayPlayerScreen() {
  display.clearDisplay();
  
  // Get current song name
  String songName = fileList[currentSongIndex];
  // Remove .mp3 extension
  if (songName.endsWith(".mp3") || songName.endsWith(".MP3")) {
    songName = songName.substring(0, songName.length() - 4);
  }
  
  // Scrolling text for long filenames
  display.setTextSize(1);
  int textWidth = songName.length() * 6;
  int displayWidth = 128;
  
  if (textWidth > displayWidth) {
    // Scroll the text
    if (millis() - lastScrollTime > scrollDelay) {
      scrollPosition++;
      if (scrollPosition > songName.length() + 3) {
        scrollPosition = 0;
      }
      lastScrollTime = millis();
    }
    
    // Create scrolling effect
    String scrollText = songName + "   " + songName;
    String displayText = scrollText.substring(scrollPosition, scrollPosition + 21);
    display.setCursor(0, 0);
    display.println(displayText);
  } else {
    display.setCursor(0, 0);
    display.println(songName);
  }
  
  // Play/Pause indicator
  display.setCursor(0, 12);
  if (isPlaying) {
    display.println("Playing...");
  } else {
    display.println("Paused");
  }
  
  // Progress bar
  uint32_t currentTime = audio.getAudioCurrentTime();
  uint32_t totalTime = audio.getAudioFileDuration();
  
  // Draw progress bar background
  display.drawRect(0, 32, 128, 8, SH110X_WHITE);
  
  // Draw progress bar fill
  if (totalTime > 0) {
    int progress = (currentTime * 126) / totalTime;
    display.fillRect(1, 33, progress, 6, SH110X_WHITE);
  }
  
  // Time display
  display.setCursor(0, 45);
  display.print(formatTime(currentTime));
  display.print(" / ");
  display.print(formatTime(totalTime));
  
  // Control hints
  display.setCursor(0, 56);
  display.setTextSize(1);
  display.print("^v:Prev/Next SEL:P/P");
  
  display.display();
}

String formatTime(uint32_t seconds) {
  int mins = seconds / 60;
  int secs = seconds % 60;
  String result = "";
  if (mins < 10) result += "0";
  result += String(mins);
  result += ":";
  if (secs < 10) result += "0";
  result += String(secs);
  return result;
}
