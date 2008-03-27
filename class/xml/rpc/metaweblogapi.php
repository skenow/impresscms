<?php
// $Id: metaweblogapi.php,v 1.1 2007/03/16 02:42:17 catzwolf Exp $
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

class MetaWeblogApi extends ZariliaXmlRpcApi
{
    function MetaWeblogApi(&$params, &$response, &$addon)
    {
        $this->ZariliaXmlRpcApi($params, $response, $addon);
        $this->_setZariliaTagMap('storyid', 'postid');
        $this->_setZariliaTagMap('published', 'dateCreated');
        $this->_setZariliaTagMap('uid', 'userid');
        //$this->_setZariliaTagMap('hometext', 'description');
    }

    function newPost()
    {
        if (!$this->_checkUser($this->params[1], $this->params[2])) {
            
            $this->response->add(new ZariliaXmlRpcFault(104));
        } else {
            if (!$fields =& $this->_getPostFields(null, $this->params[0])) {
                $this->response->add(new ZariliaXmlRpcFault(106));
            } else {
                $missing = array();
                $post = array();
                foreach ($fields as $tag => $detail) {
                    $maptag = $this->_getZariliaTagMap($tag);
                    if (!isset($this->params[3][$maptag])) {
                        $data = $this->_getTagCdata($this->params[3]['description'], $maptag, true);
                        if (trim($data) == ''){
                            if ($detail['required']) {
                                $missing[] = $maptag;
                            }
                        } else {
                            $post[$tag] = $data;
                        }
                    } else {
                        $post[$tag] = $this->params[3][$maptag];
                    }
                }
                if (count($missing) > 0) {
                    $msg = '';
                    foreach ($missing as $m) {
                        $msg .= '<'.$m.'> ';echo $m;
                    }
                    $this->response->add(new ZariliaXmlRpcFault(109, $msg));
                } else {
                    $newparams = array();
                    $newparams[0] = $this->params[0];
                    $newparams[1] = $this->params[1];
                    $newparams[2] = $this->params[2];
                    foreach ($post as $key => $value) {
                        $newparams[3][$key] =& $value;
                        unset($value);
                    }
                    $newparams[3]['zarilia_text'] = $this->params[3]['description'];
                    if (isset($this->params[3]['categories']) && is_array($this->params[3]['categories'])) {
                        foreach ($this->params[3]['categories'] as $k => $v) {
                            $newparams[3]['categories'][$k] = $v;
                        }
                    }
                    $newparams[4] = $this->params[4];
                    $zariliaapi =& $this->_getZariliaApi($newparams);
                    $zariliaapi->_setUser($this->user, $this->isadmin);
                    $zariliaapi->newPost();
                }
            }
        }
    }

    function editPost()
    {
        if (!$this->_checkUser($this->params[1], $this->params[2])) {
            $this->response->add(new ZariliaXmlRpcFault(104));
        } else {
            if (!$fields =& $this->_getPostFields($this->params[0])) {
            } else {
                $missing = array();
                $post = array();
                foreach ($fields as $tag => $detail) {
                    $maptag = $this->_getZariliaTagMap($tag);
                    if (!isset($this->params[3][$maptag])) {
                        $data = $this->_getTagCdata($this->params[3]['description'], $maptag, true);
                        if (trim($data) == ''){
                            if ($detail['required']) {
                                $missing[] = $tag;
                            }
                        } else {
                            $post[$tag] = $data;
                        }
                    } else {
                        $post[$tag] =& $this->params[3][$maptag];
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
                    $newparams[0] = $this->params[0];
                    $newparams[1] = $this->params[1];
                    $newparams[2] = $this->params[2];
                    foreach ($post as $key => $value) {
                        $newparams[3][$key] =& $value;
                        unset($value);
                    }
                    if (isset($this->params[3]['categories']) && is_array($this->params[3]['categories'])) {
                        foreach ($this->params[3]['categories'] as $k => $v) {
                            $newparams[3]['categories'][$k] = $v;
                        }
                    }
                    $newparams[3]['zarilia_text'] = $this->params[3]['description'];
                    $newparams[4] = $this->params[4];
                    $zariliaapi =& $this->_getZariliaApi($newparams);
                    $zariliaapi->_setUser($this->user, $this->isadmin);
                    $zariliaapi->editPost();
                }
            }
        }
    }

    function getPost()
    {
        if (!$this->_checkUser($this->params[1], $this->params[2])) {
            $this->response->add(new ZariliaXmlRpcFault(104));
        } else {
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
                        $struct->add('link', new ZariliaXmlRpcString(ZAR_URL.'/addons/zariliasections/item.php?item='.$value));
                        $struct->add('permaLink', new ZariliaXmlRpcString(ZAR_URL.'/addons/zariliasections/item.php?item='.$value));
                        break;
                    case 'title':
                        $struct->add('title', new ZariliaXmlRpcString($value));
                        break;
                    default :
                        $content .= '<'.$key.'>'.trim($value).'</'.$key.'>';
                        break;
                    }
                }
                $struct->add('description', new ZariliaXmlRpcString($content));
                $this->response->add($struct);
            } else {
                $this->response->add(new ZariliaXmlRpcFault(106));
            }
        }
    }

    function getRecentPosts()
    {
        if (!$this->_checkUser($this->params[1], $this->params[2])) {
            $this->response->add(new ZariliaXmlRpcFault(104));
        } else {
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
                                $struct->add('link', new ZariliaXmlRpcString(ZAR_URL.'/addons/news/article.php?item_id='.$value));
                                $struct->add('permaLink', new ZariliaXmlRpcString(ZAR_URL.'/addons/news/article.php?item_id='.$value));
                                break;
                            case 'title':
                                $struct->add('title', new ZariliaXmlRpcString($value));
                                break;
                            default :
                                $content .= '<'.$key.'>'.trim($value).'</'.$key.'>';
                                break;
                            }
                        }
                        $struct->add('description', new ZariliaXmlRpcString($content));
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

    function getCategories()
    {
        if (!$this->_checkUser($this->params[1], $this->params[2])) {
            $this->response->add(new ZariliaXmlRpcFault(104));
        } else {
            $zariliaapi =& $this->_getZariliaApi($this->params);
            $zariliaapi->_setUser($this->user, $this->isadmin);
            $ret =& $zariliaapi->getCategories(false);
            if (is_array($ret)) {
                $arr = new ZariliaXmlRpcArray();
                foreach ($ret as $id => $detail) {
                    $struct = new ZariliaXmlRpcStruct();
                    $struct->add('description', new ZariliaXmlRpcString($detail));
                    $struct->add('htmlUrl', new ZariliaXmlRpcString(ZAR_URL.'/addons/news/index.php?storytopic='.$id));
                    $struct->add('rssUrl', new ZariliaXmlRpcString(''));
                    $catstruct = new ZariliaXmlRpcStruct();
                    $catstruct->add($detail['title'], $struct);
                    $arr->add($catstruct);
                    unset($struct);
                    unset($catstruct);
                }
                $this->response->add($arr);
            } else {
                $this->response->add(new ZariliaXmlRpcFault(106));
            }
        }
    }
}
?>