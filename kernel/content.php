<?php
// $Id: content.php,v 1.5 2007/05/09 14:14:30 catzwolf Exp $
// ------------------------------------------------------------------------ //
// Zarilia - PHP Content Management System                      			//
// Copyright (c) 2007 Zarilia                           				//
// //
// Authors: 																//
// John Neill ( AKA Catzwolf )                                     			//
// Raimondas Rimkevicius ( AKA Mekdrop )									//
// //
// URL: http:www.zarilia.com 												//
// Project: Zarilia Project                                               //
// -------------------------------------------------------------------------//
defined( 'ZAR_ROOT_PATH' ) or die( 'You do not have permission to access this file!' );

/**
 * ZariliaContent
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2006
 * @version $Id: content.php,v 1.5 2007/05/09 14:14:30 catzwolf Exp $
 * @access public
 */
class ZariliaContent extends ZariliaObject {
    /**
     * constructor
     */
    function ZariliaContent() {
        $this->zariliaObject();
        $this->initVar( 'content_id', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'content_sid', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'content_cid', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'content_uid', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'content_alias', XOBJ_DTYPE_TXTBOX, null, false, 60 );
        $this->initVar( 'content_created', XOBJ_DTYPE_LTIME, time(), false );
        $this->initVar( 'content_published', XOBJ_DTYPE_LTIME, time(), false );
        $this->initVar( 'content_updated', XOBJ_DTYPE_LTIME, null, false );
        $this->initVar( 'content_expired', XOBJ_DTYPE_LTIME, null, false );
        $this->initVar( 'content_title', XOBJ_DTYPE_TXTBOX, null, false, 100 );
        $this->initVar( 'content_subtitle', XOBJ_DTYPE_TXTBOX, null, false, 150 );
        $this->initVar( 'content_intro', XOBJ_DTYPE_TXTAREA, null, false, null, 1 );
        $this->initVar( 'content_body', XOBJ_DTYPE_TXTAREA, null, false, null, 1 );
        $this->initVar( 'content_images', XOBJ_DTYPE_IMAGE, null, false, 150 );
        $this->initVar( 'content_summary', XOBJ_DTYPE_TXTAREA, null, false, null, 1 );
        $this->initVar( 'content_counter', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'content_type', XOBJ_DTYPE_TXTBOX, null, false, 60 );
        $this->initVar( 'content_hits', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'content_version', XOBJ_DTYPE_OTHER, 1.00, false );
        $this->initVar( 'content_approved', XOBJ_DTYPE_INT, 1, false );
        $this->initVar( 'content_weight', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'content_display', XOBJ_DTYPE_INT, 1, false );
        $this->initVar( 'content_meta', XOBJ_DTYPE_TXTAREA, null, false, null, 1 );
        $this->initVar( 'content_keywords', XOBJ_DTYPE_TXTAREA, null, false, null, 1 );
        $this->initVar( 'content_spotlight', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'content_spotlightmain', XOBJ_DTYPE_INT, null, false );
    }

    /**
     * ZariliaContent::formEdit()
     *
     * @return
     */
    function formEdit() {
        include_once ZAR_ROOT_PATH . '/kernel/kernel_forms/content.php';
    }

    /**
     * ZariliaContent::getIcons()
     *
     * @return
     */
    function getIcons() {
        /*Html output here*/
        $ret = "";
        $ret .= "<a target='_blank' href='" . ZAR_URL . "/index.php?page_type=" . $this->getVar( 'content_type' ) . "&amp;id=" . $this->getVar( 'content_id' ) . "&amp;act=print'>" . zarilia_img_show( 'content_print', _PRINT_ICON, null, 'gif' ) . "</a>";
        $ret .= "&nbsp;<a target='_blank' href='" . ZAR_URL . "/index.php?page_type=" . $this->getVar( 'content_type' ) . "&amp;id=" . $this->getVar( 'content_id' ) . "&amp;act=dopdf'>" . zarilia_img_show( 'content_pdf', _PDF_ICON, null, 'gif' ) . "</a>";
        $ret .= "&nbsp;<a href='" . ZAR_URL . "/index.php?page_type=" . $this->getVar( 'content_type' ) . "&amp;id=" . $this->getVar( 'content_id' ) . "&amp;act=email'>" . zarilia_img_show( 'content_email', _EMAIL_ICON, null, 'gif' ) . "</a>";
        // $ret .= "&nbsp;<a href='" . ZAR_URL . "/index.php?page_type=" . $this->getVar( 'content_type' ) . "&id=" . $this->getVar( 'content_id' ) . "&amp;act=rss'>" . zarilia_img_show( 'content_rss', _RSS_ICON, null, 'gif' ) . "</a>";
        $ret .= '<a href="#" rel="sidebar" onclick="if(document.all &amp;&amp; !window.opera){ window.external.AddFavorite(location.href, document.title); return false; }else{ this.title = document.title; }" title="bookmark this page">' . zarilia_img_show( 'content_bookmark', 'bookmark this page', null, 'gif' ) . '</a>';
        return $ret;
    }

