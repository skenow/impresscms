<?php
defined( 'ZAR_ROOT_PATH' ) or die( 'You do not have permission to access this file!' );
/**
 *
 * @version $Id: functions.php,v 1.2 2007/04/21 09:42:24 catzwolf Exp $
 * @copyright 2006
 */

function add_critera_text( &$criteria, $k = '', $v = '', $m = '', $url = 0 ) {
    global $_REQUEST;
    $value = zarilia_cleanRequestVars( $_REQUEST, $v, '', XOBJ_DTYPE_TXTBOX );
    if ( empty( $value ) ) return;
    $value = ( intval( $url == 0 ) ) ? $myts->addslashes( $value ) : formatURL( trim( $value ) );
    if ( !empty( $v ) ) {
        $match = zarilia_cleanRequestVars( $_REQUEST, $m, ZAR_MATCH_CONTAIN );
        switch ( $match ) {
            case ZAR_MATCH_START:
                $criteria->add( new Criteria( $k, $value . '%', 'LIKE' ) );
                break;
            case ZAR_MATCH_END:
                $criteria->add( new Criteria( $k, '%' . $value, 'LIKE' ) );
                break;
            case ZAR_MATCH_EQUAL:
                $criteria->add( new Criteria( $k, $value ) );
                break;
            case ZAR_MATCH_CONTAIN:
            default:
                $criteria->add( new Criteria( $k, '%' . $value . '%', 'LIKE' ) );
                break;
        }
    }
}

function add_critera_time( &$criteria, $k = '', $v = '' ) {
    $value = zarilia_cleanRequestVars( $_REQUEST, $v, 0 );
    if ( empty( $value ) ) return;
    $time = time() - ( 60 * 60 * 24 * $value );
    if ( $time > 0 ) {
        $criteria->add( new Criteria( $k, $time, '<' ) );
    }
}

function add_critera_numeric( &$criteria, $k = '', $v = '', $m = '' ) {
    $value = zarilia_cleanRequestVars( $_REQUEST, $v, 0 );
    $criteria->add( new Criteria( $k, $value, $m ) );
}

function finduser_nav( $total = 0, $limit = 0, $start = 0 ) {
    $ret = '';
    if ( intval( $total ) == 0 || intval( $limit ) == 0 ) {
        return $ret;
    }
    $totalpages = @ceil( $total / $limit );
    if ( $totalpages > 1 ) {
        $ret .= "<form name='findnext' id='findnext' op='index.php' method='post'>\n<input type='hidden' name='op' value='findusers' />";
        foreach ( $_POST as $k => $v ) {
            $ret .= "<input type='hidden' name='$k' value='" . stripslashes( $v ) . "' />\n";
        }
        if ( !isset( $_POST['limit'] ) ) {
            $ret .= "<input type='hidden' name='limit' value='" . $limit . "' />\n";
        }
        if ( !isset( $_POST['start'] ) ) {
            $ret .= "<input type='hidden' name='start' value='" . $start . "' />\n";
        }
        $prev = $start - $limit;
        $ret .= "" . _PAGE . "";
        if ( $start - $limit >= 0 ) {
            $ret .= "<a href=\"javascript:cpUpdateValue(" . $prev . ");\">&nbsp;" . zarilia_img_show( 'n_previous', _PREVIOUS ) . "</a>\n";
        }
        $counter = 1;
        $currentpage = ( $start + $limit ) / $limit;
        while ( $counter <= $totalpages ) {
            if ( $counter == $currentpage ) {
                $ret .= "&nbsp;<b>(" . $counter . ")</b>&nbsp;";
            } elseif ( ( $counter > $currentpage-4 && $counter < $currentpage + 4 ) || $counter == 1 || $counter == $totalpages ) {
                if ( $counter == $totalpages && $currentpage < $totalpages-4 ) {
                    $ret .= "...";
                }
                $ret .= "<a href=\"javascript:cpUpdateValue(" . ( $counter-1 ) * $limit . ");\">&nbsp;" . $counter . "</a>\n";
                if ( $counter == 1 && $currentpage > 5 ) {
                    $ret .= "...";
                }
            }
            $counter++;
        }
        $next = $start + $limit;
        if ( $total > $next ) {
            $ret .= "<a href=\"javascript:cpUpdateValue(" . $next . ");\">&nbsp;" . zarilia_img_show( 'n_next', _NEXT ) . "</a>&nbsp;\n";
        }
        $ret .= "</form>";
        echo "<div style='text-align: right;'>" . $ret . "</div><br />";
    }
}

?>