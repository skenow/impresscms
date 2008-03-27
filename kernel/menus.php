<?php
// $Id: menus.php,v 1.6 2007/05/09 14:14:30 catzwolf Exp $
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
 * Zarilia Menus Class
 *
 * @package kernel
 * @author John Neill AKA Catzwolf
 * @copyright (c) 2006 Zarilia
 */
class ZariliaMenus extends ZariliaObject
{
    /**
     * constructor
     */
    function ZariliaMenus()
    {
        $this->zariliaObject();
        $this->initVar( 'menu_id', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'menu_pid', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'menu_type', XOBJ_DTYPE_TXTBOX, 0, false, 10 );
        $this->initVar( 'menu_title', XOBJ_DTYPE_TXTBOX, null, false, 60 );
        $this->initVar( 'menu_link', XOBJ_DTYPE_TXTBOX, null, false, 255 );
        $this->initVar( 'menu_image', XOBJ_DTYPE_TXTBOX, null, false, 255 );
        $this->initVar( 'menu_weight', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'menu_mid', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'menu_name', XOBJ_DTYPE_TXTBOX, null, false, 255 );
        $this->initVar( 'menu_sectionid', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'menu_display', XOBJ_DTYPE_INT, 1, false );
        $this->initVar( 'menu_target', XOBJ_DTYPE_TXTBOX, 0, false, 15 );
        $this->initVar( 'menu_class', XOBJ_DTYPE_TXTBOX, null, false, 15 );
    }

    /**
     * ZariliaMenus::formEdit()
     *
     * @param string $_menu_type
     * @return
     */
    function formEdit()
    {
        if ( is_readable( ZAR_ROOT_PATH . '/kernel/kernel_forms/menus.php' ) )
        {
            include_once ZAR_ROOT_PATH . '/kernel/kernel_forms/menus.php';
        }
    }
}

/**
 * ZariliaMenusHandler
 *
 * @package
 * @author John Neill AKA Catzwolf
 * @copyright Copyright (c) 2006
 * @version $Id: menus.php,v 1.6 2007/05/09 14:14:30 catzwolf Exp $
 * @access public
 */
class ZariliaMenusHandler extends ZariliaPersistableObjectHandler
{
    /**
     * ZariliaMenusHandler::ZariliaMenusHandler()
     *
     * @param  $db
     * @return
     */
    function ZariliaMenusHandler( &$db )
    {
        $this->ZariliaPersistableObjectHandler( $db, 'menus', 'ZariliaMenus', 'menu_id', 'menu_title', 'menu_read' );
    }

    function getMenuList()
    {
        return array( 'mainmenu' => _MAINMENU, 'usermenu' => _USERMENU, 'topmenu' => _TOPMENU, 'footermenu' => _FOOTERMENU );
    }

    /**
     * ZariliaMenusHandler::getMenus()
     *
     * @param array $nav
     * @param mixed $_mid
     * @return
     */
    function &getMenus( $nav = array(), $_menu_type = null, $doCount = false, $isAdmin = false )
    {
        $criteria = new CriteriaCompo();
        if ( $_menu_type != null )
        {
            $criteria->add( new Criteria( 'menu_type', $_menu_type ) );
        }
        if ( $isAdmin == false )
        {
            $criteria->add( new Criteria( 'menu_display', 1 ) );
        }
        $obj['count'] = ( $doCount != false ) ? $this->getCount( $criteria, false ) : null;
        if ( $isAdmin == true )
        {
            $criteria->setSort( @$nav['sort'] );
            $criteria->setOrder( @$nav['order'] );
            $criteria->setStart( @$nav['start'] );
            $criteria->setLimit( @$nav['limit'] );
        }
        else
        {
            $criteria->add( new Criteria( 'menu_display', 1 ) );
            $criteria->setSort( 'menu_weight' );
            $criteria->setOrder( 'ASC' );
        }
        $obj['list'] = $this->getObjects( $criteria, true, true );
        return $obj;
    }

    /**
     * ZariliaMenusHandler::getMenublock()
     *
     * @param string $_menu_type
     * @return
     */
    function &getMenublock( $_menu_type = null, $doCount = false )
    {
        $criteria = new CriteriaCompo();
        if ( $_menu_type != false )
        {
            $criteria->add( new Criteria( 'menu_type', $_menu_type ) );
        }
        $criteria->add( new Criteria( 'menu_display', 1 ) );
        $menublock['count'] = ( $doCount ) ? $this->getCount( $criteria, false ) : null;
        $criteria->setSort( 'menu_weight' );
        $criteria->setOrder( 'ASC' );
        $menublock['list'] = $this->getObjects( $criteria, true, true );
        return $menublock;
    }

