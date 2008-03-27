t_name' );
    $tblColors[$menucat_name] = 'show&confcat_id=' . $menuConfig -> getVar( 'confcat_id' );
} 
unset( $menu_config );
include ZAR_ROOT_PATH . '/include/comment_edit.php';

?>