/*
	Name: BEAUTYSEGMENT.CPP
	Copyright: 
	Author: 
	Date: 01-08-23 10:40
	Description: 
	Doan dep trng day int a[] 
  co dang a[d..c] chua day du cac so 1..n
  Cho 1 hoan vi cua 1..M. Hien thi cac doan dep  
*/
#include <bits/stdc++.h>
using namespace std;

void Go() {
   cout << " ? ";
   fflush(stdin);
   if (cin.get() == '.') exit(0);
}

void Print(int a[], int d, int c, const char *msg = "") {
  cout << msg;
  for(int i = d; i <= c; ++i) 
    cout << " " << a[i];
}

void Beauty(int h[], int n) {
  int p[n+1];
  p[0] = 0;
  for(int i = 1; i <= n; ++i) 
    p[h[i]] = i; 
  Print(h, 1, n, "\n Init h: ");
  Print(p, 1, n, "\n      p: ");
  int d = 0; // dem so luong doan dep
  int L = p[1], R = p[1];
  int dt = R-L+1;  
  for(int v = 1; v <= n; ++v) {
    if(p[v] < L || p[v] > R) {
      L = min(L,p[v]);
      R = max(R, p[v]);
      dt = R-L+1;
    }
    if (v == dt) {
        ++d;      
        cout << "\n Doan dep No " << d << ": " << L << ".." << R;
    } 
  } // for
  cout << "\n Total " << d;
}
 
main() {
  int h[] = {0, 8, 6, 2, 1, 3, 7, 5, 9, 4};
  int n = sizeof(h) / sizeof(int) - 1;
  Beauty(h, n);
  cout << "\n T h e   E n d";
  return 0;
}
