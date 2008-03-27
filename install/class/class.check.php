<?php
/**
 * CheckIfDependsOn
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2006
 * @version $Id: class.check.php,v 1.2 2007/04/21 09:44:36 catzwolf Exp $
 * @access public
 */
class CheckIfDependsOn {
    var $was_errors = false;

    /**
     * CheckIfDependsOn::check_version()
     *
     * @param mixed $current
     * @param mixed $needed
     * @param mixed $name
     * @param string $comment
     * @return
     */
    function check_version( $current, $needed, $name, $comment = '' ) {
        $rez = version_compare( $needed, $current ) < 0;
        if ( $comment == '' ) {
            if ( !$rez ) $this->was_errors = true;
        }
        return $this->generateMsg( $rez, '<b>' . $name . ' ' . $current . '</b> ( ' . _INSTALL_L174 . ' ' . $needed . '+' . ( ( $comment != '' )?( '; ' . $comment ):'' ) . ' )', ( $rez?_INSTALL_L175:_INSTALL_L176 ) );
    }

    /**
     * CheckIfDependsOn::check_extention()
     *
     * @param mixed $name
     * @param string $comment
     * @return
     */
    function check_extention( $name, $comment = '' ) {
        $rez = extension_loaded( $name );
        if ( $comment == '' ) {
            if ( !$rez ) $this->was_errors = true;
        }
        return $this->generateMsg( $rez, '<b>' . $name . '</b> ' . ' ' . ( ( $comment != '' )?( ' (' . $comment . ')' ):'' ), ( $rez ?_INSTALL_L175:_INSTALL_L176 ) );
    }

    /**
     * CheckIfDependsOn::check_extention()
     *
     * @param mixed $name
     * @param string $comment
     * @return
     */
    function check_function( $name, $comment = '' ) {
        $rez = function_exists( $name );
        if ( $comment == '' ) {
            if ( !$rez ) $this->was_errors = true;
        }
        return $this->generateMsg( $rez, '<b>' . $name . '</b> ' . ( ( $comment != '' )?( ' (' . $comment . ')' ):'' ), ( $rez ? _INSTALL_L175:_INSTALL_L176 ) );
    }

    /**
     * CheckIfDependsOn::check_extention()
     *
     * @param mixed $name
     * @param string $comment
     * @return
     */
    function check_ini( $name, $setting, $recomend, $comment = '' ) {
        $rez = $this->php_ini( $setting, $recomend );
        if ( $comment == '' ) {
            if ( !$rez ) $this->was_errors = true;
        }
        $value = ( $rez == $recomend ) ? 1 : 0;
        $recomended = ( $recomend == 0 ) ? 'OFF' : 'ON';
        $output = ( $rez == 0 ) ? 'OFF' : 'ON';
        return $this->generateMsg( $value, "<b>$name</b> ( Recommended: <b>$recomended</b> )", $output );
    }

    function php_ini( $val ) {
        $ret = ( ini_get( $val ) == '1' ? 1 : 0 );
        return $ret;
    }

    /**
     * CheckIfDependsOn::check_iswritable()
     *
     * @param mixed $name
     * @param string $type
     * @return
     */
    function check_iswritable( $name, $type = 'folder' ) {
        $rez = is_writable( $name );
        if ( !$rez ) $this->was_errors = true;
        $cnt = '';
        if ( !is_dir( $name ) ) {
            if ( !$rez ) {
                $cnt = _INSTALL_L83;
            } else {
                $cnt = _INSTALL_L84;
            }
        } else {
            if ( !$rez ) {
                $cnt = _INSTALL_L85;
            } else {
                $cnt = _INSTALL_L86;
            }
        }
        return $this->generateMsg( $rez, sprintf( $cnt, $name ) );
    }

    /**
     * CheckIfDependsOn::generateMsg()
     *
     * @param mixed $value
     * @param mixed $text
     * @return
     */
    function generateMsg( $value, $text, $avail = '' ) {
        $ret = '<tr><td align="right">';
        $ret .= ( $value == 1 ) ? _OKIMG : _NGIMG;
        $ret .= "&nbsp;</td><td align=\"left\">\n";
        $ret .= $text;
        $ret .= "</td><td>$avail</td></tr>";
        return $ret;
    }
}

?>