#include <WiFi.h>
#include <WebServer.h>
#include <SD.h>
#include <SPI.h>
#include <Wire.h>
#include <Adafruit_GFX.h>
#include <Adafruit_SH110X.h>

// WiFi credentials - CHANGE THESE!
const char* ssid = "Thoa Giang";
const char* password = "0978181004";

// Display settings
#define SCREEN_WIDTH 128
#define SCREEN_HEIGHT 64
#define SDA_PIN 21
#define SCL_PIN 22

// SD Card pins
#define SD_MISO 13
#define SD_MOSI 14
#define SD_SCK 27
#define SD_CS 33  // Changed from 33 to 5 - try this first

// Create display object for SH1106
Adafruit_SH1106G display = Adafruit_SH1106G(SCREEN_WIDTH, SCREEN_HEIGHT, &Wire, -1);

WebServer server(80);

// HTML page for file upload
const char* uploadHTML = R"rawliteral(
<!DOCTYPE html>
<html>
<head>
  <title>ESP32 MP3 Upload</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body { font-family: Arial; text-align: center; margin: 20px; }
    .container { max-width: 500px; margin: auto; }
    input[type=file] { margin: 20px 0; }
    button { background-color: #4CAF50; color: white; padding: 14px 20px; 
             border: none; cursor: pointer; font-size: 16px; }
    button:hover { background-color: #45a049; }
    .status { margin-top: 20px; padding: 10px; }
    .success { background-color: #d4edda; color: #155724; }
    .error { background-color: #f8d7da; color: #721c24; }
  </style>
</head>
<body>
  <div class="container">
    <h1>ESP32 MP3 File Upload</h1>
    <form method="POST" action="/upload" enctype="multipart/form-data">
      <input type="file" name="file" accept=".mp3" required>
      <br>
      <button type="submit">Upload MP3</button>
    </form>
    <div id="status"></div>
    <hr>
    <h3>Files on SD Card:</h3>
    <div id="files"></div>
    <button onclick="location.reload()">Refresh</button>
  </div>
  <script>
    fetch('/list').then(r => r.json()).then(data => {
      document.getElementById('files').innerHTML = data.files.join('<br>') || 'No files';
    });
  </script>
</body>
</html>
)rawliteral";

void setup() {
  Serial.begin(115200);
  
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
  display.println("Initializing...");
  display.display();
  
  // Initialize SD card
  SPI.begin(SD_SCK, SD_MISO, SD_MOSI, SD_CS);
  
  Serial.println("Testing SD card...");
  Serial.print("MISO: "); Serial.println(SD_MISO);
  Serial.print("MOSI: "); Serial.println(SD_MOSI);
  Serial.print("SCK: "); Serial.println(SD_SCK);
  Serial.print("CS: "); Serial.println(SD_CS);
  
  if (!SD.begin(SD_CS)) {
    Serial.println("SD Card initialization failed!");
    Serial.println("Possible reasons:");
    Serial.println("1. SD card not inserted");
    Serial.println("2. Wrong wiring");
    Serial.println("3. Faulty SD card");
    Serial.println("4. SD card needs FAT32 format");
    
    display.clearDisplay();
    display.setCursor(0,0);
    display.println("SD Card FAILED!");
    display.println("");
    display.println("Check:");
    display.println("- Card inserted?");
    display.println("- Wiring OK?");
    display.println("- FAT32 format?");
    display.display();
    while(1);
    display.setCursor(0,0);
    display.println("SD Card FAILED!");
    display.display();
    while(1);
  }
  Serial.println("SD Card initialized.");
  
  display.clearDisplay();
  display.setCursor(0,0);
  display.println("SD Card OK");
  display.println("Connecting WiFi...");
  display.display();
  
  // Connect to WiFi
  WiFi.begin(ssid, password);
  Serial.print("Connecting to WiFi");
  
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  
  Serial.println("\nConnected!");
  Serial.print("IP Address: ");
  Serial.println(WiFi.localIP());
  
  // Display IP address on screen
  display.clearDisplay();
  display.setCursor(0,0);
  display.setTextSize(1);
  display.println("WiFi Connected!");
  display.println("");
  display.println("Upload at:");
  display.setTextSize(2);
  display.println(WiFi.localIP());
  display.display();
  
  // Setup web server routes
  server.on("/", HTTP_GET, []() {
    server.send(200, "text/html", uploadHTML);
  });
  
  server.on("/upload", HTTP_POST, 
    []() {
      server.send(200, "text/html", 
        "<html><body><h2>Upload Complete!</h2>"
        "<a href='/'>Back to Upload</a></body></html>");
    },
    handleFileUpload
  );
  
  server.on("/list", HTTP_GET, []() {
    String json = "{\"files\":[";
    File root = SD.open("/");
    File file = root.openNextFile();
    bool first = true;
    
    while (file) {
      if (!file.isDirectory()) {
        String fileName = String(file.name());
        if (fileName.endsWith(".mp3") || fileName.endsWith(".MP3")) {
          if (!first) json += ",";
          json += "\"" + fileName + "\"";
          first = false;
        }
      }
      file.close();
      file = root.openNextFile();
    }
    root.close();
    
    json += "]}";
    server.send(200, "application/json", json);
  });
  
  server.begin();
  Serial.println("Web server started!");
  Serial.println("Open your browser and go to: http://" + WiFi.localIP().toString());
}

void loop() {
  server.handleClient();
}

void handleFileUpload() {
  HTTPUpload& upload = server.upload();
  static File uploadFile;
  
  if (upload.status == UPLOAD_FILE_START) {
    String filename = "/" + upload.filename;
    Serial.println("Upload Start: " + filename);
    
    // Delete if exists
    if (SD.exists(filename)) {
      SD.remove(filename);
    }
    
    uploadFile = SD.open(filename, FILE_WRITE);
    if (!uploadFile) {
      Serial.println("Failed to open file for writing");
      return;
    }
  } 
  else if (upload.status == UPLOAD_FILE_WRITE) {
    if (uploadFile) {
      uploadFile.write(upload.buf, upload.currentSize);
      Serial.print(".");
    }
  } 
  else if (upload.status == UPLOAD_FILE_END) {
    if (uploadFile) {
      uploadFile.close();
      Serial.println("\nUpload Complete: " + String(upload.totalSize) + " bytes");
    }
  }
}
