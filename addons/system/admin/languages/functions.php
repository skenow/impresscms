<?php

/**
 *
 * @version $Id: functions.php,v 1.2 2007/04/21 09:42:27 catzwolf Exp $
 * @copyright 2006
 */

function getTabs() {
    $rez = array();
    foreach( array( 'Custom', 'Core', 'Addons' ) as $value )
    $rez[$value] = 'index.php?fct=languages&amp;op=translate';
    return $rez;
}

function GetFiles( $path, $prefix = '' ) {
    $files = array();
    if ( !dir( $path ) ) {
        return $files;
    }
    if ( $handle = opendir( $path ) ) {
        while ( false !== ( $file = readdir( $handle ) ) ) {
            if ( $file != "." && $file != ".." ) {
                if ( is_dir( $file ) ) {
                    $files = array_merge( $files, GetFiles( $path . "/$file", "$file/" ) );
                } else {
                    if ( strtolower( substr( $file, strlen( $file )-4 ) ) == ".php" )
                        $files[] = $prefix . $file;
                }
            }
        }
        closedir( $handle );
    }
    return $files;
}

function GetLangData( $file, $url = null ) {
    if ( is_dir( $file ) || !file_exists( $file ) ) {
        return array();
    }

    $content = array();
    foreach( file( $file ) as $key => $value ) {
        $value = trim( $value );
        if ( substr( strtolower( $value ), 0, 7 ) == 'define(' ) {
            $value = trim( substr( $value, 7, -2 ) );
            $delim = substr( $value, 0, 1 );
            $i = 0;
            while ( true ) {
                $i = strpos( $value, $delim, $i + 1 );
                if ( substr( $value, $i-1, 1 ) != "\\" ) break;
            }
            $name = substr( $value, 1, $i-1 );
            $value = trim( substr( $value, strpos( $value, ',', $i ) + 1 ) );
            $delim = substr( $value, 0, 1 );
            $i = 0;
            while ( true ) {
                $i = strpos( $value, $delim, $i + 1 );
                if ( substr( $value, $i-1, 1 ) != "\\" ) break;
            }
            $value = substr( $value, 1, $i-1 );
            $content[] = array( 'key' => $name,
                'text' => stripslashes( $value ),
                'op' => '<a href="' . $url . '&amp;item=' . $name . '">' . zarilia_img_show( 'edit', _EDIT, 'middle' ) . '</a>'
                );
        }
    }
    return $content;
}

function GetLanguages() {
    $files = array();
    $path = ZAR_ROOT_PATH . '/language/';
    if ( $handle = opendir( $path ) ) {
        while ( false !== ( $file = readdir( $handle ) ) ) {
            if ( $file != "." && $file != ".." && $file != "CVS" ) {
                if ( is_dir( $path . '/' . $file ) ) $files[] = $file;
            }
        }
    }
    return $files;
}

function ParseList( &$form, $path, $file, $string ) {
    foreach ( GetLanguages() as $name )
    if ( file_exists( "$path/$name/$file" ) ) {
        $found = false;
        foreach( GetLangData( "$path/$name/$file", "" ) as $item )
        if ( $item['key'] == $string ) {
            $form->addElement( new ZariliaFormTextArea( $name, "language_$name", $item['text'], $rows = 5, $cols = 65 ), true );
            $found = true;
            break;
        }
        if ( !$found )
            $form->addElement( new ZariliaFormTextArea( $name, "language_$name", "", $rows = 5, $cols = 65 ), true );
    } else {
        $form->addElement( new ZariliaFormTextArea( $name, "language_$name", "", $rows = 5, $cols = 65 ), true );
    }
}

/**
 * SaveVar()
 *
 * @param  $file
 * @param  $string
 * @param  $value
 * @return
 */
function SaveVar( $file, $string, $value ) {
    if ( !is_readable( $file ) ) {
        echo "not readable";
        return false;
    }
    chmod( $file, 0777 );
    if ( !is_writable( $file ) ) {
        return false;
    }

    $data = GetLangData( $file );
    $found = false;
    foreach( $data as $key => $item )
    if ( $item['key'] == $string ) {
        $data[$key]['text'] = $value;
        $found = true;
        break;
    }
    if ( !$found ) {
        $data[] = array( 'key' => $string, 'text' => $value );
    }
    if ( !$handle = fopen( $file, 'r+' ) ) {
        return false;
    }
    flock( $handle, LOCK_EX ) or die( "Error! can't lock!" );
    ftruncate( $handle, 0 );
    fwrite( $handle, '<' );
    fwrite( $handle, '?php' );
    fwrite( $handle, "\r\n" );
    fwrite( $handle, '// This is Zarilia language file.' );
    fwrite( $handle, "\r\n" );
    fwrite( $handle, '// This file was modified generated by Zarilia on ' );
    fwrite( $handle, date( "r" ) );
    fwrite( $handle, "\r\n\r\n" );
    foreach( $data as $item )
    fwrite( $handle, 'define("' . $item['key'] . '","' . $item['text'] . '");' . "\r\n" );
    fwrite( $handle, '?' );
    fwrite( $handle, '>' );
    flock( $handle, LOCK_UN ) or die( 'Error! can\'t unlock!' );
    fclose( $handle );
    return true;
}

function SaveVars( $string, $path, $file ) {
    global $tblColors, $op;
    foreach ( GetLanguages() as $name )
    if ( !SaveVar( "$path/$name/$file", $string, zarilia_cleanRequestVars( $_REQUEST, "language_$name", '', XOBJ_DTYPE_TXTBOX ) ) ) {
        include_once ZAR_ROOT_PATH . "/class/zariliaformloader.php";
        zarilia_admin_menu( _MD_AM_TRANSLATING, '', $op );
        $form = new ZariliaSimpleForm( _MD_AM_CANTSAVE, 'mstring', ZAR_URL . '/addons/system/index.php', 'post' );
        foreach ( $_REQUEST as $key => $value )
        $form->addElement( new ZariliaFormHidden( $key, $value ), false );
        $button_tray = new ZariliaFormElementTray( '', '' );
        $button_tray->addElement( new ZariliaFormButton( '', 'cancel', _CANCEL, 'button', 'onClick="history.go(-1);return true;"' ) );
        $button_tray->addElement( new ZariliaFormButton( '', 'submit', _MD_AM_RETRY, 'submit' ), true );
        $form->addElement( $button_tray );
        $form->display();
        zarilia_cp_footer();
        exit();
    }
}

?>