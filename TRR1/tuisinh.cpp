#include<iostream>
#include<queue>
#include<iomanip>
using namespace std;

bool chk(string s, int max, double w[]){
    double weight = 0;
    for(int i=0;i<s.length();i++){
        if(s[i]=='1') weight+=w[i];
    }
    if(weight<=max) return true;
    else return false;
}

int main(){
    int size;
    cin >> size;
    double maxw;
    cin >> maxw;
    double w[size];
    int val[size];
    for(int i=0;i<size;i++){
        cin >> w[i];
    }
    for(int i=0;i<size;i++){
        cin >> val[i];
    }
    queue<string> q;
    q.push("");
    double max = 0;
    string maxs;
    while(!q.empty()){
        string s=q.front();
        q.pop();
        if(s.length()>size) break;
        else if(s.length()==size&&chk(s,maxw,w)){
            int temp=0;
            for(int i=0;i<size;i++){
                if(s[i]=='1') temp+=val[i];
            }
            if(temp>=max){
                max = temp;
                maxs = s;
            }
        }
        q.push(s+"1");
        q.push(s+"0");
    }
    cout << fixed << setprecision(1) << max << endl;
    for(int i=0;i<size;i++){
        cout << maxs[i] << " ";
    }
}