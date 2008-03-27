<?php
// $Id: addonsadmin.php,v 1.4 2007/04/21 09:41:51 catzwolf Exp $
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
if ( !is_object( $zariliaUser ) || !is_object( $zariliaAddon ) || !$zariliaUser->isAdmin( $zariliaAddon->getVar( 'mid' ) ) ) {
    exit( "Access Denied" );
}

function zarilia_addon_configs( $addon ) {
    $configs = $addon->getInfo( 'config' );
    if ( $configs != false ) {
        if ( $addon->getVar( 'hascomments' ) != 0 ) {
            require_once( ZAR_ROOT_PATH . '/include/comment_constants.php' );
            array_push( $configs, array( 'name' => 'com_rule', 'title' => '_CM_COMRULES', 'description' => '', 'formtype' => 'select', 'valuetype' => 'int', 'default' => 1, 'options' => array( '_CM_COMNOCOM' => ZAR_COMMENT_APPROVENONE, '_CM_COMAPPROVEALL' => ZAR_COMMENT_APPROVEALL, '_CM_COMAPPROVEUSER' => ZAR_COMMENT_APPROVEUSER, '_CM_COMAPPROVEADMIN' => ZAR_COMMENT_APPROVEADMIN ) ) );
            array_push( $configs, array( 'name' => 'com_anonpost', 'title' => '_CM_COMANONPOST', 'description' => '', 'formtype' => 'yesno', 'valuetype' => 'int', 'default' => 0 ) );
        }
    } else {
        if ( $addon->getVar( 'hascomments' ) != 0 ) {
            $configs = array();
            require_once( ZAR_ROOT_PATH . '/include/comment_constants.php' );
            $configs[] = array( 'name' => 'com_rule', 'title' => '_CM_COMRULES', 'description' => '', 'formtype' => 'select', 'valuetype' => 'int', 'default' => 1, 'options' => array( '_CM_COMNOCOM' => ZAR_COMMENT_APPROVENONE, '_CM_COMAPPROVEALL' => ZAR_COMMENT_APPROVEALL, '_CM_COMAPPROVEUSER' => ZAR_COMMENT_APPROVEUSER, '_CM_COMAPPROVEADMIN' => ZAR_COMMENT_APPROVEADMIN ) );
            $configs[] = array( 'name' => 'com_anonpost', 'title' => '_CM_COMANONPOST', 'description' => '', 'formtype' => 'yesno', 'valuetype' => 'int', 'default' => 0 );
        }
    }
    // RMV-NOTIFY
    if ( $addon->getVar( 'hasnotification' ) != 0 ) {
        if ( empty( $configs ) ) {
            $configs = array();
        }
        // Main notification options
        require_once ZAR_ROOT_PATH . '/include/notification_constants.php';
        require_once ZAR_ROOT_PATH . '/include/notification_functions.php';
        $options = array();
        $options['_NOT_CONFIG_DISABLE'] = ZAR_NOTIFICATION_DISABLE;
        $options['_NOT_CONFIG_ENABLEBLOCK'] = ZAR_NOTIFICATION_ENABLEBLOCK;
        $options['_NOT_CONFIG_ENABLEINLINE'] = ZAR_NOTIFICATION_ENABLEINLINE;
        $options['_NOT_CONFIG_ENABLEBOTH'] = ZAR_NOTIFICATION_ENABLEBOTH;
        $configs[] = array ( 'name' => 'notification_enabled', 'title' => '_NOT_CONFIG_ENABLE', 'description' => '_NOT_CONFIG_ENABLEDSC', 'formtype' => 'select', 'valuetype' => 'int', 'default' => ZAR_NOTIFICATION_ENABLEBOTH, 'options' => $options );
        // Event-specific notification options
        // FIXME: doesn't work when update addon... can't read back the array of options properly...  " changing to &quot;
        $options = array();
        $categories = &notificationCategoryInfo( '', $addon->getVar( 'mid' ) );
        foreach ( $categories as $category ) {
            $events = &notificationEvents ( $category['name'], false, $addon->getVar( 'mid' ) );
            foreach ( $events as $event ) {
                if ( !empty( $event['invisible'] ) ) {
                    continue;
                }
                $option_name = $category['title'] . ' : ' . $event['title'];
                $option_value = $category['name'] . '-' . $event['name'];
                $options[$option_name] = $option_value;
            }
        }
        $configs[] = array ( 'name' => 'notification_events', 'title' => '_NOT_CONFIG_EVENTS', 'description' => '_NOT_CONFIG_EVENTSDSC', 'formtype' => 'select_multi', 'valuetype' => 'array', 'default' => array_values( $options ), 'options' => $options );
    }
    return $configs;
}

