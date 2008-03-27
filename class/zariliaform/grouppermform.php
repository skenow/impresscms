<?php 
// $Id: grouppermform.php,v 1.2 2007/04/22 07:21:38 catzwolf Exp $
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

require_once ZAR_ROOT_PATH . '/class/zariliaform/formelement.php';
require_once ZAR_ROOT_PATH . '/class/zariliaform/formhidden.php';
require_once ZAR_ROOT_PATH . '/class/zariliaform/formbutton.php';
require_once ZAR_ROOT_PATH . '/class/zariliaform/formelementtray.php';
require_once ZAR_ROOT_PATH . '/class/zariliaform/form.php';

/**
 * Renders a form for setting addon specific group permissions
 * 
 * @author Kazumi Ono  
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 * @package kernel
 * @subpackage form
 */
class ZariliaGroupPermForm extends ZariliaForm {
    /**
     * Addons ID
     * 
     * @var int 
     */
    var $_modid;
    /**
     * Tree structure of items
     * 
     * @var array 
     */
    var $_itemTree;
    /**
     * Name of permission
     * 
     * @var string 
     */
    var $_permName;
    /**
     * Description of permission
     * 
     * @var string 
     */
    var $_permDesc;

    /**
     * Constructor
     */
    function ZariliaGroupPermForm( $title, $modid, $permname, $permdesc, $permgroup = "", $url = "", $saveurl = "" ) {
        if ( $saveurl == "" ) {
            $saveurl = ZAR_URL . '/addons/system/admin/groupperm.php';
        } 
        $this->ZariliaForm( $title, 'groupperm_form', $saveurl, 'post' );
        $this->_modid = intval( $modid );
        $this->_permName = $permname;
        $this->_permDesc = $permdesc;
        $this->_permGroup = $permgroup;
        $this->addElement( new ZariliaFormHidden( 'modid', $this->_modid ) );
        if ( $url != "" ) {
            $this->addElement( new ZariliaFormHidden( 'redirect_url', $url ) );
        } 
    } 

    /**
     * Adds an item to which permission will be assigned
     * 
     * @param string $itemName 
     * @param int $itemId 
     * @param int $itemParent 
     * @access public 
     */
    function addItem( $itemId, $itemName, $itemParent = 0 ) {
        $this->_itemTree[$itemParent]['children'][] = $itemId;
        $this->_itemTree[$itemId]['parent'] = $itemParent;
        $this->_itemTree[$itemId]['name'] = $itemName;
        $this->_itemTree[$itemId]['id'] = $itemId;
    } 

    /**
     * Loads all child ids for an item to be used in javascript
     * 
     * @param int $itemId 
     * @param array $childIds 
     * @access private 
     */
    function _loadAllChildItemIds( $itemId, &$childIds ) {
        if ( !empty( $this->_itemTree[$itemId]['children'] ) ) {
            $first_child = $this->_itemTree[$itemId]['children'];
            foreach ( $first_child as $fcid ) {
                array_push( $childIds, $fcid );
                if ( !empty( $this->_itemTree[$fcid]['children'] ) ) {
                    foreach ( $this->_itemTree[$fcid]['children'] as $_fcid ) {
                        array_push( $childIds, $_fcid );
                        $this->_loadAllChildItemIds( $_fcid, $childIds );
                    } 
                } 
            } 
        } 
    } 

