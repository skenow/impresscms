<?php
/**
 * ZariliaCallback
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2007
 * @version $Id: objectcallback.php,v 1.2 2007/05/05 11:12:12 catzwolf Exp $
 * @access public
 */
class ZariliaCallback extends ZariliaObjectHandler {
    var $_callback;
    var $_obj;
    var $_id;

    /**
     * ZariliaCallback::ZariliaCallback()
     */
    function ZariliaCallback()
    {
    }

    /**
     * ZariliaCallback::getSingleton()
     *
     * @return
     */
    function &getSingleton()
    {
        static $instance;
        if ( !isset( $instance ) ) {
            $instance = new ZariliaCallback();
        }
        return $instance;
    }

    function setCallback()
    {
        $this->_callback = func_get_arg( 0 );
		if (isset($this->_callback->keyName)) {
			if (isset($_REQUEST[$this->_callback->keyName])) {
				$this->_id = zarilia_cleanRequestVars( $_REQUEST, @$this->_callback->keyName, 0 , XOBJ_DTYPE_INT);
			} else {
				$this->_id = zarilia_cleanRequestVars( $_REQUEST, strtoupper(@$this->_callback->keyName), 0 , XOBJ_DTYPE_INT);
			}
		} else {
			$this->_id = 0;
		}		
    }

    function setmenu()
    {
        $this->_menuid = &func_get_arg( 0 );
    }

    function setRedirect()
    {
        $this->_redirect = &func_get_arg( 0 );
    }

    function getId( $isNew = false )
    {
        if ( $isNew == false ) {
            return ( $this->_id > 0 ) ? $this->_id : 0;
        } else {
            return ( $this->_id > 0 ) ? $this->_id : true;
        }
    }

    function help()
    {
        zarilia_cp_header();
        if ( file_exists( ZAR_ROOT_PATH . "/addons/system/admin/" . $GLOBALS['fct'] . "/admin_help.php" ) ) {
            @include ZAR_ROOT_PATH . "/addons/system/admin/" . $GLOBALS['fct'] . "/admin_help.php";
        }
    }

    function about()
    {
        zarilia_cp_header();
        $GLOBALS['menu_handler']->render( $this->_menuid );
        require_once( ZAR_ROOT_PATH . "/class/class.about.php" );
        $zarilia_about = new ZariliaAbout();
        $zarilia_about->display();
    }

    function edit()
    {
        $_function = ( $this->getId() > 0 ) ? 'get': 'create';
        zarilia_cp_header();
        $GLOBALS['menu_handler']->render( $this->_menuid );
        $_obj = &call_user_func( array( $this->_callback, $_function ), $this->getId( true ) );
        if ( is_object( $_obj ) ) {
            $_obj->formEdit();
        } else {
            $GLOBALS['zariliaLogger']->sysRender();
        }
    }

    /*Generic Save*/
    function save()
    {
        $_function = ( $this->getId() > 0 ) ? 'get': 'create';
        $_obj = &call_user_func( array( $this->_callback, $_function ), $this->getId( true ) );
        /**
         * do a correct error check here FIXME!!
         */
        $_obj->setVars( $_REQUEST );
        if ( call_user_func( array( $this->_callback, 'insert' ), $_obj, false ) ) {
            redirect_header( $_SERVER['HTTP_REFERER'], 0, ( $_obj->isNew() ) ? _DBCREATED : _DBUPDATED );
        } else {
            zarilia_cp_header();
            $GLOBALS['menu_handler']->render( $this->_menuid );
            $GLOBALS['zariliaLogger']->sysRender();
        }
    }

    function cloned()
    {
        $_obj = &call_user_func( array( $this->_callback, 'get' ), $this->_id );
        if ( !is_object( $_obj ) ) {
            zarilia_cp_header();
            $GLOBALS['menu_handler']->render( $this->_menuid );
            $GLOBALS['zariliaLogger']->sysRender();
        } else {
            $_obj->setNew();
            if ( call_user_func( array( $this->_callback, 'insert' ), $_obj, false ) ) {
                redirect_header( $_SERVER['HTTP_REFERER'], 0, ( $_obj->isNew() ) ? _DBCREATED : _DBUPDATED );
            } else {
                zarilia_cp_header();
                $GLOBALS['menu_handler']->render( $this->_menuid );
                $GLOBALS['zariliaLogger']->sysRender();
            }
        }
    }

