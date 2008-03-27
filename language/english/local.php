<?php
// $Id: local.php,v 1.1 2007/03/16 02:44:24 catzwolf Exp $
// Local handler for string and encoding
class ZariliaLocal {
    // localized substr
    function substr( $str, $start, $length, $trimmarker = '...' ) {
        if ( !ZAR_USE_MULTIBYTES ) {
            return ( strlen( $str ) - $start <= $length ) ? substr( $str, $start, $length ) : substr( $str, $start, $length - strlen( $trimmarker ) ) . $trimmarker;
        }
        if ( function_exists( 'mb_internal_encoding' ) && @mb_internal_encoding( _CHARSET ) ) {
            $str2 = mb_strcut( $str , $start , $length - strlen( $trimmarker ) );
            $text = $str2 . ( mb_strlen( $str ) != mb_strlen( $str2 ) ? $trimmarker : '' );
            return $text;
        }
    }

    function &utf8_encode( &$text ) {
        if ( empty( $text ) )
            return '';

        if ( ZAR_USE_MULTIBYTES == 1 ) {
            if ( function_exists( 'mb_convert_encoding' ) ) {
                $text = mb_convert_encoding( $text, 'UTF-8', 'auto' );
                return $text;
            }
            return $text;
        }
        return utf8_encode( $text );
    }

    function &convert_encoding( &$text, $to = 'utf-8', $from = '' ) {
        if ( empty( $text ) )
            return '';
        if ( empty( $from ) )
            $from = _CHARSET;
        if ( empty( $to ) || !strcasecmp( $to, $from ) )
            return $text;

        if ( ZAR_USE_MULTIBYTES && function_exists( 'mb_convert_encoding' ) ) $converted_text = @mb_convert_encoding( $text, $to, $from );
        else
        if ( function_exists( 'iconv' ) ) {
            $converted_text = @iconv( $from, $to . "//TRANSLIT", $text );
        }
        $text = empty( $converted_text )?$text:$converted_text;
        return $text;
    }

    function trim( $text ) {
        return trim( $text );
    }

    /**
     * Function to display formatted times in user timezone
     */
    function formatTimestamp( $time, $format = "l", $timeoffset = "" ) {
        global $zariliaConfig, $zariliaUser;
        $usertimestamp = zarilia_getUserTimestamp( $time, $timeoffset );
        switch ( strtolower( $format ) ) {
            case 's':
                $datestring = _SHORTDATESTRING;
                break;
            case 'm':
                $datestring = _MEDIUMDATESTRING;
                break;
            case 'mysql':
                $datestring = "Y-m-d H:i:s";
                break;
            case 'rss':
                $datestring = "r";
                break;
            case 'l':
                $datestring = _DATESTRING;
                break;
            case 'c':
            case 'custom':
                $usernow = $usertimestamp + time() - $time;
                $today = mktime( 0, 0, 0, date( "m", $usernow ), date( "d", $usernow ), date( "Y", $usernow ) );
                $thisyear = mktime( 0, 0, 0, 1, 1, date( "Y", $usernow ) );
                $time_diff = ( $today - $usertimestamp ) / ( 24 * 60 * 60 ); // days
                if ( $time_diff < 0 ) {
                    $datestring = _TODAY;
                } elseif ( $time_diff < 1 ) {
                    $datestring = _YESTERDAY;
                } elseif ( $usertimestamp > $thisyear ) {
                    $datestring = _MONTHDAY;
                } else {
                    $datestring = _YEARMONTHDAY;
                }
                break;
            default:
                if ( $format != '' ) {
                    $datestring = $format;
                } else {
                    $datestring = _DATESTRING;
                }
                break;
        }
        return ucfirst( date( $datestring, $usertimestamp ) );
    }
    // adding your new functions
    // calling the function:
    // Method 1: echo zarilia_local("hello", "Some greeting words");
    // Method 2: echo ZariliaLocal::hello("Some greeting words");
    function error_page() {
    }

    function &hello( $text ) {
        return "<div>Hello, " . $text . "</div>";
    }
}

?>