<?php
// $Id: bloggerapi.php,v 1.1 2007/03/16 02:42:17 catzwolf Exp $
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

if (!defined('ZAR_ROOT_PATH')) {
	die("ZARILIA root path not defined");
}
require_once ZAR_ROOT_PATH.'/class/xml/rpc/xmlrpcapi.php';

class BloggerApi extends ZariliaXmlRpcApi
{

    function BloggerApi(&$params, &$response, &$addon)
    {
        $this->ZariliaXmlRpcApi($params, $response, $addon);
        $this->_setZariliaTagMap('storyid', 'postid');
        $this->_setZariliaTagMap('published', 'dateCreated');
        $this->_setZariliaTagMap('uid', 'userid');
    }

    function newPost()
    {
        if (!$this->_checkUser($this->params[2], $this->params[3])) {
            $this->response->add(new ZariliaXmlRpcFault(104));
        } else {
            if (!$fields =& $this->_getPostFields(null, $this->params[1])) {
                $this->response->add(new ZariliaXmlRpcFault(106));
            } else {
                $missing = array();
                $post = array();
                foreach ($fields as $tag => $detail) {
                    $maptag = $this->_getZariliaTagMap($tag);
                    $data = $this->_getTagCdata($this->params[4], $maptag, true);
                    if (trim($data) == ''){
                        if ($detail['required']) {
                            $missing[] = $maptag;
                        }
                    } else {
                        $post[$tag] = $data;
                    }
                }
                if (count($missing) > 0) {
                    $msg = '';
                    foreach ($missing as $m) {
                        $msg .= '<'.$m.'> ';
                    }
                    $this->response->add(new ZariliaXmlRpcFault(109, $msg));
                } else {
                    $newparams = array();
                    // Zarilia Api ignores App key
                    $newparams[0] = $this->params[1];
                    $newparams[1] = $this->params[2];
                    $newparams[2] = $this->params[3];
                    foreach ($post as $key => $value) {
                        $newparams[3][$key] =& $value;
                        unset($value);
                    }
                    $newparams[3]['zarilia_text'] =& $this->params[4];
                    $newparams[4] = $this->params[5];
                    $zariliaapi =& $this->_getZariliaApi($newparams);
                    $zariliaapi->_setUser($this->user, $this->isadmin);
                    $zariliaapi->newPost();
                }
            }
        }
    }

    function editPost()
    {
        if (!$this->_checkUser($this->params[2], $this->params[3])) {
            $this->response->add(new ZariliaXmlRpcFault(104));
        } else {
            if (!$fields =& $this->_getPostFields($this->params[1])) {
            } else {
                $missing = array();
                $post = array();
                foreach ($fields as $tag => $detail) {
                    $data = $this->_getTagCdata($this->params[4], $tag, true);
                    if (trim($data) == ''){
                        if ($detail['required']) {
                            $missing[] = $tag;
                        }
                    } else {
                        $post[$tag] = $data;
                    }
                }
                if (count($missing) > 0) {
                    $msg = '';
                    foreach ($missing as $m) {
                        $msg .= '<'.$m.'> ';
                    }
                    $this->response->add(new ZariliaXmlRpcFault(109, $msg));
                } else {
                    $newparams = array();
                    // ZARILIA API ignores App key (index 0 of params)
                    $newparams[0] = $this->params[1];
                    $newparams[1] = $this->params[2];
                    $newparams[2] = $this->params[3];
                    foreach ($post as $key => $value) {
                        $newparams[3][$key] =& $value;
                        unset($value);
                    }
                    $newparams[3]['zarilia_text'] =& $this->params[4];
                    $newparams[4] = $this->params[5];
                    $zariliaapi =& $this->_getZariliaApi($newparams);
                    $zariliaapi->_setUser($this->user, $this->isadmin);
                    $zariliaapi->editPost();
                }
            }
        }
    }

    function deletePost()
    {
        if (!$this->_checkUser($this->params[2], $this->params[3])) {
            $this->response->add(new ZariliaXmlRpcFault(104));
        } else {
            // ZARILIA API ignores App key (index 0 of params)
            array_shift($this->params);
            $zariliaapi =& $this->_getZariliaApi($this->params);
            $zariliaapi->_setUser($this->user, $this->isadmin);
            $zariliaapi->deletePost();
        }
    }

