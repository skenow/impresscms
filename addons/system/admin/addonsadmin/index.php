<?php
// $Id: index.php,v 1.5 2007/05/05 11:09:37 catzwolf Exp $
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
require_once ZAR_ROOT_PATH . '/class/zariliablock.php';
require_once ZAR_ROOT_PATH . '/class/class.menubar.php';
require_once ZAR_ROOT_PATH . "/addons/system/admin/addonsadmin/addonsadmin.php";

require_once "admin_menu.php";
$addon_handler = &zarilia_gethandler( 'addon' );
switch ( $op ) {
    case 'help':
        zarilia_cp_header();
        $menu_handler->render( 0 );
        if ( file_exists( ZAR_ROOT_PATH . "/addons/system/admin/" . $fct . "/admin_help.php" ) ) {
            @include ZAR_ROOT_PATH . "/addons/system/admin/" . $fct . "/admin_help.php";
        }
        break;

    case 'about':
        zarilia_cp_header();
        $menu_handler->render( 5 );
        require_once( ZAR_ROOT_PATH . "/class/class.about.php" );
        $zarilia_about = new ZariliaAbout();
        $zarilia_about->display();
        break;

    case 'uploadform':
        include_once ZAR_ROOT_PATH . '/class/zariliaformloader.php';
        zarilia_cp_header();
        $menu_handler->render( 4 );
        $form = new ZariliaThemeForm( _MA_AD_UPLOADADDON, 'addonupload_form', 'index.php' );
        $form->setExtra( 'enctype="multipart/form-data"' );
        $form->addElement( new ZariliaFormFile( _MA_AD_CHOOSEADDON . '<br /><span style="color:#ff0000;">' . _MA_AD_ONLYADDON . '</span>', 'addon_upload' ) );
        $form->addElement( new ZariliaFormHidden( 'op', 'uploadaddon' ) );
        $form->addElement( new ZariliaFormHidden( 'fct', 'addonsadmin' ) );
        $form->addElement( new ZariliaFormButton( '', 'upload_button', _MA_AD_UPLOAD, 'submit' ) );
        $form->display();
        break;

    case 'uploadaddon':
        include_once ZAR_ROOT_PATH . '/class/uploader.php';
        $uploader = new ZariliaMediaUploader( ZAR_ADDONS_PATH, array( 'application/zip', 'application/x-zip', 'application/x-zip-compressed', 'application/octetstream', 'application/x-compress', 'application/x-compressed', 'multipart/x-zip' ), 5000000 );
        for ( $i = 0; $i < count( $_REQUEST['zarilia_upload_file'] ); $i++ ) {
            if ( $uploader->fetchMedia( $_REQUEST['zarilia_upload_file'][$i] ) ) {
                if ( !$uploader->upload() ) {
                    $GLOBALS['zariliaLogger']->setSysError( E_USER_NOTICE, $uploader->getErrors() );
                } else {
                    require_once ZAR_ROOT_PATH . '/class/class.zipfileextract.php';
                    $get_file_dest = $uploader->getSavedFileName();
                    $zip = new ZipExtract( ZAR_ADDONS_PATH . $get_file_dest );
                    $zip->extract_all( ZAR_ADDONS_PATH );
                    unlink( ZAR_ADDONS_PATH . $get_file_dest );
                    zarilia_cp_header();
                    echo "<div>" . _MA_AD_ADDON_UNZIPPED . "</div>";
                    zarilia_cp_footer();
                    exit();
                }
            } else {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_NOTICE, $uploader->getErrors() );
            }
        }
        /*
		* If on error
		*/
        if ( $GLOBALS['zariliaLogger']->getSysErrorCount() ) {
			cp_showErrors( 'Upload Failed', $heading = '', $description = '', $image = '' );
        }
        break;

    case 'delfile':
        $addon = zarilia_cleanRequestVars( $_REQUEST, 'addon', 0 );
        $ok = zarilia_cleanRequestVars( $_REQUEST, 'ok', 0 );
        if ( $ok == 1 ) {
            /*
			* Need switch here to delete or move items below this structure
			*/
            $_dir = ZAR_ADDONS_PATH . DIRECTORY_SEPARATOR . $addon . DIRECTORY_SEPARATOR;
            if ( $addon == 'system' || zarilia_destroy( $_dir ) == false ) {
				cp_showErrors( 'Installer Failed', $heading = '', $description = '', $image = '' );
                zarilia_cp_header();
                $menu_handler->render( $act + 1 );
                $GLOBALS['zariliaLogger']->sysRender( E_USER_ERROR, _MA_AD_RUSUREINSFAILED );
                zarilia_cp_footer();
            } else {
                // zarilia_destroy( $_dir );
                redirect_header( $addonversion['adminpath'] . '&amp;op=list&amp;act=2', 1, _MA_AD_DELFILED );
            }
            break;
        } else {
            zarilia_cp_header();
            $menu_handler->render( $act + 1 );
            zarilia_confirm( array( 'fct' => $fct, 'op' => 'delfile', 'addon' => $addon, 'ok' => 1 ), $addonversion['adminpath'], sprintf( _MA_AD_WAYSYWTDTR, $addon ) );
        }
        break;

    case 'install':
        $ok = zarilia_cleanRequestVars( $_REQUEST, 'ok', 0 );
        $addon = zarilia_cleanRequestVars( $_REQUEST, 'addon', '', XOBJ_DTYPE_TXTBOX );
        if ( $ok ) {
            zarilia_addon_install( $addon );
            if ( !$GLOBALS['zariliaLogger']->getSysErrorCount() ) {
                zarilia_update_interface();
                redirect_header( $addonversion['adminpath'] . '&amp;op=list', 1, _MA_AD_RUSUREINSUPDATED );
            } else {
                zarilia_cp_header();
                $menu_handler->render( 1 );
                $GLOBALS['zariliaLogger']->sysRender( E_USER_ERROR, _MA_AD_RUSUREINSFAILED );
            }
        } else {
            $mod = &$addon_handler->create();
            $mod->loadInfoAsVar( $addon );
            zarilia_cp_header();
            $menu_handler->render( 1 );
            if ( !$mod ) {
                $GLOBALS['zariliaLogger']->sysRender( E_USER_ERROR, _MA_AD_SELECTMNOTEXIST );
                zarilia_cp_footer();
                break;
            }
            zarilia_confirm( array( 'addon' => $addon, 'op' => 'install', 'ok' => 1, 'fct' => 'addonsadmin' ), $addonversion['adminpath'], sprintf( _MA_AD_RUSUREINS, $mod->getVar( 'name' ) ) );
        }
        break;

    case 'uninstall':
        $ok = zarilia_cleanRequestVars( $_REQUEST, 'ok', 0 );
        $addon = zarilia_cleanRequestVars( $_REQUEST, 'addon', '', XOBJ_DTYPE_TXTBOX );
        if ( $ok ) {
            zarilia_addon_uninstall( $addon );
            if ( !$GLOBALS['zariliaLogger']->getSysErrorCount() ) {
                zarilia_update_interface();
                redirect_header( $addonversion['adminpath'] . '&amp;op=list', 1, _MA_AD_USUREUNISTALLUPDATED );
            } else {
                zarilia_cp_header();
                $menu_handler->render( 1 );
                $GLOBALS['zariliaLogger']->sysRender( E_USER_ERROR, _MA_AD_USUREUNISTALLFAILED );
            }
        } else {
            $mod = $addon_handler->getByDirname( $addon );
            zarilia_cp_header();
            $menu_handler->render( 1 );
            if ( !$mod ) {
                $GLOBALS['zariliaLogger']->sysRender( E_USER_ERROR, _MA_AD_SELECTMNOTEXIST );
                zarilia_cp_footer();
                break;
            }
            zarilia_confirm( array( 'addon' => $addon, 'op' => 'uninstall', 'ok' => 1, 'fct' => 'addonsadmin' ), $addonversion['adminpath'], sprintf( _MA_AD_RUSUREUNISTALL, $mod->getVar( 'name' ) ) );
        }
        break;

    case 'update':
        $ok = zarilia_cleanRequestVars( $_REQUEST, 'ok', 0 );
        $dirname = zarilia_cleanRequestVars( $_REQUEST, 'dirname', '', XOBJ_DTYPE_TXTBOX );
        $addon = zarilia_cleanRequestVars( $_REQUEST, 'addon', '', XOBJ_DTYPE_TXTBOX );
        if ( $ok ) {
            include_once ZAR_ROOT_PATH . '/class/template.php';

            $dirname = trim( $dirname );
            $addon = &$addon_handler->getByDirname( $dirname );
            zarilia_template_clear_addon_cache( $addon->getVar( 'mid' ) );

            /*
			*  we dont want to change the addon name set by admin
            */
            $temp_name = $addon->getVar( 'name' );
            $addon->loadInfoAsVar( $dirname );
            $addon->setVar( 'name', $temp_name );

            /*
			* Lets save the addon
			*/
            if ( !$addon_handler->insert( $addon ) ) {
                /*
				* We could not update the addon, lets throw out and big warning sign to panic the user!
				*/
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, 'Could not update ' . $addon->getVar( 'name' ) );
            } else {
                $newmid = $addon_id = $addon->getVar( 'mid' );
                /*
				* lets delete all cached templates
				*/
                $tplfile_handler = &zarilia_gethandler( 'tplfile' );
                $deltpl = &$tplfile_handler->find( 'default', 'addon', $addon_id );
                $delng = array();
                if ( is_array( $deltpl ) ) {
                    $zariliaTempTpl = new ZariliaTpl();
                    // clear cache files
                    $zariliaTempTpl->clear_cache( null, 'mod_' . $dirname );
                    // delete template file entry in db
                    $dcount = count( $deltpl );
                    for ( $i = 0; $i < $dcount; $i++ ) {
                        if ( !$tplfile_handler->delete( $deltpl[$i] ) ) {
                            $delng[] = $deltpl[$i]->getVar( 'tpl_file' );
                        }
                    }
                }
                /*
				* Lets update block templates
				*/
                $templates = $addon->getInfo( 'templates' );
                if ( $templates != false ) {
                    foreach ( $templates as $tpl ) {
                        $tpl['file'] = trim( $tpl['file'] );
                        if ( !in_array( $tpl['file'], $delng ) ) {
                            $tpldata = &zarilia_addon_gettemplate( $dirname, $tpl['file'] );
                            if ( $tpldata == false ) {
                                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, 'ERROR: Could not insert template <b>' . $tpl['file'] . '</b> to the database. File does not exist!!' );
                            } else {
                                $tplfile = &$tplfile_handler->create();
                                $tplfile->setVar( 'tpl_refid', $newmid );
                                $tplfile->setVar( 'tpl_lastimported', 0 );
                                $tplfile->setVar( 'tpl_lastmodified', time() );
                                if ( preg_match( "/\.css$/i", $tpl['file'] ) ) {
                                    $tplfile->setVar( 'tpl_type', 'css' );
                                } else {
                                    $tplfile->setVar( 'tpl_type', 'addon' );
                                }
                                $tplfile->setVar( 'tpl_source', $tpldata, true );
                                $tplfile->setVar( 'tpl_addon', $dirname );
                                $tplfile->setVar( 'tpl_tplset', 'default' );
                                $tplfile->setVar( 'tpl_file', $tpl['file'], true );
                                $tplfile->setVar( 'tpl_desc', $tpl['description'], true );
                                if ( !$tplfile_handler->insert( $tplfile ) ) {
                                    $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, 'ERROR: Could not insert template <b>' . $tpl['file'] . '</b> to the database.' );
                                } else {
                                    $newid = $tplfile->getVar( 'tpl_id' );
                                    if ( $zariliaConfig['template_set'] == 'default' ) {
                                        if ( !zarilia_template_touch( $newid ) ) {
                                            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, 'ERROR: Could not recompile template <b>' . $tpl['file'] . '</b>.' );
                                        }
                                    }
                                }
                                unset( $tpldata );
                            }
                        } else {
                            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, 'ERROR: Could not delete old template <b>' . $tpl['file'] . '</b>. Aborting update of this file.' );
                        }
                    }
                }
                $blocks = $addon->getInfo( 'blocks' );
                if ( $blocks != false ) {
                    $count = count( $blocks );
                    $showfuncs = array();
                    $funcfiles = array();
                    for ( $i = 1; $i <= $count; $i++ ) {
                        if ( isset( $blocks[$i]['show_func'] ) && $blocks[$i]['show_func'] != '' && isset( $blocks[$i]['file'] ) && $blocks[$i]['file'] != '' ) {
                            $editfunc = isset( $blocks[$i]['edit_func'] ) ? $blocks[$i]['edit_func'] : '';
                            $showfuncs[] = $blocks[$i]['show_func'];
                            $funcfiles[] = $blocks[$i]['file'];
                            $template = '';
                            if ( ( isset( $blocks[$i]['template'] ) && trim( $blocks[$i]['template'] ) != '' ) ) {
                                $content = &zarilia_addon_gettemplate( $dirname, $blocks[$i]['template'], true );
                            }
                            if ( !$content || $content == false ) {
                                $content = '';
                                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, 'ERROR: Could not update ' . $fblock['name'] );
                            } else {
                                $template = $blocks[$i]['template'];
                            }
                            $options = '';
                            if ( !empty( $blocks[$i]['options'] ) ) {
                                $options = $blocks[$i]['options'];
                            }
                            $sql = "SELECT bid, name FROM " . $zariliaDB->prefix( 'newblocks' ) . " WHERE mid=" . $addon->getVar( 'mid' ) . " AND func_num=" . $i . " AND show_func='" . addslashes( $blocks[$i]['show_func'] ) . "' AND func_file='" . addslashes( $blocks[$i]['file'] ) . "'";
                            $fresult = $zariliaDB->Execute( $sql );
                            $fcount = 0;
                            while ( $fblock = $fresult->FetchRow() ) {								
                                $fcount++;
                                $sql = "UPDATE " . $zariliaDB->prefix( "newblocks" ) . " SET name='" . addslashes( $blocks[$i]['name'] ) . "', edit_func='" . addslashes( $editfunc ) . "', options='" . addslashes( $options ) . "', content='', template='" . $template . "', last_modified=" . time() . ", description='" . addslashes( $blocks[$i]['description'] ) . "' WHERE bid=" . $fblock['bid'];								
                                $result = $zariliaDB->Execute( $sql );
                                if ( !$result = $zariliaDB->Execute( $sql ) ) {
                                    $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, 'ERROR: Could not update ' . $fblock['name'] );
                                } else {
                                    if ( $template != '' ) {
                                        $tplfile = &$tplfile_handler->find( 'default', 'block', $fblock['bid'] );
                                        if ( count( $tplfile ) == 0 ) {
                                            $tplfile_new = &$tplfile_handler->create();
                                            $tplfile_new->setVar( 'tpl_addon', $dirname );
                                            $tplfile_new->setVar( 'tpl_refid', $fblock['bid'] );
                                            $tplfile_new->setVar( 'tpl_tplset', 'default' );
                                            $tplfile_new->setVar( 'tpl_file', $blocks[$i]['template'], true );
                                            $tplfile_new->setVar( 'tpl_type', 'block' );
                                        } else {
                                            $tplfile_new = $tplfile[0];
                                        }										
                                        $tplfile_new->setVar( 'tpl_source', $content, true );
                                        $tplfile_new->setVar( 'tpl_desc', $blocks[$i]['description'], true );
                                        $tplfile_new->setVar( 'tpl_lastmodified', time() );
                                        $tplfile_new->setVar( 'tpl_lastimported', 0 );
                                        if ( !$tplfile_handler->insert( $tplfile_new ) ) {
                                            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, 'ERROR: Could not update template <b>' . $blocks[$i]['template'] . '</b>.' );
                                        } else {
                                            if ( $zariliaConfig['template_set'] == 'default' ) {
                                                if ( !zarilia_template_touch( $tplfile_new->getVar( 'tpl_id' ) ) ) {
                                                    $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, 'ERROR: Could not recompile template <b>' . $blocks[$i]['template'] . '</b>.' );
                                                }
                                            }
                                        }
                                    }
                                }
                            }

                            if ( $fcount == 0 ) {
                                $newbid = $zariliaDB->genId( $zariliaDB->prefix( 'newblocks' ) . '_bid_seq' );
                                $block_name = addslashes( $blocks[$i]['name'] );
                                $block_title = addslashes( $blocks[$i]['name'] );
                                $sql = "INSERT INTO " . $zariliaDB->prefix( "newblocks" ) . " (bid, mid, func_num, options, name, title, content, side, weight, block_type, isactive, dirname, func_file, show_func, edit_func, template, last_modified, description) VALUES (" . $newbid . ", " . $addon->getVar( 'mid' ) . ", " . $i . ",'" . addslashes( $options ) . "','" . $block_name . "', '" . $block_name . "', '', 9, 0, 'M', 1, '" . addslashes( $dirname ) . "', '" . addslashes( $blocks[$i]['file'] ) . "', '" . addslashes( $blocks[$i]['show_func'] ) . "', '" . addslashes( $editfunc ) . "', '" . $template . "', " . time() . ", '" . $block_title . "' )";
                                $result = $zariliaDB->Execute( $sql );
                                if ( !$result ) {
                                    $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, 'ERROR: Could not create ' . $blocks[$i]['name'] );
                                } else {
                                    if ( empty( $newbid ) ) {
                                        $newbid = $zariliaDB->getInsertId();
                                    }
                                    $groups = &$zariliaUser->getGroups();
                                    $gperm_handler = &zarilia_gethandler( 'groupperm' );
                                    foreach ( $groups as $mygroup ) {
                                        $bperm = &$gperm_handler->create();
                                        $bperm->setVar( 'gperm_groupid', $mygroup );
                                        $bperm->setVar( 'gperm_itemid', $newbid );
                                        $bperm->setVar( 'gperm_name', 'block_read' );
                                        $bperm->setVar( 'gperm_modid', 1 );
                                        if ( !$gperm_handler->insert( $bperm ) ) {
                                            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, 'ERROR: Could not add block access right. Block ID: <b>' . $newbid . '</b> Group ID: <b>' . $mygroup . '</b>' );
                                        }
                                    }
                                    if ( $template != '' ) {
                                        $tplfile = &$tplfile_handler->create();
                                        $tplfile->setVar( 'tpl_addon', $dirname );
                                        $tplfile->setVar( 'tpl_refid', $newbid );
                                        $tplfile->setVar( 'tpl_source', $content, true );
                                        $tplfile->setVar( 'tpl_tplset', 'default' );
                                        $tplfile->setVar( 'tpl_file', $blocks[$i]['template'], true );
                                        $tplfile->setVar( 'tpl_type', 'block' );
                                        $tplfile->setVar( 'tpl_lastimported', 0 );
                                        $tplfile->setVar( 'tpl_lastmodified', time() );
                                        $tplfile->setVar( 'tpl_desc', $blocks[$i]['description'], true );
                                        if ( !$tplfile_handler->insert( $tplfile ) ) {
                                            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, 'ERROR: Could not insert template <b>' . $blocks[$i]['template'] . '</b> to the database.' );
                                        } else {
                                            $newid = $tplfile->getVar( 'tpl_id' );
                                            if ( $zariliaConfig['template_set'] == 'default' ) {
                                                if ( !zarilia_template_touch( $newid ) ) {
                                                    $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, 'ERROR: Template <b>' . $blocks[$i]['template'] . '</b> recompile failed.' );
                                                }
                                            }
                                        }
                                    }
                                    $sql = 'INSERT INTO ' . $zariliaDB->prefix( 'block_addon_link' ) . ' (block_id, addon_id) VALUES (' . $newbid . ', -1)';
                                    $zariliaDB->Execute( $sql );
                                }
                            }
                        }
                    }
                    /*
					* Lets delete unused blocks
					*/
                    $block_arr = ZariliaBlock::getByAddon( $addon->getVar( 'mid' ) );
                    foreach ( $block_arr as $block ) {
                        if ( !in_array( $block->getVar( 'show_func' ), $showfuncs ) || !in_array( $block->getVar( 'func_file' ), $funcfiles ) ) {
                            $sql = sprintf( "DELETE FROM %s WHERE bid = %u", $zariliaDB->prefix( 'newblocks' ), $block->getVar( 'bid' ) );
                            if ( !$zariliaDB->Execute( $sql ) ) {
                                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, 'ERROR: Could not delete block <b>' . $block->getVar( 'name' ) . '</b>. Block ID: <b>' . $block->getVar( 'bid' ) . '</b>' );
                            } else {
                                if ( $block->getVar( 'template' ) != '' ) {
                                    $tplfiles = &$tplfile_handler->find( null, 'block', $block->getVar( 'bid' ) );
                                    if ( is_array( $tplfiles ) ) {
                                        $btcount = count( $tplfiles );
                                        for ( $k = 0; $k < $btcount; $k++ ) {
                                            if ( !$tplfile_handler->delete( $tplfiles[$k] ) ) {
                                                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, 'ERROR: Could not remove deprecated block template. (ID: <b>' . $tplfiles[$k]->getVar( 'tpl_id' ) . '</b>)' );
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                } else {
                    $block_arr = ZariliaBlock::getByAddon( $addon->getVar( 'mid' ) );
                    foreach ( $block_arr as $block ) {
                        $sql = sprintf( "DELETE FROM %s WHERE bid = %u", $zariliaDB->prefix( 'newblocks' ), $block->getVar( 'bid' ) );
                        if ( !$zariliaDB->Execute( $sql ) ) {
                            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, 'ERROR: Could not delete block <b>' . $block->getVar( 'name' ) . '</b>. Block ID: <b>' . $block->getVar( 'bid' ) . '</b>' );
                        } else {
                            if ( $block->getVar( 'template' ) != '' ) {
                                $tplfiles = &$tplfile_handler->find( null, 'block', $block->getVar( 'bid' ) );
                                if ( is_array( $tplfiles ) ) {
                                    $btcount = count( $tplfiles );
                                    for ( $k = 0; $k < $btcount; $k++ ) {
                                        if ( !$tplfile_handler->delete( $tplfiles[$k] ) ) {
                                            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, 'ERROR: Could not remove deprecated block template. (ID: <b>' . $tplfiles[$k]->getVar( 'tpl_id' ) . '</b>)' );
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                /*
				*  first delete all config entries
				*/
                $config_handler = &zarilia_gethandler( 'config' );
                $configs = &$config_handler->getConfigs( new Criteria( 'conf_modid', $addon->getVar( 'mid' ) ) );
                $confcount = count( $configs );
                $config_delng = array();
                if ( $confcount > 0 ) {
                    for ( $i = 0; $i < $confcount; $i++ ) {
                        if ( !$config_handler->deleteConfig( $configs[$i] ) ) {
                            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, 'ERROR: Could not delete config data from the database. Config ID: <b>' . $configs[$i]->getvar( 'conf_id' ) . '</b>' );
                            // save the name of config failed to delete for later use
                            $config_delng[] = $configs[$i]->getvar( 'conf_name' );
                        } else {
                            $config_old[$configs[$i]->getvar( 'conf_name' )]['value'] = $configs[$i]->getvar( 'conf_value', 'N' );
                            $config_old[$configs[$i]->getvar( 'conf_name' )]['formtype'] = $configs[$i]->getvar( 'conf_formtype' );
                            $config_old[$configs[$i]->getvar( 'conf_name' )]['valuetype'] = $configs[$i]->getvar( 'conf_valuetype' );
                        }
                    }
                }
                /*
				*  now reinsert them with the new settings
				*/
                $configs = zarilia_addon_configs( $addon );
                if ( $configs != false ) {
                    $config_handler = &zarilia_gethandler( 'config' );
                    $order = 0;
                    foreach ( $configs as $config ) {
                        // only insert ones that have been deleted previously with success
                        if ( !in_array( $config['name'], $config_delng ) ) {
                            $confobj = &$config_handler->createConfig();
                            $confobj->setVar( 'conf_modid', $newmid );
                            $confobj->setVar( 'conf_catid', 0 );
                            $confobj->setVar( 'conf_name', $config['name'] );
                            $confobj->setVar( 'conf_title', $config['title'], true );
                            $confobj->setVar( 'conf_desc', $config['description'], true );
                            $confobj->setVar( 'conf_formtype', $config['formtype'] );
                            $confobj->setVar( 'conf_valuetype', $config['valuetype'] );
                            if ( isset( $config_old[$config['name']]['value'] ) && $config_old[$config['name']]['formtype'] == $config['formtype'] && $config_old[$config['name']]['valuetype'] == $config['valuetype'] ) {
                                // preserver the old value if any
                                // form type and value type must be the same
                                $confobj->setVar( 'conf_value', $config_old[$config['name']]['value'], true );
                            } else {
                                $confobj->setConfValueForInput( $config['default'], true );
                            }
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
                                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, 'ERROR: Could not insert config <b>' . $config['name'] . '</b> to the database.' );
                            }
                            unset( $confobj );
                        }
                    }
                    unset( $configs );
                }
                // execute addon specific update script if any
                $update_script = $addon->getInfo( 'onUpdate' );
                if ( false != $update_script && trim( $update_script ) != '' ) {
                    $prev_version = $addon->getVar( 'version' );
                    include_once ZAR_ROOT_PATH . '/addons/' . $dirname . '/' . trim( $update_script );
                    if ( function_exists( 'zarilia_addon_update_' . $dirname ) ) {
                        $func = 'zarilia_addon_update_' . $dirname;
                        if ( !$func( $addon, $prev_version ) ) {
                            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, '' );
                            $msgs[] = 'Failed to execute ' . $func;
                        }
                    }
                }
            }
            if ( !$GLOBALS['zariliaLogger']->getSysErrorCount() ) {
                zarilia_update_interface();
                redirect_header( $addonversion['adminpath'] . '&amp;op=list', 1, _MA_AD_USUREUPDUPDATED );
            } else {
                zarilia_cp_header();
                $menu_handler->render( 1 );
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, _MA_AD_USUREUPDFAILED );
                $GLOBALS['zariliaLogger']->sysRender();
            }
        } else {
            $mod = &$addon_handler->getByDirname( $addon );
            zarilia_cp_header();
            $menu_handler->render( 1 );
            if ( !$mod ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, _MA_AD_SELECTMNOTEXIST );
                $GLOBALS['zariliaLogger']->sysRender();
                zarilia_cp_footer();
                break;
            }
            zarilia_confirm( array( 'dirname' => $addon, 'op' => 'update', 'ok' => 1, 'fct' => 'addonsadmin' ), 'index.php', sprintf( _MA_AD_RUSUREUPD, $mod->getVar( 'name' ) ) );
        }
        break;

    case 'updateall':
        include_once ZAR_ROOT_PATH . '/class/template.php';
        $addon_mid = zarilia_cleanRequestVars( $_REQUEST, 'mid', array() );
        $addon_name = zarilia_cleanRequestVars( $_REQUEST, 'name', array() );
        $addon_weight = zarilia_cleanRequestVars( $_REQUEST, 'weight', array() );
        $addon_isactive = zarilia_cleanRequestVars( $_REQUEST, 'isactive', array() );
        foreach ( $addon_mid as $id => $addon_id ) {
            $addon_obj = $addon_handler->get( $addon_id );
            if ( isset( $addon_isactive[$id] ) && ( $addon_obj->getVar( 'dirname' ) != "system" || $addon_obj->getVar( 'dirname' ) == $zariliaConfig['startpage'] ) ) {
                $addon_obj->setVar( 'isactive', $addon_isactive[$id] );
            }
            if ( isset( $addon_name[$id] ) ) {
                $addon_obj->setVar( 'name', $addon_name[$id] );
            }
            if ( isset( $addon_weight[$id] ) ) {
                $addon_obj->setVar( 'weight', $addon_weight[$id] );
            }
            /**
             */
            if ( !$addon_handler->insert( $addon_obj, false ) ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $addon_obj->getVar( 'name' ) ) );
            } else {
                if ( isset( $addon_isactive[$id] ) ) {
                    $addon_obj->setVar( 'isactive', $addon_isactive[$id] );
                    $blocks = &ZariliaBlock::getByAddon( $addon_id );
                    for ( $i = 0; $i < count( $blocks ); $i++ ) {
                        $blocks[$i]->setVar( 'isactive', $addon_isactive[$id] );
                        if ( !$blocks[$i]->store() ) {
                            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $addon_obj->getVar( 'name' ) ) );
                        }
                    }
                }
            }
        }
        if ( !$GLOBALS['zariliaLogger']->getSysErrorCount() ) {
            redirect_header( $addonversion['adminpath'] . '&amp;op=list', 1, _DBUPDATED );
        } else {
            zarilia_cp_header();
            $menu_handler->render( 1 );
            $GLOBALS['zariliaLogger']->sysRender();
        }
        break;

    case 'list':
    default:
        require_once ZAR_ROOT_PATH . '/class/class.tlist.php';

        /*
        * required for Navigation
        */
        $nav['start'] = zarilia_cleanRequestVars( $_REQUEST, 'start', 0 );
        $nav['sort'] = zarilia_cleanRequestVars( $_REQUEST, 'sort', 'mid' );
        $nav['order'] = zarilia_cleanRequestVars( $_REQUEST, 'order', 'ASC', XOBJ_DTYPE_TXTBOX );
        $nav['limit'] = zarilia_cleanRequestVars( $_REQUEST, 'limit', 10 );

        $act = zarilia_cleanRequestVars( $_REQUEST, 'act', 0 );
        $listed_mods = array();
        /*
		* Start actual display
		*/
        zarilia_cp_header();
        $menu_handler->render( $act + 1 );
        $tlist = new ZariliaTList();
        switch ( $act ) {
            case 0:
            case 1:
            default:
                $tlist->AddHeader( 'mid', '5', 'center', false );
                $tlist->AddHeader( 'name', '15%', 'left', true );
                $tlist->AddHeader( 'version', '', 'center', true );
                $tlist->AddHeader( 'last_update', '', 'center', true );
                $tlist->AddHeader( 'isactive', '', 'center', 1 );
                $tlist->AddHeader( 'weight', '', 'center', 1 );
                $tlist->AddHeader( 'ACTION', '', 'left', false );
                $tlist->AddFormStart( 'post', $addonversion['adminpath'], 'addons' );
                $tlist->addFooter( $addon_handler->setSubmit( $fct ) );

                $button = array( 'edit', 'delete', 'clone' );
                $addon = &$addon_handler->getAddons( $nav, $act );
                $button = array( 'addon_home', 'addon_update', 'addon_uninstall' );
                $i = 0;
                foreach ( $addon['list'] as $obj ) {
                    $mid = $obj->getVar( 'mid' );
                    $tlist->addHidden( $mid, 'mid' );
                    $buttons = "";
                    if ( $obj->getVar( 'name' ) != "system" && $obj->getVar( 'hasadmin' ) == 1 ) {
                        $buttons .= "<a href='" . ZAR_URL . "/addons/" . $obj->getVar( 'dirname' ) . "/" . $obj->getInfo( 'adminindex' ) . "'>" . zarilia_img_show( 'addon_home', _ADDON_HOME ) . "</a>&nbsp;";
                    }
                    $buttons .= "<a href='" . ZAR_URL . "/addons/system/index.php?fct=addonsadmin&amp;op=update&amp;addon=" . $obj->getVar( 'dirname' ) . "'>" . zarilia_img_show( 'addon_update', _ADDON_UPDATE ) . "</a>&nbsp;";
                    if ( $obj->getVar( 'dirname' ) != "system" || $act == 1 ) {
                        $buttons .= "<a href='" . ZAR_URL . "/addons/system/index.php?fct=addonsadmin&amp;op=uninstall&amp;addon=" . $obj->getVar( 'dirname' ) . "'>" . zarilia_img_show( 'addon_uninstall', _ADDON_UNINSTALL ) . "</a>&nbsp;";
                    }
                    $tlist->add(
                        array( $mid,
                            $obj->getTextbox( 'mid', 'name', '50' ),
                            round( $obj->getInfo( 'version' ), 2 ),
                            $obj->getVar( 'last_update' ),
                            $obj->getDisplay(),
                            $obj->getTextboxes(),
                            $buttons
                            ) );
                    $i++;
                }
				$acount = $addon['count'];
                break;

            case 2:
                $tlist->AddHeader( 'mid', '', 'center', false );
                $tlist->AddHeader( 'name', '', 'left', false );
                $tlist->AddHeader( 'dirname', '', 'left', false );
                $tlist->AddHeader( 'version', '', 'center', false );
                $tlist->AddHeader( 'author', '', 'center', false );
                $tlist->AddHeader( 'description', '', 'left', false );
                $tlist->AddHeader( 'ACTION', '', 'center', false );
                $tlist->addFooter();

                $installed_mods = &$addon_handler->getAddons( $nav, 0 );
				$acount = 0;
                foreach ( $installed_mods['list'] as $addon ) {
                    $listed_mods[] = $addon->getVar( 'dirname' );
					$acount++;
                }

                $addons_dir = ZAR_ROOT_PATH . "/addons";
                $handle = opendir( $addons_dir );
                $button = array( 'addon_install', 'delete' );
                $i = 0;
                while ( $file = readdir( $handle ) ) {
                    clearstatcache();
                    $file = trim( $file );
                    if ( $file != '' && strtolower( $file ) != 'cvs' && !preg_match( "/^[.]{1,2}$/", $file ) && is_dir( $addons_dir . '/' . $file ) ) {
                        if ( !in_array( $file, $listed_mods ) ) {
                            $addon = &$addon_handler->create();
                            $addon->loadInfo( $file );
                            $buttons = "<a href='" . ZAR_URL . "/addons/system/index.php?fct=addonsadmin&amp;op=install&amp;addon=" . $addon->getInfo( "dirname" ) . "'>" . zarilia_img_show( 'addon_install', _ADDON_INSTALL ) . "</a>&nbsp;<a href='" . ZAR_URL . "/addons/system/index.php?fct=addonsadmin&amp;op=delfile&amp;addon=" . $addon->getInfo( "dirname" ) . "'>" . zarilia_img_show( 'delete', _DELETE ) . "</a>";
                            if ( $addon->getInfo( "version" ) ) {
                                $tlist->add(
                                    array( $i + 1,
                                        ucfirst( $addon->getInfo( "name" ) ),
                                        $file,
                                        round( $addon->getInfo( 'version' ), 2 ),
                                        $addon->getInfo( "author" ),
                                        $addon->getInfo( "description" ),
                                        $buttons
                                        ) );
                                $i++;
                            }
                            unset( $addon );
                        }
                    }
                }
                break;
        } // switch
        $tlist->render();
        zarilia_cp_legend( $button );
        zarilia_pagnav( $acount, $nav['limit'], $nav['start'], 'start', 1, 'index.php?fct=addonsadmin&op=list&act=' . $act );
        break;

    case 'index':
    case 'default':
        zarilia_cp_header();
        zarilia_admin_menu(
            _MD_AD_ACTION_BOX, array( $addonversion['adminpath'] . "&amp;op=uploadform" => _MA_AD_UPLOAD ),
            _MD_AD_MAINTENANCE_BOX, array( $addonversion['adminpath'] . "&amp;op=optimize" => _MD_AD_OPTIMIZE, $addonversion['adminpath'] . "&amp;op=empty" => _MD_AD_CLEARENTRIES )
            );
        $menu_handler->render( 0 );
        break;
} // switch
zarilia_cp_footer();

?>
