<?php

class action_system_db_GetReservedTables
    extends icms_action_base_Module {
    
    /**
     * Reserved tables names list without prefixes
     *
     * @var array 
     */
    public static $reservedTables = array(
		'avatar', 'avatar_users_link', 'block_module_link', 'xoopscomments',
		'config', 'configcategory', 'configoption', 'image', 'imagebody',
		'imagecategory', 'imgset', 'imgset_tplset_link', 'imgsetimg', 'groups',
		'groups_users_link', 'group_permission', 'online',
		'priv_msgs', 'ranks', 'session', 'smiles', 'users', 'newblocks',
		'modules', 'tplfile', 'tplset', 'tplsource', 'xoopsnotifications',
	);
    
    
    public function exec(\icms_action_Response &$response) {
        $response->add('reserved_tables', self::$reservedTables);
    }
    
}