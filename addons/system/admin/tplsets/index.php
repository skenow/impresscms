<?php
// $Id: index.php,v 1.4 2007/05/05 11:10:38 catzwolf Exp $
// ------------------------------------------------------------------------ //
// Zarilia - PHP Content Management System                      			//
// Copyright (c) 2007 Zarilia                           				//
// 																			//
// Authors: 																//
// John Neill ( AKA Catzwolf )                                     			//
// Raimondas Rimkevicius ( AKA Mekdrop )									//
// 							 												//
// URL: http:www.zarilia.com 												//
// Project: Zarilia Project                                               //
// -------------------------------------------------------------------------//

if ( !is_object( $zariliaUser ) || !is_object( $zariliaAddon ) || !$zariliaUser->isAdmin( $zariliaAddon->getVar( 'mid' ) ) ) {
    exit( "Access Denied" );
}
require_once "admin_menu.php";

if ( isset( $_POST ) ) {
    foreach ( $_POST as $k => $v ) {
        ${$k} = $v;
    }
}

$id = zarilia_cleanRequestVars( $_REQUEST, 'id', 0 );
$moddir = zarilia_cleanRequestVars( $_REQUEST, 'moddir', null, XOBJ_DTYPE_TXTBOX );
$file = zarilia_cleanRequestVars( $_REQUEST, 'file', null, XOBJ_DTYPE_TXTBOX );
$type = zarilia_cleanRequestVars( $_REQUEST, 'type', null, XOBJ_DTYPE_TXTBOX );
$tplset = zarilia_cleanRequestVars( $_REQUEST, 'tplset', null, XOBJ_DTYPE_TXTBOX );

if ( $op == 'edittpl_go' ) {
    if ( isset( $previewtpl ) ) {
        $op = 'previewtpl';
    }
}
$tplset_handler = &zarilia_gethandler( 'tplset' );
$tpltpl_handler = &zarilia_gethandler( 'tplfile' );