    /**
     * ZariliaMenusHandler::getMenuItem()
     *
     * @param integer $mid
     * @param mixed $menu_name
     * @param mixed $id
     * @param mixed $as_object
     * @param mixed $config
     * @return
     */
    function &getMenuItem( $mid = 1, $menu_name, $id, $as_object = true, $config = false )
    {
        $ret = false;
        $criteria = new CriteriaCompo();
        $criteria->add( new Criteria( 'menu_mid', $mid ) );
        $criteria->add( new Criteria( 'menu_name', $menu_name ) );
        $criteria->add( new Criteria( 'menu_sectionid', $id ) );
        $criteria->setLimit( 1 );
        $obj_array = $this->getObjects( $criteria, false, $as_object );
        if ( !is_array( $obj_array ) || count( $obj_array ) != 1 )
        {
            $ret = false;
            return $ret;
        }
        else
        {
            $ret = &$obj_array[0];
        }
        return $ret;
    }

    /**
     * ZariliaMenusHandler::deleteMenuItem()
     *
     * @param mixed $menu_mid
     * @param mixed $menu_name
     * @param mixed $menu_sectionid
     * @param mixed $as_object
     * @param mixed $config
     * @return
     */
    function deleteMenuItem( $menu_mid, $menu_name, $menu_sectionid, $as_object = true, $config = false )
    {
        $ret = false;
        $criteria = new CriteriaCompo();
        $criteria->add( new Criteria( 'menu_mid', $menu_mid ) );
        $criteria->add( new Criteria( 'menu_name', $menu_name ) );
        $criteria->add( new Criteria( 'menu_sectionid', $menu_sectionid ) );
        $criteria->setLimit( 1 );
        $obj_array = $this->getObjects( $criteria, false, $as_object, false );
        if ( !is_array( $obj_array ) || count( $obj_array ) == 0 )
        {
            // return false;
        }
        else
        {
            if ( $this->delete( $obj_array[0] ) )
            {
                // return true;
            }
        }
        return false;
    }

    /**
     * ZariliaMenusHandler::updateMenuItem()
     *
     * @param mixed $menu_mid
     * @param mixed $menu_name
     * @param mixed $menu_sectionid
     * @param mixed $as_object
     * @param mixed $config
     * @return
     */
    function &updateMenuItem( $menu_mid, $menu_name, $menu_sectionid, $as_object = true, $config = false )
    {
        $ret = false;
        $criteria = new CriteriaCompo();
        $criteria->add( new Criteria( 'menu_mid', $menu_mid ) );
        $criteria->add( new Criteria( 'menu_name', $menu_name ) );
        $criteria->add( new Criteria( 'menu_sectionid', $menu_sectionid ) );
        $criteria->setLimit( 1 );
        $obj_array = $this->getObjects( $criteria, false, $as_object );
        if ( !is_array( $obj_array ) || count( $obj_array ) != 1 )
        {
            $ret = false;
        }
        if ( $this->delete( $obj_array[0] ) )
        {
            $ret = true;
        }
        return $ret;
    }

    function &doSort( &$obj, $type = '' )
    {
		if (!is_array($obj)) {
			$false = false;
			return $false;
		}
        foreach( $obj as $sort )
        {
            $thisMenu[$sort->getVar( 'menu_type' )][] = $sort; //array( 'url' => $sort->getVar( 'menu_link' ), 'title' => $obj->getVar( 'menu_title' ), 'image' => $obj->getVar( 'menu_image' ) );
        }
        return $thisMenu;
    }

