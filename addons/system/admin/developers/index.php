<?php
// $Id: index.php,v 1.3 2007/04/21 09:42:14 catzwolf Exp $
// ------------------------------------------------------------------------ //
// Zarilia - PHP Content Management System                      			//
// Copyright (c) 2007 Zarilia                           					//
// 																			//
// Authors: 																//
// John Neill ( AKA Catzwolf )                                     			//
// Raimondas Rimkevicius ( AKA Mekdrop )									//
// 							 												//
// URL: http:www.zarilia.com 												//
// Project: Zarilia Project                                               	//
// -------------------------------------------------------------------------//
$op = zarilia_cleanRequestVars( $_REQUEST, 'op', 'default', XOBJ_DTYPE_TXTBOX );
$fct = zarilia_cleanRequestVars( $_REQUEST, 'fct', 'category', XOBJ_DTYPE_TXTBOX );
$nav['start'] = zarilia_cleanRequestVars( $_REQUEST, 'start', 0 );
$nav['sort'] = zarilia_cleanRequestVars( $_REQUEST, 'sort', 'cid' );
$nav['order'] = zarilia_cleanRequestVars( $_REQUEST, 'order', 'DESC', XOBJ_DTYPE_TXTBOX );

$confcat_id = zarilia_cleanRequestVars( $_REQUEST, 'confcat_id', 1 );

function category_pulldown_menu( $confcat_id = 0 ) {
    $confcat_handler = &zarilia_gethandler( 'configcategory' );
    $menu_config = &$confcat_handler->getObjects();
    foreach ( $menu_config as $menuConfig ) {
        $menucat_name = $menuConfig->getVar( 'confcat_id' );
        $menu_items[$menucat_name] = constant( $menuConfig->getVar( 'confcat_name' ) );
    }
    $cat_select = "<select size=\"1\" name=\"category\" onchange=\"location='" . ZAR_URL . "/addons/system/index.php?fct=developers&amp;op=start&confcat_id='+this.options[this.selectedIndex].value\">";
    foreach ( $menu_items as $k => $v ) {
        $sel = '';
        if ( $k == $confcat_id ) {
            $sel = ' selected="selected"';
        }
        $cat_select .= '<option value="' . $k . '"' . $sel . '>' . $v . '</option>';
    }
    $cat_select .= '</select> ';
    echo "<div style='padding-bottom: 12px;'>" . $cat_select . "</div>";
}

