#include<iostream>
using namespace std;
int n,k,a[100],ok;
void sinh(){
	int i=k;
	while(i>=1&& a[i]==n-k+i){
		i--;
	}		
	if(i==0){
		ok=0;
	}
	else{
		a[i]++;
		for(int j=i+1;j<=k;j++){
			a[j]=a[j-1]+1;
		}
	}
}
int main(){
		cin>>k>>n;
		for(int i=1;i<=k;i++) cin>>a[i];
		ok=1;
		int dem=0;
		while(ok&&dem<5){
			sinh();
			for(int i=1;i<=k;i++) cout<<a[i]<<" ";
			cout<<endl;
			dem++;
		}
		cout<<endl;
}