switch ( $op ) {
    case 'list':
        require_once ZAR_ROOT_PATH . '/class/class.tlist.php';

        zarilia_cp_header();
        zarilia_admin_menu(
            _MD_AD_ACTION_BOX, array( $addonversion['adminpath'] . "&amp;op=edit" => _MA_AD_EMENU_CREATE ),
            _MD_AD_MAINTENANCE_BOX, zariliaMainAction()
            );
        $menu_handler->render( 1 );

        $tlist = new ZariliaTList();
        $tlist->AddHeader( 'tplset_id', '5', 'center', false );
        $tlist->AddHeader( 'tplset_name', '', 'left', false );
        $tlist->AddHeader( 'tplset_created', '5%', 'center', false );
        $tlist->AddHeader( 'tplset_templates', '20%', 'left', false );
        $tlist->AddHeader( 'tplset_count', '', 'center', false );
        $tlist->AddHeader( 'tplset_default', '', 'center', false );
        $tlist->AddHeader( 'tplset_action', '', 'center', false );
        $tlist->setPath( 'op=' . $op );

        $installed_mods = $tpltpl_handler->getAddonTplCount( 'default' );
        $tplsets = &$tplset_handler->getObjects();
        foreach ( $tplsets as $obj ) {
            $installed_themes[] = $tplset_name = $obj->getVar( 'tplset_name' );
            $tplset_desc = $obj->getVar( 'tplset_desc' );
            $tplset_date = formatTimestamp( $obj->getVar( 'tplset_created' ), 's' );
            $tplstats = $tpltpl_handler->getAddonTplCount( $tplset_name );
            $default_image = ( $tplset_name == $zariliaConfig['template_set'] ) ? zarilia_img_show( 'default', _MD_DEFAULTTHEME ) : '';
            $mod_name = $mod_filecount = '';
            if ( count( $tplstats ) > 0 ) {
                foreach ( $tplstats as $moddir => $filecount ) {
                    $addon = &$addon_handler->getByDirname( $moddir );
                    if ( is_object( $addon ) ) {
                        if ( $installed_mods[$moddir] > $filecount ) {
                            $filecount = '<span style="color:#ff0000;">' . $filecount . '</span>';
                        }
                        $mod_name .= '<div><a href="' . $addonversion['adminpath'] . '&amp;op=listtpl&amp;tplset=' . $tplset_name . '&amp;moddir=' . $moddir . '">' . $addon->getVar( 'name' ) . '</a></div>';
                        $mod_filecount .= '<div><b>' . $filecount . '</b></div>';
                    }
                    unset( $addon );
                }
                $not_installed = array_diff( array_keys( $installed_mods ), array_keys( $tplstats ) );
            } else {
                $not_installed = &array_keys( $installed_mods );
            }
            foreach ( $not_installed as $ni ) {
                $addon = &$addon_handler->getByDirname( $ni );
                $mod_name .= '<div><a href="' . $addonversion['adminpath'] . '&amp;op=listtpl&amp;tplset=' . $tplset_name . '&amp;moddir=' . $ni . '">' . $addon->getVar( 'name' ) . '</a></div>';
                $mod_filecount .= '<div><span style="color:#ff0000; font-weight: bold;">0</span> <a href="' . $addonversion['adminpath'] . '&amp;op=generatemod&amp;tplset=' . $tplset_name . '&amp;moddir=' . $ni . '">' . _MD_GENERATE . '</a></div>';
            }
            $buttons['show'] = '<a href="' . $addonversion['adminpath'] . '&amp;op=download&amp;method=tar&amp;tplset=' . $tplset_name . '">' . zarilia_img_show( 'download', _DOWNLOAD ) . '</a> ';
            $buttons['show'] .= '<a href="' . $addonversion['adminpath'] . '&amp;op=clone&amp;tplset=' . $tplset_name . '">' . zarilia_img_show( 'clone', _CLONE ) . '</a> ';
            if ( $tplset_name != 'default' && $tplset_name != $zariliaConfig['template_set'] ) {
                $buttons['show'] .= '<a href="' . $addonversion['adminpath'] . '&amp;op=delete&amp;tplset=' . $tplset_name . '">' . zarilia_img_show( 'delete', _DELETE ) . '</a>';
            }
            $tplset_id = $obj->getVar( 'tplset_id' );
            $tlist->add(
                array( $tplset_id,
                    $tplset_name . "<br />" . $tplset_desc,
                    $obj->getVar( 'tplset_created' ),
                    $mod_name,
                    $mod_filecount,
                    $default_image,
                    $buttons['show']
                    ) );
        }
        $tlist->render();
        zarilia_cp_legend( array( 'download', 'clone', 'delete', 'list' ) );
        break;

    case 'listtpl':
        if ( $moddir == '' || $tplset == '' ) {
            zarilia_cp_header();
            $menu_handler->render( 1 );
            $GLOBALS['zariliaLogger']->sysRender();
            zarilia_cp_footer();
            exit();
        }
        require_once ZAR_ROOT_PATH . '/class/class.tlist.php';

        zarilia_cp_header();
        $menu_handler->render( 1 );

        $addon = &$addon_handler->getByDirname( $moddir );
        $modname = $addon->getVar( 'name' );

/*
        $tlist = new ZariliaTList();
        $tlist->AddHeader( 'tpl_id', '5', 'center', false );
        $tlist->AddHeader( 'tpl_file', '', 'left', false );
        $tlist->AddHeader( 'tpl_lastmodified', '5%', 'center', false );
        $tlist->AddHeader( 'tpl_lastimported', '20%', 'left', false );
        $tlist->AddHeader( 'ACTION', '', 'center', false );
        $tlist->AddFormStart( 'post', $addonversion['adminpath'] . '&amp;op=' . $op, 'tplsets' );
        $tlist->render();
*/

        echo '<h4>' . $modname . '</h4>
		<form action="index.php" method="post" enctype="multipart/form-data"><table width="100%" class="outer" cellspacing="1">
		<tr><th width="40%">' . _MD_FILENAME . '</th><th>' . _MD_LASTMOD . '</th>';
        if ( $tplset != 'default' ) {
            echo '<th>' . _MD_LASTIMP . '</th><th colspan="2">' . _ACTION . '</th></tr>';
        } else {
            echo '<th>' . _ACTION . '</th></tr>';
        }
        // get files that are already installed
        $templates = &$tpltpl_handler->find( $tplset, 'addon', null, $moddir );
        $inst_files = array();
        $tcount = count( $templates );
        for ( $i = 0; $i < $tcount; $i++ ) {
            if ( $i % 2 == 0 ) {
                $class = 'even';
            } else {
                $class = 'odd';
            }
            $last_modified = $templates[$i]->getVar( 'tpl_lastmodified' );
            $last_imported = $templates[$i]->getVar( 'tpl_lastimported' );
            $last_imported_f = ( $last_imported > 0 ) ? formatTimestamp( $last_imported, 'l' ) : '';
            echo '<tr class="' . $class . '"><td class="head">' . $templates[$i]->getVar( 'tpl_file' ) . '<br /><br /><span style="font-weight:normal;">' . $templates[$i]->getVar( 'tpl_desc' ) . '</span></td><td>' . formatTimestamp( $last_modified, 'l' ) . '</td>';

            $filename = $templates[$i]->getVar( 'tpl_file' );

            if ( $tplset != 'default' ) {
                $physical_file = ZAR_THEME_PATH . '/' . $tplset . '/templates/' . $moddir . '/' . $filename;
                if ( file_exists( $physical_file ) ) {
                    $mtime = filemtime( $physical_file );
                    if ( $last_imported < $mtime ) {
                        if ( $mtime > $last_modified ) {
                            $bg = '#ff9999';
                        } elseif ( $mtime > $last_imported ) {
                            $bg = '#99ff99';
                        }
                        echo '<td style="background-color:' . $bg . ';">' . $last_imported_f . ' [<a href="' . $addonversion['adminpath'] . '&amp;tplset=' . $tplset . '&amp;moddir=' . $moddir . '&amp;op=importtpl&amp;id=' . $templates[$i]->getVar( 'tpl_id' ) . '">' . _MD_IMPORT . '</a>]';
                    } else {
                        echo '<td>' . $last_imported_f;
                    }
                } else {
                    echo '<td>' . $last_imported_f;
                }
                echo '</td><td>[<a href="' . $addonversion['adminpath'] . '&amp;op=edittpl&amp;id=' . $templates[$i]->getVar( 'tpl_id' ) . '">' . _EDIT . '</a>] [<a href="' . $addonversion['adminpath'] . '&amp;op=deletetpl&amp;id=' . $templates[$i]->getVar( 'tpl_id' ) . '">' . _DELETE . '</a>] [<a href="' . $addonversion['adminpath'] . '&amp;op=downloadtpl&amp;id=' . $templates[$i]->getVar( 'tpl_id' ) . '">' . _MD_DOWNLOAD . '</a>]</td><td align="right"><input type="file" name="' . $filename . '" id="' . $filename . '" /><input type="hidden" name="zarilia_upload_file[]" id="zarilia_upload_file[]" value="' . $filename . '" /><input type="hidden" name="old_template[' . $filename . ']" value="' . $templates[$i]->getVar( 'tpl_id' ) . '" /></td>';
            } else {
                echo '<td>[<a href="' . $addonversion['adminpath'] . '&amp;op=edittpl&amp;id=' . $templates[$i]->getVar( 'tpl_id' ) . '">' . _MD_VIEW . '</a>] [<a href="' . $addonversion['adminpath'] . '&amp;op=downloadtpl&amp;id=' . $templates[$i]->getVar( 'tpl_id' ) . '">' . _MD_DOWNLOAD . '</a>]</td>';
            }
            echo '</tr>' . "\n";
            $inst_files[] = $filename;
        }

        if ( $tplset != 'default' ) {
            include_once ZAR_ROOT_PATH . '/class/zarilialists.php';
            // get difference between already installed files and the files under addons directory. which will be recognized as files that are not installed
            $notinst_files = array_diff( ZariliaLists::getFileListAsArray( ZAR_ROOT_PATH . '/addons/' . $moddir . '/templates/' ), $inst_files );
            foreach ( $notinst_files as $nfile ) {
                if ( $nfile != 'index.html' ) {
                    echo '<tr><td style="background-color:#FFFF99; padding: 5px;">' . $nfile . '</td><td style="background-color:#FFFF99; padding: 5px;">&nbsp;</td><td style="background-color:#FFFF99; padding: 5px;">';
                    $physical_file = ZAR_THEME_PATH . '/' . $tplset . '/templates/' . $moddir . '/' . $nfile;
                    if ( file_exists( $physical_file ) ) {
                        echo '[<a href="' . $addonversion['adminpath'] . '&amp;moddir=' . $moddir . '&amp;tplset=' . $tplset . '&amp;op=importtpl&amp;file=' . urlencode( $nfile ) . '">' . _MD_IMPORT . '</a>]';
                    } else {
                        echo '&nbsp;';
                    }
                    echo '</td><td style="background-color:#FFFF99; padding: 5px;">[<a href="' . $addonversion['adminpath'] . '&amp;moddir=' . $moddir . '&amp;tplset=' . $tplset . '&amp;op=generatetpl&amp;type=addon&amp;file=' . urlencode( $nfile ) . '">' . _MD_GENERATE . '</a>]</td><td style="background-color:#FFFF99; padding: 5px; text-align:right;"><input type="file" name="' . $nfile . '" id="' . $nfile . '" /><input type="hidden" name="zarilia_upload_file[]" id="zarilia_upload_file[]" value="' . $nfile . '" /></td></tr>' . "\n";
                }
            }
        }
        echo '</table>
		<h3>Blocks</h3>
		<table width="100%" class="outer" cellspacing="1"><tr><th width="40%">' . _MD_FILENAME . '</th><th>' . _MD_LASTMOD . '</th>';
        if ( $tplset != 'default' ) {
            echo '<th>' . _MD_LASTIMP . '</th><th colspan="2">' . _AM_ACTION . '</th></tr>';
        } else {
            echo '<th>' . _AM_ACTION . '</th></tr>';
        }
        $btemplates = &$tpltpl_handler->find( $tplset, 'block', null, $moddir );
        $binst_files = array();
        $btcount = count( $btemplates );
        for ( $j = 0; $j < $btcount; $j++ ) {
            $last_imported = $btemplates[$j]->getVar( 'tpl_lastimported' );
            $last_imported_f = ( $last_imported > 0 ) ? formatTimestamp( $last_imported, 'l' ) : '';
            $last_modified = $btemplates[$j]->getVar( 'tpl_lastmodified' );
            if ( $j % 2 == 0 ) {
                $class = 'even';
            } else {
                $class = 'odd';
            }
            echo '<tr class="' . $class . '"><td class="head"><span style="font-weight:bold;">' . $btemplates[$j]->getVar( 'tpl_file' ) . '</span><br /><br /><span style="font-weight:normal;">' . $btemplates[$j]->getVar( 'tpl_desc' ) . '</span></td><td>' . formatTimestamp( $last_modified, 'l' ) . '</td>';
            $filename = $btemplates[$j]->getVar( 'tpl_file' );
            $physical_file = ZAR_THEME_PATH . '/' . $tplset . '/templates/' . $moddir . '/blocks/' . $filename;
            if ( $tplset != 'default' ) {
                if ( file_exists( $physical_file ) ) {
                    $mtime = filemtime( $physical_file );
                    if ( $last_imported < $mtime ) {
                        if ( $mtime > $last_modified ) {
                            $bg = '#ff9999';
                        } elseif ( $mtime > $last_imported ) {
                            $bg = '#99ff99';
                        }
                        echo '<td style="background-color:' . $bg . ';">' . $last_imported_f . ' [<a href="' . $addonversion['adminpath'] . '&amp;tplset=' . $tplset . '&amp;op=importtpl&amp;moddir=' . $moddir . '&amp;id=' . $btemplates[$j]->getVar( 'tpl_id' ) . '">' . _MD_IMPORT . '</a>]';
                    } else {
                        echo '<td>' . $last_imported_f;
                    }
                } else {
                    echo '<td>' . $last_imported_f;
                }
                echo '</td><td>[<a href="' . $addonversion['adminpath'] . '&amp;op=edittpl&amp;id=' . $btemplates[$j]->getVar( 'tpl_id' ) . '">' . _EDIT . '</a>] [<a href="' . $addonversion['adminpath'] . '&amp;op=deletetpl&amp;id=' . $btemplates[$j]->getVar( 'tpl_id' ) . '">' . _DELETE . '</a>] [<a href="' . $addonversion['adminpath'] . '&amp;op=downloadtpl&amp;id=' . $btemplates[$j]->getVar( 'tpl_id' ) . '">' . _MD_DOWNLOAD . '</a>]</td><td align="right"><input type="file" name="' . $filename . '" id="' . $filename . '" /><input type="hidden" name="zarilia_upload_file[]" id="zarilia_upload_file[]" value="' . $filename . '" /><input type="hidden" name="old_template[' . $filename . ']" value="' . $btemplates[$j]->getVar( 'tpl_id' ) . '" /></td>';
            } else {
                echo '<td>[<a href="' . $addonversion['adminpath'] . '&amp;op=edittpl&amp;id=' . $btemplates[$j]->getVar( 'tpl_id' ) . '">' . _MD_VIEW . '</a>] [<a href="' . $addonversion['adminpath'] . '&amp;op=downloadtpl&amp;id=' . $btemplates[$j]->getVar( 'tpl_id' ) . '">' . _MD_DOWNLOAD . '</a>]</td>';
            }
            echo '</tr>' . "\n";
            $binst_files[] = $filename;
        }
        if ( $tplset != 'default' ) {
            include_once ZAR_ROOT_PATH . '/class/zarilialists.php';
            $bnotinst_files = array_diff( ZariliaLists::getFileListAsArray( ZAR_ROOT_PATH . '/addons/' . $moddir . '/templates/blocks/' ), $binst_files );
            foreach ( $bnotinst_files as $nfile ) {
                if ( $nfile != 'index.html' ) {
                    echo '<tr style="background-color:#FFFF99;"><td style="background-color:#FFFF99; padding: 5px;">' . $nfile . '</td><td style="background-color:#FFFF99; padding: 5px;">&nbsp;</td><td style="background-color:#FFFF99; padding: 5px;">';
                    $physical_file = ZAR_THEME_PATH . '/' . $tplset . '/templates/' . $moddir . '/blocks/' . $nfile;
                    if ( file_exists( $physical_file ) ) {
                        echo '[<a href="' . $addonversion['adminpath'] . '&amp;moddir=' . $moddir . '&amp;tplset=' . $tplset . '&amp;op=importtpl&amp;file=' . urlencode( $nfile ) . '">' . _MD_IMPORT . '</a>]';
                    } else {
                        echo '&nbsp;';
                    }
                    echo '</td><td style="background-color:#FFFF99; padding: 5px;">[<a href="' . $addonversion['adminpath'] . '&amp;moddir=' . $moddir . '&amp;tplset=' . $tplset . '&amp;op=generatetpl&amp;type=block&amp;file=' . urlencode( $nfile ) . '">' . _MD_GENERATE . '</a>]</td><td style="background-color:#FFFF99; padding: 5px; text-align: right"><input type="file" name="' . $nfile . '" id="' . $nfile . '" /><input type="hidden" name="zarilia_upload_file[]" id="zarilia_upload_file[]" value="' . $nfile . '" /></td></tr>' . "\n";
                }
            }
        }
        echo '</table>';
        if ( $tplset != 'default' ) {
            echo '<div style="text-align: right; margin-top: 5px;"><input type="hidden" name="fct" value="tplsets" /><input type="hidden" name="op" value="update" /><input type="hidden" name="moddir" value="' . $moddir . '" /><input type="hidden" name="tplset" value="' . $tplset . '" /><input type="submit" value="' . _MD_UPLOAD . '" /></div></form>';
        }
        break;

    case 'edittpl':
        $id = zarilia_cleanRequestVars( $_REQUEST, 'id', 0 );
        $ok = zarilia_cleanRequestVars( $_REQUEST, 'ok', 0 );
        /**
         */
        $tplfile = &$tpltpl_handler->get( $id, true );
        if ( !$tplfile ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, 'Selected template (ID: $id) does not exist' );
            zarilia_cp_header();
            $GLOBALS['zariliaLogger']->sysRender();
            zarilia_cp_footer();
            exit();
        }
        switch ( $ok ) {
            case 0:
            default:
                zarilia_cp_header();
                $menu_handler->render( 2 );
                $form = $tplfile->formEdit();
                $form->display();
                break;
            case 1:
                if ( $tplfile->getVar( 'tpl_tplset' ) != 'default' ) {
                    $tplfile->setVar( 'tpl_source', $html );
                    $tplfile->setVar( 'tpl_lastmodified', time() );
                    if ( !$tpltpl_handler->insert( $tplfile ) ) {
                        $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, 'ERROR: Could not insert template file to the database.' );
                    } else {
                        include_once ZAR_ROOT_PATH . '/class/template.php';
                        $zariliaTpl = new ZariliaTpl();
                        if ( $zariliaTpl->is_cached( 'db:' . $tplfile->getVar( 'tpl_file' ) ) ) {
                            if ( !$zariliaTpl->clear_cache( 'db:' . $tplfile->getVar( 'tpl_file' ) ) ) {
                            }
                        }
                        if ( $tplfile->getVar( 'tpl_tplset' ) == $zariliaConfig['template_set'] ) {
                            zarilia_template_touch( $id );
                        }
                    }
                } else {
                    $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, 'ERROR: Default template files cannot be edited.' );
                }
                if ( $GLOBALS['zariliaLogger']->getSysErrorCount() ) {
                    zarilia_cp_header();
                    $menu_handler->render( 1 );
                    $GLOBALS['zariliaLogger']->sysRender();
                    zarilia_cp_footer();
                    exit();
                } else {
                    redirect_header( $addonversion['adminpath'] . '&amp;op=list', 2, _DBUPDATED );
                }
                break;
        } // switch
        break;

    case 'deletetpl':
        $id = zarilia_cleanRequestVars( $_REQUEST, 'id', 0 );
        $ok = zarilia_cleanRequestVars( $_REQUEST, 'ok', 0 );
        $tplfile = $tpltpl_handler->get( $id );
        if ( !is_object( $tplfile ) ) {
            zarilia_cp_header();
            $menu_handler->render( 1 );
            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, 'Selected template (ID: $id) does not exist' );
            $GLOBALS['zariliaLogger']->sysRender();
            zarilia_cp_footer();
            exit();
        }

        switch ( $ok ) {
            case 0:
            default:
                zarilia_cp_header();
                $menu_handler->render( 1 );
                zarilia_confirm(
                    array( 'op' => 'deletetpl', 'id' => $tplfile->getVar( 'tpl_id' ), 'ok' => 1 ), $addonversion['adminpath'], _MD_RUSUREDELTPL );
                break;
            case 1:
                if ( $tplfile->getVar( 'tpl_tplset' ) != 'default' ) {
                    if ( !$tpltpl_handler->delete( $tplfile ) ) {
                        $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, 'Could not delete ' . $tplfile->getVar( 'tpl_file' ) . ' from the database.' );
                    } else {
                        // need to compile default zarilia template
                        if ( $tplfile->getVar( 'tpl_tplset' ) == $zariliaConfig['template_set'] ) {
                            $defaulttpl = &$tpltpl_handler->find( 'default', $tplfile->getVar( 'tpl_type' ), $tplfile->getVar( 'tpl_refid' ), null, $tplfile->getVar( 'tpl_file' ) );
                            if ( count( $defaulttpl ) > 0 ) {
                                include_once ZAR_ROOT_PATH . '/class/template.php';
                                zarilia_template_touch( $defaulttpl[0]->getVar( 'tpl_id' ), true );
                            }
                        }
                    }
                } else {
                    $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, 'Default template files cannot be deleted.' );
                }
                if ( $GLOBALS['zariliaLogger']->getSysErrorCount() ) {
                    zarilia_cp_header();
                    $menu_handler->render( 1 );
                    $GLOBALS['zariliaLogger']->sysRender();
                    zarilia_cp_footer();
                    exit();
                } else {
                    redirect_header( $addonversion['adminpath'] . '&amp;op=listtpl&amp;moddir=' . $tplfile->getVar( 'tpl_addon' ) . '&amp;tplset=' . urlencode( $tplfile->getVar( 'tpl_tplset' ) ), 2, _DBUPDATED );
                }
        }
        break;

    case 'delete':
        $ok = zarilia_cleanRequestVars( $_REQUEST, 'ok', 0 );
        switch ( $ok ) {
            case 0:
            default:
                zarilia_cp_header();
                $menu_handler->render( 1 );
                zarilia_confirm( array( 'op' => 'delete', 'tplset' => $tplset, 'ok' => 1 ), $addonversion['adminpath'] . '&amp;op=list', _MD_RUSUREDELTH );
                break;
            case 1:
                if ( $tplset != 'default' && $tplset != $zariliaConfig['template_set'] ) {
                    $templates = &$tpltpl_handler->getObjects( new Criteria( 'tpl_tplset', $tplset ) );
                    $tcount = count( $templates );
                    if ( $tcount > 0 ) {
                        for ( $i = 0; $i < $tcount; $i++ ) {
                            if ( !$tpltpl_handler->delete( $templates[$i] ) ) {
                                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, 'ERROR: Could not delete template <b>' . $templates[$i]->getVar( 'tpl_file' ) . '</b>. ID: <b>' . $templates[$i]->getVar( 'tpl_id' ) . '</b>' );
                            }
                        }
                    }
                    $tplsets = &$tplset_handler->getObjects( new Criteria( 'tplset_name', $tplset ) );
                    if ( count( $tplsets ) > 0 && is_object( $tplsets[0] ) ) {
                        $msgs[] = 'Deleting template set data...';
                        if ( !$tplset_handler->delete( $tplsets[0] ) ) {
                            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, 'ERROR: Template set ' . $tplset . ' could not be deleted.' );
                        }
                    }
                } else {
                    $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, 'ERROR: Default template files cannot be deleted' );
                }
                if ( $GLOBALS['zariliaLogger']->getSysErrorCount() ) {
                    zarilia_cp_header();
                    $menu_handler->render( 1 );
                    $GLOBALS['zariliaLogger']->sysRender();
                    zarilia_cp_footer();
                    exit();
                } else {
                    redirect_header( $addonversion['adminpath'] . '&amp;op=list', 2, _DBUPDATED );
                }
                break;
        } // switch
        break;

    case 'clone':
        $ok = zarilia_cleanRequestVars( $_REQUEST, 'ok', 0 );
        switch ( $ok ) {
            case 0:
            default:
                include_once ZAR_ROOT_PATH . '/class/zariliaformloader.php';
                $form = new ZariliaThemeForm( _MD_CLONETHEME, 'template_form', 'index.php', 'post', true );
                $form->addElement( new ZariliaFormLabel( _MD_THEMENAME, $tplset ) );
                $form->addElement( new ZariliaFormText( _MD_NEWNAME, 'newtheme', 30, 50 ), true );
                $form->addElement( new ZariliaFormHidden( 'tplset', $tplset ) );
                $form->addElement( new ZariliaFormHidden( 'op', 'clone' ) );
                $form->addElement( new ZariliaFormHidden( 'ok', '1' ) );
                $form->addElement( new ZariliaFormHidden( 'fct', 'tplsets' ) );
                $form->addElement( new ZariliaFormButton( '', 'tpl_button', _SUBMIT, 'submit' ) );
                zarilia_cp_header();
                $menu_handler->render( 1 );
                $form->display();
                break;
            case 1:
                $newtheme = zarilia_cleanRequestVars( $_REQUEST, 'newtheme', 0 );
                if ( $tplset == $newtheme ) {
                    $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, 'Template set name must be a different name.' );
                } elseif ( $tpltpl_handler->getCount( new Criteria( 'tpl_tplset', $newtheme ) ) > 0 ) {
                    $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, 'Template set <b>' . $newtheme . '</b> already exists.' );
                } else {
                    $tplsetobj = &$tplset_handler->create();
                    $tplsetobj->setVar( 'tplset_name', $newtheme );
                    $tplsetobj->setVar( 'tplset_created', time() );
                    if ( !$tplset_handler->insert( $tplsetobj ) ) {
                        $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, 'ERROR: Default template files cannot be deleted' );
                    } else {
                        $tplsetid = $tplsetobj->getVar( 'tplset_id' );
                        $templates = &$tpltpl_handler->getObjects( new Criteria( 'tpl_tplset', $tplset ), true );
                        $tcount = count( $templates );
                        if ( $tcount > 0 ) {
                            for ( $i = 0; $i < $tcount; $i++ ) {
                                $newtpl = &$templates[$i]->zariliaClone();
                                $newtpl->setVar( 'tpl_tplset', $newtheme );
                                $newtpl->setVar( 'tpl_id', 0 );
                                $newtpl->setVar( 'tpl_lastimported', 0 );
                                $newtpl->setVar( 'tpl_lastmodified', time() );
                                if ( !$tpltpl_handler->insert( $newtpl ) ) {
                                    $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, 'ERROR: Failed copying template <b>' . $templates[$i]->getVar( 'tpl_file' ) . '</b>. ID: <b>' . $templates[$i]->getVar( 'tpl_id' ) . '</b>' );
                                }
                                unset( $newtpl );
                            }
                        } else {
                            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, 'ERROR: Template files for ' . $theme . ' do not exist' );
                        }
                    }
                }
                if ( $GLOBALS['zariliaLogger']->getSysErrorCount() ) {
                    zarilia_cp_header();
                    $menu_handler->render( 1 );
                    $GLOBALS['zariliaLogger']->sysRender();
                    zarilia_cp_footer();
                    exit();
                } else {
                    redirect_header( $addonversion['adminpath'] . '&amp;op=list', 2, _DBUPDATED );
                }
                break;
        } // switch
        break;

    case 'viewdefault':
        $tplfile = &$tpltpl_handler->get( $id );
        $default = &$tpltpl_handler->find( 'default', $tplfile->getVar( 'tpl_type' ), $tplfile->getVar( 'tpl_refid' ), null, $tplfile->getVar( 'tpl_file' ) );
        echo "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>";
        echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="' . _LANGCODE . '" lang="' . _LANGCODE . '">
        <head>
        <meta http-equiv="content-type" content="text/html; charset=' . _CHARSET . '" />
        <meta http-equiv="content-language" content="' . _LANGCODE . '" />
        <title>' . htmlspecialchars( $zariliaConfig['sitename'] ) . ' Administration</title>
        </head><body>';
        if ( is_object( $default[0] ) ) {
            $tpltpl_handler->loadSource( $default[0] );
            $last_modified = $default[0]->getVar( 'tpl_lastmodified' );
            $last_imported = $default[0]->getVar( 'tpl_lastimported' );
            if ( $default[0]->getVar( 'tpl_type' ) == 'block' ) {
                $path = ZAR_ROOT_PATH . '/addons/' . $default[0]->getVar( 'tpl_addon' ) . '/blocks/' . $default[0]->getVar( 'tpl_file' );
            } else {
                $path = ZAR_ROOT_PATH . '/addons/' . $default[0]->getVar( 'tpl_addon' ) . '/' . $default[0]->getVar( 'tpl_file' );
            }
            $colorchange = '';
            if ( !file_exists( $path ) ) {
                $filemodified_date = _MD_NOFILE;
                $lastimported_date = _MD_NOFILE;
            } else {
                $tpl_modified = filemtime( $path );
                $filemodified_date = formatTimestamp( $tpl_modified, 'l' );
                if ( $tpl_modified > $last_imported ) {
                    $colorchange = ' bgcolor="#ffCC99"';
                }
                $lastimported_date = formatTimestamp( $last_imported, 'l' );
            }
            include_once ZAR_ROOT_PATH . '/class/zariliaformloader.php';
            $form = new ZariliaThemeForm( _MD_VIEWDEFAULT, 'template_form', 'index.php' );
            $form->addElement( new ZariliaFormTextArea( _MD_FILEHTML, 'html', $default[0]->getVar( 'tpl_source' ), 25 ) );
            $form->display();
        } else {
            echo 'Selected file does not exist';
        }
        echo '<div style="text-align:center;">[<a href="#" onclick="javascript:window.close();">' . _CLOSE . '</a>]</div></body></html>';
        break;

    case 'downloadtpl':
        $tpl = &$tpltpl_handler->get( intval( $id ), true );
        if ( is_object( $tpl ) ) {
            $output = $tpl->getVar( 'tpl_source' );
            strlen( $output );
            header( 'Cache-Control: no-cache, must-revalidate' );
            header( 'Pragma: no-cache' );
            header( 'Content-Type: application/force-download' );
            if ( preg_match( "/MSIE 5.5/", $_SERVER['HTTP_USER_AGENT'] ) ) {
                header( 'Content-Disposition: filename=' . $tpl->getVar( 'tpl_file' ) );
            } else {
                header( 'Content-Disposition: attachment; filename=' . $tpl->getVar( 'tpl_file' ) );
            }
            header( 'Content-length: ' . strlen( $output ) );
            echo $output;
        }
        break;
    // case 'uploadtpl':
    // $id = intval( $_GET['id'] );
    // $tpl = &$tpltpl_handler->get( $id );
    // zarilia_cp_header();
    // $menu_handler->render( 1 );
    // if ( is_object( $tpl ) ) {
    // include_once ZAR_ROOT_PATH . '/class/zariliaformloader.php';
    // $form = new ZariliaThemeForm( _MD_UPLOAD, 'tplupload_form', 'index.php', 'post', true );
    // $form->setExtra( 'enctype="multipart/form-data"' );
    // $form->addElement( new ZariliaFormLabel( _MD_FILENAME, $tpl->getVar( 'tpl_file' ) . ' (' . $tpl->getVar( 'tpl_tplset' ) . ')' ) );
    // $form->addElement( new ZariliaFormFile( _MD_CHOOSEFILE . '<br /><span style="color:#ff0000;">' . _MD_UPWILLREPLACE . '</span>', 'tpl_upload', 200000 ), true );
    // $form->addElement( new ZariliaFormHidden( 'tpl_id', $id ) );
    // $form->addElement( new ZariliaFormHidden( 'op', 'uploadtpl_go' ) );
    // $form->addElement( new ZariliaFormHidden( 'fct', 'tplsets' ) );
    // $form->addElement( new ZariliaFormButton( '', 'upload_button', _MD_UPLOAD, 'submit' ) );
    // $form->display();
    // zarilia_cp_footer();
    // exit();
    // } else {
    // echo 'Selected template does not exist';
    // }
    // break;
    // case 'uploadtpl_go':
    // $tpl = &$tpltpl_handler->get( $tpl_id );
    // if ( is_object( $tpl ) ) {
    // include_once ZAR_ROOT_PATH . '/class/uploader.php';
    // $uploader = new ZariliaMediaUploader( ZAR_UPLOAD_PATH, array( 'text/html', 'application/x-cdf', 'text/plain' ), 200000 );
    // $uploader->setPrefix( 'tmp' );
    // if ( $uploader->fetchMedia( $_POST['zarilia_upload_file'][0] ) ) {
    // if ( !$uploader->upload() ) {
    // $err = $uploader->getErrors();
    // } else {
    // $tpl->setVar( 'tpl_lastmodified', time() );
    // $fp = @fopen ( $uploader->getSavedDestination(), 'r' );
    // $fsource = @fread ( $fp, filesize( $uploader->getSavedDestination() ) );
    //  @fclose ( $fp );
    // $tpl->setVar( 'tpl_source', $fsource, true );
    //  @unlink ( $uploader->getSavedDestination() );
    // if ( !$tpltpl_handler->insert( $tpl ) ) {
    // $err = 'Failed inserting data to database';
    // } else {
    // if ( $tpl->getVar( 'tpl_tplset' ) == $zariliaConfig['template_set'] ) {
    // include_once ZAR_ROOT_PATH . '/class/template.php';
    // zarilia_template_touch( $tpl_id, true );
    // }
    // }
    // }
    // } else {
    // $err = implode( '<br />', $uploader->getErrors( false ) );
    // }
    // if ( isset( $err ) ) {
    // zarilia_cp_header( false );
    // zarilia_error( $err );
    // zarilia_cp_footer();
    // exit();
    // }
    // redirect_header( $addonversion['adminpath'] . '&amp;op=listtpl&amp;moddir=' . $tpl->getVar( 'tpl_addon' ) . '&amp;tplset=' . urlencode( $tpl->getVar( 'tpl_tplset' ) ), 2, _DBUPDATED );
    // }
    // break;
    // upload new file
    /*
    case 'uploadtpl2':
        zarilia_cp_header();
        $tplset = htmlspecialchars( $tplset );
        $moddir = htmlspecialchars( $moddir );
        echo '<a href="' . $addonversion['adminpath'] . '">' . _MD_TPLMAIN . '</a>&nbsp;<span style="font-weight:bold;">&raquo;&raquo;</span>&nbsp;<a href="./' . $addonversion['adminpath'] . '&amp;op=listtpl&amp;moddir=' . $moddir . '&amp;tplset=' . $tplset . '">' . $tplset . '</a>&nbsp;<span style="font-weight:bold;">&raquo;&raquo;</span>&nbsp;' . _MD_UPLOAD . '<br /><br />';
        include_once ZAR_ROOT_PATH . '/class/zariliaformloader.php';
        $form = new ZariliaThemeForm( _MD_UPLOAD, 'tplupload_form', 'index.php', 'post', true );
        $form->setExtra( 'enctype="multipart/form-data"' );
        $form->addElement( new ZariliaFormLabel( _MD_FILENAME, $file ) );
        $form->addElement( new ZariliaFormFile( _MD_CHOOSEFILE . '<br /><span style="color:#ff0000;">' . _MD_UPWILLREPLACE . '</span>', 'tpl_upload', 200000 ), true );
        $form->addElement( new ZariliaFormHidden( 'moddir', $moddir ) );
        $form->addElement( new ZariliaFormHidden( 'tplset', $tplset ) );
        $form->addElement( new ZariliaFormHidden( 'file', $file ) );
        $form->addElement( new ZariliaFormHidden( 'type', $type ) );
        $form->addElement( new ZariliaFormHidden( 'op', 'uploadtpl2_go' ) );
        $form->addElement( new ZariliaFormHidden( 'fct', 'tplsets' ) );
        $form->addElement( new ZariliaFormButton( '', 'ploadtarupload_button', _MD_UPLOAD, 'submit' ) );
        $form->display();
        break;

    case 'uploadtpl2_go':
        include_once ZAR_ROOT_PATH . '/class/uploader.php';
        $uploader = new ZariliaMediaUploader( ZAR_UPLOAD_PATH, array( 'text/html', 'application/x-cdf', 'text/plain' ), 200000 );
        $uploader->setPrefix( 'tmp' );
        if ( $uploader->fetchMedia( $_POST['zarilia_upload_file'][0] ) ) {
            if ( !$uploader->upload() ) {
                $err = $uploader->getErrors();
            } else {
                $tplfile = &$tpltpl_handler->find( 'default', $type, null, $moddir, $file );
                if ( is_array( $tplfile ) ) {
                    $tpl = &$tplfile[0]->zariliaClone();
                    $tpl->setVar( 'tpl_id', 0 );
                    $tpl->setVar( 'tpl_tplset', $tplset );
                    $tpl->setVar( 'tpl_lastmodified', time() );
                    $fp = @fopen( $uploader->getSavedDestination(), 'r' );
                    $fsource = @fread( $fp, filesize( $uploader->getSavedDestination() ) );
                    @fclose( $fp );
                    $tpl->setVar( 'tpl_source', $fsource, true );
                    @unlink( $uploader->getSavedDestination() );
                    if ( !$tpltpl_handler->insert( $tpl ) ) {
                        $err = 'Failed inserting data to database';
                    } else {
                        if ( $tplset == $zariliaConfig['template_set'] ) {
                            include_once ZAR_ROOT_PATH . '/class/template.php';
                            zarilia_template_touch( $tpl->getVar( 'tpl_id' ), true );
                        }
                    }
                } else {
                    $err = 'This template file does not need to be installed (PHP files using this template file does not exist)';
                }
            }
        } else {
            $err = implode( '<br />', $uploader->getErrors( false ) );
        }
        if ( isset( $err ) ) {
            zarilia_cp_header( false );
            zarilia_error( $err );
            zarilia_cp_footer();
            exit();
        }
        redirect_header( $addonversion['adminpath'] . '&amp;op=listtpl&amp;moddir=' . $moddir . '&amp;tplset=' . urlencode( $tplset ), 2, _DBUPDATED );
        break;
*/

    case 'download':
        if ( isset( $tplset ) ) {
            if ( false != extension_loaded( 'zlib' ) ) {
                if ( isset( $_GET['method'] ) && $_GET['method'] == 'tar' ) {
                    if ( @function_exists( 'gzencode' ) ) {
                        require_once( ZAR_ROOT_PATH . '/class/tardownloader.php' );
                        $downloader = new ZariliaTarDownloader();
                    }
                } else {
                    if ( @function_exists( 'gzcompress' ) ) {
                        require_once( ZAR_ROOT_PATH . '/class/zipdownloader.php' );
                        $downloader = new ZariliaZipDownloader();
                    }
                }

                $tplsetobj = &$tplset_handler->getByName( $tplset );
                $xml = "<" . "?xml version=\"1.0\"?" . ">\r\n<tplset>\r\n  <name>" . $tplset . "</name>\r\n  <dateCreated>" . $tplsetobj->getVar( 'tplset_created' ) . "</dateCreated>\r\n  <credits>\r\n" . $tplsetobj->getVar( 'tplset_credits' ) . "\r\n  </credits>\r\n  <generator>" . ZAR_VERSION . "</generator>\r\n  <templates>";

                $files = &$tpltpl_handler->getObjects( new Criteria( 'tpl_tplset', $tplset ), true );
                $fcount = count( $files );
                if ( $fcount > 0 ) {
                    for ( $i = 0; $i < $fcount; $i++ ) {
                        if ( $files[$i]->getVar( 'tpl_type' ) == 'block' ) {
                            $path = $tplset . '/templates/' . $files[$i]->getVar( 'tpl_addon' ) . '/blocks/' . $files[$i]->getVar( 'tpl_file' );
                            $xml .= "\r\n    <template name=\"" . $files[$i]->getVar( 'tpl_file' ) . "\">\r\n      <addon>" . $files[$i]->getVar( 'tpl_addon' ) . "</addon>\r\n      <type>block</type>\r\n      <lastModified>" . $files[$i]->getVar( 'tpl_lastmodified' ) . "</lastModified>\r\n    </template>";
                        } elseif ( $files[$i]->getVar( 'tpl_type' ) == 'addon' ) {
                            $path = $tplset . '/templates/' . $files[$i]->getVar( 'tpl_addon' ) . '/' . $files[$i]->getVar( 'tpl_file' );
                            $xml .= "\r\n    <template name=\"" . $files[$i]->getVar( 'tpl_file' ) . "\">\r\n      <addon>" . $files[$i]->getVar( 'tpl_addon' ) . "</addon>\r\n      <type>addon</type>\r\n      <lastModified>" . $files[$i]->getVar( 'tpl_lastmodified' ) . "</lastModified>\r\n    </template>";
                        }
                        $downloader->addFileData( $files[$i]->getVar( 'tpl_source' ), $path, $files[$i]->getVar( 'tpl_lastmodified' ) );
                    }

                    $xml .= "\r\n  </templates>";
                }
                $xml .= "\r\n</tplset>";
                $downloader->addFileData( $xml, $tplset . '/tplset.xml', time() );
                echo $downloader->download( $tplset, true );
            } else {
                if ( $GLOBALS['zariliaLogger']->getSysErrorCount() ) {
                    zarilia_cp_header();
                    $menu_handler->render( 1 );
                    $GLOBALS['zariliaLogger']->sysRender();
                    zarilia_cp_footer();
                    exit();
                } else {
                    redirect_header( $addonversion['adminpath'] . '&amp;op=list', 2, _DBUPDATED );
                }
                zarilia_cp_header();
                zarilia_error( _MD_NOZLIB );
                zarilia_cp_footer();
            }
        }
        break;

    case 'generatetpl':
        $ok = zarilia_cleanRequestVars( $_REQUEST, 'ok', 0 );
        switch ( $ok ) {
            case 0:
            default:
                zarilia_cp_header();
                $menu_handler->render( 1 );
                zarilia_confirm( array( 'op' => 'generatetpl', 'tplset' => $tplset, 'moddir' => $moddir, 'file' => $file, 'type' => $type, 'ok' => 1 ), $addonversion['adminpath'], _MD_PLZGENERATE );
                break;
            case 1:
                $tplfile = &$tpltpl_handler->find( 'default', $type, null, $moddir, $file, true );
                if ( count( $tplfile ) > 0 ) {
                    $newtpl = &$tplfile[0]->zariliaClone();
                    $newtpl->setVar( 'tpl_id', 0 );
                    $newtpl->setVar( 'tpl_tplset', $tplset );
                    $newtpl->setVar( 'tpl_lastmodified', time() );
                    $newtpl->setVar( 'tpl_lastimported', 0 );
                    if ( !$tpltpl_handler->insert( $newtpl ) ) {
                        $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, 'ERROR: Could not insert template <b>' . $tplfile[0]->getVar( 'tpl_file' ) . '</b> to the database.' );
                    } else {
                        if ( $tplset == $zariliaConfig['template_set'] ) {
                            include_once ZAR_ROOT_PATH . '/class/template.php';
                            zarilia_template_touch( $newtpl->getVar( 'tpl_id' ) );
                        }
                    }
                } else {
                    $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, 'ERROR: Selected file does not exist' );
                }
                if ( $GLOBALS['zariliaLogger']->getSysErrorCount() ) {
                    zarilia_cp_header();
                    $menu_handler->render( 1 );
                    $GLOBALS['zariliaLogger']->sysRender();
                    zarilia_cp_footer();
                    exit();
                } else {
                    redirect_header( $addonversion['adminpath'] . '&amp;op=list', 2, _DBUPDATED );
                }
                break;
        } // switch
        break;

    /*
    case 'generatetpl':
        zarilia_cp_header();
        zarilia_confirm( array( 'tplset' => $tplset, 'moddir' => $moddir, 'file' => $file, 'type' => $type, 'op' => 'generatetpl_go', 'fct' => 'tplsets' ), 'index.php', _MD_PLZGENERATE, _MD_GENERATE );
        break;

    case 'generatetpl_go':
        $tplfile = &$tpltpl_handler->find( 'default', $type, null, $moddir, $file, true );
        if ( count( $tplfile ) > 0 ) {
            $newtpl = &$tplfile[0]->zariliaClone();
            $newtpl->setVar( 'tpl_id', 0 );
            $newtpl->setVar( 'tpl_tplset', $tplset );
            $newtpl->setVar( 'tpl_lastmodified', time() );
            $newtpl->setVar( 'tpl_lastimported', 0 );
            if ( !$tpltpl_handler->insert( $newtpl ) ) {
                $err = 'ERROR: Could not insert template <b>' . $tplfile[0]->getVar( 'tpl_file' ) . '</b> to the database.';
            } else {
                if ( $tplset == $zariliaConfig['template_set'] ) {
                    include_once ZAR_ROOT_PATH . '/class/template.php';
                    zarilia_template_touch( $newtpl->getVar( 'tpl_id' ) );
                }
            }
        } else {
            $err = 'Selected file does not exist)';
        }
        if ( !isset( $err ) ) {
            redirect_header( $addonversion['adminpath'] . '&amp;op=listtpl&amp;moddir=' . $newtpl->getVar( 'tpl_addon' ) . '&amp;tplset=' . urlencode( $newtpl->getVar( 'tpl_tplset' ) ), 2, _DBUPDATED );
        }
        zarilia_cp_header();
        zarilia_error( $err );
        echo '<br /><a href="' . $addonversion['adminpath'] . '">' . _MD_AM_BTOTADMIN . '</a>';
        break;
*/

    case 'generatemod':
        $ok = zarilia_cleanRequestVars( $_REQUEST, 'ok', 0 );
        switch ( $ok ) {
            case 0:
            default:
                zarilia_cp_header();
                $menu_handler->render( 1 );
                zarilia_confirm( array( 'op' => 'generatemod', 'tplset' => $tplset, 'moddir' => $moddir, 'ok' => 1 ), $addonversion['adminpath'], _MD_PLZGENERATE );
                break;
            case 1:
                $tplfiles = &$tpltpl_handler->find( 'default', 'addon', null, $moddir, null, true );
                $fcount = count( $tplfiles );
                if ( $fcount > 0 ) {
                    for ( $i = 0; $i < $fcount; $i++ ) {
                        $newtpl = &$tplfiles[$i]->zariliaClone();
                        $newtpl->setVar( 'tpl_id', 0 );
                        $newtpl->setVar( 'tpl_tplset', $tplset );
                        $newtpl->setVar( 'tpl_lastmodified', time() );
                        $newtpl->setVar( 'tpl_lastimported', 0 );
                        if ( !$tpltpl_handler->insert( $newtpl ) ) {
                            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, 'ERROR: Could not insert template <b>' . $file . '</b> to the database.' );
                        } else {
                            if ( $tplset == $zariliaConfig['template_set'] ) {
                                include_once ZAR_ROOT_PATH . '/class/template.php';
                                zarilia_template_touch( $newtpl->getVar( 'tpl_id' ) );
                            }
                        }
                    }
                    flush();
                    unset( $newtpl );
                }
                unset( $files );
                $tplfiles = &$tpltpl_handler->find( 'default', 'block', null, $moddir, null, true );
                $fcount = count( $tplfiles );
                if ( $fcount > 0 ) {
                    for ( $i = 0; $i < $fcount; $i++ ) {
                        $newtpl = &$tplfiles[$i]->zariliaClone();
                        $newtpl->setVar( 'tpl_id', 0 );
                        $newtpl->setVar( 'tpl_tplset', $tplset );
                        $newtpl->setVar( 'tpl_lastmodified', time() );
                        $newtpl->setVar( 'tpl_lastimported', 0 );
                        if ( !$tpltpl_handler->insert( $newtpl ) ) {
                            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, 'ERROR: Could not insert block template <b>' . $tplfiles[$i]->getVar( 'tpl_file' ) . '</b> to the database' );
                        } else {
                            if ( $tplset == $zariliaConfig['template_set'] ) {
                                include_once ZAR_ROOT_PATH . '/class/template.php';
                                zarilia_template_touch( $newtpl->getVar( 'tpl_id' ) );
                            }
                        }
                    }
                    flush();
                    unset( $newtpl );
                }
                if ( $GLOBALS['zariliaLogger']->getSysErrorCount() ) {
                    zarilia_cp_header();
                    $menu_handler->render( 1 );
                    $GLOBALS['zariliaLogger']->sysRender();
                    zarilia_cp_footer();
                    exit();
                } else {
                    redirect_header( $addonversion['adminpath'] . '&amp;op=list', 2, _DBUPDATED );
                }
                break;
        } // switch
        break;
    // case 'generatemod':
    // zarilia_cp_header();
    // zarilia_confirm( array( 'tplset' => $tplset, 'op' => 'generatemod_go', 'fct' => 'tplsets', 'moddir' => $moddir ), 'index.php', _MD_PLZGENERATE, _MD_GENERATE );
    // break;
    // case 'generatemod_go':
    // zarilia_cp_header();
    // echo '';
    // $tplfiles = &$tpltpl_handler->find( 'default', 'addon', null, $moddir, null, true );
    // $fcount = count( $tplfiles );
    // if ( $fcount > 0 ) {
    // echo 'Installing addon template files for template set ' . $tplset . '...<br />';
    // for ( $i = 0; $i < $fcount; $i++ ) {
    // $newtpl = &$tplfiles[$i]->zariliaClone();
    // $newtpl->setVar( 'tpl_id', 0 );
    // $newtpl->setVar( 'tpl_tplset', $tplset );
    // $newtpl->setVar( 'tpl_lastmodified', time() );
    // $newtpl->setVar( 'tpl_lastimported', 0 );
    // if ( !$tpltpl_handler->insert( $newtpl ) ) {
    // echo '&nbsp;&nbsp;<span style="color:#ff0000;">ERROR: Could not insert template <b>' . $file . '</b> to the database.</span><br />';
    // } else {
    // if ( $tplset == $zariliaConfig['template_set'] ) {
    // include_once ZAR_ROOT_PATH . '/class/template.php';
    // zarilia_template_touch( $newtpl->getVar( 'tpl_id' ) );
    // }
    // echo '&nbsp;&nbsp;Template <b>' . $tplfiles[$i]->getVar( 'tpl_file' ) . '</b> added to the database.<br />';
    // }
    // }
    // flush();
    // unset( $newtpl );
    // }
    // unset( $files );
    // $tplfiles = &$tpltpl_handler->find( 'default', 'block', null, $moddir, null, true );
    // $fcount = count( $tplfiles );
    // if ( $fcount > 0 ) {
    // echo '&nbsp;&nbsp;Installing block template files...<br />';
    // for ( $i = 0; $i < $fcount; $i++ ) {
    // $newtpl = &$tplfiles[$i]->zariliaClone();
    // $newtpl->setVar( 'tpl_id', 0 );
    // $newtpl->setVar( 'tpl_tplset', $tplset );
    // $newtpl->setVar( 'tpl_lastmodified', time() );
    // $newtpl->setVar( 'tpl_lastimported', 0 );
    // if ( !$tpltpl_handler->insert( $newtpl ) ) {
    // echo '&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:#ff0000;">ERROR: Could not insert block template <b>' . $tplfiles[$i]->getVar( 'tpl_file' ) . '</b> to the database.</span><br />';
    // echo $newtpl->getHtmlErrors();
    // } else {
    // if ( $tplset == $zariliaConfig['template_set'] ) {
    // include_once ZAR_ROOT_PATH . '/class/template.php';
    // zarilia_template_touch( $newtpl->getVar( 'tpl_id' ) );
    // }
    // echo '&nbsp;&nbsp;&nbsp;&nbsp;Block template <b>' . $tplfiles[$i]->getVar( 'tpl_file' ) . '</b> added to the database.<br />';
    // }
    // }
    // flush();
    // unset( $newtpl );
    // }
    // echo '<br />Addons template files for template set <b>' . $tplset . '</b> generated and installed.<br /><br /><a href="' . $addonversion['adminpath'] . '">' . _MD_AM_BTOTADMIN . '</a>';
    // break;
    case 'uploadtar':
        $ok = zarilia_cleanRequestVars( $_REQUEST, 'ok', 0 );
        switch ( $ok ) {
            case 0:
            default:
                zarilia_cp_header();
                $menu_handler->render( 2 );
                include_once ZAR_ROOT_PATH . '/class/zariliaformloader.php';
                $form = new ZariliaThemeForm( _MD_UPLOADTAR, 'tplupload_form', $addonversion['adminpath'] );
                $form->setExtra( 'enctype="multipart/form-data"' );
                $form->addElement( new ZariliaFormFile( _MD_CHOOSETAR . '<br /><span style="color:#ff0000;">' . _MD_ONLYTAR . '</span>', 'tpl_upload', 1000000 ) );
                $form->addElement( new ZariliaFormText( _MD_NTHEMENAME . '<br /><span style="font-weight:normal;">' . _MD_ENTERTH . '</span>', 'tplset_name', 20, 50 ) );
                $form->addElement( new ZariliaFormHidden( 'op', 'uploadtar' ) );
                $form->addElement( new ZariliaFormHidden( 'ok', 1 ) );
                $form->addElement( new ZariliaFormButton( '', 'upload_button', _MD_UPLOAD, 'submit' ) );
                $form->display();
                break;
            case 1:
                include_once ZAR_ROOT_PATH . '/class/uploader.php';
                $uploader = new ZariliaMediaUploader( ZAR_UPLOAD_PATH, array( 'application/x-gzip', 'application/gzip', 'application/gzip-compressed', 'application/x-gzip-compressed', 'application/x-tar', 'application/x-tar-compressed', 'application/octet-stream' ), 1000000 );
                $uploader->setPrefix( 'tmp' );
                zarilia_cp_header();
                // zarilia_admin_menu( '', _MD_TPLMAIN, $op );
                echo '<code>';
                if ( $uploader->fetchMedia( $_POST['zarilia_upload_file'][0] ) ) {
                    if ( !$uploader->upload() ) {
                        zarilia_error( $uploader->getErrors() );
                    } else {
                        include_once ZAR_ROOT_PATH . '/class/class.tar.php';
                        $tar = new tar();
                        $tar->openTar( $uploader->getSavedDestination() );
                        @unlink( $uploader->getSavedDestination() );
                        $themefound = false;
                        foreach ( $tar->files as $id => $info ) {
                            $infoarr = explode( '/', str_replace( "\\", '/', $info['name'] ) );
                            if ( !isset( $tplset_name ) ) {
                                $tplset_name = trim( $infoarr[0] );
                            } else {
                                $tplset_name = trim( $tplset_name );
                                if ( $tplset_name == '' ) {
                                    $tplset_name = trim( $infoarr[0] );
                                }
                            }
                            if ( $tplset_name != '' ) {
                                break;
                            }
                        }
                        if ( $tplset_name == '' ) {
                            echo '<span style="color:#ff0000;">ERROR: Template file not found</span><br />';
                        } else {
                            $tplset_handler = &zarilia_gethandler( 'tplset' );
                            if ( $tplset_handler->getCount( new Criteria( 'tplset_name', $tplset_name ) ) > 0 ) {
                                echo '<span style="color:#ff0000;">ERROR: Template set <b>' . $tplset_name . '</b> already exists.</span><br />';
                            } else {
                                $tplset = &$tplset_handler->create();
                                $tplset->setVar( 'tplset_name', $tplset_name );
                                $tplset->setVar( 'tplset_created', time() );
                                if ( !$tplset_handler->insert( $tplset ) ) {
                                    echo '<span style="color:#ff0000;">ERROR: Could not create template set <b>' . $tplset_name . '</b>.</span><br />';
                                } else {
                                    $tplsetid = $tplset->getVar( 'tplset_id' );
                                    echo 'Template set <b>' . $tplset_name . '</b> created. (ID: <b>' . $tplsetid . '</b>)</span><br />';
                                    $tpltpl_handler = zarilia_gethandler( 'tplfile' );
                                    $themeimages = array();
                                    foreach ( $tar->files as $id => $info ) {
                                        $infoarr = explode( '/', str_replace( "\\", '/', $info['name'] ) );
                                        if ( isset( $infoarr[3] ) && trim( $infoarr[3] ) == 'blocks' ) {
                                            $default = &$tpltpl_handler->find( 'default', 'block', null, trim( $infoarr[2] ), trim( $infoarr[4] ) );
                                        } elseif ( ( !isset( $infoarr[4] ) || trim( $infoarr[4] ) == '' ) && $infoarr[1] == 'templates' ) {
                                            $default = &$tpltpl_handler->find( 'default', 'addon', null, trim( $infoarr[2] ), trim( $infoarr[3] ) );
                                        } elseif ( isset( $infoarr[3] ) && trim( $infoarr[3] ) == 'images' ) {
                                            $infoarr[2] = trim( $infoarr[2] );
                                            if ( preg_match( "/(.*)\.(gif|jpg|jpeg|png)$/i", $infoarr[2], $match ) ) {
                                                $themeimages[] = array( 'name' => $infoarr[2], 'content' => $info['file'] );
                                            }
                                        }
                                        if ( isset( $default ) && count( $default ) > 0 ) {
                                            $newtpl = &$default[0]->zariliaClone();
                                            $newtpl->setVar( 'tpl_id', 0 );
                                            $newtpl->setVar( 'tpl_tplset', $tplset_name );
                                            $newtpl->setVar( 'tpl_source', $info['file'], true );
                                            $newtpl->setVar( 'tpl_lastmodified', time() );
                                            if ( !$tpltpl_handler->insert( $newtpl ) ) {
                                                echo '&nbsp;&nbsp;<span style="color:#ff0000;">ERROR: Could not insert <b>' . $info['name'] . '</b> to the database.</span><br />';
                                            } else {
                                                echo '&nbsp;&nbsp;<b>' . $info['name'] . '</b> inserted to the database.<br />';
                                            }
                                            unset( $default );
                                        }
                                        unset( $info );
                                    }
                                    $icount = count( $themeimages );
                                    if ( $icount > 0 ) {
                                        $imageset_handler = &zarilia_gethandler( 'imageset' );
                                        $imgset = &$imageset_handler->create();
                                        $imgset->setVar( 'imgset_name', $tplset_name );
                                        $imgset->setVar( 'imgset_refid', 0 );
                                        if ( !$imageset_handler->insert( $imgset ) ) {
                                            echo '&nbsp;&nbsp;<span style="color:#ff0000;">ERROR: Could not create image set.</span><br />';
                                        } else {
                                            $newimgsetid = $imgset->getVar( 'imgset_id' );
                                            echo '&nbsp;&nbsp;Image set <b>' . $tplset_name . '</b> created. (ID: <b>' . $newimgsetid . '</b>)<br />';
                                            if ( !$imageset_handler->linktplset( $newimgsetid, $tplset_name ) ) {
                                                echo '&nbsp;&nbsp;<span style="color:#ff0000;">ERROR: Failed linking image set to template set <b>' . $tplset_name . '</b></span><br />';
                                            }
                                            $image_handler = &zarilia_gethandler( 'imagesetimg' );
                                            for ( $i = 0; $i < $icount; $i++ ) {
                                                if ( isset( $themeimages[$i]['name'] ) && $themeimages[$i]['name'] != '' ) {
                                                    $image = &$image_handler->create();
                                                    $image->setVar( 'imgsetimg_file', $themeimages[$i]['name'] );
                                                    $image->setVar( 'imgsetimg_imgset', $newimgsetid );
                                                    $image->setVar( 'imgsetimg_body', $themeimages[$i]['content'], true );
                                                    if ( !$image_handler->insert( $image ) ) {
                                                        echo '&nbsp;&nbsp;<span style="color:#ff0000;">ERROR: Failed storing image file data to database.</span><br />';
                                                    } else {
                                                        echo '&nbsp;&nbsp;Image file data stored into database. (ID: <b>' . $image->getVar( 'imgsetimg_id' ) . '</b>)<br />';
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                } else {
                    echo '<span style="color:#ff0000;">ERROR: Failed uploading file</span>';
                }
                echo '</code><br /><a href="admin.php?fct=tplsets">' . _MD_AM_BTOTADMIN . '</a>';
                zarilia_cp_footer();
                break;
        }
        break;
    case 'uploadtar':
        zarilia_cp_header();
        $menu_handler->render( 2 );
        include_once ZAR_ROOT_PATH . '/class/zariliaformloader.php';
        $form = new ZariliaThemeForm( _MD_UPLOADTAR, 'tplupload_form', $addonversion['adminpath'] );
        $form->setExtra( 'enctype="multipart/form-data"' );
        $form->addElement( new ZariliaFormFile( _MD_CHOOSETAR . '<br /><span style="color:#ff0000;">' . _MD_ONLYTAR . '</span>', 'tpl_upload', 1000000 ) );
        $form->addElement( new ZariliaFormText( _MD_NTHEMENAME . '<br /><span style="font-weight:normal;">' . _MD_ENTERTH . '</span>', 'tplset_name', 20, 50 ) );
        $form->addElement( new ZariliaFormHidden( 'op', 'uploadtar' ) );
        $form->addElement( new ZariliaFormHidden( 'ok', 1 ) );
        $form->addElement( new ZariliaFormButton( '', 'upload_button', _MD_UPLOAD, 'submit' ) );
        $form->display();
        break;

    case 'uploadtar_go':
        include_once ZAR_ROOT_PATH . '/class/uploader.php';
        $uploader = new ZariliaMediaUploader( ZAR_UPLOAD_PATH, array( 'application/x-gzip', 'application/gzip', 'application/gzip-compressed', 'application/x-gzip-compressed', 'application/x-tar', 'application/x-tar-compressed', 'application/octet-stream' ), 1000000 );
        $uploader->setPrefix( 'tmp' );
        zarilia_cp_header();
        echo '';
        if ( $uploader->fetchMedia( $_POST['zarilia_upload_file'][0] ) ) {
            if ( !$uploader->upload() ) {
                zarilia_error( $uploader->getErrors() );
            } else {
                include_once ZAR_ROOT_PATH . '/class/class.tar.php';
                $tar = new tar();
                $tar->openTar( $uploader->getSavedDestination() );
                @unlink( $uploader->getSavedDestination() );
                $themefound = false;
                foreach ( $tar->files as $id => $info ) {
                    $infoarr = explode( '/', str_replace( "\\", '/', $info['name'] ) );
                    if ( !isset( $tplset_name ) ) {
                        $tplset_name = trim( $infoarr[0] );
                    } else {
                        $tplset_name = trim( $tplset_name );
                        if ( $tplset_name == '' ) {
                            $tplset_name = trim( $infoarr[0] );
                        }
                    }
                    if ( $tplset_name != '' ) {
                        break;
                    }
                }
                if ( $tplset_name == '' ) {
                    echo '<span style="color:#ff0000;">ERROR: Template file not found</span><br />';
                } elseif ( preg_match( '/[' . preg_quote( '\/:*?"<>|', '/' ) . ']/', $tplset_name ) ) {
                    echo '<span style="color:#ff0000;">ERROR: Invalid Template Set Name</span><br />';
                } else {
                    if ( $tplset_handler->getCount( new Criteria( 'tplset_name', $tplset_name ) ) > 0 ) {
                        echo '<span style="color:#ff0000;">ERROR: Template set <b>' . htmlspecialchars( $tplset_name, ENT_QUOTES ) . '</b> already exists.</span><br />';
                    } else {
                        $tplset = &$tplset_handler->create();
                        $tplset->setVar( 'tplset_name', $tplset_name );
                        $tplset->setVar( 'tplset_created', time() );
                        if ( !$tplset_handler->insert( $tplset ) ) {
                            echo '<span style="color:#ff0000;">ERROR: Could not create template set <b>' . htmlspecialchars( $tplset_name, ENT_QUOTES ) . '</b>.</span><br />';
                        } else {
                            $tplsetid = $tplset->getVar( 'tplset_id' );
                            echo 'Template set <b>' . htmlspecialchars( $tplset_name, ENT_QUOTES ) . '</b> created. (ID: <b>' . $tplsetid . '</b>)</span><br />';
                            $tpltpl_handler = zarilia_gethandler( 'tplfile' );
                            $themeimages = array();
                            foreach ( $tar->files as $id => $info ) {
                                $infoarr = explode( '/', str_replace( "\\", '/', $info['name'] ) );
                                if ( isset( $infoarr[3] ) && trim( $infoarr[3] ) == 'blocks' ) {
                                    $default = &$tpltpl_handler->find( 'default', 'block', null, trim( $infoarr[2] ), trim( $infoarr[4] ) );
                                } elseif ( ( !isset( $infoarr[4] ) || trim( $infoarr[4] ) == '' ) && $infoarr[1] == 'templates' ) {
                                    $default = &$tpltpl_handler->find( 'default', 'addon', null, trim( $infoarr[2] ), trim( $infoarr[3] ) );
                                } elseif ( isset( $infoarr[3] ) && trim( $infoarr[3] ) == 'images' ) {
                                    $infoarr[2] = trim( $infoarr[2] );
                                    if ( preg_match( "/(.*)\.(gif|jpg|jpeg|png)$/i", $infoarr[2], $match ) ) {
                                        $themeimages[] = array( 'name' => $infoarr[2], 'content' => $info['file'] );
                                    }
                                }
                                if ( isset( $default ) && count( $default ) > 0 ) {
                                    $newtpl = &$default[0]->zariliaClone();
                                    $newtpl->setVar( 'tpl_id', 0 );
                                    $newtpl->setVar( 'tpl_tplset', $tplset_name );
                                    $newtpl->setVar( 'tpl_source', $info['file'], true );
                                    $newtpl->setVar( 'tpl_lastmodified', time() );
                                    if ( !$tpltpl_handler->insert( $newtpl ) ) {
                                        echo '&nbsp;&nbsp;<span style="color:#ff0000;">ERROR: Could not insert <b>' . $info['name'] . '</b> to the database.</span><br />';
                                    } else {
                                        echo '&nbsp;&nbsp;<b>' . $info['name'] . '</b> inserted to the database.<br />';
                                    }
                                    unset( $default );
                                }
                                unset( $info );
                            }
                            $icount = count( $themeimages );
                            if ( $icount > 0 ) {
                                $imageset_handler = &zarilia_gethandler( 'imageset' );
                                $imgset = &$imageset_handler->create();
                                $imgset->setVar( 'imgset_name', $tplset_name );
                                $imgset->setVar( 'imgset_refid', 0 );
                                if ( !$imageset_handler->insert( $imgset ) ) {
                                    echo '&nbsp;&nbsp;<span style="color:#ff0000;">ERROR: Could not create image set.</span><br />';
                                } else {
                                    $newimgsetid = $imgset->getVar( 'imgset_id' );
                                    echo '&nbsp;&nbsp;Image set <b>' . htmlspecialchars( $tplset_name, ENT_QUOTES ) . '</b> created. (ID: <b>' . $newimgsetid . '</b>)<br />';
                                    if ( !$imageset_handler->linktplset( $newimgsetid, $tplset_name ) ) {
                                        echo '&nbsp;&nbsp;<span style="color:#ff0000;">ERROR: Failed linking image set to template set <b>' . htmlspecialchars( $tplset_name, ENT_QUOTES ) . '</b></span><br />';
                                    }
                                    $image_handler = &zarilia_gethandler( 'imagesetimg' );
                                    for ( $i = 0; $i < $icount; $i++ ) {
                                        if ( isset( $themeimages[$i]['name'] ) && $themeimages[$i]['name'] != '' ) {
                                            $image = &$image_handler->create();
                                            $image->setVar( 'imgsetimg_file', $themeimages[$i]['name'] );
                                            $image->setVar( 'imgsetimg_imgset', $newimgsetid );
                                            $image->setVar( 'imgsetimg_body', $themeimages[$i]['content'], true );
                                            if ( !$image_handler->insert( $image ) ) {
                                                echo '&nbsp;&nbsp;<span style="color:#ff0000;">ERROR: Failed storing image file data to database.</span><br />';
                                            } else {
                                                echo '&nbsp;&nbsp;Image file data stored into database. (ID: <b>' . $image->getVar( 'imgsetimg_id' ) . '</b>)<br />';
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } else {
            $err = implode( '<br />', $uploader->getErrors( false ) );
            echo $err;
        }
        echo '<br /><a href="' . $addonversion['adminpath'] . '">' . _MD_AM_BTOTADMIN . '</a>';
        break;

    case 'previewtpl':
        require_once ZAR_ROOT_PATH . '/class/template.php';
        $html = stripSlashes( $html );

        $tplfile = &$tpltpl_handler->get( $id, true );
        $zariliaTpl = new ZariliaTpl();

        if ( is_object( $tplfile ) ) {
            $dummylayout = '<html><head><meta http-equiv="content-type" content="text/html; charset=' . _CHARSET . '" /><meta http-equiv="content-language" content="' . _LANGCODE . '" /><title>' . $zariliaConfig['sitename'] . '</title><style type="text/css" media="all">';
            $css = &$tpltpl_handler->find( $zariliaConfig['template_set'], 'css', 0, null, null, true );
            $csscount = count( $css );
            for ( $i = 0; $i < $csscount; $i++ ) {
                $dummylayout .= "\n" . $css[$i]->getVar( 'tpl_source' );
            }
            $dummylayout .= "\n" . '</style></head><body><{$content}></body></html>';
            if ( $tplfile->getVar( 'tpl_type' ) == 'block' ) {
                include_once ZAR_ROOT_PATH . '/class/zariliablock.php';
                $block = new ZariliaBlock( $tplfile->getVar( 'tpl_refid' ) );
                $zariliaTpl->assign( 'block', $block->buildBlock() );
            }
            $dummytpl = '_dummytpl_' . time() . '.html';
            $fp = fopen( ZAR_CACHE_PATH . '/' . $dummytpl, 'w' );
            fwrite( $fp, $html );
            fclose( $fp );
            $zariliaTpl->assign( 'content', $zariliaTpl->fetch( 'file:' . ZAR_CACHE_PATH . '/' . $dummytpl ) );
            $zariliaTpl->clear_compiled_tpl( 'file:' . ZAR_CACHE_PATH . '/' . $dummytpl );
            unlink( ZAR_CACHE_PATH . '/' . $dummytpl );
            $dummyfile = '_dummy_' . time() . '.html';
            $fp = fopen( ZAR_CACHE_PATH . '/' . $dummyfile, 'w' );
            fwrite( $fp, $dummylayout );
            fclose( $fp );
            $tplset = $tplfile->getVar( 'tpl_tplset' );
            $tform = array( 'tpl_tplset' => $tplset, 'tpl_id' => $id, 'tpl_file' => $tplfile->getVar( 'tpl_file' ), 'tpl_desc' => $tplfile->getVar( 'tpl_desc' ), 'tpl_lastmodified' => $tplfile->getVar( 'tpl_lastmodified' ), 'tpl_source' => htmlspecialchars( $html, ENT_QUOTES ), 'tpl_addon' => $moddir );
            include_once ZAR_ROOT_PATH . '/addons/system/admin/tplsets/tplform.php';
            zarilia_cp_header();
            echo '<a href="' . $addonversion['adminpath'] . '">' . _MD_TPLMAIN . '</a>&nbsp;<span style="font-weight:bold;">&raquo;&raquo;</span>&nbsp;<a href="./' . $addonversion['adminpath'] . '&amp;op=listtpl&amp;moddir=' . $moddir . '&amp;tplset=' . urlencode( $tplset ) . '">' . htmlspecialchars( $tplset, ENT_QUOTES ) . '</a>&nbsp;<span style="font-weight:bold;">&raquo;&raquo;</span>&nbsp;' . _MD_EDITTEMPLATE . '<br /><br />';
            $form->display();
            zarilia_cp_footer();
            echo '<script type="text/javascript">
            <!--//
            preview_window = openWithSelfMain("", "popup", 680, 450, true);
            preview_window.document.clear();
            ';
            $lines = preg_split( "/(\r\n|\r|\n)( *)/", $zariliaTpl->fetch( 'file:' . ZAR_CACHE_PATH . '/' . $dummyfile ) );
            $zariliaTpl->clear_compiled_tpl( 'file:' . ZAR_CACHE_PATH . '/' . $dummyfile );
            unlink( ZAR_CACHE_PATH . '/' . $dummyfile );
            foreach ( $lines as $line ) {
                echo 'preview_window.document.writeln("' . str_replace( '"', '\"', $line ) . '");';
            }
            echo '
            preview_window.focus();
            preview_window.document.close();
            //-->
            </script>';
        }
        break;

    case 'update':
        include_once ZAR_ROOT_PATH . '/class/uploader.php';
        $uploader = new ZariliaMediaUploader( ZAR_UPLOAD_PATH, array( 'text/html', 'application/x-cdf' ), 200000 );
        $uploader->setPrefix( 'tmp' );
        $msg = array();
        foreach ( $_POST['zarilia_upload_file'] as $upload_file ) {
            // '.' is converted to '_' when upload
            $upload_file2 = str_replace( '.', '_', $upload_file );
            if ( $uploader->fetchMedia( $upload_file2 ) ) {
                if ( !$uploader->upload() ) {
                    $msg[] = $uploader->getErrors();
                } else {
                    if ( !isset( $old_template[$upload_file] ) ) {
                        $tplfile = &$tpltpl_handler->find( 'default', null, null, $moddir, $upload_file );
                        if ( count( $tplfile ) > 0 ) {
                            $tpl = &$tplfile[0]->zariliaClone();
                            $tpl->setVar( 'tpl_id', 0 );
                            $tpl->setVar( 'tpl_tplset', $tplset );
                        } else {
                            $msg[] = 'Template file <b>' . $upload_file . '</b> does not need to be installed (PHP files using this template file does not exist)';
                            continue;
                        }
                    } else {
                        $tpl = &$tpltpl_handler->get( $old_template[$upload_file] );
                    }
                    $tpl->setVar( 'tpl_lastmodified', time() );
                    $fp = @fopen( $uploader->getSavedDestination(), 'r' );
                    $fsource = @fread( $fp, filesize( $uploader->getSavedDestination() ) );
                    @fclose( $fp );
                    $tpl->setVar( 'tpl_source', $fsource, true );
                    @unlink( $uploader->getSavedDestination() );
                    if ( !$tpltpl_handler->insert( $tpl ) ) {
                        $msg[] = 'Failed inserting data for ' . $upload_file . ' to database';
                    } else {
                        $msg[] = 'Template file <b>' . $upload_file . '</b> updated.';
                        if ( $tplset == $zariliaConfig['template_set'] ) {
                            include_once ZAR_ROOT_PATH . '/class/template.php';
                            if ( zarilia_template_touch( $tpl->getVar( 'tpl_id' ), true ) ) {
                                $msg[] = 'Template file <b>' . $upload_file . '</b> compiled.';
                            }
                        }
                    }
                }
            } else {
                if ( $uploader->getMediaName() == '' ) {
                    continue;
                } else {
                    $msg[] = $uploader->getErrors();
                }
            }
        }
        zarilia_cp_header();
        echo '';
        foreach ( $msg as $m ) {
            echo $m . '<br />';
        }
        echo '<br /><a href="' . $addonversion['adminpath'] . '&amp;op=listtpl&amp;tplset=' . urlencode( $tplset ) . '&amp;moddir=' . $moddir . '">' . _MD_AM_BTOTADMIN . '</a>';
        break;

    case 'importtpl':
        zarilia_cp_header();
        if ( !empty( $id ) ) {
            zarilia_confirm( array( 'tplset' => $tplset, 'moddir' => $moddir, 'id' => $id, 'op' => 'importtpl_go', 'fct' => 'tplsets' ), 'index.php', _MD_RUSUREIMPT, _MD_IMPORT );
        } elseif ( isset( $file ) ) {
            zarilia_confirm( array( 'tplset' => $tplset, 'moddir' => $moddir, 'file' => $file, 'op' => 'importtpl_go', 'fct' => 'tplsets' ), 'index.php', _MD_RUSUREIMPT, _MD_IMPORT );
        }
        break;

    case 'importtpl_go':
        $tplfile = '';
        if ( !empty( $id ) ) {
            $tplfile = &$tpltpl_handler->get( $id, true );
        } else {
            $tplfiles = &$tpltpl_handler->find( 'default', null, null, null, trim( $file ), true );
            $tplfile = ( count( $tplfiles ) > 0 ) ? $tplfiles[0] : '';
        }
        $error = true;
        if ( is_object( $tplfile ) ) {
            switch ( $tplfile->getVar( 'tpl_type' ) ) {
                case 'addon':
                    $filepath = ZAR_THEME_PATH . '/' . $tplset . '/templates/' . $tplfile->getVar( 'tpl_addon' ) . '/' . $tplfile->getVar( 'tpl_file' );
                    break;
                case 'block':
                    $filepath = ZAR_THEME_PATH . '/' . $tplset . '/templates/' . $tplfile->getVar( 'tpl_addon' ) . '/blocks/' . $tplfile->getVar( 'tpl_file' );
                    break;
                default:
                    break;
            }
            if ( file_exists( $filepath ) ) {
                if ( false != $fp = fopen( $filepath, 'r' ) ) {
                    $filesource = fread( $fp, filesize( $filepath ) );
                    fclose( $fp );
                    $tplfile->setVar( 'tpl_source', $filesource, true );
                    $tplfile->setVar( 'tpl_tplset', $tplset );
                    $tplfile->setVar( 'tpl_lastmodified', time() );
                    $tplfile->setVar( 'tpl_lastimported', time() );
                    if ( !$tpltpl_handler->insert( $tplfile ) ) {
                    } else {
                        $error = false;
                    }
                }
            }
        }
        if ( false != $error ) {
            zarilia_cp_header();
            zarilia_error( 'Could not import file ' . $filepath );
            echo '<br /><a href="' . $addonversion['adminpath'] . '&amp;op=listtpl&amp;tplset=' . urlencode( $tplset ) . '&amp;moddir=' . $moddir . '">' . _MD_AM_BTOTADMIN . '</a>';
            zarilia_cp_footer();
            exit();
        }
        redirect_header( $addonversion['adminpath'] . '&amp;op=listtpl&amp;moddir=' . $tplfile->getVar( 'tpl_addon' ) . '&amp;tplset=' . urlencode( $tplfile->getVar( 'tpl_tplset' ) ), 2, _DBUPDATED );
        break;

    case 'index':
    case 'default':
        zarilia_cp_header();
/*
        zarilia_admin_menu(
            _MD_AD_MAINTENANCE_BOX, zariliaMainAction()
            );
*/
        $menu_handler->render( 0 );
        break;
}
zarilia_cp_footer();

?>