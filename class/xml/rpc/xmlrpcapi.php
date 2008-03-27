<?php
// $Id: xmlrpcapi.php,v 1.1 2007/03/16 02:42:17 catzwolf Exp $
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



class ZariliaXmlRpcApi
{

    // reference to method parameters 
    var $params;

    // reference to xmlrpc document class object
    var $response;

    // reference to addon class object
    var $addon;

    // map between zarilia tags and blogger specific tags
    var $zariliaTagMap = array();

    // user class object
    var $user;
    
    var $isadmin = false;



    function ZariliaXmlRpcApi(&$params, &$response, &$addon)
    {
        $this->params =& $params;
        $this->response =& $response;
        $this->addon =& $addon;
    }

    function _setUser(&$user, $isadmin = false) 
    {
        if (is_object($user)) {
            $this->user =& $user;
            $this->isadmin = $isadmin;
        }
    }

    function _checkUser($username, $password)
    {
        if (isset($this->user)) {
            return true;
        }
		$member_handler =& zarilia_gethandler('member');
        $this->user =& $member_handler->loginUser(addslashes($username), addslashes($password));
        if (!is_object($this->user)) {
            unset($this->user);
            return false;
        }
		$addonperm_handler =& zarilia_gethandler('groupperm');
        if (!$addonperm_handler->checkRight('addon_read', $this->addon->getVar('mid'), $this->user->getGroups())) {
            unset($this->user);
            return false;
        }
        return true;
    }

    function _checkAdmin()
    {
        if ($this->isadmin) {
            return true;
        }
        if (!isset($this->user)) {
            return false;
        }
        if (!$this->user->isAdmin($this->addon->getVar('mid'))) {
            return false;
        }
        $this->isadmin = true;
        return true;
    }

    function &_getPostFields($post_id = null, $blog_id = null)
    {
        $ret = array();
        $ret['title'] = array('required' => true, 'form_type' => 'textbox', 'value_type' => 'text');
        $ret['hometext'] = array('required' => false, 'form_type' => 'textarea', 'data_type' => 'textarea');
        $ret['moretext'] = array('required' => false, 'form_type' => 'textarea', 'data_type' => 'textarea');
        $ret['categories'] = array('required' => false, 'form_type' => 'select_multi', 'data_type' => 'array');
        /*
        if (!isset($blog_id)) {
            if (!isset($post_id)) {
                return false;
            }
            $itemman =& $this->mf->get(MANAGER_ITEM);
            $item =& $itemman->get($post_id);
            $blog_id = $item->getVar('sect_id');
        }
        $sectman =& $this->mf->get(MANAGER_SECTION);
        $this->section =& $sectman->get($blog_id);
        $ret =& $this->section->getVar('sect_fields');
        */
        return $ret;
    }

    function _setZariliaTagMap($zariliatag, $blogtag)
    {
        if (trim($blogtag) != '') {
            $this->zariliaTagMap[$zariliatag] = $blogtag;
        }
    }

    function _getZariliaTagMap($zariliatag)
    {
        if (isset($this->zariliaTagMap[$zariliatag])) {
            return $this->zariliaTagMap[$zariliatag];
        }
        return $zariliatag;
    }

    function _getTagCdata(&$text, $tag, $remove = true)
    {
        $ret = '';
        $match = array();
        if (preg_match("/\<".$tag."\>(.*)\<\/".$tag."\>/is", $text, $match)) {
            if ($remove) {
                $text = str_replace($match[0], '', $text);
            }
            $ret = $match[1];
        }
        return $ret;
    }

    // kind of dirty method to load ZARILIA API and create a new object thereof
    // returns itself if the calling object is ZARILIA API 
    function &_getZariliaApi(&$params)
    {
        if (strtolower(get_class($this)) != 'zariliaapi') {
            require_once(ZAR_ROOT_PATH.'/class/xml/rpc/zariliaapi.php');
            return new ZariliaApi($params, $this->response, $this->addon);
        } else {
            return $this;
        }
    }
}
?>