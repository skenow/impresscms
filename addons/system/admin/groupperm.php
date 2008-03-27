<?php
// $Id: groupperm.php,v 1.2 2007/03/30 22:04:38 catzwolf Exp $
include '../../../include/cp_header.php';

$modid = zarilia_cleanRequestVars( $_REQUEST, 'modid', 0 );
$permGroup = zarilia_cleanRequestVars( $_REQUEST, 'permGroup', null );

// we dont want system addon permissions to be changed here
if ( $modid <= 1 || !is_object( $zariliaUser ) || !$zariliaUser -> isAdmin( $modid ) ) {
    redirect_header( ZAR_URL . '/index.php', 1, _NOPERM );
    exit();
}

$addon_handler = &zarilia_gethandler( 'addon' );
$addon = &$addon_handler -> get( $modid );
if ( !is_object( $addon ) || !$addon -> getVar( 'isactive' ) ) {
    redirect_header( ZAR_URL . '/index.php', 1, _ADDONNOEXIST );
    exit();
}

$member_handler = &zarilia_gethandler( 'member' );
$group_list = &$member_handler -> getGroupList();
if ( is_array( $_POST['perms'] ) && !empty( $_POST['perms'] ) ) {
    $gperm_handler = zarilia_gethandler( 'groupperm' );
    foreach ( $_POST['perms'] as $perm_name => $perm_data ) {
        if ( false != $gperm_handler -> deleteByAddon( $modid, $perm_name, 0, $permGroup ) ) {
            foreach ( $perm_data['groups'] as $group_id => $item_ids ) {
                $msg[] .= sprintf( _MD_AM_PERM_ADDED, '<b>' . $group_list[$group_id] . '</b>' );
                foreach ( $item_ids as $item_id => $selected ) {
                    if ( $selected == 1 ) {
                        // make sure that all parent ids are selected as well
                        if ( $perm_data['parents'][$item_id] != '' ) {
                            $parent_ids = explode( ':', $perm_data['parents'][$item_id] );
                            foreach ( $parent_ids as $pid ) {
                                if ( $pid != 0 && !in_array( $pid, array_keys( $item_ids ) ) ) {
                                    // one of the parent items were not selected, so skip this item
                                    $msg[] = sprintf( _MD_AM_PERMADDNG, '<b>' . $perm_name . '</b>', '<b>' . $perm_data['itemname'][$item_id] . '</b>', '<b>' . $group_list[$group_id] . '</b>' ) . ' (' . _MD_AM_PERMADDNGP . ')';
                                    continue 2;
                                }
                            }
                        }
                        $gperm = &$gperm_handler -> create();
                        $gperm -> setVar( 'gperm_groupid', $group_id );
                        $gperm -> setVar( 'gperm_name', $perm_name );
                        $gperm -> setVar( 'gperm_modid', $modid );
                        $gperm -> setVar( 'gperm_itemid', $item_id );
                        if ( !$gperm_handler -> insert( $gperm ) ) {
                            $msg[] = sprintf( _MD_AM_PERMADDNG, $item_id, $perm_data['itemname'][$item_id] );
                        } else {
                            $msg[] = sprintf( _MD_AM_PERMADDOK, $item_id, $perm_data['itemname'][$item_id] );
                        }
                        unset( $gperm );
                    }
                }
            }
        } else {+ $msg[] = sprintf( _MD_AM_PERMRESETNG, $addon -> getVar( 'name' ) . '(' . $perm_name . ')' );
        }
    }
}

$backlink = ZAR_URL . '/index.php';
if ( $addon -> getVar( 'hasadmin' ) ) {
    $adminindex = isset( $_POST['redirect_url'] ) ? $_POST['redirect_url'] : $addon -> getInfo( 'adminindex' );
    if ( $adminindex ) {
        $backlink = ZAR_URL . '/addons/' . $addon -> getVar( 'dirname' ) . '/' . $adminindex;
    }
}

$msg[] = '<br /><br /><a href="' . $backlink . '">' . _BACK . '</a>';
zarilia_cp_header();
echo "<h3 style='margin-top: 0px; color: #0A3760; text-align: left;'>" . _MD_AM_PERMS . "</h3>";
echo "<h4 style='text-align: left;'>" . _MD_AM_PERM_ADD . "</h4>";
zarilia_result( $msg );
zarilia_cp_footer();

?>