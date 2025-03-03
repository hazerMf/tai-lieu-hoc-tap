#include<iostream>
#include<iomanip>
#include<algorithm>
using namespace std;
int n;
int x[1000],s[1000],check[1000];
double m=999;
double a[100][100];

void tao(){
    for(int i=2;i<=n;i++) check[i]=1;
}

void sinh(int i){
    for(int j=2;j<=n;j++){
        if(check[j]){
            x[i]=j;
            check[j]=0;
            if(i==n){
                double sum=0;
                for(int k=1;k<n;k++){
                    sum+=a[x[k]][x[k+1]];
                }
                sum+=a[x[n]][x[1]];
                if(sum<m){
                    m=sum;
                    for(int k=1;k<=n;k++) s[k]=x[k];
                }
            }
            else sinh(i+1);
            check[j]=1;
        }
    }
}

int main(){
    cin >> n;
    for(int i=1;i<=n;i++){
        for(int j=1;j<=n;j++){
            cin >> a[i][j];
        }
    }
    tao();
    x[1]=1;
    sinh(2);
    cout << fixed << setprecision(1) << m << endl;
    for(int i=1;i<=n;i++) cout << s[i] << " ";
    cout << 1 ;
}