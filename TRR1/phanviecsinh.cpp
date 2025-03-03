#include<iostream>
#include<vector>
#include<algorithm>
using namespace std;

void sinh(int a[],int n,int& ok){
    int i=n-1;
    while(i>=1&&a[i]>a[i+1]) i--;
    if(i==0) ok = 0;
    else{
        int j=n;
        while(a[i]>a[j]) j--;
        swap(a[i],a[j]);
        reverse(a+i+1,a+n+1);
    }
}

int main(){
    vector<int> v,c;
    vector<vector<int>> vv;
    int n,min=999;cin>>n;
    int a[n+1][n+1],l[n+1];
    for(int i=1;i<=n;i++) l[i]=i;
    for(int i=1;i<=n;i++){
        for(int j=1;j<=n;j++) cin >> a[i][j];
    }
    int ok=1;
    while(ok){
        int sum=0;
        for(int i=1;i<=n;i++) sum += a[i][l[i]];
        if(sum<=min){
            min = sum;
            c.push_back(sum);
            for(int i=1;i<=n;i++) v.push_back(l[i]);
            vv.push_back(v);
            v.clear();
        }
        sinh(l,n,ok);
    }
    for(int i=0;i<vv.size();i++){
        if(c[i]==min){
            for(int j=0;j<n;j++){
                cout << "Man" << j+1 << "->Job"<<vv[i][j]<<"||";
            }
            cout << endl;
        }
    }
}