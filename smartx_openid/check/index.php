<?php

##############################################################
##  COPYRIGHT © 2004    DAVE LERNER    ALL RIGHTS RESERVED  ##
##############################################################

error_reporting(E_ALL);

// ---------------------------------------
// Browser Check
//
// Test whether various client features are enabled.
//
// The processing is organized into three pages:
//
// Page 1
//    This is the initial page generated when the script is first executed.
//    This page sets test cookie 1, and does a self-redirect, passing the cookie name in the URL for use by page 2.
//    The cookie name parameter in the URL has the second function of ensuring that the URLs for this page and the
//    following page are different, so that HTTP_REFERER "spoofing" is more readily detected.
//
// Page 2
//    This page uses Javascript to set a javascript-enabled flag, to read test cookie 1, to set test cookie 2
//    and to get the client clock.
//    The results are passed to page 3 in hidden form fields.
//
// Page 3
//    This page checks and displays the test results.  The cookies are deleted.
//
// Cookie usage:
//
//    Cookie 1 is set by HTTP headers, and read both by HTTP headers and by Javascript.
//    Cookie 2 is set by Javascript and read by HTTP headers.
//    So cookie 1 is used to test whether cookies can be set and read by HTTP headers, and whether cookies can be
//    read by Javascript.  Cookie 2 is used to test whether cookies can be set by Javascript.
//    The name of cookie 1 passed in the URL is also used to test that HTTP_REFERER was correctly provided by the
//    browser.
//    Note that although the script deletes the cookies after the test is complete, stray cookies may be left on
//    the client if the test is not completed.
// ---------------------------------------
include("../mainfile.php");
include(XOOPS_ROOT_PATH."/header.php");

$xoopsOption['show_rblock'] = 1; 


// configuration parameters
require_once('config.inc.php');

// Determine language for displayed text.
$bchk['lang'] = get_language();
require_once("lang_{$bchk['lang']}.inc.php");

// Generate appropriate page.
$cookie1_name    = @$_GET['cookie1_name'];
$cookie1_timeout = $bchk['COOKIE_TIMEOUT_SECONDS'];
if (!empty($_POST['action_page3'])) {
	do_page3();
} elseif (!empty($cookie1_name) and time() - cookie_timestamp($cookie1_name) <= $cookie1_timeout) {
	do_page2();
} else {
	do_page1();
}

// ----------------
function do_page1()
{
	global $bchk;

	// Set test cookie1.
	// The cookie name is generated using a timestamp and random number, so that it will be unique for each test.
	// The cookie value is the MD5 digest of the name, to make the value unique, but easy to check.
	// The cookie name is passed to the next page in the URL.
	$cookie1_name  = make_cookie_name('bchk1');
	setcookie($cookie1_name, md5($cookie1_name), time() + $bchk['COOKIE_EXPIRES_MINUTES'] * 60);

	header("Location: {$_SERVER['PHP_SELF']}?cookie1_name=$cookie1_name&bchk_lang={$bchk['lang']}");
	exit; // ensure nothing is done after the location header is output
}

