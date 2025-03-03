#include<iostream>
#include<vector>
using namespace std;

void sinh(int a[],int n,int& ok){
    int i=n;
    while(i>=1&&a[i]==1){
        a[i]=0;--i;
    }
    if(i==0) ok=0;
    else a[i]=1;
}

int main(){
    vector<int> v,c;
    vector<vector<int>> vv;
    int k,n,m=0;
    cin >> k >> n;
    int t[k+1][n+1],l[k+1]={};
    for(int i=1;i<=k;i++){
        for(int j=1;j<=n;j++) cin >> t[i][j];
    }
    int ok=1;
    while(ok){
        sinh(l,k,ok);
        int b[n+1]={0};
        int check=1,sum=0;
        for(int i=1;i<=k;i++){
            if(l[i]==1){
                for(int j=1;j<=n;j++){
                    if(t[i][j]==1){
                        if(b[j]==0){
                            b[j]=1;sum++;
                        }
                        else{
                            check=0;
                        }
                    }
                }
            }
        }
        if(check==1&&m<=sum){
            m=sum;
            c.push_back(sum);
            for(int i=1;i<=k;i++) v.push_back(l[i]);
            vv.push_back(v);
            v.clear();
        }
    }
    for(int i=0;i<vv.size();i++){
        if(c[i]==m){
            for(int j=0;j<vv[i].size();j++) cout << vv[i][j]<<" ";
            cout << endl;
        }
    }
}