    /**
     * ZariliaContent::getSection()
     *
     * @param mixed $linked
     * @return
     */
    function getSection( $linked = true ) {
        global $section_handler;
        static $section_array;

        $section_handler = &zarilia_gethandler( 'section' );
        if ( !isset( $section_array ) ) {
            $section_array = $section_handler->getList();
        }
        $ret = '';
        if ( ( !$this->getVar( 'content_sid' ) || $this->getVar( 'content_sid' ) == 0 ) && $this->getVar( 'content_type' ) == 'static' ) {
            $ret = 'Static Page';
        } else {
            if ( $linked == true ) {
                $ret = '<a href="' . ZAR_URL . '/addons/system/index.php?fct=section&op=edit&section_id=' . $this->getVar( 'content_sid' ) . '">' . $section_array[$this->getVar( 'content_sid' )] . '</a>';
            } else {
                $ret = $section_array[$this->getVar( 'content_sid' )];
            }
        }
        return $ret;
    }

    /**
     * ZariliaContent::getCategory()
     *
     * @param mixed $linked
     * @return
     */
    function getCategory( $linked = true ) {
        global $category_handler;
        static $category_array;

        $category_handler = &zarilia_gethandler( 'category' );
        if ( !isset( $category_array ) ) {
            $category_array = $category_handler->getList();
        }
        if ( !$this->getVar( 'content_cid' ) || $this->getVar( 'content_cid' ) == 0 || !isset( $category_array[$this->getVar( 'content_cid' )] ) ) {
            $ret = '-------------';
        } else {
            if ( $linked == true ) {
                $ret = '<a href="' . ZAR_URL . '/addons/system/index.php?fct=category&op=edit&category_id=' . $this->getVar( 'content_cid' ) . '">' . $category_array[$this->getVar( 'content_cid' )] . '</a>';
            } else {
                $ret = $category_array[$this->getVar( 'content_cid' )];
            }
        }
        return $ret;
    }

    /**
     * ZariliaContent::getZariliaUser()
     *
     * @return
     */
    function getcpUser( $is_linked = true, $usereal = false, $uid = null ) {
        $member_handler = &zarilia_gethandler( 'member' );
        $uid = ( $uid != null ) ? $uid : $this->getVar( 'content_uid' );
        $content_user = &$member_handler->getUser( $uid );
        if ( is_object( $content_user ) ) {
            if ( intval( $usereal ) ) {
                $ret['name'] = $content_user->getVar( 'name' );
            } else {
                $ret['name'] = $content_user->getVar( 'uname' );
            }
            if ( $this->getVar( 'content_alias' ) != '' ) {
                $ret['name'] = $this->getVar( 'content_alias' );
            }
            if ( $is_linked ) {
                $ret['name'] = '<a href="' . ZAR_URL . '/index.php?page_type=userinfo&uid=' . $content_user->getVar( 'uid' ) . '">' . $ret['name'] . '</a>';
            }
            $ret['avatar'] = $content_user->getVar( 'user_avatar' ) ? $content_user->getVar( 'user_avatar' ) : 'nouserimage.jpg';
            $ret['online'] = $content_user->isOnline();
        } else {
            $ret['name'] = $GLOBALS['zariliaConfig']['anonymous'];
        }
        return $ret;
    }

    function getUser( $show = 'edit' ) {
        switch ( $show ) {
            case 'show':
                $ret = ( $this->getVar( 'content_alias' ) != '' ) ? $this->getVar( 'content_alias' ) : zarilia_getLinkedUnameFromId( $this->getVar( 'content_uid' ), 0, 1 );
                break;
            case 'edit':
            default:
                $ret = zarilia_getLinkedUnameFromId( $this->getVar( 'content_uid' ), 0, 1 );
                break;
        } // switch
        return $ret;
    }
}

