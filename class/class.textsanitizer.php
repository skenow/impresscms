<?php
// $Id: class.textsanitizer.php,v 1.2 2007/03/30 22:05:45 catzwolf Exp $
// ------------------------------------------------------------------------ //
// Zarilia - PHP Content Management System                      			//
// Copyright (c) 2007 Zarilia                           				//
// 																			//
// Authors: 																//
// John Neill ( AKA Catzwolf )                                     			//
// Raimondas Rimkevicius ( AKA Mekdrop )									//
// 							 												//
// URL: http:www.zarilia.com 												//
// Project: Zarilia Project                                               //
// -------------------------------------------------------------------------//

/**
 * Class to "clean up" text for various uses
 *
 * <b>Singleton</b>
 *
 * @package kernel
 * @subpackage core
 * @author Kazumi Ono
 * @author Goghs Cheng
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 */

//define( "IMAGE_FILTER_WORD", 1 );
//define( "IMAGE_FILTER_INTEGER", 2 );
//define( "IMAGE_FILTER_STRING", 3 );

class MyTextSanitizer {
    /**
     *
     * @var array
     */
    var $smileys = array();

    /**
     */
    var $censorConf;

    /**
     * Constructor of this class
     *
     * Gets allowed html tags from admin config settings
     * <br> should not be allowed since nl2br will be used
     * when storing data.
     *
     * @access private
     * @todo Sofar, this does nuttin' ;-)
     */
    function MyTextSanitizer() {
    }

    /**
     * Access the only instance of this class
     *
     * @return object
     * @static
     * @staticvar object
     */
    function &getInstance() {
        static $instance;
        if ( !isset( $instance ) ) {
            $instance = new MyTextSanitizer();
        }
        return $instance;
    }

    /**
     * Get the smileys
     *
     * @return array
     */
    function getSmileys() {
        return $this->smileys;
    }

    /**
     * Replace emoticons in the message with smiley images
     *
     * @param string $message
     * @return string
     */
    function &smiley( $message ) {
        $db = &ZariliaDatabaseFactory::getDatabaseConnection();
        if ( count( $this->smileys ) == 0 ) {
            if ( $getsmiles = $db->Execute( "SELECT * FROM " . $db->prefix( "smiles" ) .' WHERE display = 1') ) {
                while ( $smiles = $getsmiles->FetchRow() ) {
                    $message = str_replace( $smiles['code'], '<img src="' . ZAR_UPLOAD_URL . '/' . htmlspecialchars( $smiles['smile_url'] ) . '" alt="" />', $message );
                    array_push( $this->smileys, $smiles );
                }
            }
        } elseif ( is_array( $this->smileys ) ) {
            foreach ( $this->smileys as $smile ) {
                $message = str_replace( $smile['code'], '<img src="' . ZAR_UPLOAD_URL . '/' . htmlspecialchars( $smile['smile_url'] ) . '" alt="" />', $message );
            }
        }
        return $message;
    }

    /**
     * Make links in the text clickable
     *
     * @param string $text
     * @return string
     */
    function &makeClickable( &$text ) {
        $patterns = array( "/(^|[^]_a-z0-9-=\"'\/])([a-z]+?):\/\/([^, \r\n\"\(\)'<>]+)/i", "/(^|[^]_a-z0-9-=\"'\/])www\.([a-z0-9\-]+)\.([^, \r\n\"\(\)'<>]+)/i", "/(^|[^]_a-z0-9-=\"'\/])ftp\.([a-z0-9\-]+)\.([^, \r\n\"\(\)'<>]+)/i", "/(^|[^]_a-z0-9-=\"'\/:\.])([a-z0-9\-_\.]+?)@([^, \r\n\"\(\)'<>\[\]]+)/i" );
        $replacements = array( "\\1<a href=\"\\2://\\3\" target=\"_blank\">\\2://\\3</a>", "\\1<a href=\"http://www.\\2.\\3\" target=\"_blank\">www.\\2.\\3</a>", "\\1<a href=\"ftp://ftp.\\2.\\3\" target=\"_blank\">ftp.\\2.\\3</a>", "\\1<a href=\"mailto:\\2@\\3\">\\2@\\3</a>" );
        $text = preg_replace( $patterns, $replacements, $text );
        return $text;
    }

