<?php

##############################################################
##  COPYRIGHT © 2004    DAVE LERNER    ALL RIGHTS RESERVED  ##
##############################################################

// Browser Check configuration parameters

// character set
$bchk['CHARSET'] = 'iso-8859-1';

// Default language for displayed text.
// Must be a key in $bchk['LANGUAGES'], and the file "lang_{$bchk['LANGUAGE_DEFAULT']}.inc.php" must exist.
$bchk['LANGUAGE_DEFAULT'] = 'en';

// Auto-detect language using the "Accept-language" HTTP header provided by the client.
// Only languages defined in $bchk['LANGUAGES'] are available.
// 1 = auto-detection enabled, 0 = auto-detection disabled
$bchk['LANGUAGE_AUTO'] = 1;

// Available languages for displayed text.
//
// The array keys are the language codes used in the "Accept-language" HTTP header,
// and are also used in forming the language definition filenames.
//
// The array values are the descriptive language names that will be displayed to the user.
//
// Example: If $bchk['LANGUAGES']['en'] == 'english', then the language name displayed to the user is 'english',
// and the language file is lang_en.inc.php.
$bchk['LANGUAGES']['en'] = 'english';
$bchk['LANGUAGES']['fr'] = 'français';

// These flags control which test results are displayed.
//
// Set flag to 1 to show the results for a test, 0 to hide the results.
//
// If you get reports from users about certain tests failing, and these tests aren't relevant
// for your situation, you may wish to hide the results for those tests.
//
// For descriptions of the tests, run the script, or see the function make_test_results_display().
$bchk['TEST_COOKIES']                = 1;
$bchk['TEST_REFERRER_H']             = 1;
$bchk['TEST_REFERRER_HS']            = 1;
$bchk['TEST_REFERRER_HSQ']           = 1;
$bchk['TEST_JAVASCRIPT']             = 1;
$bchk['TEST_JAVASCRIPT_READ_COOKIE'] = 1;
$bchk['TEST_JAVASCRIPT_SET_COOKIE']  = 1;
$bchk['TEST_CLOCK']                  = 1;

// maximum difference allowed between server and client clocks
$bchk['CLOCK_TOLERANCE_MINUTES'] = 15;

// expires value for cookies, relative to "now"
//
// The script deletes the test cookies after the test is complete, but in case
// stray cookies are left on the client, this value should be relatively small.
$bchk['COOKIE_EXPIRES_MINUTES'] = 60;

// HTML font colors for displaying test results
$bchk['PASSED_TEST_COLOR'] = 'green';
$bchk['FAILED_TEST_COLOR'] = 'red';

// for simulating a clock error
//
// If this value is nonzero, it will be added to the value read from the client clock.
$bchk['FAKE_CLOCK_ERROR_SECONDS'] = 0;

// For detecting attempt to invoke test with "old" cookie name in URL,
// which could occur if the URL is bookmarked.
//
// Setting this to a small value on a slow server could result in the script
// looping indefinitely, so do NOT adjust this unless you know what you're doing.
$bchk['COOKIE_TIMEOUT_SECONDS'] = 60;

?>