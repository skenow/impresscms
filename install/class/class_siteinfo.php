<?php
// $Id: class_siteinfo.php,v 1.2 2007/04/21 09:44:36 catzwolf Exp $
// ------------------------------------------------------------------------ //
// Zarilia - PHP Content Management System                      			//
// Copyright (c) 2007 Zarilia                           				//
// //
// Authors: 																//
// John Neill ( AKA Catzwolf )                                     			//
// Raimondas Rimkevicius ( AKA Mekdrop )									//
// //
// URL: http:www.zarilia.com 												//
// Project: Zarilia Project                                               //
// -------------------------------------------------------------------------//
/**
 * siteinfo_manager
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2007
 * @version $Id: class_siteinfo.php,v 1.2 2007/04/21 09:44:36 catzwolf Exp $
 * @access public
 */
class siteinfo_manager
{
    var $cpConfig = array();
    var $file_main = '../data/settings/site.global.php';
//    var $file_dist = '../siteinfo.dist.php';

    var $report = array();
    var $errors = array();

    /**
     * siteinfo_manager()
     *
     * @return
     */
    function siteinfo_manager()
    {
        // dummy
    }

    function loadSiteinfo()
    {
        global $cpConfig;

		require_once '../class/cache/settings.class.php';
		$zariliaSettings = &ZariliaSettings::getInstance();
		global $zariliaSettings;
		$this->cpConfig = $cpConfig = &$zariliaSettings->readAll('site.global');
    }

    function loadArray( &$siteInfo )
    {
        $this->cpConfig = &$siteInfo;
    }

    /*function loadSiteinfobackup()
    {
        global $cpConfig;
        if ( file_exists( $this->file_main ) )
        {
            include $this->file_main;
            $this->cpConfig = @$cpConfig;
        }
    }*/

    /**
     * Populate var scope with $_post or array given through $value
     *
     * @param mixed $values
     * @return
     */
    function createNew( &$values )
    {
        $getArgs = func_get_args();
		require_once '../class/cache/settings.class.php';
		$zariliaSettings = &ZariliaSettings::getInstance();

		foreach (
			array (
			    'users' => 0,
			    'configoption' => 1,
			    'config' => 1,
			    'events' => 1,
			    'group_permission' => 1,
			    'groups' => 1,
			    'groups_users_link' => 1,
			    'language_base' => 0,
			    'language_ext' => 0,
			    'addons' => 1,
			    'newblocks' => 1,
			    'online' => 1,
			    'tplfile' => 1,
			    'tplset' => 1,
			    'tplsource' => 1,
			    'session' => 0,
			    'block_addon_link' => 1,
			    'ranks' => 0,
			    'zarilianotifications' => 0,
			    'configcategory' => 0,
			    'smiles' => 0,
			    'streaming' => 0,
			    'security' => 0,
			    'avatar' => 0,
			    'mediacategory' => 0,
			    'zariliacomments' => 0,
			    'errors' => 0,
			    'profile' => 1,
			    'messages' => 0,
			    'messages_buddy' => 0,
			    'messages_sent' => 0,
				'avatar_user_link' => 0,
				'mimetypes' => 1
		  ) as $table => $type) {
			$zariliaSettings->write('site.global', 'tables', $table, $type);
		}

		$zariliaSettings->write('site.global', 'db', 'type', $values['database']);
		$zariliaSettings->write('site.global', 'db', 'prefix', $values['prefix']);
		$zariliaSettings->write('site.global', 'db', 'host', $values['dbhost']);
		$zariliaSettings->write('site.global', 'db', 'user', $values['dbuname']);
		$zariliaSettings->write('site.global', 'db', 'pass', $values['dbpass']);
		$zariliaSettings->write('site.global', 'db', 'name', $values['dbname']);
		$zariliaSettings->write('site.global', 'db', 'pconnect', $values['db_pconnect']);
		
        foreach( array( 'admin', 'users', 'anonymous', 'moderators', 'submitters', 'subscription', 'banned' ) as $groupID => $group )  {
			$zariliaSettings->write('site.global', 'groups', $groupID + 1, strtoupper( $group ));
        }
		$zariliaSettings->write('site.global', 'path', 'root', $values['root_path']);
        

        $zariliaPathTrans = isset( $_SERVER['PATH_TRANSLATED'] ) ? $_SERVER['PATH_TRANSLATED'] : $_SERVER['SCRIPT_FILENAME'];
        if ( DIRECTORY_SEPARATOR != '/' ) {
            // IIS6 doubles the \ chars
            $zariliaPathTrans = str_replace( strpos( $zariliaPathTrans, '\\\\', 2 ) ? '\\\\' : DIRECTORY_SEPARATOR, '/', $zariliaPathTrans );
        }
		$zariliaSettings->write('site.global', 'path', 'check', strcasecmp( substr( $zariliaPathTrans, 0, strlen( stripslashes( $_POST['root_path'] ) ) ), $values['root_path'] ) ? 0 : 1);

		$zariliaSettings->write('site.global', 'sites', '::krx', 'dpx');
		$zariliaSettings->remove('site.global', 'sites', '::krx');

		$this->cpConfig = $cpConfig = &$zariliaSettings->readAll('site.global');

		$zariliaSettings->write('siteinfo.default', 'config', 'url', $values['zarilia_url']);
		$zariliaSettings->write('siteinfo.default', 'config', 'prefix', $values['prefix']);

    }