// ----------------
function do_page2()
{
	global $bchk;

	// Get the name of cookie1, and pass it to the next page in a hidden form field.
	// Compute the cookie value for use by the Javascript.
	$cookie1_name  = $_GET['cookie1_name'];
	$cookie1_value = md5($cookie1_name);

	// Parameters for setting test cookie2.
	// The cookie is set by Javascript, and the cookie name is passed to the next page in a hidden form field.
	$cookie2_name  = make_cookie_name('bchk2');
	$cookie2_value  = md5($cookie2_name);
	$cookie2_expires = (time() + $bchk['COOKIE_EXPIRES_MINUTES'] * 60) * 1000; // milliseconds

	$results_display = make_test_results_display();

	$language_options = '';
	foreach ($bchk['LANGUAGES'] as $lang_code => $lang_name) {
		if (is_file("lang_{$lang_code}.inc.php")) {
			$selected = $lang_code == $bchk['lang'] ? "selected='selected'" : '';
			$language_options .= "<option value='$lang_code' $selected>$lang_name</option>";
		}
	}

	// This copyright notice must not be removed.
	$copyright_notice = copyright_notice();

	echo <<<END

	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset={$bchk['CHARSET']}" />
		<title>Browser Check</title>
		<script language='javascript' type='text/javascript'>
		<!--
			function set_cookie(name, value, expires)
			{
				document.cookie = name + '=' + escape(value) + '; expires=' + expires.toGMTString();
			}
			function get_cookie(name)
			{
				var cookie = document.cookie;
			    var start = cookie.indexOf(name + '=');
			    var len = start + name.length + 1;
			    if (!start && name != cookie.substring(0, name.length)) return null;
			    if (start == -1) return null;
			    var end = cookie.indexOf(';', len);
			    if (end == -1) end = cookie.length;
			    return unescape(cookie.substring(len, end));
			}
			function on_submit(the_form)
			{
				the_form.javascript_on.value = 1; // set to a nonzero value
				var client_time = new Date(); // today's date
				the_form.client_unixtimestamp.value = client_time.valueOf(); // milliseconds since January 1 1970 00:00:00 GMT
	
				// Fetch test cookie1, verify that it was read correctly, and pass this test result
				// to the next page in a hidden form field.
				var cookie1_value = get_cookie('$cookie1_name');
				if ((cookie1_value != null) && (cookie1_value == '$cookie1_value')) {
					the_form.cookie1_ok.value = 1;
				}
	
				// set test cookie2
				var cookie2_expires = new Date($cookie2_expires);
				set_cookie('$cookie2_name', '$cookie2_value', cookie2_expires);
			}
		//-->
		</script>
	</head>
	<body>
	{$bchk['LANG_HEADER_INITIAL_PAGE']}
	$results_display
	<table width='75%'>
	<tr>
		<td align='left'>
			<form name='muffy' method='post' action='{$_SERVER['PHP_SELF']}'>
			<input type='hidden' name='cookie1_name' value='$cookie1_name' />
			<input type='hidden' name='cookie2_name' value='$cookie2_name' />
			<!-- The following values will remain zero unless changed by Javascript. -->
			<input type='hidden' name='javascript_on' value='0' />
			<input type='hidden' name='client_unixtimestamp' value='0' />
			<input type='hidden' name='cookie1_ok' value='0' />
			<input type='hidden' name='bchk_lang' value='{$bchk['lang']}' />
			<b>{$bchk['LANG_CLICK_HERE1']}
			<input type='submit' name='action_page3' value='{$bchk['LANG_CLICK_HERE2']}' onclick='on_submit(this.form)' />
			{$bchk['LANG_CLICK_HERE3']}</b>
			</form>
		</td>
		<td align='right'>
			<form name='buffy' method='post' action='{$_SERVER['PHP_SELF']}'>
			<table>
			<tr>
				<td><select name='bchk_lang' size='3'>$language_options</select></td>
				<td><input type='submit' name='action_lang' value='{$bchk['LANG_SELECT_LANGUAGE']}' /></td>
			</tr>
			</table>
			</form>
		</td>
	</tr>
	</table>
	{$bchk['LANG_FOOTER_INITIAL_PAGE']}
	<br /><p>$copyright_notice</p>
	</body>
	</html>

END;

}

