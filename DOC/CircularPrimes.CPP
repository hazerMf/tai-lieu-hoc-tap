/*
	Name: Circular Primes
	Copyright: (C) 2023
	Author: Devcpp Fan 
	Date: 22-06-23 11:50
	Description:
	Liet ke cac so nguyen to quay vong
	
*/
#include <bits/stdc++.h> 
using namespace std;
const int MN = 1000000; // 1M
typedef bitset<MN+1> BS;
BS e;
const int p10[] = {1, 10, 100, 1000, 10000, 100000, 1000000}; // 10^i
const bool BADDIG[] = {true, false, true, false, true, true, true, false, true, false};

// cac so ng to < n
// Sang Eratostjhenes
void Sieve(int n) {	
	e.set(); // all 1
	e[0] = e[1] = 0;
	// xoa cac so chan tu 4
	for(int i = 4; i <= n; i += 2) e[i] = 0;  
	int sn = (int) sqrt(n);
	// duyet cac so le
	for(int i = 3; i <= sn; i += 2)
	  if (e[i]) 
	     for(int j = i*i; j < n; j += i) 
	        e[j] = 0;	
}

// so chu so cua x
int Len(int x) {
	// x < 10: len = 1
	return (x < 10) ? 1 : Len(x / 10) + 1;	
}

// Moi lan quay p deu la ng to
// neu p chua cac chu so bad thi bo: 024685 
bool AllRotate(int p) {	
	int m = Len(p);
	for(int i = 1; i < m; ++i) {
		 // quay
		 int c = p % 10;
		 if (BADDIG[c]) return 0;
		 p = c * p10[m-1] + (p / 10);
		 if (e[p] == 0) return 0;
  }
	return 1;
}

void CircularPrimes(int n) {
	Sieve(n);
	//for(int i = 1; i < 100; ++i) 
	  //if (e[i]) cout << " " << i;
	int d = 1;
	cout << "\n 2";
	for(int i = 3; i < 10; i += 2) {
		if (e[i]) {
		  ++d;
		  cout << "\n " << i;
	  }	
  }
  for(int i = 11; i < n; i += 2) {
		if (e[i]) { // i nguyen to 
		  if (AllRotate(i)) { // moi phep quay deu nguyen to
		    ++d;
				cout << "\n " << i;
			}
	  }	
  }
  cout << "\n Total " << d;
}

main() {  
  CircularPrimes(MN);
  //for (int x = 0; x < 200; x += 15) 
	  //cout << "\n " << x << " : " << Len(x);
  cout << endl << "\n\n T h e   E n d \n";
  return 0;
}

