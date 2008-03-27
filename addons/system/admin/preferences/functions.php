<?php
if ( !is_object( $zariliaUser ) || !is_object( $zariliaAddon ) || !$zariliaUser->isAdmin( $zariliaAddon->getVar( 'mid' ) ) ) {
    exit( "Access Denied" );
}

function &showaddonfield ( &$form, &$addon_handler, &$config_handler, $type, $title, $name, $value, $valuetype, $conf_id, $conf_value, $description ) {
    global $zariliaUser;
    static $myts = null;
    if ( $myts == null ) $myts = &MyTextSanitizer::getInstance();
    switch ( $type ) {
        case 'editor':
            $ele = new ZariliaFormSelectEditor( $title, $name, $value, $noHtml = false, 1, false, true );
            break;

        case 'editor_multi':
            $ele = new ZariliaFormSelectEditor( $title, $name, $value, $noHtml = false, 5, true, true );
            break;

        case 'zariliatextarea':
        case 'textarea':
        case 'htmltextarea':
            $options['name'] = $name;
            if ( $valuetype == 'array' ) {
                // this is exceptional.. only when value type is array need a smarter way for this
                $options['value'] = ( $conf_value != '' ) ? htmlspecialchars( implode( '|', $value ) ): $name;
            } else {
                $options['value'] = $value;
            }

            $ele = new ZariliaFormEditor( $title, $zariliaUser->getVar( 'editor' ), $options, $nohtml = false, $onfailure = "textarea" );
            break;

        case 'select':
            $ele = new ZariliaFormSelect( $title, $name, $value );
            $options = &$config_handler->getConfigOptions( new Criteria( 'conf_id', $conf_id ) );
            $opcount = count( $options );
            for ( $j = 0; $j < $opcount; $j++ ) {
                $optval = defined( $options[$j]->getVar( 'confop_value' ) ) ? constant( $options[$j]->getVar( 'confop_value' ) ) : $options[$j]->getVar( 'confop_value' );
                $optkey = defined( $options[$j]->getVar( 'confop_name' ) ) ? constant( $options[$j]->getVar( 'confop_name' ) ) : $options[$j]->getVar( 'confop_name' );
                $ele->addOption( $optval, $optkey );
            }
            break;
        case 'select_multi':
            $ele = new ZariliaFormSelect( $title, $name, $value, 5, true );
            $options = &$config_handler->getConfigOptions( new Criteria( 'conf_id', $conf_id ) );
            $opcount = count( $options );
            for ( $j = 0; $j < $opcount; $j++ ) {
                $optval = defined( $options[$j]->getVar( 'confop_value' ) ) ? constant( $options[$j]->getVar( 'confop_value' ) ) : $options[$j]->getVar( 'confop_value' );
                $optkey = defined( $options[$j]->getVar( 'confop_name' ) ) ? constant( $options[$j]->getVar( 'confop_name' ) ) : $options[$j]->getVar( 'confop_name' );
                $ele->addOption( $optval, $optkey );
            }
            break;
        case 'yesno':
            $ele = new ZariliaFormRadioYN( $title, $name, $value, _YES, _NO );
            break;
        case 'group':
            $ele = new ZariliaFormSelectGroup( $title, $name, true, $value, 1, false );
            break;
        case 'group_multi':
            $ele = new ZariliaFormSelectGroup( $title, $name, true, $value, 5, true );
            break;
        // RMV-NOTIFY: added 'user' and 'user_multi'
        case 'user':
            $ele = new ZariliaFormSelectUser( $title, $name, false, $value, 1, false );
            break;
        case 'user_multi':
            $ele = new ZariliaFormSelectUser( $title, $name, false, $value, 5, true );
            break;
        case 'password':
            $ele = new ZariliaFormPassword( $title, $name, 50, 255, htmlspecialchars( $value ) );
            $ele->setMultiChange();
            break;
        case 'textbox':
        default:
            $ele = new ZariliaFormText( $title, $name, 50, 255, htmlspecialchars( $value ) );
            break;
    }
    $ele->setDescription( $description );
    return $ele;
}

