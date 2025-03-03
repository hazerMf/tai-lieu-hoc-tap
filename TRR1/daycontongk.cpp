#include<iostream>
#include<queue>
using namespace std;

void chk(string s,int m,int nums[]){
    int tong = 0;
    for(int i=0;i<s.length();i++){
        if(s[i]=='1') tong += nums[i];
    }
    if(tong==m){
        for(int i=0;i<s.length();i++){
            if(s[i]=='1') cout << nums[i] << " ";
        }
        cout << endl;
    }else{
        return;
    }
}

int main(){
    int m,n;
    cin >> m >> n;
    int nums[n];
    for(int i=0;i<n;i++){
        cin >> nums[i];
    }
    queue<string> q;
    q.push("");
    while(!q.empty()){
        string s=q.front();
        q.pop();
        if(s.length()>n) break;
        if(s.length()==n) chk(s,m,nums);
        q.push(s+'0');
        q.push(s+'1');
    }
}