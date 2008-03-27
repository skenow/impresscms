<?php
// $Id: submit.php,v 1.23 2004/09/01 17:48:07 hthouzard Exp $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //


if (!defined('ZAR_ROOT_PATH')) {
	include_once '../../mainfile.php';
}
include_once ZAR_ROOT_PATH.'/addons/news/class/class.newsstory.php';
include_once ZAR_ROOT_PATH.'/addons/news/class/class.sfiles.php';
include_once ZAR_ROOT_PATH.'/addons/news/class/class.newstopic.php';
include_once ZAR_ROOT_PATH.'/class/uploader.php';
include_once ZAR_ROOT_PATH.'/header.php';
include_once ZAR_ROOT_PATH.'/addons/news/include/functions.php';
if (file_exists(ZAR_ROOT_PATH.'/addons/news/language/'.$zariliaConfig['language'].'/admin.php')) {
    include_once ZAR_ROOT_PATH.'/addons/news/language/'.$zariliaConfig['language'].'/admin.php';
} else {
    include_once ZAR_ROOT_PATH.'/addons/news/language/english/admin.php';
}


$myts = &MyTextSanitizer::getInstance();
$module_id = $zariliaAddon->getVar('mid');
$storyid=0;

if (is_object($zariliaUser)) {
    $groups = $zariliaUser->getGroups();
} else {
	$groups = ZAR_GROUP_ANONYMOUS;
}

$gperm_handler = &zarilia_gethandler('groupperm');

if (isset($_POST['topic_id'])) {
    $perm_itemid = intval($_POST['topic_id']);
} else {
    $perm_itemid = 0;
}
//If no access
if (!$gperm_handler->checkRight('news_submit', $perm_itemid, $groups, $module_id)) {
    redirect_header(ZAR_URL.'/addons/news/index.php', 3, _NOPERM);
    exit();
}
$op = 'form';

//If approve privileges
$approveprivilege = 0;
if (is_object($zariliaUser) && $gperm_handler->checkRight('news_approve', $perm_itemid, $groups, $module_id)) {
    $approveprivilege = 1;
}

if (isset($_POST['preview'])) {
	$op = 'preview';
} elseif (isset($_POST['post'])) {
	$op = 'post';
}
elseif ( isset($_GET['op']) && isset($_GET['storyid'])) {
	// Verify that the user can edit or delete an article
	if( $_GET['op'] == 'edit' || $_GET['op'] == 'delete' ) {
		if($zariliaAddonConfig['authoredit']==1) {
			$tmpstory = new NewsStory(intval($_GET['storyid']));
			if(is_object($zariliaUser) && $zariliaUser->getVar('uid')!=$tmpstory->uid() && !news_is_admin_group()) {
			    redirect_header(ZAR_URL.'/addons/news/index.php', 3, _NOPERM);
	    		exit();
			}
		} else {	// Users can't edit their articles
			if(!news_is_admin_group()) {
		    	redirect_header(ZAR_URL.'/addons/news/index.php', 3, _NOPERM);
	    		exit();
	    	}
		}
	}

    if ($approveprivilege && $_GET['op'] == 'edit') {
        $op = 'edit';
        $storyid = intval($_GET['storyid']);
    }
    elseif ($approveprivilege && $_GET['op'] == 'delete') {
        $op = 'delete';
        $storyid = intval($_GET['storyid']);
    }
    else {
    	if(news_getmoduleoption('authoredit') && is_object($zariliaUser) && isset($_GET['storyid']) && ($_GET['op']=='edit' || $_POST['op']=='preview' || $_POST['op']=='post')) {
    		$storyid=0;
    		$storyid = isset($_GET['storyid']) ? intval($_GET['storyid']) : intval($_POST['storyid']);
    		if(!empty($storyid)) {
    			$tmpstory = new NewsStory($storyid);
    			if($tmpstory->uid()==$zariliaUser->getVar('uid')) {
	    			$op= isset($_GET['op']) ? $_GET['op'] : $_POST['post'];
    				unset($tmpstory);
    				$approveprivilege=1;
    			} else {
	    			unset($tmpstory);
	    			if(!news_is_admin_group()) {
    					redirect_header(ZAR_URL.'/addons/news/index.php', 3, _NOPERM);
    					exit();
    				} else {
    					$approveprivilege=1;
    				}
    			}
    		}
    	} else {
    		if(!news_is_admin_group()) {
    			unset($tmpstory);
        		redirect_header(ZAR_URL.'/addons/news/index.php', 3, _NOPERM);
        		exit();
        	} else {
        		$approveprivilege=1;
        	}
        }
    }
}