    /**
     * Replace ZariliaCodes with their equivalent HTML formatting
     *
     * @param string $text
     * @param bool $allowimage Allow images in the text?
     * On FALSE, uses links to images.
     * @return string
     */
    function &zariliaCodeDecode( &$text, $allowimage = 2 ) {
        $patterns = array();
        $replacements = array();

		$patterns[] = "/\[siteurl=(['\"]?)([^\"'<>]*)\\1](.*)\[\/siteurl\]/sU";
        $replacements[] = '<a href="' . ZAR_URL . '/\\2" target="_blank">\\3</a>';
        $patterns[] = "/\[url=(['\"]?)(http[s]?:\/\/[^\"'<>]*)\\1](.*)\[\/url\]/sU";
        $replacements[] = '<a href="\\2" target="_blank">\\3</a>';
        $patterns[] = "/\[url=(['\"]?)(ftp?:\/\/[^\"'<>]*)\\1](.*)\[\/url\]/sU";
        $replacements[] = '<a href="\\2" target="_blank">\\3</a>';
        $patterns[] = "/\[url=(['\"]?)([^\"'<>]*)\\1](.*)\[\/url\]/sU";
        $replacements[] = '<a href="http://\\2" target="_blank">\\3</a>';
        $patterns[] = "/\[color=(['\"]?)([a-zA-Z0-9]*)\\1](.*)\[\/color\]/sU";
        $replacements[] = '<span style="color: #\\2;">\\3</span>';
        $patterns[] = "/\[size=(['\"]?)([a-z0-9-]*)\\1](.*)\[\/size\]/sU";
        $replacements[] = '<span style="font-size: \\2;">\\3</span>';
        $patterns[] = "/\[font=(['\"]?)([^;<>\*\(\)\"']*)\\1](.*)\[\/font\]/sU";
        $replacements[] = '<span style="font-family: \\2;">\\3</span>';
        $patterns[] = "/\[email]([^;<>\*\(\)\"']*)\[\/email\]/sU";
        $replacements[] = '<a href="mailto:\\1">\\1</a>';
        $patterns[] = "/\[b](.*)\[\/b\]/sU";
        $replacements[] = '<b>\\1</b>';
        $patterns[] = "/\[i](.*)\[\/i\]/sU";
        $replacements[] = '<i>\\1</i>';
        $patterns[] = "/\[u](.*)\[\/u\]/sU";
        $replacements[] = '<u>\\1</u>';
        $patterns[] = "/\[d](.*)\[\/d\]/sU";
        $replacements[] = '<del>\\1</del>';
        $patterns[] = "/\[object](.*)\[\/object\]/s";
        $replacements[] = '<b>\\1</b>';
        $patterns[] = "/\[media]([^\"\(\)\?\&'<>]*)\[\/media\]/sU";
        $replacements[] = '<a href="\\1" target="_blank">Play Item</a>';
        $patterns[] = "/\[img capt=(['\"]?)([^~]*)\\1]([^\"\(\)\?\&'<>]*)\[\/img\]/sU";
        switch ( $allowimage ) {
            case 0:
            default:
                $replacements[] = '';
                break;
            case 1:
                $replacements[] = '<table align="left" border="0" cellspacing="0" cellpadding="0" id="caption"><tr><td align="center"><img src="\\3" width="90" alt="" class="imglrn" /></td></tr></table>';
                break;
            case 2:
                $replacements[] = '<table align="left" border="0" cellspacing="0" cellpadding="0" id="caption"><tr><td align="center"><a href="javascript:pictWindow(\'' . ZAR_URL . '/pict.php?src=\\3\')"><img src="\\3" width="90" alt="" class="imglrn" /></a></td></tr><tr valign="top"><td align="center" valign="top"><a href="javascript:pictWindow(\'' . ZAR_URL . '/pict.php?src=\\3\')"><span class="captText"><b>\\2</b></span></a></td></tr></table>';
                break;
        } // switch

		$patterns[] = "/\[imgc capt=(['\"]?)([^~]*)\\1]([^\"\(\)\?\&'<>]*)\[\/imgc\]/sU";
        switch ( $allowimage ) {
            case 0:
            default:
                $replacements[] = '';
                break;
            case 1:
				$replacements[] = '<div align="center"><table border="0" cellspacing="0" cellpadding="0" id="caption"><tr><td align="center"><img src="\\3" width="90" alt="" class="imglrn" /></td></tr></table></div>';
                break;
            case 2:
                $replacements[] = '<div align="center"><table border="0" cellspacing="0" cellpadding="0" id="caption"><tr><td align="center"><a href="javascript:pictWindow(\'' . ZAR_URL . '/pict.php?src=\\3\')"><img src="\\3" width="90" alt="" class="imglrn" /></a></td></tr><tr valign="top"><td align="center" valign="top"><a href="javascript:pictWindow(\'' . ZAR_URL . '/pict.php?src=\\3\')"><span class="captText"><b>\\2</b></span></a></td></tr></table></div>';
                break;
        } // switch
        /*
		*
		*/
        $patterns[] = "/\[imgr capt=(['\"]?)([^~]*)\\1]([^\"\(\)\?\&'<>]*)\[\/imgr\]/sU";
        switch ( $allowimage ) {
            case 0:
            default:
                $replacements[] = '';
                break;
            case 1:
                $replacements[] = '<table align="right" border="0" cellspacing="0" cellpadding="0" id="caption"><tr><td align="center"><img src="\\3" width="90" alt="" class="imglrn" /></td></tr></table>';
                break;
            case 2:
                $replacements[] = '<table align="right" border="0" cellspacing="0" cellpadding="0" id="caption"><tr><td align="center"><a href="javascript:pictWindow(\'' . ZAR_URL . '/pict.php?src=\\3\')"><img src="\\3" width="90" alt="" class="imglrn" /></a></td></tr><tr valign="top"><td align="center" valign="top"><a href="javascript:pictWindow(\'' . ZAR_URL . '/pict.php?src=\\3\')"><span class="captText"><b>\\2</b></span></a></td></tr></table>';
                break;
        } // switch


        $patterns[] = "/\[img align=(['\"]?)(left|center|right)\\1]([^\"\(\)\?\&'<>]*)\[\/img\]/sU";
        $patterns[] = "/\[img]([^\"\(\)\?\&'<>]*)\[\/img\]/sU";
        $patterns[] = "/\[img align=(['\"]?)(left|center|right)\\1 id=(['\"]?)([0-9]*)\\3]([^\"\(\)\?\&'<>]*)\[\/img\]/sU";
        $patterns[] = "/\[img id=(['\"]?)([0-9]*)\\1]([^\"\(\)\?\&'<>]*)\[\/img\]/sU";
        if ( $allowimage != 1 ) {
            $replacements[] = '<a href="\\3" target="_blank">\\3</a>';
            $replacements[] = '<a href="\\1" target="_blank">\\1</a>';
            $replacements[] = '<a href="' . ZAR_URL . '/image.php?id=\\4" target="_blank">\\4</a>';
            $replacements[] = '<a href="' . ZAR_URL . '/image.php?id=\\2" target="_blank">\\3</a>';
        } else {
            $replacements[] = '<img src="\\3" align="\\2" alt="" />';
            $replacements[] = '<img src="\\1" alt="" />';
            $replacements[] = '<img src="' . ZAR_URL . '/image.php?id=\\4" align="\\2" alt="\\4" />';
            $replacements[] = '<img src="' . ZAR_URL . '/image.php?id=\\2" alt="\\3" />';
        }

		$patterns[] = "/\[quote]/sU";
        $replacements[] = _QUOTEC . '<div class="zariliaQuote"><blockquote>';
        $patterns[] = "/\[\/quote]/sU";
        $replacements[] = '</blockquote></div>';
        $patterns[] = "/javascript:/si";
        $replacements[] = "java script:";
        $patterns[] = "/about:/si";
        $replacements[] = "about :";

       	$patterns[] = "/\[zariliauplurl]/sU";
        $replacements[] = ZAR_UPLOAD_URL;
        $text = preg_replace( $patterns, $replacements, $text );
        return $text;
    }