    /**
     * Renders the form
     * 
     * @return string 
     * @access public 
     */
    /**
     * ZariliaGroupPermForm::render()
     * 
     * @return 
     */
    function render() {
        // load all child ids for javascript codes
        foreach ( array_keys( $this->_itemTree )as $item_id ) {
            $this->_itemTree[$item_id]['allchild'] = array();
            $this->_loadAllChildItemIds( $item_id, $this->_itemTree[$item_id]['allchild'] );
        } 

        $gperm_handler = &zarilia_gethandler( 'groupperm' );
        $member_handler = &zarilia_gethandler( 'member' );

        $glist = &$member_handler->getGroupList();
        if ( !$this->_permGroup ) {
            foreach ( array_keys( $glist ) as $i ) {
                // get selected item id(s) for each group
                $selected = $gperm_handler->getItemIds( $this->_permName, $i, $this->_modid );
                $ele = new ZariliaGroupFormCheckBox( $glist[$i], 'perms[' . $this->_permName . ']', $i, $selected );
                $ele->setOptionTree( $this->_itemTree );
                $this->addElement( $ele );
                unset( $ele );
            } 
        } else {
            $i = $this->_permGroup;
            $selected = $gperm_handler->getItemIds( $this->_permName, $i, $this->_modid );
            $ele = new ZariliaGroupFormCheckBox( $glist[$i], 'perms[' . $this->_permName . ']', $i, $selected );
            $ele->setOptionTree( $this->_itemTree );
            $this->addElement( $ele );
            unset( $ele );
        } 

        $tray = new ZariliaFormElementTray( '' );
        $tray->addElement( new ZariliaFormHidden( 'permGroup', $this->_permGroup ) );
        $tray->addElement( new ZariliaFormButton( '', 'submit', _SUBMIT, 'submit' ) );
        $tray->addElement( new ZariliaFormButton( '', 'reset', _CANCEL, 'reset' ) );
        $this->addElement( $tray );

        $ret = '<h4>' . $this->getTitle() . '</h4>';
        $ret .= '<div><b>' . $this->_permDesc . '</b></div><br />';
        $ret .= "<form name='" . $this->getName() . "' id='" . $this->getName() . "' action='" . $this->getAction() . "' method='" . $this->getMethod() . "'" . $this->getExtra() . ">\n";
        $ret .= "<table width='100%' cellspacing='1' class='outer'>\n";
        $elements = &$this->getElements();
        foreach ( array_keys( $elements ) as $i ) {
            if ( !is_object( $elements[$i] ) ) {
                $ret .= $elements[$i];
            } elseif ( !$elements[$i]->isHidden() ) {
                $ret .= "<tr valign='top' align='left'><td class='head'>" . $elements[$i]->getCaption();
                if ( $elements[$i]->getDescription() != '' ) {
                    $ret .= '<br /><br /><span style="font-weight: normal;">' . $elements[$i]->getDescription() . '</span>';
                } 
                $ret .= "</td>\n<td class='even'>\n" . $elements[$i]->render() . "\n</td></tr>\n";
            } else {
                $ret .= $elements[$i]->render();
            } 
        } 
        $ret .= '</table></form>';
        return $ret;
    } 
} 

/**
 * Renders checkbox options for a group permission form
 * 
 * @author Kazumi Ono  
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 * @package kernel
 * @subpackage form
 */
class ZariliaGroupFormCheckBox extends ZariliaFormElement {
    /**
     * Pre-selected value(s)
     * 
     * @var array ;
     */
    var $_value = array();
    /**
     * Group ID
     * 
     * @var int 
     */
    var $_groupId;
    /**
     * Option tree
     * 
     * @var array 
     */
    var $_optionTree;

    /**
     * Constructor
     */
    function ZariliaGroupFormCheckBox( $caption, $name, $groupId, $values = null ) {
        $this->setCaption( $caption );
        $this->setName( $name );
        if ( isset( $values ) ) {
            $this->setValue( $values );
        } 
        $this->_groupId = $groupId;
    } 

    /**
     * Sets pre-selected values
     * 
     * @param mixed $value A group ID or an array of group IDs
     * @access public 
     */
    function setValue( $value ) {
        if ( is_array( $value ) ) {
            foreach ( $value as $v ) {
                $this->setValue( $v );
            } 
        } else {
            $this->_value[] = $value;
        } 
    } 

    /**
     * Sets the tree structure of items
     * 
     * @param array $optionTree 
     * @access public 
     */
    function setOptionTree( &$optionTree ) {
        $this->_optionTree = &$optionTree;
    } 

