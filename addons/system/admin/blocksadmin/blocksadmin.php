<?php
// $Id: blocksadmin.php,v 1.2 2007/04/21 09:41:58 catzwolf Exp $
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
/*
*  check if the user is authorised
*/
include_once ZAR_ROOT_PATH . '/class/zariliablock.php';

function save_block( $bside, $bweight, $btitle, $bcontent, $bctype, $baddon, $bcachetime ) {
    global $zariliaUser;
    if ( empty( $baddon ) ) {
        zarilia_cp_header();
        zarilia_error( sprintf( _AM_NOTSELNG, _AM_VISIBLEIN ) );
        zarilia_cp_footer();
        exit();
    }
    $myblock = new ZariliaBlock();
    $myblock->setVar( 'side', $bside );
    $myblock->setVar( 'weight', $bweight );
    $myblock->setVar( 'weight', $bweight );
    $myblock->setVar( 'title', $btitle );
    $myblock->setVar( 'content', $bcontent );
    $myblock->setVar( 'c_type', $bctype );
    $myblock->setVar( 'block_type', 'C' );
    $myblock->setVar( 'bcachetime', $bcachetime );
    switch ( $bctype ) {
        case 'H':
            $name = _AM_CUSTOMHTML;
            break;
        case 'P':
            $name = _AM_CUSTOMPHP;
            break;
        case 'S':
            $name = _AM_CUSTOMSMILE;
            break;
        default:
            $name = _AM_CUSTOMNOSMILE;
            break;
    }
    $myblock->setVar( 'name', $name );
    $newid = $myblock->store();

    if ( !$newid ) {
        zarilia_cp_header();
        $myblock->getHtmlErrors();
        zarilia_cp_footer();
        exit();
    }

    global $zariliaDB; $db = &$zariliaDB;
    foreach ( $baddon as $bmid ) {
        $sql = 'INSERT INTO ' . $db->prefix( 'block_addon_link' ) . ' (block_id, addon_id) VALUES (' . $newid . ', ' . intval( $bmid ) . ')';
        $db->Execute( $sql );
    }
    $groups = &$zariliaUser->getGroups();
    $count = count( $groups );
    for ( $i = 0; $i < $count; $i++ ) {
        $sql = "INSERT INTO " . $db->prefix( 'group_permission' ) . " (gperm_groupid, gperm_itemid, gperm_name, gperm_modid) VALUES (" . $groups[$i] . ", " . $newid . ", 'block_read', 1)";
        $db->Execute( $sql );
    }
    redirect_header( 'index.php?fct=blocksadmin&amp;t=' . time(), 1, _DBUPDATED );
    exit();
}

function edit_block( $bid ) {
    if ( $bid ) {
        $myblock = new ZariliaBlock( $bid );

//		var_dump($myblock);

        global $zariliaDB; $db = &$zariliaDB;
        $sql = 'SELECT addon_id FROM ' . $db->prefix( 'block_addon_link' ) . ' WHERE block_id=' . intval( $bid );
        $result = $db->Execute( $sql );

        $addons = array();
        while ( $row = $result->FetchRow() ) {
            $addons[] = intval( $row['addon_id'] );
        }
        $is_custom = ( $myblock->getVar( 'block_type' ) == 'C' || $myblock->getVar( 'block_type' ) == 'E' ) ? true : false;
        $block = array( 'form_title' => _AM_EDITBLOCK,
            'name' => $myblock->getVar( 'name' ),
            'side' => $myblock->getVar( 'side' ),
            'weight' => $myblock->getVar( 'weight' ),
            'title' => $myblock->getVar( 'title', 'E' ),
            'content' => $myblock->getVar( 'content', 'E' ),
            'addons' => $addons,
            'is_custom' => $is_custom,
            'ctype' => $myblock->getVar( 'c_type' ),
            'cachetime' => $myblock->getVar( 'bcachetime' ),
            'op' => 'update',
            'bid' => $myblock->getVar( 'bid' ),
            'edit_form' => $myblock->getOptions(),
            'template' => $myblock->getVar( 'template' ),
            'options' => $myblock->getVar( 'options' )
            );
    } else {
        $block = array( 'form_title' => _AM_ADDBLOCK, 'side' => 9, 'weight' => 0, 'title' => '', 'content' => '', 'addons' => array( -1 ), 'is_custom' => true, 'ctype' => 'H', 'cachetime' => 0, 'op' => 'save', 'edit_form' => false );
    }
    include ZAR_ROOT_PATH . '/addons/system/admin/blocksadmin/blockform.php';
    $form->display();
}

