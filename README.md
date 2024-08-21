## Hello, thank you for visiting another one of my programs :) 

```
                 _                                                            _
               _(_)_                          wWWWw   _                     _(_)_                          wWWWw   _ 
   @@@@       (_)@(_)   vVVVv     _     @@@@  (___) _(_)_       @@@@       (_)@(_)   vVVVv     _     @@@@  (___) _(_)_
  @@()@@ wWWWw  (_)\    (___)   _(_)_  @@()@@   Y  (_)@(_)     @@()@@ wWWWw  (_)\    (___)   _(_)_  @@()@@   Y  (_)@(_)
   @@@@  (___)     `|/    Y    (_)@(_)  @@@@   \|/   (_)\       @@@@  (___)     `|/    Y    (_)@(_)  @@@@   \|/   (_)\
    /      Y       \|    \|/    /(_)    \|      |/      |        /      Y       \|    \|/    /(_)    \|      |/      |
 \ |     \ |/       | / \ | /  \|/       |/    \|      \|/    \ |     \ |/       | / \ | /  \|/       |/    \|      \|/
  |//   \\|///  \\\|//\\\|/// \|///  \\\|//  \\|//  \\\|//  jgs|//   \\|///  \\\|//\\\|/// \|///  \\\|//  \\|//  \\\|//
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
```

### Commission Calculation
### Usage instructions:
0. the required PHP version is at least `8.0`, as some PHP 8 features such as `match` and construction promotion have been used;
1. Exchange Rate look-up API was changed to [exchangerate-api.com](https://www.exchangerate-api.com/), as [exchangeratesapi.io](https://exchangeratesapi.io/) requires credit card information even for the free plan;
2. start off by installing the dependencies - run `composer install`;
3. the program can be run by running `php main.php input.csv`. If you would like to use a different input file, replace `input.csv` with the name of your file, e.g. `php main.php myfile.csv`;
2. all the tests (including the one to compare the results with the ones provided in the task) can be executed by running `bin/phpunit tests` or, preferably, `composer run-script run-tests`;