    /**
     * Convert linebreaks to <br /> tags
     *
     * @param string $text
     * @return string
     */
    function &nl2Br( $text ) {
        $text = preg_replace( "/(\015\012)|(\015)|(\012)/", "<br />", $text );
        return $text;
    }

    /**
     * MyTextSanitizer::addSlashes()
     *
     * @param  $text
     * @param boolean $force
     * @return
     */
    function &addSlashes( $text, $force = false ) {
        if ( !get_magic_quotes_gpc() || $force == true ) {
            $text = &addslashes( $text );
        }
        return $text;
    }

    /**
     * if magic_quotes_gpc is on, strip back slashes
     *
     * @param string $text
     * @param boolean $force
     * @return string
     */
    function &stripSlashesGPC( $text, $force = false ) {
        if ( get_magic_quotes_gpc() || $force == true ) {
            $text = stripslashes( $text );
        }
        return $text;
    }

	/*
	*  for displaying data in html textbox forms (will be removed in future)
    *
    * @param	string  $text
    *
    * @return	string
	*/
	function htmlSpecialChars( $text )
	{
		//return preg_replace("/&amp;/i", '&', htmlspecialchars($text, ENT_QUOTES));
		return preg_replace(array("/&amp;/i", "/&nbsp;/i"), array('&', '&amp;nbsp;'), htmlspecialchars($text, ENT_QUOTES));
	}


	function stripHtmlSpecialChars( $text  )
	{
		$text = stripslashes($text);
		$text = $this->htmlSpecialChars($text);
		return $text;
	}