// ----------------
function do_page3()
{
	global $bchk;

	// Fetch test cookie1 and check its value.
	isset($_POST['cookie1_name']) or trigger_error(sprintf($bchk['LANG_ERROR_MISSING_POST_VALUE'], 'cookie1_name'), E_USER_ERROR);
	$cookie1_name  = $_POST['cookie1_name'];
	$cookie_passed = isset($_COOKIE[$cookie1_name]) && $_COOKIE[$cookie1_name] == md5($cookie1_name);

	// Fetch test cookie2, check its value, and delete the cookie.
	isset($_POST['cookie2_name']) or trigger_error(sprintf($bchk['LANG_ERROR_MISSING_POST_VALUE'], 'cookie2_name'), E_USER_ERROR);
	$cookie2_name = $_POST['cookie2_name'];
	$javascript_cookie_set_passed = isset($_COOKIE[$cookie2_name]) && $_COOKIE[$cookie2_name] == md5($cookie2_name);

	// Delete cookies created by this script.
	foreach (array_keys($_COOKIE) as $cookie_name) {
		if (preg_match('/^bchk\d_\d+_[0-9a-f]+$/', $cookie_name)) {
#echo "deleting cookie '$cookie_name'<br />\n";#*#DEBUG#
			setcookie($cookie_name, 'delete_me', time() - 24 * 3600);
		}
	}

	// check HTTP_REFERER
	$http_referer = strtolower(@$_SERVER['HTTP_REFERER']);
	$referrer_h_should_be   = strtolower("http://{$_SERVER['HTTP_HOST']}");
	$referrer_hs_should_be  = strtolower("http://{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}");
	$referrer_hsq_should_be = strtolower("http://{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}?cookie1_name={$_POST['cookie1_name']}");
	$referrer_h_passed   = !empty($http_referer) && strpos($http_referer, $referrer_h_should_be)   === 0;
	$referrer_hs_passed  = !empty($http_referer) && strpos($http_referer, $referrer_hs_should_be)  === 0;
	$referrer_hsq_passed = !empty($http_referer) && strpos($http_referer, $referrer_hsq_should_be) === 0;

	// check Javascript-on flag
	isset($_POST['javascript_on']) or trigger_error(sprintf($bchk['LANG_ERROR_MISSING_POST_VALUE'], 'javascript_on'), E_USER_ERROR);
	$javascript_passed = $_POST['javascript_on'] != 0;

	// Check whether Javascript correctly read test cookie1.
	isset($_POST['cookie1_ok']) or trigger_error(sprintf($bchk['LANG_ERROR_MISSING_POST_VALUE'], 'cookie_ok'), E_USER_ERROR);
	$javascript_cookie_get_passed = $_POST['cookie1_ok'] != 0;

	// check clock
	isset($_POST['client_unixtimestamp']) or trigger_error(sprintf($bchk['LANG_ERROR_MISSING_POST_VALUE'], 'client_unixtimestamp'), E_USER_ERROR);
	$client_unixtimestamp = $_POST['client_unixtimestamp']; // milliseconds since January 1 1970 00:00:00 GMT
	$server_unixtimestamp = time() * 1000; // milliseconds since January 1 1970 00:00:00 GMT
	if ($client_unixtimestamp) {
		if ($bchk['FAKE_CLOCK_ERROR_SECONDS']) {
			$client_unixtimestamp += 1000 * $bchk['FAKE_CLOCK_ERROR_SECONDS'];
		}
		$clock_passed = abs($client_unixtimestamp - $server_unixtimestamp) <= $bchk['CLOCK_TOLERANCE_MINUTES'] * 60 * 1000;
	} else {
		$clock_passed = NULL;
	}

	$results_display = make_test_results_display(
		$cookie_passed,                $referrer_h_passed, $referrer_hs_passed,
		$referrer_hsq_passed,          $javascript_passed, $javascript_cookie_get_passed,
		$javascript_cookie_set_passed, $clock_passed
		);

	$clock_comparison_display = '';
	if ($bchk['TEST_CLOCK'] and isset($clock_passed)) {
		$clock_comparison_display = make_clock_comparison_display($server_unixtimestamp, $client_unixtimestamp, $clock_passed);
	}

	// This copyright notice must not be removed.
	$copyright_notice = copyright_notice();

	echo <<<END

	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset={$bchk['CHARSET']}" />
		<title>Browser Check</title>
	</head>
	<body>
	{$bchk['LANG_HEADER_RESULTS_PAGE']}
	$results_display
	$clock_comparison_display
	<p><b>{$bchk['LANG_CLICK_HERE4']} <a href='{$_SERVER['PHP_SELF']}?bchk_lang={$bchk['lang']}'>{$bchk['LANG_CLICK_HERE5']}</a> {$bchk['LANG_CLICK_HERE6']}</b>
	{$bchk['LANG_DO_NOT_RELOAD']}
	</p>
	{$bchk['LANG_FOOTER_RESULTS_PAGE']}
	<br /><p>$copyright_notice</p>
	</body>
	</html>

END;

}