    function displayTopMenu()
    {
        global $zariliaUser, $zariliaTpl;

		if (@$_REQUEST['debug'] == 'rebuild') {
			unset($_SESSION['user']['menu']);
			unset($_SESSION['user']['footermenu']);
			unset($_SESSION['user']['topmenu']);
		}

        if (!($menu_obj = &$this->getMenus( null ))) return false;
        if (!($thisMenu = &$this->doSort( $menu_obj['list'] ))) return false;


        if ( isset( $thisMenu['footermenu'] ) )
        {
            $total_count = count( $thisMenu['footermenu'] );
            $end_count = $total_count-1;
            foreach( $thisMenu['footermenu'] as $obj )
            {
                // topmenu
                $name = $obj->getVar( 'menu_title' );
                if ( $obj->getVar( 'menu_pid' ) == 0 )
                {
                    $menu_title = str_replace( '{X_HR}', '<hr />', $obj->getVar( 'menu_title' ) );
                    if ( $obj->getVar( 'menu_link' ) )
                    {
                        $url = str_replace( '{X_SITEURL}', ZAR_URL, $obj->getVar( 'menu_link', 'e' ) );
                        if ( $zariliaUser )
                        {
                            $url = str_replace( '{X_UID}', $zariliaUser->getVar( 'uid' ), $url );
							$url = str_replace( '{X_USERNAME}', $zariliaUser->getVar( 'uname' ), $url );
                        }
                        /**
                         */
                        if ( ( eregi( "mailto:", $url ) ) || ( eregi( "http://", $url ) ) || ( eregi( "https://", $url ) ) || ( eregi( "file://", $url ) ) || ( eregi( "ftp://", $url ) ) )
                        {
                            $link = $url;
                        }
                        else
                        {
                            $link = ZAR_URL . "/" . $url;
                        }
                    }
                    else
                    {
                        $link = 'javascript:void(0)';
                    }
                    $_SESSION['user']['footermenu'][$name] = array( 'url' => $link, 'title' => $menu_title, 'image' => ZAR_UPLOAD_PATH . '/' . $obj->getVar( 'menu_image' ) );
                }
            }
        }

        $i = 1;
		$menuTypes = array_keys($thisMenu);
		foreach ($menuTypes as $menuType) 
        foreach( $thisMenu[$menuType] as $obj )
        {
            if ( $obj->getVar( 'menu_link' ) )
            {
                $url = str_replace( '{X_SITEURL}', ZAR_URL, $obj->getVar( 'menu_link' ) );
                if ( $zariliaUser )
                {
                    $url = str_replace( '{X_UID}', $zariliaUser->getVar( 'uid' ), $url );
					$url = str_replace( '{X_USERNAME}', $zariliaUser->getVar( 'uname' ), $url );
                }
                /**
                 */
                if ( ( eregi( "mailto:", $url ) ) || ( eregi( "http://", $url ) ) || ( eregi( "https://", $url ) ) || ( eregi( "file://", $url ) ) || ( eregi( "ftp://", $url ) ) )
                {
                    $link = $url;
                }
                else
                {
                    $link = ZAR_URL . "/" . $url;
                }
            }
            else
            {
                $link = '';
            }

            if ( $obj->getVar( 'menu_pid' ) == 0 )
            {
                $name = $obj->getVar( 'menu_title' );
                if ( !isset( $_SESSION['administration']['menu'][$name] ) )
                {
                    $_SESSION['user']['menu'][$name] = array( 'url' => $link, 'title' => $name, 'items' => array(), 'id' => $i++ );
                }
                foreach( $thisMenu[$menuType] as $obj2 )
                {
                    if ( $obj2->getVar( 'menu_pid' ) == $obj->getVar( 'menu_id' ) )
                    {
                        $menu_title = str_replace( '{X_HR}', '<hr />', $obj2->getVar( 'menu_title' ) );
                        if ( $obj2->getVar( 'menu_link' ) )
                        {
                            $url = str_replace( '{X_SITEURL}', ZAR_URL, $obj2->getVar( 'menu_link', 'e' ) );
                            if ( $zariliaUser )
                            {
                                $url = str_replace( '{X_UID}', $zariliaUser->getVar( 'uid' ), $url );
								$url = str_replace( '{X_USERNAME}', $zariliaUser->getVar( 'uname' ), $url );
                            }
                            /**
                             */
                            if ( ( eregi( "mailto:", $url ) ) || ( eregi( "http://", $url ) ) || ( eregi( "https://", $url ) ) || ( eregi( "file://", $url ) ) || ( eregi( "ftp://", $url ) ) )
                            {
                                $link = $url;
                            }
                            else
                            {
                                $link = ZAR_URL . "/" . $url;
                            }
                        }
                        else
                        {
                            $link = 'javascript:void(0)';
                        }
                        if ( $obj->getVar( 'menu_image' ) )
                        {
                            $image = ZAR_UPLOAD_PATH . '/' . $obj->getVar( 'menu_image' );
                        }
                        else
                        {
                            $image = '';
                        }
                        $_SESSION['user']['menu'][$name]['items'][] = array( 'url' => $link, 'title' => $menu_title, 'image' => $image );
                    }
                }
            }
            $i++;
        }
		return true;
    }
}

?>