#include<vector>
#include<iostream>
#include<queue>
using namespace std;

bool chk(string s,int a){
    int dem = 0;
    for(int i=0;i<s.length();i++){
        if(s[i]=='1') dem++;
    }
    if(dem==a) return true;
    else return false;
}

int main(){
    int a,b;
    cin >> a >> b;
    vector<int> nums,prefix;
    vector<vector<int>> permutation;
    for(int i=1;i<=b;i++){
        nums.push_back(i);
    }
    queue<string> q;
    q.push("");
    while(!q.empty()){
        string s=q.front();
        q.pop();
        if(s.length()>b) break;
        if(s.length()==b&&chk(s,a)){
            for(int i=0;i<s.length();i++){
                if(s[i]=='1') cout << nums[i] << " ";
            }
            cout << endl;
        }
        q.push(s+'1');
        q.push(s+'0');
    }
}