switch ( strtolower( $op ) ) {
    case 'save':
        $config_handler = &zarilia_gethandler( 'config' );
        $conf_id = zarilia_cleanRequestVars( $_REQUEST, 'conf_id', 1 );
        $config_obj = ( $conf_id != null ) ? $config_handler->getConfig( $conf_id ) : $config_handler->createConfig();
        $config_obj->setVars( $_REQUEST );
        $config_obj->setVar( 'conf_sectid ', 1 );
        if ( $config_handler->insertConfig( $config_obj, false ) ) {
            redirect_header( zarilia_getenv( 'PHP_SELF' ) . "?fct=" . $fct, 1, _DBUPDATED );
        } else {
            zarilia_cp_header();
            zarilia_admin_menu( '', _MD_AM_DEVELOPERS_HEADING, $op );
            zarilia_display_Errors( $config_obj->getErrors() );
        }
        break;

    case "delete":
        /**
         * create new mimetype object
         */
        $config_handler = &zarilia_gethandler( 'config' );
        $conf_id = zarilia_cleanRequestVars( $_REQUEST, 'conf_id', 0 );

        $config_obj = $config_handler->getConfig( $conf_id );
        if ( !is_object( $config_obj ) ) {
            redirect_header( zarilia_getenv( 'PHP_SELF' ) . "?fct=" . $fct, 1, sprintf( _MD_AM_DEVELOPERS_ERROR, $_REQUEST['conf_name'] ) );
        }

        $ok = zarilia_cleanRequestVars( $_REQUEST, 'ok', 0 );
        if ( $ok ) {
            $config_handler->deleteConfig( $config_obj, false );
            redirect_header( zarilia_getenv( 'PHP_SELF' ) . "?fct=" . $fct, 1, sprintf( _MD_AM_DEVELOPERS_CONFIGDELETED, $config_obj->getVar( 'conf_name' ) ) );
        } else {
            zarilia_cp_header();
            zarilia_admin_menu( '', _MD_AM_DEVELOPERS_HEADING, $op );
            zarilia_confirm(
                array( 'op' => 'delete',
                    'conf_id' => $config_obj->getVar( 'conf_id' ),
                    'ok' => 1,
                    'conf_name' => $config_obj->getVar( 'conf_name' ) ),
                zarilia_getenv( 'PHP_SELF' ) . "?fct=" . $fct, _MD_AM_DEVELOPERS_DELETETHIS . "<br /><br>" . $config_obj->getVar( 'conf_name' ), _DELETE );
        }
        break;

    case 'edit':
        include_once ZAR_ROOT_PATH . "/class/zariliaformloader.php";

        $config_handler = &zarilia_gethandler( 'config' );
        $confcat_handler = &zarilia_gethandler( 'configcategory' );
		$menu_config = &$confcat_handler->getObjects();

		$conf_id = zarilia_cleanRequestVars( $_REQUEST, 'conf_id', 1 );
        $config = ( $conf_id == 0 ) ? $config_handler->createConfig() : $config_handler->getConfig( $conf_id );

        zarilia_cp_header();
        zarilia_admin_menu( '', _MD_AM_DEVELOPERS_HEADING, $op );

        $forminfo = ( $conf_id == 0 ) ? _MD_AM_CONF_CREATEF : _MD_AM_CONF_MODIFYF;
        $sform = new ZariliaThemeForm( $forminfo , "op", zarilia_getenv( 'PHP_SELF' ) . "?fct=developers" );
        foreach ( $menu_config as $menuConfig ) {
            $menucat_name = $menuConfig->getVar( 'confcat_id' );
            $menu_items[$menucat_name] = constant( $menuConfig->getVar( 'confcat_name' ) );
        }
        $conf_cattype = new ZariliaFormSelect( _MD_AM_ECONF_FORMTYPE, 'conf_catid', $config->getVar( 'conf_catid' ), '', '', 0 );
        $conf_cattype->addOptionArray( $menu_items );
        $sform->addElement( $conf_cattype );
        /**
         */
        $conf_name = new ZariliaFormText( _MD_AM_ECONF_NAME, 'conf_name', 25, 60, $config->getVar( 'conf_name' ), 'e' );
        // $title -> setDescription( '' );
        $conf_name->setRequired( true );
        $sform->addElement( $conf_name, true );

        $conf_value = new ZariliaFormTextArea( _MD_AM_ECONF_VALUE, 'conf_value', $config->getVar( 'conf_value' ), 7, 60, 0, 0 );
        // $title -> setDescription( '' );
        $sform->addElement( $conf_value, false );

        /**
         */
        $_conf_title = $config->getVar( 'conf_title' );
        $conf_title_tray = new ZariliaFormElementTray( _MD_AM_ECONF_TITLE, '', '', false );
        $conf_title = new ZariliaFormText( _MD_AM_ECONF_TITLE, 'conf_title', 25, 60, $_conf_title, 'e' );
        $conf_title->setRequired( true );
        $conf_title_tray->addElement( $conf_title, true );
        $conf_title_tray->addElement( new ZariliaFormlabel( '', '<div style="padding-top: 12px;">' . constant( $_conf_title ) . '</div>' ) );
        $sform->addElement( $conf_title_tray, true );

        /**
         */
        $_conf_desc = $config->getVar( 'conf_desc' );
        $conf_desc_tray = new ZariliaFormElementTray( _MD_AM_ECONF_TITLE, '', '', false );
        $conf_desc = new ZariliaFormText( _MD_AM_ECONF_TITLE, 'conf_desc', 25, 60, $_conf_desc, 'e' );
        // $conf_desc -> setDescription();
        $conf_desc->setRequired( true );
        $conf_desc_tray->addElement( $conf_desc, true );
        $conf_desc_tray->addElement( new ZariliaFormlabel( '', '<div style="padding-top: 12px;">' . constant( $_conf_desc ) . '</div>' ) );
        $sform->addElement( $conf_desc_tray, true );

        /**
         */
        $conf_formtype_array = array( 'editor', 'editor_multi', 'zariliatextarea', 'htmltextarea', 'textarea', 'select', 'select_multi', 'yesno', 'theme', 'theme_multi', 'tplset', 'timezone', 'language', 'charset', 'startpage', 'group', 'group_multi', 'user', 'user_multi', 'addon_cache', 'site_cache', 'password', 'textbox' );
        $conf_formtype = new ZariliaFormSelect( _MD_AM_ECONF_FORMTYPE, 'conf_formtype', $config->getVar( 'conf_formtype' ), '', '', 0 );
        $conf_formtype->addOptionArray( $conf_formtype_array, false );
        $sform->addElement( $conf_formtype );

        $conf_valuetype_array = array( 'textbox', 'array', 'int', 'other', 'text' );
        $conf_valuetype = new ZariliaFormSelect( _MD_AM_ECONF_VALUETYPE, 'conf_valuetype', $config->getVar( 'conf_valuetype' ), '', '', 0 );
        $conf_valuetype->addOptionArray( $conf_valuetype_array, false );
        $sform->addElement( $conf_valuetype );
        /**
         */
        $conf_order = new ZariliaFormText( _MD_AM_ECONF_ORDER, 'conf_order', 10, 10, $config->getVar( 'conf_order' ), 'e' );
        // $title -> setDescription( '' );
        $conf_order->setRequired( true );
        $sform->addElement( $conf_order, true );
        /**
         */
        $sform->addElement( new ZariliaFormHidden( 'op', 'save' ) );
        $sform->addElement( new ZariliaFormHidden( 'conf_id', $config->getVar( 'conf_id' ) ) );
        $sform->addElement( new ZariliaFormHidden( 'conf_sectid', 1 ) );

        $button_tray = new ZariliaFormElementTray( '', '' );
        $button_tray->addElement( new ZariliaFormButton( '', 'save', _SUBMIT, 'submit' ) );
        $button_tray->addElement( new ZariliaFormButton( '', 'reset', _RESET, 'reset' ) );
        $button_tray->addElement( new ZariliaFormButton( '', 'cancel', _CANCEL, 'button', 'onClick="history.go(-1);return true;"' ) );
        $sform->addElement( $button_tray, false );
        $sform->display();
        unset( $hidden );
        break;

    case 'default':
    default:
        $heading_arr = array( "conf_catid" => 0, "conf_name" => 0, "conf_value" => 0, "conf_weight" => 0, "op" => 0 );

        $config_handler = &zarilia_gethandler( 'config' );
        $criteria = new CriteriaCompo();
        $criteria->add( new Criteria( 'conf_modid', 0 ) );
        $criteria->add( new Criteria( 'conf_catid', $confcat_id ) );
        $config = &$config_handler->getConfigs( $criteria );
        $confcount = count( $config );

        zarilia_cp_header();
        zarilia_admin_menu( _MD_AD_ACTION_BOX, array( $addonversion['adminpath'] . "&amp;op=edit&conf_catid=" . $confcat_id . "&conf_id=0" => 'New Config' ));
        /**
         * Shopw category heading
         */
        category_pulldown_menu( $confcat_id );

		$button = array( 'edit', 'delete', 'clone' );
		//zarilia_headings( $heading_arr , true, '_MD_AM_', $fct );
        /**
         * show listings
         */
        if ( !$confcount ) {
            zarilia_noSelection( count( $heading_arr ) );
        } else {
            for ( $i = 0; $i < $confcount; $i++ ) {
                $id = $config[$i]->getVar( 'conf_id' );
                if (is_array( $config[$i]->getConfValueForOutput() )) {
                    $_value = '';
					foreach ($config[$i]->getConfValueForOutput() as  $k => $v )
						$_value = "<div>key: ".$k." value:".$v."</div>";
                } else {
					$_value = $config[$i]->getConfValueForOutput();
				}
				echo "
				<tr style='text-align: center;'>\n
                <td class='head'>" . $id . "</td>\n
                <td class='even' style='text-align: left;'>" . $config[$i]->getVar( 'conf_name' ) . "</td>\n
                <td class='even' style='text-align: left;'>" . $_value. "</td>\n
                <td class='even'>" . $config[$i]->getVar( 'conf_order' ) . "</td>\n
                <td class='even'>
				  <a href='" . zarilia_getenv( 'PHP_SELF' ) . "?fct=" . $fct . "&amp;op=edit&amp;conf_id=" . $id . "'>" . zarilia_img_show( 'edit', _EDIT, 'middle' ) . "</a>
				  <a href='" . zarilia_getenv( 'PHP_SELF' ) . "?fct=" . $fct . "&amp;op=delete&amp;conf_id=" . $id . "'>" . zarilia_img_show( 'delete', _DELETE, 'middle' ) . "</a>
				 </td>\n
                </tr>";
            }
        }
        zarilia_listing_footer( count( $heading_arr ) );
		zarilia_cp_legend( $button );
} // switch
zarilia_cp_footer();

?>