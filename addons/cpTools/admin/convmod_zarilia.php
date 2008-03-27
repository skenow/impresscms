<?php
// $Id: convmod_zarilia.php,v 1.1 2007/03/16 02:34:43 catzwolf Exp $
// ------------------------------------------------------------------------ //
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

/*function convert_addon( $data )
{
    if ( strstr( $data, 'ZAR_ROOT_PATH."/class/zariliaobject.php"' ) ) {
        return '';
    }
    if ( substr( $data, 0, strlen( 'function ' ) ) == 'function ' ) {
        return "\n" . $data;
    }
    $data = str_replace( array( 'ZariliaUser::uid()' ),
        array( 'ZariliaUser->getVar("uid")' ),
        $data );
    return $data;
}

$contents = file_get_contents( "$source/$file" );

if ( substr( $file, -4 ) == '.php' ) {
    include_once ZAR_ROOT_PATH . '/addons/cpTools/admin/code_parse_php.php';
}*/

function mb_str_replace($haystack, $search,$replace, $offset=0,$encoding='auto'){
    $len_sch=mb_strlen($search,$encoding);
    $len_rep=mb_strlen($replace,$encoding);

    while (($offset=mb_strpos($haystack,$search,$offset,$encoding))!==false){
        $haystack=mb_substr($haystack,0,$offset,$encoding)
            .$replace
            .mb_substr($haystack,$offset+$len_sch,1000,$encoding);
        $offset=$offset+$len_rep;
        if ($offset>mb_strlen($haystack,$encoding))break;
    }
    return $haystack;
}