/**
 * ZariliaContentHandler
 *
 * @package
 * @author John Neill AKA Catzwolf
 * @copyright Copyright (c) 2006
 * @version $Id: content.php,v 1.5 2007/05/09 14:14:30 catzwolf Exp $
 * @access public
 */
class ZariliaContentHandler extends ZariliaPersistableObjectHandler {
    var $content;
    var $_content_obj = array();
    /**
     * ZariliaContentHandler::ZariliaContentHandler()
     *
     * @param  $db
     * @return
     */
    function ZariliaContentHandler( &$db ) {
        $this->ZariliaPersistableObjectHandler( $db, 'content', 'ZariliaContent', 'content_id', 'content_title', 'content_read' );
    }

    /**
     * ZariliaContentHandler::getContentObj()
     *
     * @param array $nav
     * @param mixed $_mid
     * @param mixed $content_display
     * @return
     */
    function getContentObj( $nav = array(), $isadmin = false ) {
        $criteria = new CriteriaCompo();
        if ( $isadmin == false ) {
            $criteria->add( new Criteria( 'content_approved', 1 ) );
            $criteria->add( new Criteria( 'content_display', 1 ) );
        }

        if ( isset( $nav['content_type'] ) && $nav['content_type'] != '' ) {
            $criteria->add( new Criteria( 'content_type', $nav['content_type'] ) );
        }

        if ( isset( $nav['content_cid'] ) && $nav['content_cid'] != '' ) {
            $criteria->add( new Criteria( 'content_cid', $nav['content_cid'] ) );
        }
        $obj['count'] = $this->getCount( $criteria, false );
        // if ( !empty( $nav ) ) {
        $criteria->setSort( @$nav['sort'] );
        $criteria->setOrder( @$nav['order'] );
        $criteria->setStart( @$nav['start'] );
        $criteria->setLimit( @$nav['limit'] );
        // }
        $obj['list'] = $this->getObjects( $criteria, false );
        return $obj;
    }

    /**
     * ZariliaContentHandler::getTrashObj()
     *
     * @param array $nav
     * @return
     */
    function getTrashObj( $nav = array() ) {
        $criteria = new CriteriaCompo();
        $criteria->add( new Criteria( 'content_status', 3, '=' ) );
        $obj['count'] = $this->getCount( $criteria, false );

        $criteria->setSort( $nav['sort'] );
        $criteria->setOrder( $nav['order'] );
        $criteria->setStart( $nav['start'] );
        $criteria->setLimit( $nav['limit'] );
        $obj['list'] = $this->getObjects( $criteria, false );
        return $obj;
    }

    /**
     * ZariliaContentHandler::$this->_content_obj()
     *
     * @param integer $id
     * @param mixed $as_object
     * @return
     */
    function getContentItem( $as_object = true ) {
        $criteria = new CriteriaCompo();
        $criteria->add( new Criteria( 'content_id', $this->_id, '=' ) );
        $criteria->add( new Criteria( 'content_display', 1, '=' ) );
        $criteria->add( new Criteria( 'content_approved', 1, '=' ) );
        $criteria->add( new Criteria( 'content_status', 3, '!=' ) );
        $criteria->add( new Criteria( 'content_published', 0, '>' ), 'AND' );
        $criteria->add( new Criteria( 'content_published', time(), '<=' ), 'AND' );
        $criteria->add( new Criteria( 'content_expired', 0, '=' ), 'AND' );
        $criteria->add( new Criteria( 'content_expired', time(), '>' ), 'OR' );

        if ( $this->_uid ) {
            $criteria->add ( new Criteria( 'content_uid', $this->_uid, '=' ) );
        }
        $criteria->setLimit( 1 );
        $obj_array = $this->getObjects( $criteria, false, $as_object );

        if ( !is_array( $obj_array ) || count( $obj_array ) != 1 ) {
            return false;
        } else {
            $ret = &$obj_array[0];
        }
        return $ret;
    }

