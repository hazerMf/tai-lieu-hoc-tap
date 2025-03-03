#include<iostream>
#include<vector>
using namespace std;

vector<int>v,c;
vector<vector<int>> vv;
int k,n,m=0;
int t[100][100],l[100];

void sinh(int i){
    for(int j=0;j<2;j++){
        l[i]=j;
        if(i==k){
            int b[n+1]={0};
            int check=1,sum=0;
            for(int h=1;h<=k;h++){
                if(l[h]==1){
                    for(int p=1;p<=n;p++){
                        if(t[h][p]==1){
                            if(b[p]==0){
                                b[p]=1;sum++;
                            }
                            else check = 0;
                        }
                    }
                }
            }
            if(check==1&&m<=sum){
                m=sum;
                c.push_back(sum);
                for(int p=1;p<=k;p++) v.push_back(l[p]);
                vv.push_back(v);
                v.clear();
            }
        }
        else sinh(i+1);
    }
}

int main(){
    cin >> k >> n;
    for(int i=1;i<=k;i++){
        for(int j=1;j<=n;j++) cin >> t[i][j];
    }
    sinh(1);
    for(int i=0;i<vv.size();i++){
        if(c[i]==m){
            for(int j=0;j<vv[i].size();j++) cout << vv[i][j] << " ";
            cout << endl;
        }
    }
}