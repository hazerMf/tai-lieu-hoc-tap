#include <bits/stdc++.h>

using namespace std;

class Fraction {
    public:
        int tu;
        int mau;
        Fraction() { // contructor
            tu = 0;
            mau = 1;
        }
        Fraction(int t, int m) {
            tu = t;
            mau = m;
        }
        Fraction(int t) {
            tu = t;
            mau = 1;
        }
        inline void Print(const char *msg = "") {
            cout << tu << msg << mau << endl;
        }
        inline void Set(int t, int m) {
            tu = t;
            mau = m;
        }
        Fraction Reduce() {
            int d = __gcd(tu, mau);
            tu /= d;
            mau /= d;
            return *this;
        }
        friend bool operator == (Fraction const& x, Fraction const& y) {
            return x.tu * y.mau == y.tu * x.mau;
        }
        friend bool operator != (Fraction const& x, Fraction const& y) {
            return !(x == y);
        }
};

void Print(vector<Fraction> v, const char *msg = "") {
    cout << endl << msg;
    for (int i = 0; i < v.size(); i++) {
        v[i].Print("/");
    }
}

inline bool cmp(Fraction x, Fraction y) {
    return x.tu * y.mau < y.tu * x.mau;
}

void Farey(int n) {
    vector<Fraction> f;
    f.push_back(Fraction(0, 1));
    f.push_back(Fraction(1, 1));
    for (int mau = 2; mau <= n; mau++) {
        for (int tu = 1; tu < mau; tu++) {
            f.push_back(Fraction(tu, mau).Reduce());
        }
    }
    
    sort(f.begin(), f.end(), cmp);
    Print(f, "\nInit f: \n");
    // Luoc cac phan tu giong nhau
    vector<Fraction> res;
    int j = 0;
    res.push_back(f[j]);
    for (int i = 1; i < f.size(); i++) {
        if (f[i] != res.back()) {
            res.push_back(f[i]);
        }
    }
    Print(res, "\nRes: \n");
}

void Go() {
    cout << " ? ";
    fflush(stdin);
    if (cin.get() == '.') exit(0);
}


int main() {
    Farey(6);
    cout << endl << "\n The End \n";
    return 0;
}