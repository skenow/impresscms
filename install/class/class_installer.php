<?php

/**
 *
 * @version $Id: class_installer.php,v 1.1 2007/04/12 14:16:37 catzwolf Exp $
 * @copyright 2007
 */
class ZariliaInstall {
    var $template;
    var $language;
    var $content;
    var $title;
    var $subtitle;
    var $backstep;
    var $nextstep;
    var $reloadstep;
    var $addarray = array();
    var $template_path;
    var $javascript;
    var $onclick;
    var $shownext;

    function ZariliaInstall() {
        // dummy
        /*if ( !file_exists( '../data/settings/site.global.php' ) ) {
            @file_put_contents( '../data/settings/site.global.php', '<?php $cpConfig=array()?>' );
        }*/
    }

    function setArgs() {
        $setArgs = func_get_args();
        if ( func_num_args() == 2 ) {
            $this-> {
                $setArgs[0]} = $setArgs[1];
        }
        unset( $setArgs );
    }

    function getArgs() {
        $getArgs = func_get_args();
        if ( func_num_args() == 1 ) {
            return ( $this-> {
                    $getArgs[0]} != '' ) ? $this-> {
                $getArgs[0]}
            : '';
        }
        unset( $getArgs );
    }

    function setContent( $value ) {
        $this->_content = $value;
    }

    function addVar( $name, $value ) {
        $this->addarray[$name] = $value;
    }

    function addVars( $name, $value ) {
        if ( !isset( $this->addarray[$name] ) || !is_array( $this->addarray[$name] ) ) {
            $this->addarray[$name] = array();
        }
        $this->addarray[$name][] = $value;
    }

    function getVars( $name ) {
        if ( !empty( $this->addarray[$name] ) ) {
            return $this->addarray[$name];
        } else {
            return false;
        }
    }

    function e() {
        $argues = func_get_args();
        if ( func_num_args() > 0 ) {
            if ( !empty( $this->addarray[$argues[0]] ) ) {
                $ret = $this->addarray[$argues[0]];
                if ( ( func_num_args() == 2 ) && is_array( $ret ) ) {
                    $ret = $ret[$argues[1]];
                }
            } else {
                $ret = '';
            }
            echo $ret;
        }
    }

    function render( $template = '' ) {
        global $zariliaOption;

        if ( $template && file_exists( $this->template_path . $template ) ) {
            ob_start();
            include $this->template_path . $template;
            $this->setArgs( 'content', ob_get_contents() );
            ob_end_clean();
        }
        $this->title = ( $this->getArgs( 'title' ) ) ? $this->getArgs( 'title' ) : $GLOBALS['sequence']->getTitle( $this->op );
        $this->subtitle = $this->getArgs( 'subtitle' );
        $this->javascript = $this->getArgs( 'javascript' );
        $this->onclick = $this->getArgs( 'onclick' );

        if ( $this->op != 'langselect' ) {
            $b_back = ( $this->getArgs( 'backstep' ) ) ? $this->getArgs( 'backstep' ) : $GLOBALS['sequence']->getstep( $this->op, 'backstep' );
        }

        if ( $this->op != 'langselect' || $this->shownext != 'stop' ) {
			$b_next = ( $this->nextstep ) ? $this->getArgs( 'nextstep' ) : $GLOBALS['sequence']->getstep( $this->op, 'nextstep' );
		}
        if ( $this->reloadstep == 'forceon' || $this->reloadstep == 'forceoff' ) {
            $b_reload = ( $this->reloadstep == 'forceon' ) ? true : false;
        } else {
            $b_reload = $GLOBALS['sequence']->getstep( $this->op, 'reloadstep' );
        }
        if ( $this->op == 'langselect' ) {
            $b_restart = false;
        } else {
            $b_restart = true;
        }
        // $b_reload = ( $this->getArgs( 'reloadstep' ) ) ? $this->getArgs( 'reloadstep' ) : $GLOBALS['sequence']->getstep( $this->op, 'reloadstep' );
        include $this->template_path . $this->template;
    }

    function error( $template = '' ) {
        global $zariliaOption;
        $this->title = ( $this->getArgs( 'title' ) ) ? $this->getArgs( 'title' ) : $GLOBALS['sequence']->getTitle( $this->op );
        $this->subtitle = $this->getArgs( 'subtitle' );
        $this->content = $this->getArgs( 'content' );
        if ( $this->op != 'langselect' ) {
            $b_back = $this->getArgs( 'backstep' );
        }
        $b_reload = $this->getArgs( 'reloadstep' );
        include $this->template_path . $this->template;
    }

