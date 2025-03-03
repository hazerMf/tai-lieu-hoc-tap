#include<iostream>
#include<vector>
using namespace std;

vector<int> v,c;
vector<vector<int>> vv;
int n,m=9999;
int a[100][100],use[100]={0},l[100];

void sinh(int i){
    for(int j=1;j<=n;j++){
        if(use[j]==0){
            l[i]=j;use[j]=1;
            if(i==n){
                int sum=0;
                for(int k=1;k<=n;k++) sum +=a[k][l[k]];
                if(sum<=m){
                    m=sum;
                    c.push_back(sum);
                    for(int k=1;k<=n;k++) v.push_back(l[k]);
                    vv.push_back(v);
                    v.clear();
                }
            }
            else sinh(i+1);
            use[j]=0;
        }
    }
}

int main(){
    cin >> n;
    for(int i=1;i<=n;i++){
        for(int j=1;j<=n;j++)cin >> a[i][j];
    }
    sinh(1);
    for(int i=0;i<vv.size();i++){
        if(c[i]==m){
            for(int j=0;j<n;j++){
                cout << "Man" <<j+1<<"->Job"<<vv[i][j]<<"||";
            }
            cout << endl;
        }
    }
}