<?php
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
// Author: Hervé Thouzard, Instant Zero                                      //
// URL: http://xoops.instant-zero.com                                        //
// ------------------------------------------------------------------------- //

error_reporting(0);
include_once '../../../mainfile.php';
$myts = &MyTextSanitizer::getInstance();
include_once ZAR_ROOT_PATH.'/addons/news/class/class.newsstory.php';
include_once ZAR_ROOT_PATH.'/addons/news/include/functions.php';
//include_once 'html_entity_decode_php4.php';

// Verifications on the article
$storyid = isset($_GET['storyid']) ? intval($_GET['storyid']) : 0;

if (empty($storyid))  {
    redirect_header(ZAR_URL.'/addons/news/index.php',2,_NW_NOSTORY);
    exit();
}

$article = new NewsStory($storyid);
// Not yet published
if ( $article->published() == 0 || $article->published() > time() ) {
    redirect_header(ZAR_URL.'/addons/news/index.php', 2, _NW_NOSTORY);
    exit();
}

// Expired
if ( $article->expired() != 0 && $article->expired() < time() ) {
    redirect_header(ZAR_URL.'/addons/news/index.php', 2, _NW_NOSTORY);
    exit();
}


$gperm_handler = &zarilia_gethandler('groupperm');
if (is_object($zariliaUser)) {
    $groups = $zariliaUser->getGroups();
} else {
	$groups = ZAR_GROUP_ANONYMOUS;
}
if(!isset($zariliaAddon)) {
	$module_handler = &zarilia_gethandler('addon');
	$zariliaAddon = &$module_handler->getByDirname('news');
}

if (!$gperm_handler->checkRight('news_view', $article->topicid(), $groups, $zariliaAddon->getVar('mid'))) {
	redirect_header(ZAR_URL.'/addons/news/index.php', 3, _NOPERM);
	exit();
}

/**
 *
 **********************************************************
 * NOTE : IF YOU USE THIS CODE, PLEASE KEEP ITS COPYRIGHT *
 **********************************************************
 * This function is in charge to convert all the html entities to their ASCII equivalent
 * @author Hervé Thouzard, Instant Zero (http://www.instant-zero.com)
 * @copyright (c) Instant Zero
 * @param string $chaine	The original string to convert
 * @return string	The string with its html entities converted to ASCII
 *
 */
