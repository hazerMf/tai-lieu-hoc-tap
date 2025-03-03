/*
	Name: Rhythm.CPP
	Copyright: (C) 2023
	Author: Devcpp Fan 
	Date: 22-06-23 11:50
	Description:
	Nhip dieu
	1 dong nhat
	2 Tang chat
	3 dong bien
	4 giam chat
	5 nghich bien
	0 len xuong
*/
#include <bits/stdc++.h> 
using namespace std;

int Rhythm(float a[], int n) {
	int b = 0, t = 0, g = 0;
	for(int i = 1; i < n; ++i) 
	  if (a[i] == a[i-1]) b = 1;
	  else if (a[i] > a[i-1]) t = 1;	
	  else g = 1;
	// return (4*(n-1-t-b > 0) + 2*(t > 0) + (b > 0)) % 7 % 6; 
	return (4*g + 2*t + b) % 7 % 6; 
}

void Print(float a[], int n, const char *msg = "") {
   cout << msg;
   for(int i = 0; i < n; ++i) 
     cout << " " << a[i];
} 

void Gen(float a[], int n) {
	srand(time(NULL)); // Start random
	for(int i = 0; i  < n; ++i)
	   a[i] = rand() % 100;	
}

void GenTang(float a[], int n) {
	srand(time(NULL)); // Start random
	a[0] = rand() % 100;	
	for(int i = 1; i  < n; ++i)
	   a[i] = a[i-1] + rand() % 10;	
}

void Rev(float a[], int n) {
   int d = 0, c = n-1;
	 while(d < c) {
	 	  float x = a[d]; a[d] = a[c]; a[c] = x;
	 	  ++d; --c;	 
   }
}

main() {  
  int n = 10;
  float a[n];
  GenTang(a, n);
  Print(a, n);
  cout << "\n " << Rhythm(a,n);
  Rev(a, n);
  cout << "\n " << Rhythm(a,n);
  cout << endl << "\n\n T h e   E n d \n";
  return 0;
}

