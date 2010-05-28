<?php
/**
* Debugging functions
*
* @license GNU
* @author marcan <marcan@smartfactory.ca>
* @version  $Id$
* @link http://impresscms.org ImpressCMS
*/

/**
* Output a line of debug
*
* @param string $msg text to be outputed as a debug line
* @param bool $exit if TRUE the script will end
*/
function icms_debug($msg, $exit = false)
{
  echo "<div style='padding: 5px; color: red; font-weight: bold'>debug :: $msg</div>";
  if ($exit)
 	{
  	die();
  }
}



function icms_debug_info($text, $msg)
{
  //ob_end_flush();
	echo "<div style='padding: 5px; color: red; font-weight: bold'>$text</div>";
  echo "<div>
  <pre>
  ";
  print_r($msg);
  echo "
  </pre>
  </div>
  ";
}




function icms_error_info($text, $msg)
{
  echo "<div style='padding: 5px; color: red; font-weight: bold'>$text</div>";
  echo "<div>
  <pre>
  ";
  print_r($msg);
  echo "
  </pre>
  </div>
  ";
}


function icms_return_error_info($text, $msg)
{
  global $icmsDB;

  $content = "";

  $content .= "<div>$text</div><BR />\n\n";

/*
if (isset($icmsDB->LastKnownError))
{
*/
	$content .= "<div>\nThe Following SQL<BR />\n";
	$content .= "<pre>\n";
	$content .= $icmsDB->LastKnownQuery;
	$content .= "</pre>\n";
	$content .= "</div><BR />\n\n";
/*
}
*/

$content .= "Gave the following SQL error<BR /><div style='padding: 5px; color: red; font-weight: bold'>\n";
$content .= $msg;
$content .= "</div>\n";
$content .= "<HR><BR />\n\n";


$tmp = debug_backtrace();
$calledfromfile = $tmp[0]['file'];
$calledfromline = $tmp[0]['line'];
 
$content .= "Called from <strong>".$calledfromfile."</strong> line <strong>".$calledfromline."</strong>\n";
$content .= "<BR /><HR><BR />\n\n";

return $content;
}





function icms_ChangeText($langfile, $text, $msg = "argument 2")
{
  $tmp = debug_backtrace();
  $calledfromfile = $tmp[0]['file'];
  $calledfromline = $tmp[0]['line'];

  echo "Called from <strong>".$calledfromfile."</strong> line <strong>".$calledfromline."</strong>";

  if(!isset($langfile))
  {
    $langfile = "main.php";
  }


  //$langfile = file_get_contents('../langfile.php') or die("Error: could not find / create config file!");

  if (file_exists("G:/htdocs/icmsadminthem/modules/xfaccount/language/english/".$langfile))
  {
    $filetoopen = "G:/htdocs/icmsadminthem/modules/xfaccount/language/english/".$langfile;
  }
  else
  {
    $filetoopen = "G:/htdocs/icmsadminthem/modules/xfmod/language/english/".$langfile;
  }


  echo "opening file ".$filetoopen."<BR />";

  $fp=fopen($filetoopen, 'a');
  if ($fp == false)
  {
  	echo "fopen(".$filetoopen.") failed<BR /><HR>\n";
  	return;
  }
  else
  {
    echo "writing to file ".$filetoopen."<BR />";

    $config = "
      define(\"_XF_".strtoupper($text)."_".strtoupper($msg)."\", \"find TEXT '".$text."\t".$msg."' in language file and put it back in FILE ".$calledfromfile." LINE ".$calledfromline."\");\n";
    //$config=preg_replace("@installed=false@", "installed=true", $config);
    $write=fputs($fp, $config);
    fclose($fp);
  }

/*
$fp = fopen($cachePath, 'a');
if ($fp == false)
{
	echo "fopen(".$cachePath.") failed\n";
	return;
}
flock($fp, LOCK_EX);
ftruncate($fp, 0);
$content = serialize($content);
fwrite($fp, $content, strlen($content));
fclose($fp);
*/



}


/**
* Output a dump of a variable
*
* @param string $var variable which will be dumped
*/
function icms_debug_vardump($var)
{
if (class_exists('MyTextSanitizer'))
{
	$myts = MyTextSanitizer::getInstance();
	icms_debug($myts->displayTarea(var_export($var, true)));
	 
}
else
{
	 
	$var = var_export($var, true);
	$var = preg_replace("/(\015\012)|(\015)|(\012)/", "<br />", $var);
	icms_debug($var);
}
}




/**
 * Provides a backtrace for deprecated methods and functions, will be in the error section of debug
 *
 * @since ImpressCMS 1.3
 * @package core
 * @subpackage Debugging
 * @param string $replacement Method or function to be used instead of the deprecated method or function
 * @param string $extra Additional information to provide about the change
 */
function icms_deprecated( $replacement='', $extra='' ) {
	$trace = debug_backtrace();
	array_shift( $trace );
	$level = '';
	$msg = ' <strong><em>(Deprecated)</em></strong> - ';
	foreach ( $trace as $step ) {
	    $level .= '-';
		if ( isset( $step['file'] ) ) {
		    if( $step['function'] != 'include' && $step['function'] != 'include_once' && $step['function'] != 'require' && $step['function'] != 'require_once') {
				trigger_error( $level . $msg . (isset( $step['class'] ) ? $step['class'] : '') . (isset( $step['type'] ) ? $step['type'] : '' ) . $step['function'] . ' in ' . $step['file'] . ', line ' . $step['line'] . ( $replacement ? ' <strong><em>use ' . $replacement . ' instead</em></strong>' : '' ) . ( $extra ? ' <strong><em> ' . $extra . ' </em></strong>' : '' ), E_USER_NOTICE ) ;
			}
		}
		$msg = 'Called by ';
		$replacement = '';
	}
}