function news_unhtml($chaine)
{
	//return html_entity_decode_php4($chaine);
	$search = $replace = array();
	$chaine = html_entity_decode($chaine);

	for($i=0; $i<=255; $i++) {
		$search[] = '&#'.$i.';';
		$replace[] = chr($i);
	}
	$replace[]='-';	$search[] ="&bull;";	// $replace[] = '•';
	$replace[]='—'; $search[]='&mdash;';
	$replace[]='–'; $search[]='&ndash;';
	$replace[]='-'; $search[]='&shy;';
	$replace[]='"'; $search[]='&quot;';
	$replace[]='&'; $search[]='&amp;';
	$replace[]='ˆ'; $search[]='&circ;';
	$replace[]='¡'; $search[]='&iexcl;';
	$replace[]='¦'; $search[]='&brvbar;';
	$replace[]='¨'; $search[]='&uml;';
	$replace[]='¯'; $search[]='&macr;';
	$replace[]='´'; $search[]='&acute;';
	$replace[]='¸'; $search[]='&cedil;';
	$replace[]='¿'; $search[]='&iquest;';
	$replace[]='˜'; $search[]='&tilde;';
	$replace[]="'"; $search[]='&lsquo;';	// $replace[]='‘';
	$replace[]="'"; $search[]='&rsquo;';	// $replace[]='’';
	$replace[]='‚'; $search[]='&sbquo;';
	$replace[]='“'; $search[]='&ldquo;';
	$replace[]='”'; $search[]='&rdquo;';
	$replace[]='„'; $search[]='&bdquo;';
	$replace[]='‹'; $search[]='&lsaquo;';
	$replace[]='›'; $search[]='&rsaquo;';
	$replace[]='<'; $search[]='&lt;';
	$replace[]='>'; $search[]='&gt;';
	$replace[]='±'; $search[]='&plusmn;';
	$replace[]='«'; $search[]='&laquo;';
	$replace[]='»'; $search[]='&raquo;';
	$replace[]='×'; $search[]='&times;';
	$replace[]='÷'; $search[]='&divide;';
	$replace[]='¢'; $search[]='&cent;';
	$replace[]='£'; $search[]='&pound;';
	$replace[]='¤'; $search[]='&curren;';
	$replace[]='¥'; $search[]='&yen;';
	$replace[]='§'; $search[]='&sect;';
	$replace[]='©'; $search[]='&copy;';
	$replace[]='¬'; $search[]='&not;';
	$replace[]='®'; $search[]='&reg;';
	$replace[]='°'; $search[]='&deg;';
	$replace[]='µ'; $search[]='&micro;';
	$replace[]='¶'; $search[]='&para;';
	$replace[]='·'; $search[]='&middot;';
	$replace[]='†'; $search[]='&dagger;';
	$replace[]='‡'; $search[]='&Dagger;';
	$replace[]='‰'; $search[]='&permil;';
	$replace[]='Euro'; $search[]='&euro;';		// $replace[]='€'
	$replace[]='¼'; $search[]='&frac14;';
	$replace[]='½'; $search[]='&frac12;';
	$replace[]='¾'; $search[]='&frac34;';
	$replace[]='¹'; $search[]='&sup1;';
	$replace[]='²'; $search[]='&sup2;';
	$replace[]='³'; $search[]='&sup3;';
	$replace[]='á'; $search[]='&aacute;';
	$replace[]='Á'; $search[]='&Aacute;';
	$replace[]='â'; $search[]='&acirc;';
	$replace[]='Â'; $search[]='&Acirc;';
	$replace[]='à'; $search[]='&agrave;';
	$replace[]='À'; $search[]='&Agrave;';
	$replace[]='å'; $search[]='&aring;';
	$replace[]='Å'; $search[]='&Aring;';
	$replace[]='ã'; $search[]='&atilde;';
	$replace[]='Ã'; $search[]='&Atilde;';
	$replace[]='ä'; $search[]='&auml;';
	$replace[]='Ä'; $search[]='&Auml;';
	$replace[]='ª'; $search[]='&ordf;';
	$replace[]='æ'; $search[]='&aelig;';
	$replace[]='Æ'; $search[]='&AElig;';
	$replace[]='ç'; $search[]='&ccedil;';
	$replace[]='Ç'; $search[]='&Ccedil;';
	$replace[]='ð'; $search[]='&eth;';
	$replace[]='Ð'; $search[]='&ETH;';
	$replace[]='é'; $search[]='&eacute;';
	$replace[]='É'; $search[]='&Eacute;';
	$replace[]='ê'; $search[]='&ecirc;';
	$replace[]='Ê'; $search[]='&Ecirc;';
	$replace[]='è'; $search[]='&egrave;';
	$replace[]='È'; $search[]='&Egrave;';
	$replace[]='ë'; $search[]='&euml;';
	$replace[]='Ë'; $search[]='&Euml;';
	$replace[]='ƒ'; $search[]='&fnof;';
	$replace[]='í'; $search[]='&iacute;';
	$replace[]='Í'; $search[]='&Iacute;';
	$replace[]='î'; $search[]='&icirc;';
	$replace[]='Î'; $search[]='&Icirc;';
	$replace[]='ì'; $search[]='&igrave;';
	$replace[]='Ì'; $search[]='&Igrave;';
	$replace[]='ï'; $search[]='&iuml;';
	$replace[]='Ï'; $search[]='&Iuml;';
	$replace[]='ñ'; $search[]='&ntilde;';
	$replace[]='Ñ'; $search[]='&Ntilde;';
	$replace[]='ó'; $search[]='&oacute;';
	$replace[]='Ó'; $search[]='&Oacute;';
	$replace[]='ô'; $search[]='&ocirc;';
	$replace[]='Ô'; $search[]='&Ocirc;';
	$replace[]='ò'; $search[]='&ograve;';
	$replace[]='Ò'; $search[]='&Ograve;';
	$replace[]='º'; $search[]='&ordm;';
	$replace[]='ø'; $search[]='&oslash;';
	$replace[]='Ø'; $search[]='&Oslash;';
	$replace[]='õ'; $search[]='&otilde;';
	$replace[]='Õ'; $search[]='&Otilde;';
	$replace[]='ö'; $search[]='&ouml;';
	$replace[]='Ö'; $search[]='&Ouml;';
	$replace[]='œ'; $search[]='&oelig;';
	$replace[]='Œ'; $search[]='&OElig;';
	$replace[]='š'; $search[]='&scaron;';
	$replace[]='Š'; $search[]='&Scaron;';
	$replace[]='ß'; $search[]='&szlig;';
	$replace[]='þ'; $search[]='&thorn;';
	$replace[]='Þ'; $search[]='&THORN;';
	$replace[]='ú'; $search[]='&uacute;';
	$replace[]='Ú'; $search[]='&Uacute;';
	$replace[]='û'; $search[]='&ucirc;';
	$replace[]='Û'; $search[]='&Ucirc;';
	$replace[]='ù'; $search[]='&ugrave;';
	$replace[]='Ù'; $search[]='&Ugrave;';
	$replace[]='ü'; $search[]='&uuml;';
	$replace[]='Ü'; $search[]='&Uuml;';
	$replace[]='ý'; $search[]='&yacute;';
	$replace[]='Ý'; $search[]='&Yacute;';
	$replace[]='ÿ'; $search[]='&yuml;';
	$replace[]='Ÿ'; $search[]='&Yuml;';
	$chaine = str_replace($search, $replace, $chaine);
	return utf8_encode($chaine);
}