function zarilia_addon_install( $dirname ) {
    global $zariliaUser, $zariliaConfig, $zariliaOption, $menu_handler, $ZariliaSettings, $cpConfig;

    $db = &ZariliaDatabaseFactory::getDatabaseConnection();

    $dirname = trim( $dirname );
    $addon_handler = &zarilia_gethandler( 'addon' );
    $reservedTables = array( 'avatar', 'avatar_users_link', 'block_addon_link', 'comments', 'config', 'configcategory', 'configoption', 'image', 'imagebody', 'imagecategory', 'imgset', 'imgset_tplset_link', 'imgsetimg', 'groups', 'groups_users_link', 'group_permission', 'online', 'ranks', 'session', 'smiles', 'users', 'newblocks', 'addons', 'tplfile', 'tplset', 'tplsource' );
    $val = $addon_handler->getCount( new Criteria( 'dirname', $dirname ) );

    if ( $val == 0 ) {
        $addon = &$addon_handler->create();
        $addon->loadInfoAsVar( $dirname );
        $addon->setVar( 'weight', 1 );
        $sqlfile = &$addon->getInfo( 'sqlfile' );
        if ( $sqlfile != false && is_array( $sqlfile ) ) {
            $sql_file_path = ZAR_ROOT_PATH . "/addons/" . $dirname . "/" . $sqlfile[ZAR_DB_TYPE];
            if ( !file_exists( $sql_file_path ) ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, "SQL file not found at <b>$sql_file_path</b>" );
            } else {
                require_once ZAR_ROOT_PATH . '/class/database/sqlutility.php';
                $sql_query = fread( fopen( $sql_file_path, 'r' ), filesize( $sql_file_path ) );
                $sql_query = trim( $sql_query );
                SqlUtility::splitMySqlFile( $pieces, $sql_query );
                $created_tables = array();
				require_once ZAR_ROOT_PATH.'/class/cache/settings.class.php';
				$zariliaSettings = &ZariliaSettings::getInstance();
				if (!($globalstables = $addon->getInfo( 'globaltables' ))) $globalstables = array();
				if (!($multisitetables = $addon->getInfo( 'multisitetables' ))) $multisitetables = array();				
                foreach ( $pieces as $piece ) {
                    // [0] contains the prefixed query
                    // [4] contains unprefixed table name
                    $prefixed_query = SqlUtility::prefixQuery( $piece, $db );
                    if ( !$prefixed_query ) {
                        $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, "<b>$piece</b> is not a valid SQL!" );
                        break;
                    }
                    // check if the table name is reserved
                    if ( !in_array( $prefixed_query[4], $reservedTables ) ) {
						if (in_array($prefixed_query[4], $globalstables )) {
							$zariliaSettings->write($zariliaOption['globalconfig'], 'tables', $prefixed_query[4], 0);
//							$cpConfig[$prefixed_query[4]] = 0;
						} elseif (in_array($prefixed_query[4], $multisitetables )) {
							$zariliaSettings->write($zariliaOption['globalconfig'], 'tables', $prefixed_query[4], 1);
//							$cpConfig[$prefixed_query[4]] = 1;
						}
						$prefixed_query = SqlUtility::prefixQuery( $piece, $db );
                        if ( !$db->Execute( $prefixed_query[0] ) ) {
                            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, $prefixed_query[0], __FILE__, __LINE__ );
                            break;
                        } else {
                            if ( !in_array( $prefixed_query[4], $created_tables ) ) {
                                $created_tables[] = $prefixed_query[4];
                            }
                        }
                    } else {
                        // the table name is reserved, so halt the installation
                        $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, '<b>' . $prefixed_query[4] . '</b> is a reserved table!' );
                        break;
                    }
                }
            }
        }
        /*
		*  if no error, save the addon info and blocks info associated with it
		*/
        if ( !$GLOBALS['zariliaLogger']->getSysErrorCount() ) {
            $rez = $addon_handler->insert( $addon );
            if ( !$rez ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, 'Could not insert <b>' . $addon->getVar( 'name' ) . '</b> to database.' );
                foreach ( $created_tables as $ct ) {
                    $db->Execute( "DROP TABLE " . $db->prefix( $ct ) );
                }
                unset( $addon );
                unset( $created_tables );
                unset( $errs );
                unset( $msgs );
                /**
                 * display error and die()
                 */
                $GLOBALS['zariliaLogger']->sysRender();
            } else {
                $newmid = $addon->getVar( 'mid' );
                unset( $created_tables );
                require_once ZAR_ROOT_PATH . '/class/template.php';

                $tplfile_handler = &zarilia_gethandler( 'tplfile' );
                $templates = $addon->getInfo( 'templates' );
                if ( $templates != false ) {
                    foreach ( $templates as $tpl ) {
                        $tplfile = &$tplfile_handler->create();
                        $tpldata = &zarilia_addon_gettemplate( $dirname, $tpl['file'] );
                        $tplfile->setVar( 'tpl_source', $tpldata, true );
                        $tplfile->setVar( 'tpl_refid', $newmid );
                        $tplfile->setVar( 'tpl_tplset', 'default' );
                        $tplfile->setVar( 'tpl_file', $tpl['file'] );
                        $tplfile->setVar( 'tpl_desc', $tpl['description'], true );
                        $tplfile->setVar( 'tpl_addon', $dirname );
                        $tplfile->setVar( 'tpl_lastmodified', time() );
                        $tplfile->setVar( 'tpl_lastimported', 0 );
                        $tplfile->setVar( 'tpl_type', 'addon' );
                        if ( !$tplfile_handler->insert( $tplfile ) ) {
                            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, 'ERROR: Could not insert template <b>' . $tpl['file'] . '</b> to the database.' );
                        } else {
                            $newtplid = $tplfile->getVar( 'tpl_id' );
                            // generate compiled file
                            if ( !zarilia_template_touch( $newtplid ) ) {
                                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, 'ERROR: Failed compiling template <b>' . $tpl['file'] . '</b>.' );
                            }
                        }
                        unset( $tpldata );
                    }
                }

                zarilia_template_clear_addon_cache( $newmid );
                $blocks = $addon->getInfo( 'blocks' );
                if ( $blocks != false ) {
                    foreach ( $blocks as $blockkey => $block ) {
                        // break the loop if missing block config
                        if ( !isset( $block['file'] ) || !isset( $block['show_func'] ) ) {
                            break;
                        }
                        $options = '';
                        if ( !empty( $block['options'] ) ) {
                            $options = trim( $block['options'] );
                        }
                        $newbid = $db->genId( $db->prefix( 'newblocks' ) . '_bid_seq' );
                        $edit_func = isset( $block['edit_func'] ) ? trim( $block['edit_func'] ) : '';
                        $template = '';
                        if ( ( isset( $block['template'] ) && trim( $block['template'] ) != '' ) ) {
                            $content = &zarilia_addon_gettemplate( $dirname, $block['template'], true );
                        }
                        if ( !$content ) {
                            $content = '';
                        } else {
                            $template = trim( $block['template'] );
                        }

                        $block_name = addslashes( trim( $block['name'] ) );
                        $block_description = addslashes( trim( $block['description'] ) );
                        $block_liveupdate = ( isset( $block['liveupdate'] )?$block['liveupdate']:false )?1:0;
                        $sql = "INSERT INTO " . $db->prefix( 'newblocks' ) . "
							( bid, mid, func_num, options, name, title, content, side, weight, block_type, c_type, isactive, dirname, func_file, show_func, edit_func, template, bcachetime, last_modified, description, liveupdate )
								VALUES
							( $newbid, $newmid, " . intval( $blockkey ) . ", '$options', '" . $block_name . "','" . $block_name . "', '', 9, 0, 'M', 'H', 1, '" . addslashes( $dirname ) . "', '" . addslashes( trim( $block['file'] ) ) . "', '" . addslashes( trim( $block['show_func'] ) ) . "', '" . addslashes( $edit_func ) . "', '" . $template . "', 0, " . time() . ", '" . $block_description . "', $block_liveupdate )";
                        if ( !$db->Execute( $sql ) ) {
                            $GLOBALS['zariliaLoger']->setSysError( E_USER_ERROR, 'ERROR: Could not add block <b>' . $block['name'] . '</b> to the database! Database error: <b>' . $db->error() . '</b>' );
                        } else {
                            if ( empty( $newbid ) ) {
                                $newbid = $db->Insert_ID();
                            }
                            $sql = 'INSERT INTO ' . $db->prefix( 'block_addon_link' ) . ' (block_id, addon_id) VALUES (' . $newbid . ', -1)';
                            if (!$db->Execute( $sql )) {
							    $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, 'ERROR: Could not execute query <b>' . $sql . '</b>.' );
							}
                            if ( $template != '' ) {
                                $tplfile = &$tplfile_handler->create();
                                $tplfile->setVar( 'tpl_refid', $newbid );
                                $tplfile->setVar( 'tpl_source', $content, true );
                                $tplfile->setVar( 'tpl_tplset', 'default' );
                                $tplfile->setVar( 'tpl_file', $block['template'] );
                                $tplfile->setVar( 'tpl_addon', $dirname );
                                $tplfile->setVar( 'tpl_type', 'block' );
                                $tplfile->setVar( 'tpl_desc', $block['description'], true );
                                $tplfile->setVar( 'tpl_lastimported', 0 );
                                $tplfile->setVar( 'tpl_lastmodified', time() );
                                if ( !$tplfile_handler->insert( $tplfile ) ) {
                                    $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, 'ERROR: Could not insert template <b>' . $block['template'] . '</b> to the database.' );
                                } else {
                                    $newtplid = $tplfile->getVar( 'tpl_id' );
                                    // generate compiled file
                                    require_once ZAR_ROOT_PATH . '/class/template.php';
                                    if ( !zarilia_template_touch( $newtplid ) ) {
                                        $GLOBALS['zariliaLoger']->setSysError( E_USER_ERROR, 'ERROR: Failed compiling template <b>' . $block['template'] . '</b>.' );
                                    }
                                }
                            }
                        }
                        unset( $content );
                    }
                    unset( $blocks );
                }

                $configs = zarilia_addon_configs( $addon );
                if ( $configs != false ) {
                    $config_handler = &zarilia_gethandler( 'config' );
                    $order = 0;
                    foreach ( $configs as $config ) {
                        $confobj = &$config_handler->createConfig();
                        $confobj->setVar( 'conf_modid', $newmid );
                        $confobj->setVar( 'conf_catid', 0 );
                        $confobj->setVar( 'conf_name', $config['name'] );
                        $confobj->setVar( 'conf_title', $config['title'], true );
                        $confobj->setVar( 'conf_desc', $config['description'], true );
                        $confobj->setVar( 'conf_formtype', $config['formtype'] );
                        $confobj->setVar( 'conf_valuetype', $config['valuetype'] );
                        $confobj->setConfValueForInput( $config['default'], true );
                        $confobj->setVar( 'conf_order', $order );
                        $confop_msgs = '';
                        if ( isset( $config['options'] ) && is_array( $config['options'] ) ) {
                            foreach ( $config['options'] as $key => $value ) {
                                $confop = &$config_handler->createConfigOption();
                                $confop->setVar( 'confop_name', $key, true );
                                $confop->setVar( 'confop_value', $value, true );
                                $confobj->setConfOptions( $confop );
                                unset( $confop );
                            }
                        }
                        $order++;
                        if ( !$config_handler->insertConfig( $confobj ) ) {
                            $GLOBALS['zariliaLoger']->setSysError( E_USER_ERROR, 'ERROR: Could not insert config <b>' . $config['name'] . '</b> to the database.' );
                        }
                        unset( $confobj );
                    }
                    unset( $configs );
                }
            }

            $groups = &$zariliaUser->getGroups();
            require_once ZAR_ROOT_PATH . '/class/zariliablock.php';
            $blocks = &ZariliaBlock::getByAddon( $newmid, false );
            $msgs[] = 'Setting group rights...';
            $gperm_handler = &zarilia_gethandler( 'groupperm' );
            foreach ( $groups as $mygroup ) {
                if ( $gperm_handler->checkRight( 'addon_admin', 0, $mygroup ) ) {
                    $mperm = &$gperm_handler->create();
                    $mperm->setVar( 'gperm_groupid', $mygroup );
                    $mperm->setVar( 'gperm_itemid', $newmid );
                    $mperm->setVar( 'gperm_name', 'addon_admin' );
                    $mperm->setVar( 'gperm_modid', 1 );
                    if ( !$gperm_handler->insert( $mperm ) ) {
                        $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, 'ERROR: Could not add admin access right for Group ID <b>' . $mygroup . '</b>' );
                    }
                    unset( $mperm );
                }
                $mperm = &$gperm_handler->create();
                $mperm->setVar( 'gperm_groupid', $mygroup );
                $mperm->setVar( 'gperm_itemid', $newmid );
                $mperm->setVar( 'gperm_name', 'addon_read' );
                $mperm->setVar( 'gperm_modid', 1 );
                if ( !$gperm_handler->insert( $mperm ) ) {
                    $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, 'ERROR: Could not add user access right for Group ID: <b>' . $mygroup . '</b>' );
                }
                unset( $mperm );
                foreach ( $blocks as $blc ) {
                    $bperm = &$gperm_handler->create();
                    $bperm->setVar( 'gperm_groupid', $mygroup );
                    $bperm->setVar( 'gperm_itemid', $blc );
                    $bperm->setVar( 'gperm_name', 'block_read' );
                    $bperm->setVar( 'gperm_modid', 1 );
                    if ( !$gperm_handler->insert( $bperm ) ) {
                        $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, 'ERROR: Could not add block access right. Block ID: <b>' . $blc . '</b> Group ID: <b>' . $mygroup . '</b>' );
                    }
                    unset( $bperm );
                }
            }
            unset( $blocks );
            unset( $groups );
            // execute addon specific install script if any
            $install_script = $addon->getInfo( 'onInstall' );
            if ( false != $install_script && trim( $install_script ) != '' ) {
                require_once ZAR_ROOT_PATH . '/addons/' . $dirname . '/' . trim( $install_script );
                if ( function_exists( 'zarilia_addon_install_' . $dirname ) ) {
                    $func = 'zarilia_addon_install_' . $dirname;
                    if ( !$func( $addon ) ) {
                        $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, 'Failed to execute ' . $func );
                    }
                }
            }
            unset( $msgs );
            unset( $errs );
            unset( $addon );
        }
    } else {
        $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _MD_AM_FAILINS, "<b>" . $dirname . "</b>" ) . "&nbsp;" . _MD_AM_ERRORSC . "<br />&nbsp;&nbsp;" . sprintf( _MD_AM_ALEXISTS, $dirname ) );
    }