// --------------------
function get_language()
{
	global $bchk;

	// Get language code from request variable.
	$lang = @$_REQUEST['bchk_lang'];
	
	// If language not specified by request variable, and auto-detect enabled, try to auto-detect language.
	if (empty($lang) and $bchk['LANGUAGE_AUTO']) {
		foreach (get_client_lang_codes() as $lang_code) {
			if (isset($bchk['LANGUAGES'][$lang_code]) and is_file("lang_{$lang_code}.inc.php")) {
				$lang = $lang_code;
				break;
			}
		}
	}
	
	// If valid language not yet specified, use default.
	if (empty($lang) or !isset($bchk['LANGUAGES'][$lang]) or !is_file("lang_{$lang}.inc.php")) {
		$lang = $bchk['LANGUAGE_DEFAULT'];
	}

	return $lang;
}

// ----------------------------------
// function make_test_results_display
//
// Return string containing test-results display.
//
// Each parameter is a test-passed flag:
//   TRUE  - test passed
//   FALSE - test failed
//   NULL  - test not performed

function make_test_results_display(
	$cookie_passed                = NULL,
	$referrer_h_passed            = NULL,
	$referrer_hs_passed           = NULL,
	$referrer_hsq_passed          = NULL,
	$javascript_passed            = NULL,
	$javascript_cookie_get_passed = NULL,
	$javascript_cookie_set_passed = NULL,
	$clock_passed                 = NULL
	)
{
	global $bchk;

	// Display results for tests which are enabled.

	$s = "\n";	
	$s .= "<table border='0' width='%75' class='outer'>\n";
	$s .= "<tr><th align='center'>{$bchk['LANG_FEATURE']}</th><th align='center'>{$bchk['LANG_DESCRIPTION']}</th><th align='center'>{$bchk['LANG_TEST_RESULT']}</th></tr>\n";

	$t = '';
	$class='even';
	$t .= make_test_result_row('COOKIES',                $bchk['LANG_COOKIES_DESC'],                $cookie_passed, $class='even');
	$t .= make_test_result_row('REFERRER_H',             $bchk['LANG_REFERRER_H_DESC'],             $referrer_h_passed, $class='odd');
	$t .= make_test_result_row('REFERRER_HS',            $bchk['LANG_REFERRER_HS_DESC'],	          $referrer_hs_passed, $class='even');
	$t .= make_test_result_row('REFERRER_HSQ',           $bchk['LANG_REFERRER_HSQ_DESC'],           $referrer_hsq_passed, $class='odd');
	$t .= make_test_result_row('JAVASCRIPT',             $bchk['LANG_JAVASCRIPT_DESC'],             $javascript_passed, $class='even');
	$t .= make_test_result_row('JAVASCRIPT_READ_COOKIE', $bchk['LANG_JAVASCRIPT_READ_COOKIE_DESC'], $javascript_cookie_get_passed, $class='odd');
	$t .= make_test_result_row('JAVASCRIPT_SET_COOKIE',  $bchk['LANG_JAVASCRIPT_SET_COOKIE_DESC'],  $javascript_cookie_set_passed, $class='odd');

	$t .= make_test_result_row('CLOCK', sprintf($bchk['LANG_CLOCK_DESC'], $bchk['CLOCK_TOLERANCE_MINUTES']), $clock_passed, $class='even');

	if (empty($t)) {
		$t = "<tr><td><i>{$bchk['LANG_NO_TESTS']}</i></td><td>&nbsp;</td><td>&nbsp;</td></tr>\n";
	}

	$s .= $t;
	$s .= "</table>\n";

	return $s;
}