require_once ZAR_ROOT_PATH.'/addons/news/pdf/tcpdf.php';
$filename = ZAR_ROOT_PATH.'/addons/news/language/'.$zariliaConfig['language'].'/main.php';
if (file_exists( $filename)) {
	include_once $filename;
} else {
	include_once ZAR_ROOT_PATH.'/addons/news/language/english/main.php';
}

$filename = ZAR_ROOT_PATH.'/addons/news/pdf/config/lang/'._LANGCODE.'.php';
if(file_exists($filename)) {
	include_once $filename;
} else {
	include_once ZAR_ROOT_PATH.'/addons/news/pdf/config/lang/en.php';
}

$dateformat = news_getmoduleoption('dateformat');
$content = '';
$content .= '<b><i><u>'.$myts->undoHtmlSpecialChars($article->title()).'</u></i></b><br /><b>'.$myts->undoHtmlSpecialChars($article->topic_title()).'</b><br />'._POSTEDBY.' : '.$myts->undoHtmlSpecialChars($article->uname()).'<br />'._MD_POSTEDON.' '.formatTimestamp($article->published(),$dateformat).'<br /><br /><br />';
$content .= $myts->undoHtmlSpecialChars($article->hometext()) . '<br />' . $myts->undoHtmlSpecialChars($article->bodytext());
$content = str_replace('[pagebreak]','<br />',$content);
$content = news_unhtml($content);

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true);

$doc_title = $myts->undoHtmlSpecialChars($article->title());
$doc_keywords = 'ZARILIA';

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_title);
$pdf->SetKeywords($doc_keywords);

$firstLine = news_unhtml($article->title());
$secondLine = news_unhtml(ZAR_URL.'/addons/news/article.php?storyid=1'.$storyid);
$pdf->SetHeaderData('', '', $firstLine, $secondLine);

//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); //set image scale factor

$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

$pdf->setLanguageArray($l); //set language items


//initialize document
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->writeHTML($content, true, 0);
$pdf->Output();
?>