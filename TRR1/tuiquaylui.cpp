#include<iostream>
#include<iomanip>
using namespace std;

double ans=0;
string ansr;

void quay(string s,double max,int size,int weight[],int val[]){
    if(s.length()>size) return;
    else if(s.length()==size){
        int tong=0;
        for(int i=0;i<size;i++){
            if(s[i]=='1') tong += weight[i];
        }
        if(tong<=max){
            int dem=0;
            for(int i=0;i<size;i++){
                if(s[i]=='1') dem += val[i];
            }   
            if(dem>=ans){
                ans = dem;
                ansr = s;
            }
        }
    }
    quay(s+'1',max,size,weight,val);
    quay(s+'0',max,size,weight,val);
}

int main(){
    int size;cin >> size;
    double max;cin>> max;
    int weight[size];
    int value[size];
    for(int i=0;i<size;i++){
        cin >> weight[i];
    }
    for(int i=0;i<size;i++){
        cin >> value[i];
    }
    string s="";
    quay(s,max,size,weight,value);
    cout << fixed << setprecision(1) << ans << endl;
    for(int i=0;i<size;i++){
        cout << ansr[i] << " ";
    }
}