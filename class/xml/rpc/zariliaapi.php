<?php
// $Id: zariliaapi.php,v 1.1 2007/03/16 02:42:17 catzwolf Exp $
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

class ZariliaApi extends ZariliaXmlRpcApi
{

    function ZariliaApi(&$params, &$response, &$addon)
    {
        $this->ZariliaXmlRpcApi($params, $response, $addon);
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
                foreach ($fields as $tag => $detail) {
                    if (!isset($this->params[3][$tag])) {
                        $data = $this->_getTagCdata($this->params[3]['zarilia_text'], $tag, true);
                        if (trim($data) == ''){
                            if ($detail['required']) {
                                $missing[] = $tag;
                            }
                        } else {
                            $post[$tag] =& $data;
                        }
                    } else {
                        $post[$tag] =& $this->params[3][$tag];
                    }
                }
                if (count($missing) > 0) {
                    $msg = '';
                    foreach ($missing as $m) {
                        $msg .= '<'.$m.'> ';
                    }
                    $this->response->add(new ZariliaXmlRpcFault(109, $msg));
                } else {
                    // will be removed... don't worry if this looks bad
                    include_once ZAR_ROOT_PATH.'/addons/news/class/class.newsstory.php';
                    $story = new NewsStory();
                    $error = false;
                    if (intval($this->params[4]) > 0) {
                        if (!$this->_checkAdmin()) {
                            // non admin users cannot publish
                            $error = true;
                            $this->response->add(new ZariliaXmlRpcFault(111));
                        } else {
                            $story->setType('admin');
                            $story->setApproved(true);
                            $story->setPublished(time());
                        }
                    } else {
                        if (!$this->_checkAdmin()) {
                            $story->setType('user');
                        } else {
                            $story->setType('admin');
                        }
                    }
                    if (!$error) {
                        if (isset($post['categories']) && !empty($post['categories'][0])) {
                            $story->setTopicId(intval($post['categories'][0]['categoryId']));
                        } else {
                            $story->setTopicId(1);
                        }
                        $story->setTitle(addslashes(trim($post['title'])));
                        if (isset($post['moretext'])) {
                            $story->setBodytext(addslashes(trim($post['moretext'])));
                        }
                        if (!isset($post['hometext'])) {
                            $story->setHometext(addslashes(trim($this->params[3]['zarilia_text'])));
                        } else {
                            $story->setHometext(addslashes(trim($post['hometext'])));
                        }
                        $story->setUid($this->user->getVar('uid'));
                        $story->setHostname($_SERVER['REMOTE_ADDR']);
                        if (!$this->_checkAdmin()) {
                            $story->setNohtml(1);
                        } else {
                            $story->setNohtml(0);
                        }
                        $story->setNosmiley(0);
                        $story->setNotifyPub(1);
                        $story->setTopicalign('R');
                        $ret = $story->store();
                        if (!$ret) {
                            $this->response->add(new ZariliaXmlRpcFault(106));
                        } else {
                            $this->response->add(new ZariliaXmlRpcString($ret));
                        }
                    }
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
                foreach ($fields as $tag => $detail) {
                    if (!isset($this->params[3][$tag])) {
                        $data = $this->_getTagCdata($this->params[3]['zarilia_text'], $tag, true);
                        if (trim($data) == ''){
                            if ($detail['required']) {
                                $missing[] = $tag;
                            }
                        } else {
                            $post[$tag] = $data;
                        }
                    } else {
                        $post[$tag] = $this->params[3][$tag];
                    }
                }
                if (count($missing) > 0) {
                    $msg = '';
                    foreach ($missing as $m) {
                        $msg .= '<'.$m.'> ';
                    }
                    $this->response->add(new ZariliaXmlRpcFault(109, $msg));
                } else {
                    // will be removed... don't worry if this looks bad
                    include_once ZAR_ROOT_PATH.'/addons/news/class/class.newsstory.php';
                    $story = new NewsStory($this->params[0]);
                    $storyid = $story->storyid();
                    if (empty($storyid)) {
                        $this->response->add(new ZariliaXmlRpcFault(106));
                    } elseif (!$this->_checkAdmin()) {
                        $this->response->add(new ZariliaXmlRpcFault(111));
                    } else {
                        $story->setTitle(addslashes(trim($post['title'])));
                        if (isset($post['moretext'])) {
                            $story->setBodytext(addslashes(trim($post['moretext'])));
                        }
                        if (!isset($post['hometext'])) {
                            $story->setHometext(addslashes(trim($this->params[3]['zarilia_text'])));
                        } else {
                            $story->setHometext(addslashes(trim($post['hometext'])));
                        }
                        if ($this->params[4]) {
                            $story->setApproved(true);
                            $story->setPublished(time());
                        }
                        $story->setTopicalign('R');
                        if (!$story->store()) {
                            $this->response->add(new ZariliaXmlRpcFault(106));
                        } else {
                            $this->response->add(new ZariliaXmlRpcBoolean(true));
                        }
                    }
                }
            }
        }
    }

    function deletePost()
    {
        if (!$this->_checkUser($this->params[1], $this->params[2])) {
            $this->response->add(new ZariliaXmlRpcFault(104));
        } else {
            if (!$this->_checkAdmin()) {
                $this->response->add(new ZariliaXmlRpcFault(111));
            } else {
                // will be removed... don't worry if this looks bad
                include_once ZAR_ROOT_PATH.'/addons/news/class/class.newsstory.php';
                $story = new NewsStory($this->params[0]);
                if (!$story->delete()) {
                    $this->response->add(new ZariliaXmlRpcFault(106));
                } else {
                    $this->response->add(new ZariliaXmlRpcBoolean(true));
                }
            }
        }
    }

    // currently returns the same struct as in metaWeblogApi
    function &getPost($respond=true)
    {
        if (!$this->_checkUser($this->params[1], $this->params[2])) {
            $this->response->add(new ZariliaXmlRpcFault(104));
        } else {
            // will be removed... don't worry if this looks bad
            include_once ZAR_ROOT_PATH.'/addons/news/class/class.newsstory.php';
            $story = new NewsStory($this->params[0]);
            $ret = array('uid' => $story->uid(), 'published' => $story->published(), 'storyid' => $story->storyId(), 'title' => $story->title('Edit'), 'hometext' => $story->hometext('Edit'), 'moretext' => $story->bodytext('Edit'));
            if (!$respond) {
                return $ret;
            } else {
                if (!$ret) {
                    $this->response->add(new ZariliaXmlRpcFault(106));
                } else {
                    $struct = new ZariliaXmlRpcStruct();
                    $content = '';
                    foreach ($ret as $key => $value) {
                        switch($key) {
                        case 'uid':
                            $struct->add('userid', new ZariliaXmlRpcString($value));
                            break;
                        case 'published':
                            $struct->add('dateCreated', new ZariliaXmlRpcDatetime($value));
                            break;
                        case 'storyid':
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
                    $this->response->add($struct);
                }
            }
        }
    }

    function &getRecentPosts($respond=true)
    {
        if (!$this->_checkUser($this->params[1], $this->params[2])) {
            $this->response->add(new ZariliaXmlRpcFault(104));
        } else {
            include_once ZAR_ROOT_PATH.'/addons/news/class/class.newsstory.php';
			if (isset($this->params[4]) && intval($this->params[4]) > 0) {
				$stories =& NewsStory::getAllPublished(intval($this->params[3]), 0, $this->params[4]);
			} else {
            	$stories =& NewsStory::getAllPublished(intval($this->params[3]));
			}
            $scount = count($stories);
            $ret = array();
            for ($i = 0; $i < $scount; $i++) {
                $ret[] = array('uid' => $stories[$i]->uid(), 'published' => $stories[$i]->published(), 'storyid' => $stories[$i]->storyId(), 'title' => $stories[$i]->title('Edit'), 'hometext' => $stories[$i]->hometext('Edit'), 'moretext' => $stories[$i]->bodytext('Edit'));
            }
            if (!$respond) {
                return $ret;
            } else {
                if (count($ret) == 0) {
                    $this->response->add(new ZariliaXmlRpcFault(106, 'Found 0 Entries'));
                } else {
                    $arr = new ZariliaXmlRpcArray();
                    $count = count($ret);
                    for ($i = 0; $i < $count; $i++) {
                        $struct = new ZariliaXmlRpcStruct();
                        $content = '';
                        foreach($ret[$i] as $key => $value) {
                            switch($key) {
                            case 'uid':
                                $struct->add('userid', new ZariliaXmlRpcString($value));
                                break;
                            case 'published':
                                $struct->add('dateCreated', new ZariliaXmlRpcDatetime($value));
                                break;
                            case 'storyid':
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
            }
        }
    }

    function &getCategories($respond=true)
    {
        if (!$this->_checkUser($this->params[1], $this->params[2])) {
            $this->response->add(new ZariliaXmlRpcFault(104));
        } else {
            include_once ZAR_ROOT_PATH.'/class/zariliatopic.php';
            $db =& Database::getInstance();
            $xt = new ZariliaTopic($db->prefix('topics'));
            $ret = $xt->getTopicsList();
            if (!$respond) {
                return $ret;
            } else {
                if (count($ret) == 0) {
                    $this->response->add(new ZariliaXmlRpcFault(106, 'Found 0 Entries'));
                } else {
                    $arr = new ZariliaXmlRpcArray();
                    foreach ($ret as $topic_id => $topic_vars) {
                        $struct = new ZariliaXmlRpcStruct();
                        $struct->add('categoryId', new ZariliaXmlRpcString($topic_id));
                        $struct->add('categoryName', new ZariliaXmlRpcString($topic_vars['title']));
						$struct->add('categoryPid', new ZariliaXmlRpcString($topic_vars['pid']));
                        $arr->add($struct);
                        unset($struct);
                    }
                    $this->response->add($arr);
                }
            }
        }
    }
}
?>