    function getPost()
    {
        if (!$this->_checkUser($this->params[2], $this->params[3])) {
            $this->response->add(new ZariliaXmlRpcFault(104));
        } else {
            // ZARILIA API ignores App key (index 0 of params)
            array_shift($this->params);
            $zariliaapi =& $this->_getZariliaApi($this->params);
            $zariliaapi->_setUser($this->user, $this->isadmin);
            $ret =& $zariliaapi->getPost(false);
            if (is_array($ret)) {
                $struct = new ZariliaXmlRpcStruct();
                $content = '';
                foreach ($ret as $key => $value) {
                    $maptag = $this->_getZariliaTagMap($key);
                    switch($maptag) {
                    case 'userid':
                        $struct->add('userid', new ZariliaXmlRpcString($value));
                        break;
                    case 'dateCreated':
                        $struct->add('dateCreated', new ZariliaXmlRpcDatetime($value));
                        break;
                    case 'postid':
                        $struct->add('postid', new ZariliaXmlRpcString($value));
                        break;
                    default :
                        $content .= '<'.$key.'>'.trim($value).'</'.$key.'>';
                        break;
                    }
                }
                $struct->add('content', new ZariliaXmlRpcString($content));
                $this->response->add($struct);
            } else {
                $this->response->add(new ZariliaXmlRpcFault(106));
            }
        }
    }

    function getRecentPosts()
    {
        if (!$this->_checkUser($this->params[2], $this->params[3])) {
            $this->response->add(new ZariliaXmlRpcFault(104));
        } else {
            // ZARILIA API ignores App key (index 0 of params)
            array_shift($this->params);
            $zariliaapi =& $this->_getZariliaApi($this->params);
            $zariliaapi->_setUser($this->user, $this->isadmin);
            $ret =& $zariliaapi->getRecentPosts(false);
            if (is_array($ret)) {
                $arr = new ZariliaXmlRpcArray();
                $count = count($ret);
                if ($count == 0) {
                    $this->response->add(new ZariliaXmlRpcFault(106, 'Found 0 Entries'));
                } else {
                    for ($i = 0; $i < $count; $i++) {
                        $struct = new ZariliaXmlRpcStruct();
                        $content = '';
                        foreach($ret[$i] as $key => $value) {
                            $maptag = $this->_getZariliaTagMap($key);
                            switch($maptag) {
                            case 'userid':
                                $struct->add('userid', new ZariliaXmlRpcString($value));
                                break;
                            case 'dateCreated':
                                $struct->add('dateCreated', new ZariliaXmlRpcDatetime($value));
                                break;
                            case 'postid':
                                $struct->add('postid', new ZariliaXmlRpcString($value));
                                break;
                            default :
                                $content .= '<'.$key.'>'.trim($value).'</'.$key.'>';
                                break;
                            }
                        }
                        $struct->add('content', new ZariliaXmlRpcString($content));
                        $arr->add($struct);
                        unset($struct);
                    }
                    $this->response->add($arr);
                }
            } else {
                $this->response->add(new ZariliaXmlRpcFault(106));
            }
        }
    }

    function getUsersBlogs()
    {
        if (!$this->_checkUser($this->params[1], $this->params[2])) {
            $this->response->add(new ZariliaXmlRpcFault(104));
        } else {
            $arr = new ZariliaXmlRpcArray();
            $struct = new ZariliaXmlRpcStruct();
            $struct->add('url', new ZariliaXmlRpcString(ZAR_URL.'/addons/'.$this->addon->getVar('dirname').'/'));
            $struct->add('blogid', new ZariliaXmlRpcString($this->addon->getVar('mid')));
            $struct->add('blogName', new ZariliaXmlRpcString('ZARILIA Blog'));
            $arr->add($struct);
            $this->response->add($arr);
        }
    }

    function getUserInfo()
    {
        if (!$this->_checkUser($this->params[1], $this->params[2])) {
            $this->response->add(new ZariliaXmlRpcFault(104));
        } else {
            $struct = new ZariliaXmlRpcStruct();
            $struct->add('nickname', new ZariliaXmlRpcString($this->user->getVar('uname')));
            $struct->add('userid', new ZariliaXmlRpcString($this->user->getVar('uid')));
            $struct->add('url', new ZariliaXmlRpcString($this->user->getVar('url')));
            $struct->add('email', new ZariliaXmlRpcString($this->user->getVar('email')));
            $struct->add('lastname', new ZariliaXmlRpcString(''));
            $struct->add('firstname', new ZariliaXmlRpcString($this->user->getVar('name')));
            $this->response->add($struct);
        }
    }

    function getTemplate()
    {
        if (!$this->_checkUser($this->params[2], $this->params[3])) {
            $this->response->add(new ZariliaXmlRpcFault(104));
        } else {
            switch ($this->params[5]) {
            case 'main':
                $this->response->add(new ZariliaXmlRpcFault(107));
                break;
            case 'archiveIndex':
                $this->response->add(new ZariliaXmlRpcFault(107));
                break;
            default:
                $this->response->add(new ZariliaXmlRpcFault(107));
                break;
            }
        }
    }

    function setTemplate()
    {
        if (!$this->_checkUser($this->params[2], $this->params[3])) {
            $this->response->add(new ZariliaXmlRpcFault(104));
        } else {
            $this->response->add(new ZariliaXmlRpcFault(107));
        }
    }
}
?>