    function getLanguage() {
        global $zariliaOption, $_SESSION;
        if ( !isset( $_SESSION[$zariliaOption['InstallPrefix']] ) ) {
            $_SESSION[$zariliaOption['InstallPrefix']] = array();
            if ( isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ) {
                $accept_langs = explode( ',', $_SERVER['HTTP_ACCEPT_LANGUAGE'] );
                $language_array = array( 'en' => 'english', 'ja' => 'japanese', 'fr' => 'french', 'de' => 'german', 'nl' => 'dutch', 'es' => 'spanish', 'tw' => 'tchinese', 'cn' => 'schinese', 'ro' => 'romanian' );
                foreach ( $accept_langs as $al ) {
                    $al = strtolower( $al );
                    $al_len = strlen( $al );
                    if ( $al_len > 2 ) {
                        if ( preg_match( "/([a-z]{2});q=[0-9.]+$/", $al, $al_match ) ) {
                            $al = $al_match[1];
                        } else {
                            continue;
                        }
                    }
                    if ( isset( $language_array[$al] ) ) {
                        $_SESSION[$zariliaOption['InstallPrefix']]['language'] = $language_array[$al];
                        break;
                    }
                }
                $_SESSION[$zariliaOption['InstallPrefix']]['op'] = 'langselect';
            } else {
                $_SESSION[$zariliaOption['InstallPrefix']]['language'] = 'english';
            }
        } else {
            if ( !empty( $_REQUEST['lang'] ) ) {
                $_SESSION[$zariliaOption['InstallPrefix']]['language'] = $_REQUEST['lang'];
            }
        }

        if ( file_exists( "./language/" . $_SESSION[$zariliaOption['InstallPrefix']]['language'] . "/main.php" ) ) {
            include_once "./language/" . $_SESSION[$zariliaOption['InstallPrefix']]['language'] . "/main.php";
        } elseif ( file_exists( "./language/english/install.php" ) ) {
            include_once "./language/english/install.php";
            $_SESSION[$zariliaOption['InstallPrefix']]['language'] = 'english';
        } else {
            echo 'no language file.';
            exit();
        }
    }

    function restart() {
        global $zariliaOption;
        if ( isset( $_REQUEST['debug'] ) ) {
            switch ( $_REQUEST['debug'] ) {
                case 'restart':
                    session_destroy();
                    if ( isset( $_SESSION[@$zariliaOption['InstallPrefix']] ) ) {
                        unset( $_SESSION[$zariliaOption['InstallPrefix']] );
                    }
                    break;
            }
            // We must restart the session again after dropping it here or we will have to wait for the next sequence cycle for a new language to update!**/
            session_start();
        }
    }

    function doChecks( $op = null ) {
        if ( $op && file_exists( $this->template_path . $op ) ) {
            include $this->template_path . $op;
            $this->setArgs( 'content', ob_get_contents() );
            ob_end_clean();
        }
    }
}

class ZariliaInstallSeq {
    var $_sequence;
    function ZariliaInstallSeq() {
        // dummy
    }

    function addSeqence( $step, $title, $backstep, $nextstep, $reloadstep ) {
        $this->_sequence[$step]['title'] = $title;
        $this->_sequence[$step]['backstep'] = $backstep;
        $this->_sequence[$step]['nextstep'] = $nextstep;
        $this->_sequence[$step]['reloadstep'] = $reloadstep;
    }

    function createSteps() {
        $this->addSeqence( 'langselect', _INSTALL_B_LANGSELECT, null, 'license', true );
        $this->addSeqence( 'license', _INSTALL_B_LICENSE, 'langselect', 'server', false );
        $this->addSeqence( 'server', _INSTALL_B_SERVER, 'license', 'dbform', true );
        $this->addSeqence( 'dbform', _INSTALL_B_DBFORM, 'server', 'dbconfirm', true );
        $this->addSeqence( 'dbconfirm', _INSTALL_B_DBCONFIRM, 'dbform', 'mainfile', true );
        $this->addSeqence( 'mainfile', _INSTALL_B_MAINFILE, 'dbform', 'dbsave', true );
        $this->addSeqence( 'dbsave', _INSTALL_B_DBSAVE, 'dbform', 'createTables', true );
        $this->addSeqence( 'createTables', _INSTALL_B_CREATETABLES, 'dbform', 'siteInit', true );
        $this->addSeqence( 'siteInit', _INSTALL_B_SITEINIT, 'dbform', 'sitedefaultlanguage', true );
        $this->addSeqence( 'sitedefaultlanguage', _INSTALL_B_SITEDEFAULTLANGUAGE, 'siteInit', 'addaddons', true );
        // $this->addSeqence( 'insertlanguage', _INSTALL_B_INSERTLANGUAGE, 'sitedefaultlanguage', 'addaddons', true );
        $this->addSeqence( 'addaddons', _INSTALL_B_ADDADDONS, 'sitedefaultlanguage', 'install_it', true );
        $this->addSeqence( 'install_it', _INSTALL_B_INSTALL_IT, 'addaddons', 'finish', false );
        $this->addSeqence( 'savingdata', _INSTALL_B_INSTALL_IT, 'install_it', 'finish', false );
        $this->addSeqence( 'finish', _INSTALL_B_FINISH, 'install_it', '', false );
    }

    function getTitle( $step ) {
        return ( !empty( $this->_sequence[$step]['title'] ) ) ? $this->_sequence[$step]['title'] : '';
    }

    function getstep( $step, $name ) {
        $b_seq = ( !empty( $this->_sequence[$step][$name] ) ) ? $this->_sequence[$step][$name] : '';
        if ( $name == 'reloadstep' ) {
            return $b_seq;
        }
        $b_name = $this->getButtonName( $step, $name );
        return array( $b_seq, $b_name );
    }

    function getButtonName( $step, $bname = '' ) {
        if ( $this->_sequence[$step][$bname] ) {
            $button = strtoupper( $this->_sequence[$step][$bname] );
            $_string = "_INSTALL_B_$button";
            return @constant( $_string );
        } else {
            return '';
        }
    }
}

?>