    /**
     * Reverses {@link htmlSpecialChars()}
     *
     * @param string $text
     * @return string
     */
    function &undoHtmlSpecialChars( &$text ) {
        $text = preg_replace( array( "/&gt;/i", "/&lt;/i", "/&quot;/i", "/&#039;/i" ), array( ">", "<", "\"", "'" ), $text );
        return $text;
    }

    /**
     * Filters textarea form data in DB for display
     *
     * @param string $text
     * @param bool $html allow html?
     * @param bool $smiley allow smileys?
     * @param bool $xcode allow zariliacode?
     * @param bool $image allow inline images?
     * @param bool $br convert linebreaks?
     * @return string
     */
    function &displayTarea( &$text, $html = 0, $smiley = 1, $xcode = 1, $image = 1, $br = 1, $gpc = false ) {
        $text = &$this->stripSlashesGPC( $text );
        if ( $html != 1 ) {
            // html not allowed
            $text = htmlSpecialChars( $text, ENT_QUOTES );
        }
        $text = $this->codePreConv( $text, $xcode ); // Ryuji_edit(2003-11-18)
        $text = $this->makeClickable( $text );
        if ( $smiley != 0 ) {
            $text = &$this->smiley( $text );
        }
        if ( $xcode != 0 ) {
            $text = &$this->zariliaCodeDecode( $text, $image );
        }
        if ( $br != 0 ) {
            $text = &$this->nl2Br( $text );
        }
        $text = $this->codeConv( $text, $xcode, $image ); // Ryuji_edit(2003-11-18)
        return $text;
    }

    /**
     * Filters textarea form data submitted for preview
     *
     * @param string $text
     * @param bool $html allow html?
     * @param bool $smiley allow smileys?
     * @param bool $xcode allow zariliacode?
     * @param bool $image allow inline images?
     * @param bool $br convert linebreaks?
     * @return string
     */
    function &previewTarea( &$text, $html = 0, $smiley = 1, $xcode = 1, $image = 1, $br = 1 ) {
        return $this->displayTarea( $text, $html, $smiley, $xcode, $image, $br );
    }

    /**
     * Replaces banned words in a string with their replacements
     *
     * @param string $text
     * @return string
     * @deprecated
     */
    function &censorString( &$text ) {
        if ( !isset( $this->censorConf ) ) {
            $config_handler = &zarilia_gethandler( 'config' );
            $this->censorConf = &$config_handler->getConfigsByCat( ZAR_CONF_CENSOR );
        }
        if ( $this->censorConf['censor_enable'] == 1 ) {
            $replacement = $this->censorConf['censor_replace'];
            foreach ( $this->censorConf['censor_words'] as $bad ) {
                if ( !empty( $bad ) ) {
                    $bad = quotemeta( $bad );
                    $patterns[] = "/(\s)" . $bad . "/siU";
                    $replacements[] = "\\1" . $replacement;
                    $patterns[] = "/^" . $bad . "/siU";
                    $replacements[] = $replacement;
                    $patterns[] = "/(\n)" . $bad . "/siU";
                    $replacements[] = "\\1" . $replacement;
                    $patterns[] = "/]" . $bad . "/siU";
                    $replacements[] = "]" . $replacement;
                    $text = preg_replace( $patterns, $replacements, $text );
                }
            }
        }
        return $text;
    }
    /**
     * *#@+
     * Sanitizing of [code] tag
     */
    function codePreConv( $text, $xcode = 1 ) {
        if ( $xcode != 0 ) {
            $patterns = "/\[code](.*)\[\/code\]/esU";
            $replacements = "'[code]'.base64_encode('$1').'[/code]'";
            $text = preg_replace( $patterns, $replacements, $text );
        }
        return $text;
    }

    function codeConv( $text, $xcode = 1, $image = 1 ) {
        if ( $xcode != 0 ) {
            $patterns = "/\[code](.*)\[\/code\]/esU";
            if ( $image != 0 ) {
                // image allowed
                $replacements = "'<div class=\"zariliaCode\"><code><pre>'.MyTextSanitizer::codeSanitizer('$1').'</pre></code></div>'";
            } else {
                // image not allowed
                $replacements = "'<div class=\"zariliaCode\"><code><pre>'.MyTextSanitizer::codeSanitizer('$1', 0).'</pre></code></div>'";
            }
            $text = preg_replace( $patterns, $replacements, $text );
        }
        return $text;
    }

    function codeSanitizer( $str, $image = 1 ) {
        if ( $image != 0 ) {
            $str = $this->zariliaCodeDecode( htmlSpecialChars( str_replace( '\"', '"', base64_decode( $str ) ), ENT_QUOTES ) );
        } else {
            $str = $this->zariliaCodeDecode( htmlSpecialChars( str_replace( '\"', '"', base64_decode( $str ) ), ENT_QUOTES ), 0 );
        }
        return $str;
    }
}

?>