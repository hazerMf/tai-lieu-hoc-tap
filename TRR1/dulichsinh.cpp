#include<iostream>
#include<iomanip>
#include<algorithm>
using namespace std;

void tao(int x[],int n){
    for(int i=1;i<=n;i++) x[i]=i;
}

void sinh(int a[],int n,int& ok){
    int i=n-1;
    while(i>=2&&a[i]>a[i+1]) i--;
    if(i<=1) ok = 0;
    else{
        int j=n;
        while(a[j]<a[i]) j--;
        swap(a[i],a[j]);
        reverse(a+i+2,a+n+1);
    }
}

int main(){
    int n;cin >> n;
    double a[n+1][n+1];
    for(int i=1;i<=n;i++){
        for(int j=1;j<=n;j++){
            cin >> a[i][j];
        }
    }
    int ok=1;
    double min=9999;
    int x[n+1],s[n+1];
    tao(x,n);
    while(ok){
        double sum=0;
        for(int i=1;i<n;i++){
            sum+=a[x[i]][x[i+1]];
        }
        sum+=a[x[n]][x[1]];
        if(sum<min){
            min = sum;
            for(int i=1;i<=n;i++) s[i] = x[i];
        }
        sinh(x,n,ok);
    }
    cout << fixed << setprecision(1) << min << endl;
    for(int i=1;i<=n;i++){
        cout << s[i] << " ";
    }
    cout << "1";
}