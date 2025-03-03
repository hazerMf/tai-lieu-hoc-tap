#include<iostream>
#include<queue>
using namespace std;

bool chk(string s,int k,int m){
    int dem0 = 0,dem1 = 0;
    for(int i=0;i<s.length();i++){
        if(s[i]=='0'){
            int len0=1;
            for(int j=i+1;j<s.length();j++){
                if(s[j]=='0') len0++;
                else break;
            }
            if(len0 == k) dem0++;
            if(len0 > k) dem0--;
        }else{
            int len1=1;
            for(int j=i+1;j<s.length();j++){
                if(s[j]=='1') len1++;
                else break;
            }
            if(len1 == m) dem1++;
            if(len1 > m) dem1--;
        }
    }
    if(dem0==1&&dem1==1) return true;
    else return false;
}

int main(){
    int k,m,n;
    cin >> k >> m >> n;
    queue<string> q;
    q.push("");
    while(!q.empty()){
        string s=q.front();
        q.pop();
        if(s.length()>n) break;
        if(s.length()==n&&chk(s,k,m)){
            for(int i=0;i<s.length();i++){
                cout << s[i] << " ";
            }
            cout << endl;
        }
        q.push(s+'0');
        q.push(s+'1');
    }
}