switch(substr( $file, -4 )) {
	case '.php':
		$replace_data = array(
'XOOPS_ROOT_PATH' => 'ZAR_ROOT_PATH',
'XOOPS_URL' => 'ZAR_URL',
'/modules/' => '/addons/',
'$xoopsConfig' => '$zariliaConfig',
'$xoopsOption' => '$zariliaOption',
'$xoopsUser' => '$zariliaUser',
'$xoopsDB' => '$zariliaDB',
'$zariliaDB->query' => '$zariliaDB->Execute',
'$zariliaDB->fetchRow($result)' => '$result->FetchRow()',
'$xoopsTpl' => '$zariliaTpl',
'$zariliaTpl->assign(\'xoops_pagetitle\',' => '$zariliaTpl->addTitle(',
'$xoopsModule' => '$zariliaModule',
'xoopstree.php' => 'zariliatree.php',
'xoops_gethandler' => 'zarilia_gethandler',
'XOOPS_GROUP_' => 'ZAR_GROUP_',
'XoopsUser' => 'ZariliaUser',
'XOOPS_UPLOAD_URL' => 'ZAR_UPLOAD_URL',
'XoopsTree' => 'ZariliaTree',
'XoopsObjectTree' => 'ZariliaObjectTree',
'XoopsObject' => 'ZariliaObject',
'XoopsTpl' => 'ZariliaTpl',
'xoops_' => 'zarilia_',
'\'XOOPS\'' => '\'Zarilia\'',
'XoopsPageNav' => 'ZariliaPageNav',
'$zariliaTpl->assign(\'zarilia_meta_description\',' => '$zariliaTpl->addMeta(\'description\',',
'content="XOOPS"' => 'content="Zarilia"',
'"XOOPS"' => '"Zarilia"',
'XoopsForm' => 'ZariliaForm',
'XOOPS root path not defined' => 'Zarilia root path not defined',
'xoopsformloader.php' => 'zariliaformloader.php',
'XoopsThemeForm' => 'ZariliaThemeForm',
'xoopsForm' => 'ZariliaForm',
'xoopscomments' => 'zariliacomments',
'XOOPS_COMMENT_ACTIVE' => 'ZARILIA_COMMENT_ACTIVE',
'$zariliaDB->Execute($sql,$limit,$offset)' => '$zariliaDB->SelectLimit($sql,$limit,$offset)',
'$zariliaDB->fetchArray($result)' => '$result->FetchRow()',
'XOOPS_CACHE_PATH' => 'ZAR_CACHE_PATH',
'xoopsblock.php' => 'zariliablock.php',
'XOOPS_CONF_METAFOOTER' => 'ZAR_CONF_METAFOOTER',
'XOOPS_USE_MULTIBYTES' => 'ZAR_USE_MULTIBYTES',
'XOOPS_VERSION' => 'ZARILIA_VERSION',
'\'XOOPS \'' => '\'Zarilia \'',
'xoopseditor' => 'zariliaeditor',
'XOOPS_CONF_SEARCH' => 'ZAR_CONF_SEARCH',
'$zariliaTpl->assign(\'zarilia_meta_keywords\',' => '$zariliaTpl->addMeta(\'keywords\',',
'XOOPS_UPLOAD_PATH' => 'ZAR_UPLOAD_PATH',
'XoopsPerms' => 'ZariliaPerms',
'xoopsstory.php' => 'zariliastory.php',
'XoopsTopic' => 'ZariliaTopic',
'XOOPS_COMMENT_APPROVENONE' => 'ZAR_COMMENT_APPROVENONE',
'xoopsUser' => 'ZariliaUser',
'XOOPS_NOTIFICATION_MODE_SENDONCETHENDELETE' => 'ZAR_NOTIFICATION_MODE_SENDONCETHENDELETE',
'XoopsMediaUploader' => 'ZariliaMediaUploader',
'xoopstopic.php' => 'zariliatopic.php',
'xoopslists.php' => 'zarilialists.php',
'xoopsform' => 'zariliaform',
'XoopsGroupPermForm' => 'ZariliaGroupPermForm',
'xoopsGetElementById' => 'zariliaGetElementById',
'XoopsLists' => 'ZariliaLists',
'$zariliaModuleConfig' => '$zariliaAddonConfig',
'$zariliaModule' => '$zariliaAddOn',
'zarilia_gethandler(\'module\')' => 'zarilia_gethandler(\'addon\')',
'XoopsStory' => 'ZariliaStory',
'zariliastree.php' => 'zariliatree.php',
'modinfo.php' => 'addoninfo.php',
'$zariliaAddOn' => '$zariliaAddon',
'$zariliaAddon->name()' => '$zariliaAddon->getVar(\'name\')',
'$zariliaDB->ExecuteF' => '$zariliaDB->Execute',
'$zariliaDB->getRowsNum($result)' => '$result->NumRows()',
'define("_AM_ACTION","Action");' => '',
'$zariliaAddon->mid()' => '$zariliaAddon->getVar(\'mid\')',
' =& ' => ' = &',
'&Database::getInstance()' => '&$zariliaDB',
' =&' => '=&',
'$this->db=&$zariliaDB;' => 'global $zariliaDB; $this->db=&$zariliaDB;',
'$db=&$zariliaDB;' => 'global $zariliaDB; $db=&$zariliaDB;',
'$myrow = $db->fetchArray($result)' => '$myrow = $result->FetchRow()',
'$array = $db->fetchArray($result)' => '$array = $result->FetchRow()',
'$zariliaAddon->dirname()' => '$zariliaAddon->getVar(\'dirname\')',
'$this->db->fetchRow($result)' => '$result->FetchRow()',
'$this->db->getRowsNum($result)' => '$result->NumRows()',
'->query' => '->Execute',
'makeTboxData4Show' => 'displayTarea',
'module.textsanitizer.php' => 'class.textsanitizer.php',
'$modversion' => '$addonversion',
'XoopsModule' => 'ZariliaAddOn',
'include_once ZAR_ROOT_PATH."/class/xoopsmodule.php";' => '',
'define("_ADD"' => 'if (!defined(\'_ADD\')) define("_ADD"',
'define("_EDIT"' => 'if (!defined(\'_EDIT\')) define("_EDIT"',
'define("_DELETE"' => 'if (!defined(\'_DELETE\')) define("DELETE"',
'OpenTable();' => '',
'CloseTable();' => '',
'ZAR_ROOT_PATH.\'/uploads' => 'ZAR_UPLOAD_PATH.\'',
'ZAR_URL.\'/uploads' => 'ZAR_UPLOAD_URL.\'',
'zarilia_getmodulehandler'=>'zarilia_getaddonhandler',
'XoopsSimpleForm' => 'ZariliaSimpleForm',
'$zariliaDB->getRowsNum($result)' => '$result->RecordCount()',
'quoteString(' => 'qstr(',
'$this->db->fetchArray($result)'=>'$result->fetchRow()',
'online_module' => 'online_addon',
'xoopsModule' => 'zariliaAddon',
'xoopsDB' => 'zariliaDB',
'xoopsModuleConfig' => 'zariliaModuleConfig'
//'loadModuleAdminMenu' => 'compatTemplate::loadModuleAdminMenu' // ZAR_ROOT_PATH.'class/compat/template.php'
		);
		$contents = file_get_contents( "$source/$file" );
		$contents = str_replace(array_keys($replace_data), array_values($replace_data), $contents);
/*		foreach ($replace_data as $search => $replace) {
			//$contents = mb_str_replace($contents, $search, $replace);

		}*/
		if ($file == 'modinfo.php') $file = 'addoninfo.php';
		if ($file == 'xoops_version.php') $file = 'zarilia_version.php';
	break;
	default:
		copy("$source/$file", "$dest/$file");
		return;
}

$handle = fopen( "$dest/$file", "w" );
fwrite( $handle, $contents );
fclose( $handle );

?>