function &showfield( &$form, &$addon_handler, &$config_handler, $type, $title, $name, $value, $valuetype, $conf_id, $conf_value, $description ) {
    global $zariliaUser;
    static $myts = null;
    if ( $myts == null ) $myts = &MyTextSanitizer::getInstance();
    switch ( $type ) {
        case 'theme':
        case 'theme_multi':
            $ele = ( $type != 'theme_multi' ) ? new ZariliaFormSelect( $title, $name, $value ) : new ZariliaFormSelect( $title, $name, $value, 5, true );
            $handle = opendir( ZAR_THEME_PATH . '/' );
            $dirlist = array();
            while ( false !== ( $file = readdir( $handle ) ) ) {
                if ( is_dir( ZAR_THEME_PATH . '/' . $file ) && !preg_match( "/^[.]{1,2}$/", $file ) && strtolower( $file ) != 'cvs' ) {
                    $dirlist[$file] = $file;
                }
            }
            closedir( $handle );
            if ( !empty( $dirlist ) ) {
                asort( $dirlist );
                $ele->addOptionArray( $dirlist );
            }
            // $themeset_handler =& zarilia_gethandler('themeset');
            // $themesetlist =& $themeset_handler->getList();
            // asort($themesetlist);
            // foreach ($themesetlist as $key => $name) {
            // $ele->addOption($key, $name.' ('._MD_AM_THEMESET.')');
            // }
            // old theme value is used to determine whether to update cache or not. kind of dirty way
            $form->addElement( new ZariliaFormHidden( '_old_theme', $value ) );
            break;
        case 'tplset':
            $ele = new ZariliaFormSelect( $title, $name, $value );
            $tplset_handler = &zarilia_gethandler( 'tplset' );
            $tplsetlist = &$tplset_handler->getList();
            asort( $tplsetlist );
            foreach ( $tplsetlist as $key => $name ) {
                $ele->addOption( $key, $name );
            }
            // old theme value is used to determine whether to update cache or not. kind of dirty way
            $form->addElement( new ZariliaFormHidden( '_old_theme', $value ) );
            break;
        case 'timezone':
            $ele = new ZariliaFormSelectTimezone( $title, $name, $value );
            break;
        case 'language':
            $ele = new ZariliaFormSelectLang( $title, $name, $value );
            break;
        case 'startpage':
            $ele = new ZariliaFormSelect( $title, $name, $value );
            $criteria = new CriteriaCompo( new Criteria( 'hasmain', 1 ) );
            $criteria->add( new Criteria( 'isactive', 1 ) );
            $addonslist = &$addon_handler->getList( $criteria, true );
            $addonslist[] = _MD_AM_NONE;
            $ele->addOptionArray( $addonslist );
            break;
        case 'group':
            $ele = new ZariliaFormSelectGroup( $title, $name, true, $value, 1, false );
            break;
        case 'group_multi':
            $ele = new ZariliaFormSelectGroup( $title, $name, true, $value, 5, true );
            break;
        // RMV-NOTIFY - added 'user' and 'user_multi'
        case 'user':
            $ele = new ZariliaFormSelectUser( $title, $name, false, $value, 1, false );
            break;
        case 'user_multi':
            $ele = new ZariliaFormSelectUser( $title, $name, false, $value, 5, true );
            break;
        case 'addon_cache':
            $addons = &$addon_handler->getObjects( new Criteria( 'hasmain', 1 ), true );
            $currrent_val = $value;
            $cache_options = array( '0' => _NOCACHE, '30' => sprintf( _SECONDS, 30 ), '60' => _MINUTE, '300' => sprintf( _MINUTES, 5 ), '1800' => sprintf( _MINUTES, 30 ), '3600' => _HOUR, '18000' => sprintf( _HOURS, 5 ), '86400' => _DAY, '259200' => sprintf( _DAYS, 3 ), '604800' => _WEEK );
            if ( count( $addons ) > 0 ) {
                $ele = new ZariliaFormElementTray( $title, '<br /> ' );
                foreach ( array_keys( $addons ) as $mid ) {
                    $c_val = isset( $currrent_val[$mid] ) ? intval( $currrent_val[$mid] ) : null;
                    $selform = new ZariliaFormSelect( $addons[$mid]->getVar( 'name' ) . ": ", $name . "[$mid]", $c_val );
                    $selform->addOptionArray( $cache_options );
                    $ele->addElement( $selform );
                    unset( $selform );
                }
            } else {
                $ele = new ZariliaFormLabel( $title, _MD_AM_NOADDON );
            }
            $ele->setMultiChange();
            break;
        case 'site_cache':
            $ele = new ZariliaFormSelect( $title, $name, $value );
            $ele->addOptionArray( array( '0' => _NOCACHE, '30' => sprintf( _SECONDS, 30 ), '60' => _MINUTE, '300' => sprintf( _MINUTES, 5 ), '1800' => sprintf( _MINUTES, 30 ), '3600' => _HOUR, '18000' => sprintf( _HOURS, 5 ), '86400' => _DAY, '259200' => sprintf( _DAYS, 3 ), '604800' => _WEEK ) );
            $ele->setMultiChange();
            break;
        case 'password':
            $myts = &MyTextSanitizer::getInstance();
            $ele = new ZariliaFormPassword( $title, $name, 50, 255, htmlspecialchars( $value ) );
            $ele->setMultiChange();
            break;
        case 'charset':
            $ele = new ZariliaFormSelectCharset( $title, $name, $value );
            $ele->setMultiChange();
            break;
        case 'mledit':
            include_once ZAR_ROOT_PATH . '/class/mledit/mledit.php';
            $ele = new ZariliaFormSelect( $title, $name, $value );
            $ele->addOptionArray( MultiLanguageEditor::getStyles() );
            $ele->setMultiChange();
            break;
        default:
            $ele = &showaddonfield( $form, $addon_handler, $config_handler, $type, $title, $name, $value, $valuetype, $conf_id, $conf_value, $description );
            break;
    }
    if ( !$ele->getDescription() ) {
        $ele->setDescription( $description );
    }
    return $ele;
}