//    $GLOBALS['zariliaLogger']->sysRender();
}

function &zarilia_addon_gettemplate( $dirname, $template, $block = false ) {
    $ret = '';
    global $zariliaConfig;

    $_theme_dir = ( is_dir( $zariliaConfig['theme_set'] ) ) ? $zariliaConfig['theme_set'] : 'default';
    $path = ZAR_THEME_PATH . '/' . $zariliaConfig['theme_set'] . '/addons/' . $dirname . '/';
    if ( $block == true ) {
        $path .= 'blocks/';
    }
    $path .= $template;

    /*
	*
	*/
    if ( !file_exists( $path ) ) {
        unset( $path );
        $path = ZAR_ROOT_PATH . '/addons/' . $dirname . '/templates/';
        if ( $block == true ) {
            $path .= 'blocks/';
        }
        $path .= $template;
    }

    /*
	* fall back on the default theme if no other is found
	*/
    if ( !file_exists( $path ) ) {
        unset( $path );
        $path = ZAR_THEME_PATH . '/default/addons/' . $dirname . '/';
        if ( $block == true ) {
            $path .= 'blocks/';
        }
        $path .= $template;
    }
    $lines = ( file_exists( $path ) ) ? $lines = file( $path ) : $ret;
    if ( is_array( $lines ) ) {
        $count = count( $lines );
        for ( $i = 0; $i < $count; $i++ ) {
            $ret .= str_replace( "\n", "\r\n", str_replace( "\r\n", "\n", $lines[$i] ) );
        }
    }
    return $ret;
}

