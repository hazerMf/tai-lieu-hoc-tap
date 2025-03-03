#include<iostream>
#include<vector>
using namespace std;

bool chk(int a){
    if(a%2==0) return false;
    if(a%5==0) return false;
    if(a%15==0) return false;
    return true;
}

int main(){
    vector<int> val;
    for(int i=4000;i<=5000;i++){
        if(chk(i)) val.push_back(i);
    }
    cout << val.size() << endl;
    for(auto i:val) cout << i << " ";
    cout << endl<< val.size() ;
}