    /**
     * ZariliaContentHandler::getcType()
     *
     * @param mixed $id
     * @return
     */
    function getcType( $id ) {
        $id = intval( $id );
        if ( $id == 0 ) {
            return 'static';
        }

        $sql = 'SELECT * FROM ' . $this->db->prefix( 'section' ) . ' WHERE section_id = ' . $id;
        if ( !$result = $this->db->SelectLimit( $sql, 0, 0 ) ) {
			$GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, 'Database error: '. $sql, __FILE__, __LINE__ );
            return false;
        } while ( $myrow = $result->FetchRow() ) {
            $ret = &$myrow['section_type'];
        }
        return $ret;
    }

    /**
     * ZariliaContentHandler::getContent()
     *
     * @param array $_values
     * @return
     */
    function getContent( $_values = array() ) {
        global $zariliaOption, $zariliaConfig, $zariliaTpl;

        if ( is_array( $_values ) && count( $_values ) ) {
            extract( $_values );
            $this->_id = $id;
            $this->_cid = $cid;
            $this->_uid = $uid;
            $this->_page_type = $page_type;
            $this->_act = $act;
        }

        $_values['start'] = zarilia_cleanRequestVars( $_REQUEST, 'start', 0 );
        $_values['sort'] = zarilia_cleanRequestVars( $_REQUEST, 'sort', 'content_id' );
        $_values['order'] = zarilia_cleanRequestVars( $_REQUEST, 'order', 'DESC', XOBJ_DTYPE_TXTBOX );
        $_values['limit'] = zarilia_cleanRequestVars( $_REQUEST, 'limit', 10 );
        $_values['content_type'] = zarilia_cleanRequestVars( $_REQUEST, 'page_type', 'news' );

        if ( !empty( $this->_page_type ) && !is_readable( ZAR_ROOT_PATH . '/content/system/content_' . $this->_page_type . '.php' ) ) {
            $this->_page_type = 'page_error';
        }
        if ( is_readable( ZAR_ROOT_PATH . '/language/' . $zariliaConfig['language'] . '/' . $this->_page_type . '.php' ) ) {
            require ZAR_ROOT_PATH . '/language/' . $zariliaConfig['language'] . '/' . $this->_page_type . '.php';
        }
        switch ( $this->_page_type ) {
            case 'blog':
            case 'news':
            case 'links':
            case 'downloads':
            case 'articles':
            case 'faq':
            case 'rss':
                switch ( $this->_act ) {
                    case 'pda':
                        $this->getPda( $_values );
                        break;
                    case 'print':
                    case 'dopdf':
                        $this->_content_obj = &$this->getContentItem();
                        if ( $this->_content_obj ) {
                            $this->getAction();
                        }
                        break;
                }
                include_once ZAR_ROOT_PATH . '/content/system/content_' . $this->_page_type . '.php';
                break;

            case 'static':
                $this->_content_obj = &$this->getContentItem();
                if ( $this->_content_obj ) {
                    switch ( $this->_act ) {
                        case 'print':
                        case 'dopdf':
                            $this->getAction();
                            break;
                    }
                    include_once ZAR_ROOT_PATH . '/content/system/content_' . $this->_page_type . '.php';
                } else {
                    $this->doErrorPage();
                    break;
                }
                break;

            case 'stream':
                $_values['sort'] = 'streaming_id'; //zarilia_cleanRequestVars( $_REQUEST, 'sort', 'content_id' );
                $_values['streaming_display'] = 1; //zarilia_cleanRequestVars( $_REQUEST, 'sort', 'content_id' );
                break;

            case 'media':
                include_once ZAR_ROOT_PATH . '/content/system/content_' . $this->_page_type . '.php';
                break;

            case 'backend':
                include_once ZAR_ROOT_PATH . '/content/system/content_' . $this->_page_type . '.php';
                break;

            case 'friend':
                include_once ZAR_ROOT_PATH . '/content/system/content_' . $this->_page_type . '.php';
                break;

            case 'page_error':
                $this->doErrorPage();
                break;

            case 'default';
            // This will be the front page item if nothing else is selected
            //include_once ZAR_ROOT_PATH . '/content/system/content_' . $this->_page_type . '.php';
            break;
    } // switch
}

