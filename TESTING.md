

About
-----
* BrickOO has been developed using [PHPUnit](http://www.phpunit.de/manual/3.6/en).
* The test suites assure the functionality of the framework.
* All test suites **must** pass and should not contain any skipped test cases.
* The test suites **must** cover all classes provided by BrickOO.
* The test suites can be found in the [Tests directory](https://github.com/celestino/BrickOO/tree/master/Tests), which relies on the same structure as the framework.


Requirements
------------
BrickOO requires PHPUnit 3.6 for the tests.


Installation
------------
The PHPUnit documentation can be found here:
[PHPUnit](http://www.phpunit.de/manual/3.6/en/installation.html)


Testing BrickOO
---------------
* On GNU/Linux you just run phpunit, on Windows you may need to run phpunit.bat within your PHP directory.
* Testing the framework can be done by running this line:
    `phpunit -c /path/to/brickoo/Tests/phpunit.xml /path/to/brickoo/Tests`
* The HMTL code coverage can be created by running this line:
    `phpunit -c /path/to/BrickOO/Tests/phpunit.xml --coverage-html /path/to/your/coverage/directory /path/to/brickoo/Tests`
* You can also just test one test suite if you like, example:
    `phpunit -c /path/to/brickoo/Tests/phpunit.xml /path/to/brickoo/Tests/Library/Core/BrickooTest.php`


Testing using PTI in Eclipse
----------------------------
* Select under Debug the print output to console mode.
* Select the `/path/to/brickoo/Tests/bootstrap.php file` as bootstrap file.