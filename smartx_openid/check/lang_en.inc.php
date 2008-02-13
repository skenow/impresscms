<?php

// Warning: Some of the constant values here contain the sprintf format code "%s".  That format code must not be removed.

// header text for initial page
$bchk['LANG_HEADER_INITIAL_PAGE'] = "
	<h3>The following client (browser) features will be tested:</h3>
";

// footer text for initial page
$bchk['LANG_FOOTER_INITIAL_PAGE'] = "
";

// header text for test results page
$bchk['LANG_HEADER_RESULTS_PAGE'] = "
	<h3>Here are the test results:</h3>
";

// footer text for test results page
$bchk['LANG_FOOTER_RESULTS_PAGE'] = "
	<!-- example
	<p>If one or more of these tests failed, this reference may be helpful in correcting the issue:
	<a href='http://example.com/' target='_blank'>Example</a>
	</p>
	-->
";

// text for link
$bchk['LANG_CLICK_HERE1'] = 'Click';
$bchk['LANG_CLICK_HERE2'] = 'HERE';
$bchk['LANG_CLICK_HERE3'] = 'to perform the tests.';

$bchk['LANG_SELECT_LANGUAGE'] = 'Select language';

// text for link
$bchk['LANG_CLICK_HERE4'] = 'Click';
$bchk['LANG_CLICK_HERE5'] = 'HERE';
$bchk['LANG_CLICK_HERE6'] = 'to repeat the tests.';

$bchk['LANG_DO_NOT_RELOAD'] = "
	Please do <em>not</em> use the browser's Back, Refresh or Reload buttons, since that may produce incorrect test results.
";

// errors
$bchk['LANG_ERROR_MISSING_POST_VALUE'] = "Internal error: missing POST value '%s'";
$bchk['LANG_ERROR_INTERNAL']           = 'Internal error: %s';

// test results column headers
$bchk['LANG_FEATURE']     = 'Feature';
$bchk['LANG_DESCRIPTION'] = 'Description';
$bchk['LANG_TEST_RESULT'] = 'Test result';

// test names and descriptions
$bchk['LANG_COOKIES']                     = 'Cookies';
$bchk['LANG_COOKIES_DESC']                = 'Cookies can be set and read. (via HTTP headers)';
$bchk['LANG_REFERRER_H']                  = 'Referrer-H';
$bchk['LANG_REFERRER_H_DESC']             = 'The URL of the referring page can be read. (host name)';
$bchk['LANG_REFERRER_HS']                 = 'Referrer-HS';
$bchk['LANG_REFERRER_HS_DESC']            = 'The URL of the referring page can be read. (host name + script name)';
$bchk['LANG_REFERRER_HSQ']                = 'Referrer-HSQ';
$bchk['LANG_REFERRER_HSQ_DESC']           = 'The URL of the referring page can be read. (host name + script name + query string)';
$bchk['LANG_JAVASCRIPT']                  = 'Javascript';
$bchk['LANG_JAVASCRIPT_DESC']             = 'Javascript within a web page can be executed.';
$bchk['LANG_JAVASCRIPT_READ_COOKIE']      = 'Javascript read cookie';
$bchk['LANG_JAVASCRIPT_READ_COOKIE_DESC'] = 'Cookies can be read using Javascript.';
$bchk['LANG_JAVASCRIPT_SET_COOKIE']       = 'Javascript set cookie';
$bchk['LANG_JAVASCRIPT_SET_COOKIE_DESC']  = 'Cookies can be set using Javascript.';
$bchk['LANG_CLOCK']                       = 'Clock';
$bchk['LANG_CLOCK_DESC']                  = 'The server and client clocks agree within %s minutes. (uses Javascript)';

$bchk['LANG_NO_TESTS'] = 'No tests enabled';

// test results
$bchk['LANG_PASS'] = 'PASS';
$bchk['LANG_FAIL'] = 'FAIL';

// clock test details
$bchk['LANG_SECONDS']                = 'seconds';
$bchk['LANG_MINUTES']                = 'minutes';
$bchk['LANG_HOURS']                  = 'hours';
$bchk['LANG_DAYS']                   = 'days';
$bchk['LANG_SERVER_CLOCK']           = 'Server clock';
$bchk['LANG_CLIENT_CLOCK']           = 'Client clock';
$bchk['LANG_DIFFERENCE']             = 'Diference';
$bchk['LANG_SIMULATING_CLOCK_ERROR'] = 'Simulating client clock error of %s seconds.';
$bchk['LANG_NOTE_INTERNET_LAG']      = 'Note that even if both clocks are set accurately, some difference between the clocks is normal, due to internet lag.';

$bchk['LANG_'] = '';

?>