function zarilia_addon_uninstall( $dirname ) {
    require_once ZAR_ROOT_PATH . '/class/template.php';

    global $zariliaConfig, $zariliaOption;

    $reservedTables = array( 'avatar', 'avatar_users_link', 'block_addon_link', 'comments', 'config', 'configcategory', 'configoption', 'image', 'imagebody', 'imagecategory', 'imgset', 'imgset_tplset_link', 'imgsetimg', 'groups', 'groups_users_link', 'group_permission', 'online', 'ranks', 'session', 'smiles', 'users', 'newblocks', 'addons', 'tplfile', 'tplset', 'tplsource' );

    $db = &ZariliaDatabaseFactory::getdatabaseconnection();
    $addon_handler = &zarilia_gethandler( 'addon' );    

	if (!($addon = &$addon_handler->getByDirname( $dirname ))) {
		$GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf('Can\'t find installed add-on called <b>%s</b>',$dirname));
        return false;
	}

	zarilia_template_clear_addon_cache( $addon->getVar( 'mid' ) );
    if ( $addon->getVar( 'dirname' ) == 'system' ) {
        $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _MD_AM_FAILUNINS, "<b>" . $addon->getVar( 'name' ) . "</b>" ) );
        return false;
        // return "<p>" . sprintf( _MD_AM_FAILUNINS, "<b>" . $addon->getVar( 'name' ) . "</b>" ) . "&nbsp;" . _MD_AM_ERRORSC . "<br /> - " . _MD_AM_SYSNO . "</p>";
    } elseif ( $addon->getVar( 'dirname' ) == $zariliaConfig['startpage'] ) {
        $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _MD_AM_FAILUNINS, "<b>" . $addon->getVar( 'name' ) . "</b>" ) );
        return false;
        // return "<p>" . sprintf( _MD_AM_FAILUNINS, "<b>" . $addon->getVar( 'name' ) . "</b>" ) . "&nbsp;" . _MD_AM_ERRORSC . "<br /> - " . _MD_AM_STRTNO . "</p>";
    } else {
        if ( !$addon_handler->delete( $addon ) ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, 'ERROR: Could not delete ' . $addon->getVar( 'name' ) );
        } else {
            // delete template files
            $tplfile_handler = zarilia_gethandler( 'tplfile' );
            $templates = &$tplfile_handler->find( null, 'addon', $addon->getVar( 'mid' ) );
            $tcount = count( $templates );
            if ( $tcount > 0 ) {
                for ( $i = 0; $i < $tcount; $i++ ) {
                    if ( !$tplfile_handler->delete( $templates[$i] ) ) {
                        $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, 'ERROR: Could not delete template ' . $templates[$i]->getVar( 'tpl_file' ) . ' from the database. Template ID: <b>' . $templates[$i]->getVar( 'tpl_id' ) . '</b>' );
                    }
                }
            }
            unset( $templates );
            /*
			*  delete blocks and block tempalte files
			*/
            $block_arr = &ZariliaBlock::getByAddon( $addon->getVar( 'mid' ) );
            if ( is_array( $block_arr ) ) {
                $bcount = count( $block_arr );
                $msgs[] = 'Deleting block...';
                for ( $i = 0; $i < $bcount; $i++ ) {
                    if ( !$block_arr[$i]->delete() ) {
                        $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, 'ERROR: Could not delete block <b>' . $block_arr[$i]->getVar( 'name' ) . '</b> Block ID: <b>' . $block_arr[$i]->getVar( 'bid' ) . '</b>' );
                    }
                    if ( $block_arr[$i]->getVar( 'template' ) != '' ) {
                        $templates = &$tplfile_handler->find( null, 'block', $block_arr[$i]->getVar( 'bid' ) );
                        $btcount = count( $templates );
                        if ( $btcount > 0 ) {
                            for ( $j = 0; $j < $btcount; $j++ ) {
                                if ( !$tplfile_handler->delete( $templates[$j] ) ) {
                                    $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">ERROR: Could not delete block template ' . $templates[$j]->getVar( 'tpl_file' ) . ' from the database. Template ID: <b>' . $templates[$j]->getVar( 'tpl_id' ) . '</b></span>';
                                }
                            }
                        }
                        unset( $templates );
                    }
                }
            }

            $dropsqlfile = &$addon->getInfo( 'dropsqlfile' );
            if ( $dropsqlfile != false && is_array( $dropsqlfile ) ) {
                $sql_file_path = ZAR_ROOT_PATH . "/addons/" . $dirname . "/" . $dropsqlfile[ZAR_DB_TYPE];
                if ( !file_exists( $sql_file_path ) ) {
                    $errs[] = "SQL file not found at <b>$sql_file_path</b>";
                    $error = true;
                } else {
                    $msgs[] = "SQL file found at <b>$sql_file_path</b>.<br  /> Modifing tables...";
                    require_once ZAR_ROOT_PATH . '/class/database/sqlutility.php';
                    $sql_query = fread( fopen( $sql_file_path, 'r' ), filesize( $sql_file_path ) );
                    $sql_query = trim( $sql_query );
                    SqlUtility::splitMySqlFile( $pieces, $sql_query );
                    $created_tables = array();
                    foreach ( $pieces as $piece ) {
                        // [0] contains the prefixed query
                        // [4] contains unprefixed table name
                        $prefixed_query = SqlUtility::prefixQuery( $piece, $db->prefix() );
                        if ( !$prefixed_query ) {
                            $errs[] = "<b>$piece</b> is not a valid SQL!";
                            $error = true;
                            break;
                        }
                        // not reserved, so try to create one
                        if ( !$db->Execute( $prefixed_query[0] ) ) {
                            $errs[] = $db->error();
                            $error = true;
                            break;
                        } else {
                            if ( !in_array( $prefixed_query[4], $created_tables ) ) {
                                $msgs[] = '&nbsp;&nbsp;Table <b>' . $db->prefix( $prefixed_query[4] ) . '</b> modified/removed.';
                                $created_tables[] = $prefixed_query[4];
                            } else {
                                $msgs[] = '&nbsp;&nbsp;Data removed from table <b>' . $db->prefix( $prefixed_query[4] ) . '</b>.';
                            }
                        }
                    }
                }
            }
            // delete tables used by this addon
            $modtables = $addon->getInfo( 'tables' );
            if ( $modtables != false && is_array( $modtables ) ) {
                $msgs[] = 'Deleting addon tables...';
                foreach ( $modtables as $table ) {
                    // prevent deletion of reserved core tables!
                    if ( !in_array( $table, $reservedTables ) ) {
                        $sql = 'DROP TABLE ' . $db->prefix( $table );
                        if ( !$db->Execute( $sql ) ) {
                            $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">ERROR: Could not drop table <b>' . $db->prefix( $table ) . '</b>.</span>';
                        } else {
                            $msgs[] = '&nbsp;&nbsp;Table <b>' . $db->prefix( $table ) . '</b> dropped.</span>';
                        }
                    } else {
                        $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">ERROR: Not allowed to drop table <b>' . $db->prefix( $table ) . '</b>!</span>';
                    }
                }
            }
	
			// removing info about multisite and global tables
			require_once ZAR_ROOT_PATH.'/class/cache/settings.class.php';
			$zariliaSettings = &ZariliaSettings::getInstance();
			if (!($globalstables = $addon->getInfo( 'globaltables' ))) $globalstables = array();
			if (!($multisitetables = $addon->getInfo( 'multisitetables' ))) $multisitetables = array();		
			$rtables = array_merge($globalstables, $multisitetables);
			foreach ($rtables as $name) {
				$zariliaSettings->remove($zariliaOption['globalconfig'], 'tables', $name);
			}
			unset($globalstables, $multisitetables, $rtables, $name);

            // delete comments if any
            if ( $addon->getVar( 'hascomments' ) != 0 ) {
                $msgs[] = 'Deleting comments...';
                $comment_handler = &zarilia_gethandler( 'comment' );
                if ( !$comment_handler->deleteByAddon( $addon->getVar( 'mid' ) ) ) {
                    $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">ERROR: Could not delete comments</span>';
                } else {
                    $msgs[] = '&nbsp;&nbsp;Comments deleted';
                }
            }
            // RMV-NOTIFY
            // delete notifications if any
            if ( $addon->getVar( 'hasnotification' ) != 0 ) {
                $msgs[] = 'Deleting notifications...';
                if ( !zarilia_notification_deletebyaddon( $addon->getVar( 'mid' ) ) ) {
                    $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">ERROR: Could not delete notifications</span>';
                } else {
                    $msgs[] = '&nbsp;&nbsp;Notifications deleted';
                }
            }
            // delete permissions if any
            $gperm_handler = &zarilia_gethandler( 'groupperm' );
            if ( !$gperm_handler->deleteByAddon( $addon->getVar( 'mid' ) ) ) {
                $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">ERROR: Could not delete group permissions</span>';
            } else {
                $msgs[] = '&nbsp;&nbsp;Group permissions deleted';
            }
            // delete addon config options if any
            if ( $addon->getVar( 'hasconfig' ) != 0 || $addon->getVar( 'hascomments' ) != 0 ) {
                $config_handler = &zarilia_gethandler( 'config' );
                $configs = &$config_handler->getConfigs( new Criteria( 'conf_modid', $addon->getVar( 'mid' ) ) );
                $confcount = count( $configs );
                if ( $confcount > 0 ) {
                    $msgs[] = 'Deleting addon config options...';
                    for ( $i = 0; $i < $confcount; $i++ ) {
                        if ( !$config_handler->deleteConfig( $configs[$i] ) ) {
                            $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">ERROR: Could not delete config data from the database. Config ID: <b>' . $configs[$i]->getvar( 'conf_id' ) . '</b></span>';
                        } else {
                            $msgs[] = '&nbsp;&nbsp;Config data deleted from the database. Config ID: <b>' . $configs[$i]->getVar( 'conf_id' ) . '</b>';
                        }
                    }
                }
            }
            // execute addon specific install script if any
            $uninstall_script = $addon->getInfo( 'onUninstall' );
            if ( false != $uninstall_script && trim( $uninstall_script ) != '' ) {
                require_once ZAR_ROOT_PATH . '/addons/' . $dirname . '/' . trim( $uninstall_script );
                if ( function_exists( 'zarilia_addon_uninstall_' . $dirname ) ) {
                    $func = 'zarilia_addon_uninstall_' . $dirname;
                    if ( !$func( $addon ) ) {
                        $msgs[] = 'Failed to execute <b>' . $func . '</b>';
                    }
                }
            }
        }
    }
}

?>