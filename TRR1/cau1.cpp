#include<iostream>
#include<vector>
using namespace std;

bool logical(bool p,bool q,bool r,bool s){
    return ((!(p)||q)&&(r==s)) || (!(p));
}

int main(){
    std::vector<bool> boolval = {false,true};
    for(bool p:boolval){
        for(bool q:boolval){
            for(bool r:boolval){
                for(bool s:boolval){
                    bool resulst=logical(p,q,r,s);
                    std::cout << (resulst ? "T" : "F") << std::endl;
                }
            }
        }
    }
    return 0;
}