function update_block( $bid, $bside, $bweight, $btitle, $bcontent, $bctype, $bcachetime, $baddon, $options = array() ) {
    global $zariliaConfig;
    if ( empty( $baddon ) ) {
        zarilia_cp_header();
        zarilia_error( sprintf( _AM_NOTSELNG, _AM_VISIBLEIN ) );
        zarilia_cp_footer();
        exit();
    }
    $myblock = new ZariliaBlock( $bid );
    $myblock->setVar( 'side', $bside );
    $myblock->setVar( 'weight', $bweight );
    $myblock->setVar( 'title', $btitle );
    $myblock->setVar( 'content', $bcontent );
    $myblock->setVar( 'bcachetime', $bcachetime );
    if ( isset( $options ) ) {
        $options_count = count( $options );
        if ( $options_count > 0 ) {
            // Convert array values to comma-separated
            for ( $i = 0; $i < $options_count; $i++ ) {
                if ( is_array( $options[$i] ) ) {
                    $options[$i] = implode( ',', $options[$i] );
                }
            }
            $options = implode( '|', $options );
            $myblock->setVar( 'options', $options );
        }
    }
    if ( $myblock->getVar( 'block_type' ) == 'C' ) {
        switch ( $bctype ) {
            case 'H':
                $name = _AM_CUSTOMHTML;
                break;
            case 'P':
                $name = _AM_CUSTOMPHP;
                break;
            case 'S':
                $name = _AM_CUSTOMSMILE;
                break;
            default:
                $name = _AM_CUSTOMNOSMILE;
                break;
        }
        $myblock->setVar( 'name', $name );
        $myblock->setVar( 'c_type', $bctype );
    } else {
        $myblock->setVar( 'c_type', 'H' );
    }
    $msg = _DBUPDATED;
    if ( $myblock->store() != false ) {
        global $zariliaDB; $db = &$zariliaDB;
        $sql = sprintf( "DELETE FROM %s WHERE block_id = %u", $db->prefix( 'block_addon_link' ), $bid );
        $db->Execute( $sql );
        foreach ( $baddon as $bmid ) {
            $sql = sprintf( "INSERT INTO %s (block_id, addon_id) VALUES (%u, %d)", $db->prefix( 'block_addon_link' ), $bid, intval( $bmid ) );
            $db->Execute( $sql );
        }

        include_once ZAR_ROOT_PATH . '/class/template.php';
        $zariliaTpl = new ZariliaTpl();
        $zariliaTpl->zarilia_setCaching( 2 );
        if ( $myblock->getVar( 'template' ) != '' ) {
            if ( $zariliaTpl->is_cached( 'db:' . $myblock->getVar( 'template' ), 'blk_' . $myblock->getVar( 'bid' ) ) ) {
                if ( !$zariliaTpl->clear_cache( 'db:' . $myblock->getVar( 'template' ), 'blk_' . $myblock->getVar( 'bid' ) ) ) {
                    $msg = 'Unable to clear cache for block ID ' . $bid;
                }
            }
        } else {
            if ( $zariliaTpl->is_cached( 'db:system_dummy.html', 'blk_' . $bid ) ) {
                if ( !$zariliaTpl->clear_cache( 'db:system_dummy.html', 'blk_' . $bid ) ) {
                    $msg = 'Unable to clear cache for block ID ' . $bid;
                }
            }
        }
    } else {
        $msg = 'Failed update of block. ID:' . $bid;
    }
    redirect_header( 'index.php?fct=blocksadmin&amp;t=' . time(), 1, $msg );
    exit();
}

function delete_block( $bid ) {
    $myblock = new ZariliaBlock( $bid );
    if ( $myblock->getVar( 'block_type' ) == 'S' ) {
        $message = _AM_SYSTEMCANT;
        redirect_header( 'index.php?fct=blocksadmin', 4, $message );
        exit();
    } elseif ( $myblock->getVar( 'block_type' ) == 'M' ) {
        $message = _AM_ADDONCANT;
        redirect_header( 'index.php?fct=blocksadmin', 4, $message );
        exit();
    } else {
        zarilia_confirm( array( 'fct' => 'blocksadmin', 'op' => 'delete_ok', 'bid' => $myblock->getVar( 'bid' ) ), 'index.php', sprintf( _AM_RUSUREDEL, $myblock->getVar( 'title' ) ) );
    }
}

function delete_block_ok( $bid ) {
    $myblock = new ZariliaBlock( $bid );
    $myblock->delete();
    if ( $myblock->getVar( 'template' ) != '' ) {
        $tplfile_handler = &zarilia_gethandler( 'tplfile' );
        $btemplate = &$tplfile_handler->find( $GLOBALS['zariliaConfig']['template_set'], 'block', $bid );
        if ( count( $btemplate ) > 0 ) {
            $tplman->delete( $btemplate[0] );
        }
    }
    redirect_header( 'index.php?fct=blocksadmin&amp;t=' . time(), 1, _DBUPDATED );
    exit();
}

