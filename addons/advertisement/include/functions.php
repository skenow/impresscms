<?php
if ( !defined( 'ZAR_ROOT_PATH' ) ) {
    exit( 'You cannot access this file directly' );
}

function clientCountOutput( $count ) {
    echo "<div><strong>" . _MA_AD_TOTAL_CLIENTS . "</strong>: $count</div><br />";
}

/**
 * Function to display banners in all pages
 */
function showbanner() {
    echo zarilia_getbanner();
}

/**
 * showHtmlbanner()
 *
 * @param unknown $bannum
 * @return
 */
function showHtmlbanner( $bannum = null ) {
    echo '
	<table cellspacing="0">
	 <tr>
      <td id="headerbanner">' . zarilia_getbanner( $bannum ) . '
     </tr>
    </table>';
}

/**
 * Function to get banner html tags for use in templates
 */
function zarilia_getbanner( $bannum = 0, $show_html = 0 ) {
    global $zariliaConfig, $zariliaDB;

    $sql = "SELECT * FROM " . $zariliaDB->prefix( "banner" );
    $myrow = $zariliaDB->fetchRow( $zariliaDB->Execute( $sql ) );
    $numrows = $zariliaDB->getRowsNum( $zariliaDB->Execute( $sql ) );

    if ( $numrows > 0 ) {
        $sql = "SELECT bid, cid, imptotal, impmade, clicks, imageurl, clickurl, date, htmlbanner, htmlcode FROM " . $zariliaDB->prefix( 'banner' );
        if ( $bannum != 0 ) {
            if ( is_array( $bannum ) && count( $bannum ) ) {
                $bannum = $bannum[0];
            } else {
                $bannum = ( $numrows == 1 && intval( $bannum ) == 0 ) ? intval( $myrow['bid'] ) : $bannum;
            }
            $sql .= " WHERE bid = " . $bannum;
        } else {
            $sql .= " ORDER BY RAND() LIMIT 0,1 ";
        }
        $result = $zariliaDB->Execute( $sql );
        list ( $bid, $cid, $imptotal, $impmade, $clicks, $imageurl, $clickurl, $date, $htmlbanner, $htmlcode ) = $zariliaDB->fetchRow( $result );

        if ( empty( $htmlbanner ) && empty( $imageurl ) ) {
            return '';
        }

        if ( $zariliaConfig['my_ip'] == zarilia_getenv( 'REMOTE_ADDR' ) ) {
            return '';
        } else {
            $result = $zariliaDB->queryF( sprintf( "UPDATE %s SET impmade = impmade+1 WHERE bid = %u", $zariliaDB->prefix( "banner" ), $bid ) );
        }

        /**
         * Check if this impression is the last one and print the banner
         */
        if ( $imptotal == $impmade ) {
            $newid = $zariliaDB->genId( $zariliaDB->prefix( "bannerfinish" ) . "_bid_seq" );
            $sql = sprintf( "INSERT INTO %s (bid, cid, impressions, clicks, datestart, dateend) VALUES (%u, %u, %u, %u, %u, %u)", $zariliaDB->prefix( "bannerfinish" ), $newid, $cid, $impmade, $clicks, $date, time() );
            $result = $zariliaDB->queryF( $sql );
            $zariliaDB->queryF( sprintf( "DELETE FROM %s WHERE bid = %u", $zariliaDB->prefix( "banner" ), $bid ) );
        }

        if ( $htmlbanner && empty( $imageurl ) ) {
            $bannerobject = $htmlcode;
        } else {
            $bannerobject = '<div style="text-align: center;"><a href="' . ZAR_URL . '/banners.php?op=click&amp;bid=' . $bid . '" target="_blank">';
            if ( stristr( $imageurl, '.swf' ) ) {
                $bannerobject = $bannerobject
                 . '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0" width="468" height="60">'
                 . '<param name=movie value="' . $imageurl . '">'
                 . '<param name=quality value=high>'
                 . '<embed src="' . $imageurl . '" quality=high pluginspage="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash"; type="application/x-shockwave-flash" width="468" height="60">'
                 . '</embed>'
                 . '</object>';
            } else {
                $bannerobject = $bannerobject . '<img src="' . $imageurl . '" alt="" />';
            }
            $bannerobject = $bannerobject . '</a></div>';
        }
        if ( $show_html ) {
            $ret = '
			<table cellspacing="0">
			 <tr>
		      <td id="headerbanner">' . $bannerobject . '
		     </tr>
		    </table>';
            return $ret;
        } else {
            return $bannerobject;
        }
    }
}

/**
 * createdir()
 *
 * @param string $name
 * @return
 */
function createdir( $name = '' ) {
    global $client_handler, $menu_handler;
    $new_name = preg_replace( '!\s+!', '_', strtolower( $name ) );
    $banner_dir = "banners/$new_name";
    if ( false == $client_handler->get_create_folder( ZAR_UPLOAD_PATH . '/' . $banner_dir, 0777 ) ) {
        zarilia_cp_header();
        $menu_handler->render( 1 );
        $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _MA_AD_CREATEDIR, ZAR_UPLOAD_PATH . '/' . $banner_dir, $new_name ) );
        $GLOBALS['zariliaLogger']->sysRender();
        zarilia_cp_footer();
        exit();
    }
    return $banner_dir;
}

?>