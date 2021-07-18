This application was made using the following:
1. Laravel 7.29
2. Mysql 8
3. Redis 5.0.7

Development tools:
1. Visual Studio Code
2. Postman / Rest Client (VS Code extension)
3. WSL2 (optional)
4. PHP 7 with enabled extensions

Preparations (when writing this code, I used ubuntu in wsl):
1. Install necessary programs (mentioned above).
2. Make sure that all required applications and servers are running.
3. In mysql, create a database with name "kumudb"
4. In the source folder, run this command. "php artisan migrate"
5. Using terminal. run "php artisan serve"


These are the endpoints:
POST /register - Use this to register an account, use the token included in the response.

POST /login
GET /users
POST /user/{id}
GET /user/{id}
POST /logout



BONUS CHALLENGE:

Calculate Hamming Distance
The Hamming distance between two integers is the number of positions at which the corresponding
bits are different.
Given two integers x and y, calculate the Hamming distance.
Note:
0 ≤ x, y < 2^31

0 ≤ x means 0, 1, 2, 3 and so on.

y < 2^31 means 2,147,483,648 ; 2,147,483,647 ; and so on.

These two ranges will have common numbers between 0 - 2,147,483,648 (2^31)

All less than 0 are included in y < 2^31, while all numbers beyond 2^31 are part of 0 <= x.

Therefore:

Hamming distance = all numbers less than 0 and greater than 2,147,483,648 so my answer is Infinite. We use this reasoning for easier conclusion, "If size of two strings is not same then the hamming distance between them is infinite."