    /**
     * Save Siteinfo data to file
     *
     * @param array $siteInfo
     * @return
     */
    function saveSiteinfo( $siteInfo = '' )  {
        /*clearstatcache();
        if ( is_array( $siteInfo ) && !empty( $siteInfo ) )
        {
            $this->cpConfig = &$siteInfo;
        }

        $mode = ( !file_exists( $this->file_main ) ) ? 'x' : 'w';
        $tp = @fopen( $this->file_main, $mode );
        $newline = "\n";
        $copyright = $this->getCopyright();
        fwrite( $tp, '<?php ' . $newline );
        fwrite( $tp, $copyright . $newline . $newline );

        fwrite( $tp, ' $cpConfig = ' . var_export( $this->cpConfig, true ) . ';' . $newline );
        fwrite( $tp, '?>' );
        fclose( $tp );
        if ( $tp )
        {
            return true;
        }
        else
        {
            return false;
        }*/
		return true;
    }

    /**
     * Update Vars within the var scope of siteinfo
     *
     * @param mixed $key
     * @param mixed $level
     * @param mixed $value
     * @return
     */
    function updateVars( $key, $level, $value )
    {
        if ( isset( $level ) && !empty( $level ) )
        {
            if ( isset( $this->cpConfig[$key][$level] ) )
            {
                $this->cpConfig[$key][$level] = $value;
            }
        }
        else
        {
            if ( isset( $this->cpConfig[$key] ) )
            {
                $this->cpConfig[$key] = $value;
            }
        }
    }

    /**
     * Copy the dist file over the orignal
     *
     * @return
     */
    /*function copyDistFile()
    {
        if ( !copy( $this->file_dist, $this->file_main ) )
        {
            $this->report[] = _NGIMG . sprintf( _INSTALL_L126, '<b>' . $this->file_main . '</b>' );
            $this->error = true;
            return false;
        }
        $this->report[] = _OKIMG . sprintf( _INSTALL_L125, '<b>' . $this->file_main . '</b>', '<b>' . $this->file_dist . '</b>' );
        return true;
    }*/

    function getCopyright()
    {
        $ret = "// ------------------------------------------------------------------------ //
// Zarilia - PHP Content Management System                      			//
// Copyright (c) 2007 Zarilia                           					//
// //
// Authors: 																//
// John Neill ( AKA Catzwolf )                                     			//
// Raimondas Rimkevicius ( AKA Mekdrop )									//
// //
// URL: http:www.zarilia.com 												//
// Project: Zarilia Project                                               	//
// -------------------------------------------------------------------------//

/** DO NOT EDIT THIS FILE UNLESS YOU KNOW WHAT YOU ARE DOING: YOU COULD SHUTDOWN YOUR SITE!!**/";
        return $ret;
    }
}

?>