    function delete()
    {
        $_obj = &call_user_func( array( $this->_callback, 'get' ), $this->_id );		
        if ( is_object( $_obj ) ) {
            $ok = zarilia_cleanRequestVars( $_REQUEST, 'ok', 0 );
            switch ( $ok ) {
                case 0:
                default:
                    zarilia_cp_header();
                    $GLOBALS['menu_handler']->render( $this->_menuid );
                    echo $this->_callback->keyName;
                    zarilia_confirm( array( 'op' => 'delete', $this->_callback->keyName => $this->_id, 'ok' => 1 ), $_SERVER['PHP_SELF'], sprintf( _MD_AM_WAYSYWTDTR, $_obj->getVar( $this->_callback->identifierName ) ) );
                    break;
                case 1:
                    if ( call_user_func( array( $this->_callback, 'deleteAvatar' ), $_obj ) ) {
                        echo "shit";
                        redirect_header( $_SERVER['HTTP_REFERER'], 0, _DBUPDATED );
                    }
                    break;
            } // switch
        }
        if ( $GLOBALS['zariliaLogger']->getSysErrorCount() ) {
            zarilia_cp_header();
            $GLOBALS['menu_handler']->render( $this->_menuid );
            $GLOBALS['zariliaLogger']->sysRender();
        }
    }

    function cdall()
    {
        $op = &func_get_arg( 0 );
        $checkbox = zarilia_cleanRequestVars( $_REQUEST, 'checkbox', array() );
        foreach ( array_keys( $checkbox ) as $id ) {
            $_obj = &call_user_func( array( $this->_callback, 'get' ), $id );
            if ( is_object( $_obj ) ) {
                switch ( $op ) {
                    case 'cloneall':
                        $_obj->setNew();
                        call_user_func( array( $this->_callback, 'insert' ), $_obj, false );
                        break;
                    case 'deleteall':
                        call_user_func( array( $this->_callback, 'delete' ), $_obj, false );
                        break;
                    default:
                        break;
                } // switch
            }
        }
        if ( !$GLOBALS['zariliaLogger']->getSysErrorCount() ) {
            redirect_header( $_SERVER['HTTP_REFERER'], 0, _DBUPDATED );
        } else {
            zarilia_cp_header();
            $GLOBALS['menu_handler']->render( $this->_menuid );
            $GLOBALS['zariliaLogger']->sysRender();
        }
    }

    function updateAll()
    {
        $array_keys = &func_get_arg( 0 );
        $checkbox = zarilia_cleanRequestVars( $_REQUEST, 'checkbox', array() );
        $i = 0;
        foreach ( array_keys( $checkbox ) as $id ) {
            $_obj = &call_user_func( array( $this->_callback, 'get' ), $id );
            for( $i = 0; $i < count( $array_keys ); $i++ ) {
                $temp_array = zarilia_cleanRequestVars( $_REQUEST, $array_keys[$i], array() );
                $_obj->setVar( $array_keys[$i], $temp_array[$id] );
            } // for
            call_user_func( array( $this->_callback, 'insert' ), $_obj, false );
        }
        if ( !$GLOBALS['zariliaLogger']->getSysErrorCount() ) {
            redirect_header( $_SERVER['HTTP_REFERER'], 0, _DBUPDATED );
        } else {
            zarilia_cp_header();
            $GLOBALS['menu_handler']->render( $this->_menuid );
            $GLOBALS['zariliaLogger']->sysRender();
        }
    }

    function optimize()
    {
        if ( !call_user_func( array( $this->_callback, 'optimize' ) ) ) {
            zarilia_cp_header();
            $GLOBALS['menu_handler']->render( $this->_menuid );
            $GLOBALS['zariliaLogger']->sysRender();
            zarilia_cp_footer();
            exit();
        }
        redirect_header( $_SERVER['HTTP_REFERER'], 0, _DBOPT );
    }

    function form()
    {
        call_user_func( array( $this->_callback, 'form' ) );
    }
}

?>