switch ($op) {
    case 'edit':
        if (!$approveprivilege) {
            redirect_header(ZAR_URL.'/addons/news/index.php', 0, _NOPERM);
            exit();
            break;
        }
        //if($storyid==0 && isset($_POST['storyid'])) {
     	//	$storyid=intval($_POST['storyid']);
       	//}
        $story = new NewsStory($storyid);
        if (!$gperm_handler->checkRight('news_view', $story->topicid(), $groups, $module_id)) {
            redirect_header(ZAR_URL.'/addons/news/index.php', 0, _NOPERM);
            exit();
        }
        echo"<table width='100%' border='0' cellspacing='1' class='outer'><tr><td class=\"odd\">";
        echo '<h4>' . _AM_EDITARTICLE . '</h4>';
        $title = $story->title('Edit');
        $hometext = $story->hometext('Edit');
        $bodytext = $story->bodytext('Edit');
        $nohtml = $story->nohtml();
        $nosmiley = $story->nosmiley();
        $description = $story->description();
        $keywords = $story->keywords();
        $ihome = $story->ihome();
        $newsauthor=$story->uid();
        $topicid = $story->topicid();
        $notifypub=$story->notifypub();
        $approve = 0;
        $published = $story->published();
        if (isset($published) && $published > 0) {
            $approve = 1;
        }
        if ( $story -> published() != 0) {
            $published = $story->published();
        }
		if ( $story -> expired() != 0) {
            $expired = $story->expired();
        } else {
            $expired = 0;
        }
		$type = $story -> type();
        $topicdisplay = $story -> topicdisplay();
        $topicalign = $story -> topicalign( false );
        include_once ZAR_ROOT_PATH.'/addons/news/include/storyform.inc.php';
        echo'</td></tr></table>';
        break;

	case 'preview':
		$topic_id = intval($_POST['topic_id']);
		$xt = new NewsTopic($topic_id);
		if(isset($_GET['storyid'])) {
			$storyid=intval($_GET['storyid']);
		} else {
			if(isset($_POST['storyid'])) {
				$storyid=intval($_POST['storyid']);
			} else {
				$storyid=0;
			}
		}

		if (!empty($storyid)) {
		    $story = new NewsStory($storyid);
	    	$published = $story -> published();
	    	$expired = $story -> expired();
		} else {
		    $story = new NewsStory();
	    	$published = isset($_POST['publish_date']) ? $_POST['publish_date'] : 0;
	    	if(!empty($published) && isset($_POST['autodate']) && intval($_POST['autodate']==1)) {
		    	$published=strtotime($published['date']) + $published['time'];
	    	} else {
				$published=0;
	    	}
	    	$expired= isset($_POST['expiry_date']) ? $_POST['expiry_date'] : 0;
	    	if(!empty($expired) && isset($_POST['autoexpdate']) && intval($_POST['autoexpdate']==1)) {
		    	$expired=strtotime($expired['date']) + $expired['time'];
	    	} else {
				$expired=0;
			}
		}
		$topicid = $topic_id;
		if(isset($_POST['topicdisplay'])) {
			$topicdisplay=intval($_POST['topicdisplay']);
		} else {
			$topicdisplay=1;
		}

		$approve=isset($_POST['approve']) ? intval($_POST['approve']) : 0;
		$topicalign='R';
		if(isset($_POST['topicalign'])) {
			$topicalign=$_POST['topicalign'];
		}
		$story->setTitle($_POST['title']);
		$story->setHometext($_POST['hometext']);
		if ($approveprivilege) {
	    	$story->setTopicdisplay($topicdisplay);
	    	$story->setTopicalign($topicalign);
	    	$story->setBodytext($_POST['bodytext']);
			if(news_getmoduleoption('metadata')) {
	        	$story->Setkeywords($_POST['keywords']);
        		$story->Setdescription($_POST['description']);
        		$story->setIhome(intval($_POST['ihome']));
        	}
		} else {
		    $noname = isset($_POST['noname']) ? intval($_POST['noname']) : 0;
		}

		if ($approveprivilege || (is_object($zariliaUser) && $zariliaUser->isAdmin($zariliaAddon->getVar('mid')))) {
			if(isset($_POST['author'])) {
				$story->setUid(intval($_POST['author']));
			}
		}

		$notifypub = isset($_POST['notifypub']) ? intval($_POST['notifypub']) : 0;

		$nosmiley=isset($_POST['nosmiley']) ? intval($_POST['nosmiley']) : 0;
		if (isset($nosmiley) && ($nosmiley == 0 || $nosmiley == 1)) {
		    $story -> setNosmiley($nosmiley);
		} else {
	    	$nosmiley = 0;
		}
		if ($approveprivilege) {
		    $nohtml = isset($_POST['nohtml']) ? intval($_POST['nohtml']) : 0;
			$story->setNohtml($nohtml);
			if (!isset($_POST['approve'])) {
			    $approve = 0;
			}
		} else {
			$story->setNohtml = 1;
		}

		$title = $story->title('InForm');
	  	$hometext = $story->hometext('InForm');
	  	if ($approveprivilege) {
  	    	$bodytext = $story->bodytext('InForm');
  	    	$ihome = $story -> ihome();
  	    	$description = $story->description('E');
  	    	$keywords = $story->keywords('E');
  		}

		//Display post preview
		$newsauthor=$story->uid();
		$p_title = $story->title('Preview');
		$p_hometext = $story->hometext('Preview');
		if ($approveprivilege) {
		    $p_bodytext = $story->bodytext('Preview');
	    	$p_hometext .= '<br /><br />'.$p_bodytext;
		}
		$topicalign2 = isset($story->topicalign) ? 'align="'.$story->topicalign().'"' : '';
		$p_hometext = (($xt->topic_imgurl() != '') && $topicdisplay) ? '<img src="images/topics/'.$xt->topic_imgurl().'" '.$topicalign2.' alt="" />'.$p_hometext : $p_hometext;

		echo '<h1>'.$p_title.'</h1>';
		echo $p_hometext;
//		themecenterposts($p_title, $p_hometext);

		//Display post edit form
		$returnside=intval($_POST['returnside']);
		include_once ZAR_ROOT_PATH.'/addons/news/include/storyform.inc.php';
		break;

	case 'post':
		$nohtml_db = isset($_POST['nohtml']) ? $_POST['nohtml'] : 1;
		if (is_object($zariliaUser) ) {
			$uid = $zariliaUser->getVar('uid');
			if ($approveprivilege) {
			    $nohtml_db = empty($_POST['nohtml']) ? 0 : 1;
			}
			if (isset($_POST['author']) && ($approveprivilege || $zariliaUser->isAdmin($zariliaAddon->getVar('mid'))) ) {
				$uid=intval($_POST['author']);
			}
		} else {
		    $uid = 0;
		}

		if(isset($_GET['storyid'])) {
			$storyid=intval($_GET['storyid']);
		} else {
			if(isset($_POST['storyid'])) {
				$storyid=intval($_POST['storyid']);
			} else {
				$storyid=0;
			}
		}

		if (empty($storyid)) {
		    $story = new NewsStory();
		    $editmode = false;
		} else {
	    	$story = new NewsStory($storyid);
	    	$editmode = true;
		}
		$story->setUid($uid);
		$story->setTitle($_POST['title']);
		$story->setHometext($_POST['hometext']);
		$story->setTopicId(intval($_POST['topic_id']));
		$story->setHostname(zarilia_getenv('REMOTE_ADDR'));
		$story->setNohtml($nohtml_db);
		$nosmiley = isset($_POST['nosmiley']) ? intval($_POST['nosmiley']) : 0;
		$story->setNosmiley($nosmiley);
		$notifypub = isset($_POST['notifypub']) ? intval($_POST['notifypub']) : 0;
		$story->setNotifyPub($notifypub);
		$story->setType($_POST['type']);

		if (!empty( $_POST['autodate'] ) && $approveprivilege) {
		    $publish_date=$_POST['publish_date'];
	    	$pubdate = strtotime($publish_date['date']) + $publish_date['time'];
	    	//$offset = $zariliaUser -> timezone() - $zariliaConfig['server_TZ'];
	    	//$pubdate = $pubdate - ( $offset * 3600 );
	    	$story -> setPublished( $pubdate );
		}
		if (!empty( $_POST['autoexpdate'] ) && $approveprivilege) {
			$expiry_date=$_POST['expiry_date'];
	    	$expiry_date = strtotime($expiry_date['date']) + $expiry_date['time'];
	    	$offset = $zariliaUser -> timezone() - $zariliaConfig['server_TZ'];
	    	$expiry_date = $expiry_date - ( $offset * 3600 );
	    	$story -> setExpired( $expiry_date );
		} else {
		    $story -> setExpired( 0 );
		}

		if ($approveprivilege) {
			if(news_getmoduleoption('metadata')) {
				$story->Setdescription($_POST['description']);
        		$story->Setkeywords($_POST['keywords']);
        	}
	    	$story->setTopicdisplay($_POST['topicdisplay']);	// Display Topic Image ? (Yes or No)
	    	$story->setTopicalign($_POST['topicalign']);		// Topic Align, 'Right' or 'Left'
   			$story->setIhome($_POST['ihome']);				// Publish in home ? (Yes or No)
	    	if (isset($_POST['bodytext'])) {
		        $story->setBodytext($_POST['bodytext']);
	    	} else {
		        $story->setBodytext(' ');
	    	}
	    	$approve = isset($_POST['approve']) ? intval($_POST['approve']) : 0;

		    if (!$story->published() && $approve) {
	        	$story->setPublished(time());
	    	}
	    	if (!$story->expired()) {
		        $story->setExpired(0);
	    	}

	    	if(!$approve) {
		    	$story->setPublished(0);
	    	}
		} elseif ( $zariliaAddonConfig['autoapprove'] == 1 && !$approveprivilege) {
	    	if (empty($storyid)) {
				$approve = 1;
			} else {
				$approve = isset($_POST['approve']) ? intval($_POST['approve']) : 0;
			}
			if($approve) {
	    		$story->setPublished(time());
    		} else {
				$story->setPublished(0);
			}
    		$story->setExpired(0);
			$story->setTopicalign('R');
		} else {
		    $approve = 0;
		}
		$story->setApproved($approve);

		if($approve) {
			news_updateCache();
		}

		// Increment author's posts count (only if it's a new article)
		// First case, it's not an anonyous, the story is approved and it's a new story
		if($uid && $approve && empty($storyid)) {
			$tmpuser=new ZariliaUser($uid);
        	$member_handler = &zarilia_gethandler('member');
        	$member_handler->updateUserByField($tmpuser, 'posts', $tmpuser->getVar('posts') + 1);
		}

		// Second case, it's not an anonymous, the story is NOT approved and it's NOT a new story (typical when someone is approving a submited story)
		if(is_object($zariliaUser) && $approve && !empty($storyid)) {
			$storytemp = new NewsStory( $storyid );
			if(!$storytemp->published() && $storytemp->uid()>0) {	// the article has been submited but not approved
				$tmpuser=new ZariliaUser($storytemp->uid());
        		$member_handler = &zarilia_gethandler('member');
        		$member_handler->updateUserByField($tmpuser, 'posts', $tmpuser->getVar('posts') + 1);
        	}
        	unset($storytemp);
		}

		$result = $story->store();
		if ($result) {
			if(!$editmode) {
				// 	Notification
				// TODO: modifier afin qu'en cas de prépublication, la notification ne se fasse pas
				$notification_handler = &zarilia_gethandler('notification');
				$tags = array();
				$tags['STORY_NAME'] = $story->title();
				$tags['STORY_URL'] = ZAR_URL . '/addons/' . $zariliaAddon->getVar('dirname') . '/article.php?storyid=' . $story->storyid();
				// If notify checkbox is set, add subscription for approve
				if ($notifypub && $approve) {
					include_once ZAR_ROOT_PATH . '/include/notification_constants.php';
					$notification_handler->subscribe('story', $story->storyid(), 'approve', ZAR_NOTIFICATION_MODE_SENDONCETHENDELETE,$zariliaAddon->getVar('mid'),$story->uid());
				}

				if ($approve == 1) {
					$notification_handler->triggerEvent('global', 0, 'new_story', $tags);
					$notification_handler->triggerEvent('story', $story->storyid(), 'approve', $tags);
					// Added by Lankford on 2007/3/23
					$notification_handler->triggerEvent('category', $story->topicid(), 'new_story', $tags);
				} else {
					$tags['WAITINGSTORIES_URL'] = ZAR_URL . '/addons/' . $zariliaAddon->getVar('dirname') . '/admin/index.php?op=newarticle';
					$notification_handler->triggerEvent('global', 0, 'story_submit', $tags);
				}
			}

			$allowupload = false;
			switch ($zariliaAddonConfig['uploadgroups']) {
				case 1: //Submitters and Approvers
					$allowupload = true;
					break;
				case 2: //Approvers only
					$allowupload = $approveprivilege ? true : false;
					break;
				case 3: //Upload Disabled
					$allowupload = false;
					break;
			}

			if($allowupload) {
				// Manage upload(s)
				if(isset($_POST['delupload']) && count($_POST['delupload'])>0) {
					foreach ($_POST['delupload'] as $onefile) {
						$sfiles = new sFiles($onefile);
						$sfiles->delete();
					}
				}

				if(isset($_POST['zarilia_upload_file'])) {
					$fldname = $_FILES[$_POST['zarilia_upload_file'][0]];
					$fldname = (get_magic_quotes_gpc()) ? stripslashes($fldname['name']) : $fldname['name'];
					if(zarilia_trim($fldname!='')) {
						$sfiles = new sFiles();
						$destname=$sfiles->createUploadName(ZAR_UPLOAD_PATH,$fldname);
						/**
						 * You can attach files to your news
						 */
						$permittedtypes = explode("\n",str_replace("\r",'',news_getmoduleoption('mimetypes')));
						array_walk($permittedtypes, 'trim');
						$uploader = new ZariliaMediaUploader( ZAR_UPLOAD_PATH, $permittedtypes, $zariliaAddonConfig['maxuploadsize']);
						$uploader->setTargetFileName($destname);
						if ($uploader->fetchMedia($_POST['zarilia_upload_file'][0])) {
							if ($uploader->upload()) {
								$sfiles->setFileRealName($uploader->getMediaName());
								$sfiles->setStoryid($story->storyid());
								$sfiles->setMimetype($sfiles->giveMimetype(ZAR_UPLOAD_PATH.'/'.$uploader->getMediaName()));
								$sfiles->setDownloadname($destname);
								if(!$sfiles->store()) {
									echo _AM_UPLOAD_DBERROR_SAVE;
								}
							} else {
								echo _AM_UPLOAD_ERROR. ' ' . $uploader->getErrors();
							}
						} else {
							echo $uploader->getErrors();
						}
					}
				}
			}
		} else {
			echo _ERRORS;
		}
		$returnside = isset($_POST['returnside']) ? intval($_POST['returnside']) : 0;
		if(!$returnside) {
			redirect_header(ZAR_URL.'/addons/news/index.php',2,_NW_THANKS);
			exit();
		} else {
			redirect_header(ZAR_URL.'/addons/news/admin/index.php?op=newarticle',2,_NW_THANKS);
			exit();
		}
		break;

	case 'form':
		$xt = new NewsTopic();
		$title = '';
		$hometext = '';
		$noname = 0;
		$nohtml = 0;
		$nosmiley = 0;
		$notifypub = 1;
		$topicid = 0;
		if ($approveprivilege) {
			$description='';
			$keywords='';
	    	$topicdisplay = 0;
	    	$topicalign = 'R';
	    	$ihome = 0;
	    	$bodytext = '';
	    	$approve = 0;
	    	$autodate = '';
	    	$expired = 0;
	    	$published = 0;
		}
		if($zariliaAddonConfig['autoapprove'] == 1) {
			$approve=1;
		}
		include_once ZAR_ROOT_PATH.'/addons/news/include/storyform.inc.php';
		break;
}
include_once ZAR_ROOT_PATH.'/footer.php';
?>