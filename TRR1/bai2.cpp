#include <iostream>
#include <vector>

bool is_valid_number(int num) {
    std::vector<int> digits(10, 0);
    int distinct_digits = 0;

    while (num > 0) {
        int digit = num % 10;
        if (digit == 2 || digit == 3 || digit == 4 || digit == 6 || digit == 7) {
            return false;  
        }
        if (digits[digit] == 0) {
            digits[digit] = 1;
            distinct_digits++;
        } else {
            return false; 
        }
        num /= 10;
    }

    return distinct_digits == 5;
}

int main() {
    int count = 0;
    for (int number = 10000; number < 100000; ++number) {
        if (is_valid_number(number)) {
            count++;
        }
    }
    std::cout << count << std::endl;
    for (int number = 10000; number < 100000; ++number) {
        if (is_valid_number(number)) {
            std::cout << number << ' ';

        }
    } std::cout<<std::endl;
    std::cout << count << std::endl;
    return 0;
}