// -----------------------------
// function make_test_result_row
//
// Make a table row for an individual test's result.
//
// $test_name        - name of test
// $test_description - description of test
// $test_passed      - TRUE if test passed, FALSE if test failed, NULL if test was not performed

function make_test_result_row($test_name, $test_description, $test_passed, $class)
{
	global $bchk;

	if ($bchk['TEST_' . $test_name]) {
		if (!isset($test_passed)) {
			$test_passed_string = '&nbsp;';
		} elseif ($test_passed) {
			$test_passed_string = "<font color='{$bchk['PASSED_TEST_COLOR']}'><b>{$bchk['LANG_PASS']}</b></font>";
		} else {
			$test_passed_string = "<font color='{$bchk['FAILED_TEST_COLOR']}'><b>{$bchk['LANG_FAIL']}</b></font>";
		}
		$name = $bchk['LANG_' . $test_name];
		$s = "<tr><td class=$class>$name</td><td class=$class>$test_description</td><td class=$class>$test_passed_string</td></tr>\n";
	} else {       
		$s = '';
	}

	return $s;
}

// --------------------------------------
// function make_clock_comparison_display
//
// Return string containing clock-comparison display.
// 
// $server_unixtimestamp - server clock value (milliseconds since January 1 1970 00:00:00 GMT)
// $client_unixtimestamp - client clock value (milliseconds since January 1 1970 00:00:00 GMT)
// $comparison_passed    - TRUE if clock comparison is within tolerance, else FALSE

function make_clock_comparison_display($server_unixtimestamp, $client_unixtimestamp, $comparison_passed)
{
	global $bchk;

	// difference in milliseconds
	$delta = $client_unixtimestamp - $server_unixtimestamp;

	// convert displayed difference to appropriate units
	$one_second = 1000;
	$one_minute = 60 * $one_second;
	$one_hour   = 60 * $one_minute;
	$one_day    = 24 * $one_hour;
	if (abs($delta) < 10 * $one_minute) {
		$delta_string = round($delta/$one_second, 2) . ' ' . $bchk['LANG_SECONDS'];
	} elseif (abs($delta) < 2 * $one_hour) {
		$delta_string = round($delta/$one_minute, 2) . ' ' . $bchk['LANG_MINUTES'];
	} elseif (abs($delta) < 2 * $one_day) {
		$delta_string = round($delta/$one_hour,   2) . ' ' . $bchk['LANG_HOURS'];
	} else {
		$delta_string = round($delta/$one_day,    2) . ' ' . $bchk['LANG_DAYS'];
	}

	// format string for displaying difference
	$delta_color = $bchk[$comparison_passed ? 'PASSED_TEST_COLOR' : 'FAILED_TEST_COLOR'];
	$delta_display = "<font color='$delta_color'><b>$delta_string</b></font>";

	// Javascript is used here only because it's a convenient way of displaying the clock times
	// in a user-friendly format using the client's time zone.
	// Since reading the client clock requires Javascript anyway, this doesn't add any browser requirements.

	$sim_msg = sprintf($bchk['LANG_SIMULATING_CLOCK_ERROR'], $bchk['FAKE_CLOCK_ERROR_SECONDS']);

	$t = <<<END

	<script language='javascript' type='text/javascript'>
	<!--
		var server_time = new Date($server_unixtimestamp);
		var client_time = new Date($client_unixtimestamp);
		document.write("<br /><table border='1' width='%75'>");
		document.write('<tr><th align="center">{$bchk['LANG_SERVER_CLOCK']}</th><th align="center">{$bchk['LANG_CLIENT_CLOCK']}</th><th align="center">{$bchk['LANG_DIFFERENCE']}</th></tr>');
		document.write('<tr><td class="even">' + server_time + '</td><td class="even">' + client_time + '</td><td class="even">' + "$delta_display" + '</td></tr>');
		if ({$bchk['FAKE_CLOCK_ERROR_SECONDS']} != 0) {
			document.write("<tr><td colspan='3'><i>$sim_msg</i></td></tr>");
		}
		document.write("<tr><td colspan='3'>{$bchk['LANG_NOTE_INTERNET_LAG']}</td></tr>");
		document.write('</table>');
	//-->
	</script>

END;

	return $t;
}