function &showfield2( &$form, &$addon_handler, &$config_handler, $type, $title, $name, $value, $valuetype, $conf_id, $conf_value, $description ) {
    global $zariliaUser;
    static $myts = null;
    if ( $myts == null ) $myts = &MyTextSanitizer::getInstance();
    switch ( $type ) {
        case 'theme':
        case 'theme_multi':
            $ele = ( $type != 'theme_multi' ) ? new ZariliaFormSelect( $title, $name, $value ) : new ZariliaFormSelect( $title, $name, $value, 5, true );
            $handle = opendir( ZAR_THEME_PATH . '/' );
            $dirlist = array();
            while ( false !== ( $file = readdir( $handle ) ) ) {
                if ( is_dir( ZAR_THEME_PATH . '/' . $file ) && !preg_match( "/^[.]{1,2}$/", $file ) && strtolower( $file ) != 'cvs' ) {
                    $dirlist[$file] = $file;
                }
            }
            closedir( $handle );
            if ( !empty( $dirlist ) ) {
                asort( $dirlist );
                $ele->addOptionArray( $dirlist );
            }
            // $themeset_handler =& zarilia_gethandler('themeset');
            // $themesetlist =& $themeset_handler->getList();
            // asort($themesetlist);
            // foreach ($themesetlist as $key => $name) {
            // $ele->addOption($key, $name.' ('._MD_AM_THEMESET.')');
            // }
            // old theme value is used to determine whether to update cache or not. kind of dirty way
            $form->addElement( new ZariliaFormHidden( '_old_theme', $value ) );
            break;
        case 'tplset':
            $ele = new ZariliaFormSelect( $title, $name, $value );
            $tplset_handler = &zarilia_gethandler( 'tplset' );
            $tplsetlist = &$tplset_handler->getList();
            asort( $tplsetlist );
            foreach ( $tplsetlist as $key => $name ) {
                $ele->addOption( $key, $name );
            }
            // old theme value is used to determine whether to update cache or not. kind of dirty way
            $form->addElement( new ZariliaFormHidden( '_old_theme', $value ) );
            break;
        case 'timezone':
            $ele = new ZariliaFormSelectTimezone( $title, $name, $value );
            break;
        case 'language':
            $ele = new ZariliaFormSelectLang( $title, $name, $value );
            break;
        case 'startpage':
            $ele = new ZariliaFormSelect( $title, $name, $value );
            $criteria = new CriteriaCompo( new Criteria( 'hasmain', 1 ) );
            $criteria->add( new Criteria( 'isactive', 1 ) );
            $addonslist = &$addon_handler->getList( $criteria, true );
            $addonslist[] = _MD_AM_NONE;
            $ele->addOptionArray( $addonslist );
            break;
        case 'group':
            $ele = new ZariliaFormSelectGroup( $title, $name, true, $value, 1, false );
            break;
        case 'group_multi':
            $ele = new ZariliaFormSelectGroup( $title, $name, true, $value, 5, true );
            break;
        // RMV-NOTIFY - added 'user' and 'user_multi'
        case 'user':
            $ele = new ZariliaFormSelectUser( $title, $name, false, $value, 1, false );
            break;
        case 'user_multi':
            $ele = new ZariliaFormSelectUser( $title, $name, false, $value, 5, true );
            break;
        case 'addon_cache':
            $addons = &$addon_handler->getObjects( new Criteria( 'hasmain', 1 ), true );
            $currrent_val = $value;
            $cache_options = array( '0' => _NOCACHE, '30' => sprintf( _SECONDS, 30 ), '60' => _MINUTE, '300' => sprintf( _MINUTES, 5 ), '1800' => sprintf( _MINUTES, 30 ), '3600' => _HOUR, '18000' => sprintf( _HOURS, 5 ), '86400' => _DAY, '259200' => sprintf( _DAYS, 3 ), '604800' => _WEEK );
            if ( count( $addons ) > 0 ) {
                $ele = new ZariliaFormElementTray( $title, '<br /> ' );
                foreach ( array_keys( $addons ) as $mid ) {
                    $c_val = isset( $currrent_val[$mid] ) ? intval( $currrent_val[$mid] ) : null;
                    $selform = new ZariliaFormSelect( $addons[$mid]->getVar( 'name' ) . ": ", $name . "[$mid]", $c_val );
                    $selform->addOptionArray( $cache_options );
                    $ele->addElement( $selform );
                    unset( $selform );
                }
            } else {
                $ele = new ZariliaFormLabel( $title, _MD_AM_NOADDON );
            }
            $ele->setMultiChange();
            break;
        case 'site_cache':
            $ele = new ZariliaFormSelect( $title, $name, $value );
            $ele->addOptionArray( array( '0' => _NOCACHE, '30' => sprintf( _SECONDS, 30 ), '60' => _MINUTE, '300' => sprintf( _MINUTES, 5 ), '1800' => sprintf( _MINUTES, 30 ), '3600' => _HOUR, '18000' => sprintf( _HOURS, 5 ), '86400' => _DAY, '259200' => sprintf( _DAYS, 3 ), '604800' => _WEEK ) );
            $ele->setMultiChange();
            break;
        case 'password':
            $myts = &MyTextSanitizer::getInstance();
            $ele = new ZariliaFormPassword( $title, $name, 50, 255, htmlspecialchars( $value ) );
            $ele->setMultiChange();
            break;
        case 'charset':
            $ele = new ZariliaFormSelectCharset( $title, $name, $value );
            $ele->setMultiChange();
            break;
        case 'mledit':
            include_once ZAR_ROOT_PATH . '/class/mledit/mledit.php';
            $ele = new ZariliaFormSelect( $title, $name, $value );
            $ele->addOptionArray( MultiLanguageEditor::getStyles() );
            $ele->setMultiChange();
            break;
        default:
            $ele = &showaddonfield( $form, $addon_handler, $config_handler, $type, $title, $name, $value, $valuetype, $conf_id, $conf_value, $description );
            break;
    }
    if ( !$ele->getDescription() ) {
        $ele->setDescription( $description );
    }
    return $ele;
}

function getFieldTypes() {
    return array( 'editor', 'editor_multi', 'zariliatextarea', 'htmltextarea', 'textarea', 'select', 'select_multi', 'yesno', 'theme', 'theme_multi', 'tplset', 'timezone', 'language', 'charset', 'startpage', 'group', 'group_multi', 'user', 'user_multi', 'addon_cache', 'site_cache', 'password', 'textbox', 'mledit' );
}

?>