    /**
     * Renders checkbox options for this group
     * 
     * @return string 
     * @access public 
     */
    function render() {
        $cols = 1;

        $ret = '
		<table width="100%" cellspacing="1" cellpadding="1">
		 <tr>
		  <td><b/>';
        //$ret .= _ALL . " </b><input id=\"" . $checkallbtn_id . "\" type=\"checkbox\" value=\"\" onclick=\"var optionids = new Array(" . $option_ids_str . "); zariliaCheckAllElements(optionids, '" . $checkallbtn_id . "');\" />";

        $ret .= '
		  <table width="100%" cellspacing="1" cellpadding="1">
		    <tr>';
        foreach ( $this->_optionTree[0]['children'] as $topitem ) {
            if ( $cols > 2 ) {
                $ret .= '</tr><tr>';
                $cols = 1;
            } 
            $tree = '<td valign="top">';
            $this->_renderOptionTree( $tree, $this->_optionTree[$topitem], '' );
            $ret .= $tree . '</td>';
            $cols++;
        } 
        $ret .= '</tr></table></td><td>';

        foreach ( array_keys( $this->_optionTree ) as $id ) {
            if ( !empty( $id ) ) {
                $option_ids[] = "'" . $this->getName() . '[groups][' . $this->_groupId . '][' . $id . ']' . "'";
            } 
        } 
        $checkallbtn_id = $this->getName() . '[checkallbtn][' . $this->_groupId . ']';
        $option_ids_str = implode( ', ', $option_ids );
        $ret .= '</td>
		        </tr>
				<tr>
				 <td><b>';
        $ret .= _ALL . " </b><input id=\"" . $checkallbtn_id . "\" type=\"checkbox\" value=\"\" onclick=\"var optionids = new Array(" . $option_ids_str . "); zariliaCheckAllElements(optionids, '" . $checkallbtn_id . "');\" />";
        $ret .= '</td></tr></table>';
        return $ret;
    } 

    /**
     * Renders checkbox options for an item tree
     * 
     * @param string $tree 
     * @param array $option 
     * @param string $prefix 
     * @param array $parentIds 
     * @access private 
     */
    function _renderOptionTree( &$tree, $option, $prefix, $parentIds = array() ) {
        $tree .= $prefix . "<input type=\"checkbox\" name=\"" . $this->getName() . "[groups][" . $this->_groupId . "][" . $option['id'] . "]\" id=\"" . $this->getName() . "[groups][" . $this->_groupId . "][" . $option['id'] . "]\" onclick=\""; 
        // If there are parent elements, add javascript that will
        // make them selected when this element is checked to make
        // sure permissions to parent items are added as well.
        foreach ( $parentIds as $pid ) {
            $parent_ele = $this->getName() . '[groups][' . $this->_groupId . '][' . $pid . ']';
            $tree .= "var ele = zariliaGetElementById('" . $parent_ele . "'); if(ele.checked != true) {ele.checked = this.checked;}";
        } 
        // If there are child elements, add javascript that will
        // make them unchecked when this element is unchecked to make
        // sure permissions to child items are not added when there
        // is no permission to this item.
        foreach ( $option['allchild'] as $cid ) {
            $child_ele = $this->getName() . '[groups][' . $this->_groupId . '][' . $cid . ']';
            $tree .= "var ele = zariliaGetElementById('" . $child_ele . "'); if(this.checked != true) {ele.checked = false;}";
        } 
        $tree .= '" value="1"';
        if ( in_array( $option['id'], $this->_value ) ) {
            $tree .= ' checked="checked"';
        } 
        $tree .= " />" . $option['name'] . "<input type=\"hidden\" name=\"" . $this->getName() . "[parents][" . $option['id'] . "]\" value=\"" . implode( ':', $parentIds ) . "\" /><input type=\"hidden\" name=\"" . $this->getName() . "[itemname][" . $option['id'] . "]\" value=\"" . htmlspecialchars( $option['name'] ) . "\" /><br />\n";
        if ( isset( $option['children'] ) ) {
            foreach ( $option['children'] as $child ) {
                array_push( $parentIds, $option['id'] );
                $this->_renderOptionTree( $tree, $this->_optionTree[$child], $prefix . '&nbsp;-', $parentIds );
            } 
        } 
    } 
} 

?>