// ---------------------------------------------------
// Generate a "unique", timestamped name for a cookie.
//
// $root - string for initial part of cookie name (must not contain '_')

function make_cookie_name($root)
{
	global $bchk;

	strstr($root, '_') and trigger_error(sprintf($bchk['LANG_ERROR_INTERNAL'], 'make_cookie_name'), E_USER_ERROR);

	// get some environmental parameters
	$remote_addr = @$_SERVER['REMOTE_ADDR'];
	$remote_port = @$_SERVER['REMOTE_PORT'];
	$process_id  = function_exists('getmypid') ? getmypid() : '';
	list($usec, $sec) = function_exists('microtime') ? explode(' ', microtime()) : array(0, time());

	// combine parameters into string, and get last eight characters of MD5 digest of the string
	$tag = substr(md5("$remote_addr-$remote_port-$process_id-$sec-$usec"), -8);

	// form cookie name from root string, timestamp and "unique" tag
	return $root . '_' . $sec . '_' . $tag;
}

// --------------------------------------------------------------------
// Return timestamp from cookie name generated with make_cookie_name().
//
// The timestamp is the number of seconds since January 1 1970 00:00:00 GMT.
// If the timestamp is not found, zero is returned.

function cookie_timestamp($cookie_name)
{
	@list($root, $timestamp, $rand) = explode('_', $cookie_name);
	return (isset($timestamp) and is_numeric($timestamp)) ? $timestamp : 0;
}

// ------------------------------------------------------------------------------------------------
// Use 'Accept-language' header to build list of language codes, in descending order of preference.
function get_client_lang_codes()
{
	$lang_codes = array();
	if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
		foreach (explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']) as $lang_spec) {
			if (preg_match('/([a-z]+)(?:-[a-z]+)?(?:;q=(\d+\.\d+))?/', trim($lang_spec), $matches)) {
				$lang_code = $matches[1];
				$qvalue    = isset($matches[2]) ? $matches[2] : 1;
				if ($qvalue > 0.0001) {
					$lang_codes[$lang_code] = isset($lang_codes[$lang_code]) ? max($lang_codes[$lang_code], $qvalue) : $qvalue;
				}
			}
		}
		arsort($lang_codes, SORT_NUMERIC);
	}
#var_dump('HTTP_ACCEPT_LANGUAGE', $_SERVER['HTTP_ACCEPT_LANGUAGE'], 'lang_codes', array_keys($lang_codes));#*#DEBUG#
	return array_keys($lang_codes);
}

// ------------------------------------------
// This copyright notice must not be removed.

function copyright_notice()
{
	$sp = '&nbsp;&nbsp;&nbsp;&nbsp;';
	return "<font color='#808080' size='1' face='Verdana,Arial,Helvetica'>Browser Check 1.4.1{$sp}Copyright &copy; 2004{$sp}<a href='http://Dave-L.com'>Dave Lerner</a>{$sp}All Rights Reserved</font>";
}
include(XOOPS_ROOT_PATH."/footer.php");
?>