function getDownload( $_values = array() ) {
    if ( is_array( $_values ) && count( $_values ) ) {
        extract( $_values );
        $this->_id = $id;
        $this->_cid = $cid;
        $this->_uid = $uid;
        $this->_page_type = $page_type;
        $this->_act = $act;
    }
    /**
     */
    switch ( $this->_act ) {
        case 'image':
        case 'media':
            $_media_obj = $media_handler->get( $this->_id );
            if ( $_media_obj ) {
                header( 'Content-type: ' . $_media_obj[0]->getVar( 'media_mimetype' ) );
                header( 'Cache-control: max-age=31536000' );
                header( 'Expires: ' . gmdate( "D, d M Y H:i:s", time() + 31536000 ) . 'GMT' );
                header( 'Content-disposition: filename=' . $_media_obj[0]->getVar( 'media_name' ) );
                header( 'Content-Length: ' . strlen( $_media_obj[0]->getVar( 'media_body' ) ) );
                header( 'Last-Modified: ' . gmdate( "D, d M Y H:i:s", $_media_obj[0]->getVar( 'media_created' ) ) . 'GMT' );
                echo $image[0]->getVar( 'image_body' );
            }
            break;

        case 'stream':
            $streaming_handler = &zarilia_gethandler( 'streaming' );
            $_streaming_obj = $streaming_handler->get( $this->_id );
            if ( $_streaming_obj ) {
                $fileurl = ZAR_UPLOAD_PATH . '/streams/' . $_streaming_obj->getVar( 'streaming_file' );
                $filename = basename( $fileurl );
                $filemimetype = $_streaming_obj->getVar( 'streaming_mimetype' );
                $filesize = filesize( $fileurl );
            }
            break;

        case 'file':
            $fileurl = isset( $_REQUEST['file'] ) ? base64_decode( urldecode( $_REQUEST['file'] ) ) : '';
            $filename = basename( $fileurl );
            $filemimetype = '';
            $filesize = filesize( $fileurl );
            break;

        case 'default':
        default:
            break;
    } // switch
    $ext = pathinfo( $filename, PATHINFO_EXTENSION );
    if ( strtolower( $ext ) == 'php' ) die( 'You are trying to view files that you have no business viewing.' );

    /*proceed with download here*/
    if ( function_exists( 'mb_http_output' ) ) {
        mb_http_output( 'pass' );
    }
    if ( ini_get( 'zlib.output_compression' ) ) {
        ini_set( 'zlib.output_compression', 'Off' );
    }

    if ( !headers_sent() ) {
        switch ( $this->_act ) {
            case 'image':
                header( 'Content-type: ' . $image[0]->getVar( 'image_mimetype' ) );
                header( 'Cache-control: max-age=31536000' );
                header( 'Expires: ' . gmdate( "D, d M Y H:i:s", time() + 31536000 ) . 'GMT' );
                header( 'Content-disposition: filename=' . $image[0]->getVar( 'image_name' ) );
                header( 'Content-Length: ' . strlen( $image[0]->getVar( 'image_body' ) ) );
                header( 'Last-Modified: ' . gmdate( "D, d M Y H:i:s", $image[0]->getVar( 'image_created' ) ) . 'GMT' );
                echo $image[0]->getVar( 'image_body' );
                break;

            case 'media':
            case 'stream':
            case 'file':
                header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
                header( "Cache-Control: private", false );
                header( "Content-type: " . $filemimetype );
                header( "Content-Disposition: attachment; filename=\"" . $filename . "\";" );
                header( "Content-Length: " . $filesize );
                // readfile( $url );
                $fp = @fopen( $fileurl, "rb" );
                fpassthru( $fp );
                fclose( $fp );
                break;

            case 'default':
            default:
                header( 'Content-type: image/gif' );
                readfile( ZAR_UPLOAD_PATH . '/blank.gif' );
                break;
        } // switch
    }
}

/**
 * ZariliaContentHandler::render()
 *
 * @return
 */
function render() {
    echo $this->content;
}

/**
 * ZariliaContentHandler::doErrorPage()
 *
 * @param mixed $errorType
 * @return
 */
function doErrorPage() {
    // global $zariliaLogger;
    $errorLogger = &ZariliaLogger::instance();
    $errorLogger->doPageNotFound();
}

/*
	* Get User name for item
	*/
function &getcpUser( $is_linked = true, $usereal = false, $uid = null ) {
    $member_handler = &zarilia_gethandler( 'member' );
    $content_user = &$member_handler->$this->_content_obj( $uid );
    if ( is_object( $content_user ) ) {
        if ( $usereal == true && $content_user->getVar( 'name' ) ) {
            $ret['name'] = $content_user->getVar( 'name' );
        } else {
            $ret['name'] = $content_user->getVar( 'uname' );
        }
        if ( $is_linked == true ) {
            $ret['name'] = '<a href="' . ZAR_URL . '/index.php?page_type=userinfo&uid=' . $content_user->getVar( 'uid' ) . '">' . $ret['name'] . '</a>';
        }
        $ret['avatar'] = $content_user->getVar( 'user_avatar' ) ? $content_user->getVar( 'user_avatar' ) : 'nouserimage.jpg';
        $ret['online'] = $content_user->isOnline();
    } else {
        $ret['name'] = $GLOBALS['zariliaConfig']['anonymous'];
    }
    return $ret;
}

