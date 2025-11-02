const int r = 2,g = 4,b = 5;

void setup() {
  // Initialize the LED pin as an output
  pinMode(r, OUTPUT);              
  pinMode(g, OUTPUT);
  pinMode(b, OUTPUT);
}

void loop() {
  
  for(int i =0;i<255;i+=15){
    setColor(255,i,0);
    delay(150);
  }
  for(int i =255;i>0;i-=15){
    setColor(i,255,0);
    delay(150);
  }
  
  for(int i =0;i<255;i+=15){
    setColor(0,255,i);
    delay(150);
  }
  for(int i =255;i>0;i-=15){
    setColor(0,i,255);
    delay(150);
  }

  for(int i =0;i<255;i+=15){
    setColor(i,0,255);
    delay(150);
  }
  for(int i =255;i>0;i-=15){
    setColor(255,0,i);
    delay(150);
  }
}

// Helper function to set RGB values
void setColor(int red, int green, int blue) {
  analogWrite(r, red);
  analogWrite(g, green);
  analogWrite(b, blue);
}