function order_block( $bid, $weight, $side, $bcachetime ) {
    $myblock = new ZariliaBlock( $bid );
    $myblock->setVar( 'weight', $weight );
    $myblock->setVar( 'side', $side );
    $myblock->setVar( 'bcachetime', $bcachetime );
    $myblock->store();
}

function clone_block( $bid ) {
    global $zariliaConfig;
    zarilia_cp_header();
    $myblock = new ZariliaBlock( $bid );
    global $zariliaDB; $db = &$zariliaDB;
    $sql = 'SELECT addon_id FROM ' . $db->prefix( 'block_addon_link' ) . ' WHERE block_id=' . intval( $bid );
    $result = $db->Execute( $sql );
    $addons = array();
    while ( $row = $result->FetchRow() ) {
        $addons[] = intval( $row['addon_id'] );
    }
    $is_custom = ( $myblock->getVar( 'block_type' ) == 'C' || $myblock->getVar( 'block_type' ) == 'E' ) ? true : false;
    $block = array( 'form_title' => _AM_CLONEBLOCK, 'name' => $myblock->getVar( 'name' ), 'side' => $myblock->getVar( 'side' ), 'weight' => $myblock->getVar( 'weight' ), 'content' => $myblock->getVar( 'content', 'N' ), 'addons' => $addons, 'is_custom' => $is_custom, 'ctype' => $myblock->getVar( 'c_type' ), 'cachetime' => $myblock->getVar( 'bcachetime' ), 'op' => 'clone_ok', 'bid' => $myblock->getVar( 'bid' ), 'edit_form' => $myblock->getOptions(), 'template' => $myblock->getVar( 'template' ), 'options' => $myblock->getVar( 'options' ) );
    echo '<a href="index.php?fct=blocksadmin">' . _AM_BADMIN . '</a>&nbsp;<span style="font-weight:bold;">&raquo;&raquo;</span>&nbsp;' . _AM_CLONEBLOCK . '<br /><br />';
    include ZAR_ROOT_PATH . '/addons/system/admin/blocksadmin/blockform.php';
    $form->display();
    zarilia_cp_footer();
    exit();
}

function clone_block_ok( $bid, $bside, $bweight, $bcachetime, $baddon, $options ) {
    global $zariliaUser;
    $block = new ZariliaBlock( $bid );
    $clone = &$block->zariliaClone();
    if ( empty( $baddon ) ) {
        zarilia_cp_header();
        zarilia_error( sprintf( _AM_NOTSELNG, _AM_VISIBLEIN ) );
        zarilia_cp_footer();
        exit();
    }
    $clone->setVar( 'side', $bside );
    $clone->setVar( 'weight', $bweight );
    $clone->setVar( 'content', $bcontent );
    // $clone->setVar('title', $btitle);
    $clone->setVar( 'bcachetime', $bcachetime );
    if ( isset( $options ) && ( count( $options ) > 0 ) ) {
        $options = implode( '|', $options );
        $clone->setVar( 'options', $options );
    }
    $clone->setVar( 'bid', 0 );
    if ( $block->getVar( 'block_type' ) == 'C' || $block->getVar( 'block_type' ) == 'E' ) {
        $clone->setVar( 'block_type', 'E' );
    } else {
        $clone->setVar( 'block_type', 'D' );
    }
    $newid = $clone->store();
    if ( !$newid ) {
        zarilia_cp_header();
        $clone->getHtmlErrors();
        zarilia_cp_footer();
        exit();
    }

    if ( $clone->getVar( 'template' ) != '' ) {
        $tplfile_handler = &zarilia_gethandler( 'tplfile' );
        $btemplate = &$tplfile_handler->find( $GLOBALS['zariliaConfig']['template_set'], 'block', $bid );
        if ( count( $btemplate ) > 0 ) {
            $tplclone = &$btemplate[0]->zariliaClone();
            $tplclone->setVar( 'tpl_id', 0 );
            $tplclone->setVar( 'tpl_refid', $newid );
            $tplman->insert( $tplclone );
        }
    }

    global $zariliaDB; $db = &$zariliaDB;
    foreach ( $baddon as $bmid ) {
        $sql = 'INSERT INTO ' . $db->prefix( 'block_addon_link' ) . ' (block_id, addon_id) VALUES (' . $newid . ', ' . $bmid . ')';
        $db->Execute( $sql );
    }
    $groups = &$zariliaUser->getGroups();
    $count = count( $groups );
    for ( $i = 0; $i < $count; $i++ ) {
        $sql = "INSERT INTO " . $db->prefix( 'group_permission' ) . " (gperm_groupid, gperm_itemid, gperm_modid, gperm_name) VALUES (" . $groups[$i] . ", " . $newid . ", 1, 'block_read')";
        $db->Execute( $sql );
    }
    redirect_header( 'index.php?fct=blocksadmin&amp;t=' . time(), 1, _DBUPDATED );
}

?>