/**
 * ZariliaContentHandler::do_backbutton()
 *
 * @param string $type
 * @return
 */
function do_backbutton( $type = 'link' ) {
    $ret = '';
    switch ( $type ) {
        case 'image':
            break;
        case 'link':
            $ret .= '<div style="padding-top: 12px; text-align: left;"><a href=\'javascript:history.go(-1)\'>[ ' . _CONTENT_BACK . ' ]</a></div>';
            break;
        case 'none':
        default:
            break;
    } // switch
    return $ret;
}
/**
 * ZariliaContentHandler::getAction()
 *
 * @param mixed $obj
 * @param string $op
 * @return
 */
function getAction() {
    global $zariliaConfig;

    $pdf_data['creator'] = "Zarilia";
    $pdf_data['title'] = $this->_content_obj->getVar( 'content_title' );
    $pdf_data['subtitle'] = $this->_content_obj->getVar( 'content_subtitle' );
    $pdf_data['subsubtitle'] = '';

    $pdf_data['renderdate'] = $this->_content_obj->formatTimeStamp( 'today' );
    $pdf_data['pdate'] = $this->_content_obj->formatTimeStamp( 'content_published' );
    $pdf_data['udate'] = $this->_content_obj->formatTimeStamp( 'content_updated' );
    $pdf_data['slogan'] = $zariliaConfig['sitename'] . ' - ' . $zariliaConfig['slogan'];

    $content = '';
    if ( $this->_content_obj->getVar( 'content_intro' ) ) {
        $content .= $this->_content_obj->getVar( 'content_intro' ) . "\n\n";
    }
    $content .= $this->_content_obj->getVar( 'content_body' );
    $content = str_replace( '[pagebreak]', "\n\n", $content );
    $pdf_data['content'] = $content;

    $pdf_data['author'] = ( $this->_content_obj->getVar( 'content_alias' ) ) ? $this->_content_obj->getVar( 'content_alias' ) : zarilia_getLinkedUnameFromId( $this->_content_obj->getVar( 'content_uid' ), $usereal = 0, $is_linked = 0 );
    $pdf_data['keywords'] = $this->_content_obj->getVar( "content_keywords" );
    $pdf_data['meta'] = $this->_content_obj->getVar( "content_meta" );
    $pdf_data['sitename'] = $zariliaConfig["sitename"];
    $pdf_data['itemurl'] = ZAR_URL . '/index.php?page_type=' . $this->_content_obj->getVar( 'content_type' ) . '&id=' . $this->_content_obj->getVar( 'content_id' );

    /* do switch */
    switch ( $this->_act ) {
        case 'print':
            require ZAR_ROOT_PATH . '/class/class.print.php';
            $print = new cp_doPrint( $pdf_data );
            $print->renderPrint();
            break;

        case 'dopdf':
            require ZAR_ROOT_PATH . '/class/class.pdf.php';
            $pdf = new cp_dopdf( $pdf_data );
            $pdf->renderpdf();
            break;
    } // switch
}

function getPda() {
    $_content_obj = $this->getContentObj( $_values, 0 );
    if ( !$_content_obj['count'] ) {
        $output = "No files to view, sorry";
    } else {
        foreach ( $_content_obj['list'] as $obj ) {
            $content['content_id'] = $obj->getVar( 'content_id' );
            $content['content_title'] = $obj->getVar( 'content_title' );
            $output .= "<a href='" . ZAR_URL . "/index.php?page_type=news&id=" . $obj->getVar( 'content_id' ) . "&act=print'>" . $obj->getVar( 'content_title' ) . "</a><br />";
        }
    }
    global $zariliaConfig;

    if ( !headers_sent() ) {
        header( "Content-Type: text/html" );
        $this->content = "<html><head><title>" . $zariliaConfig['sitename'] . "</title>
		      <meta name='HandheldFriendly' content='True' />
			  <meta name='PalmComputingPlatform' content='True' />
			  </head>
			  <body>";
        $this->content .= "<img src='images/logo.gif' alt='" . $zariliaConfig['sitename'] . "' border='0' /><br />
		   	  <h2>" . $zariliaConfig['slogan'] . "</h2>
		      <div>";
        $this->content .= $output;
        $this->content .= "</body></html>";
        unset( $output, $_content_obj );
        $this->render();
    }
    exit();
}
}

?>