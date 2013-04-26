<?php

class action_system_db_DropTables extends icms_action_base_Module {

    public function __construct($params = array()) {
        $this->initVar('reserved_tables', self::DTYPE_LIST);
        $this->initVar('tables', self::DTYPE_LIST);
        
        parent::__construct($params);
    }

    public function exec(\icms_action_Response &$response) {
        // delete tables used by this module        
        if (is_array($this->tables)) {
            $response->msg(_MD_AM_MOD_TABLES_DELETE);
            foreach ($this->tables as $table) {
                // prevent deletion of reserved core tables!
                if (!in_array($table, $this->reserved_tables)) {
                    $sql = 'DROP TABLE ' . $db->prefix($table);
                    if (!$db->query($sql)) {
                        $response->error(sprintf('&nbsp;&nbsp;<span style="color:#ff0000;">' . _MD_AM_MOD_TABLE_DELETE_FAIL . '</span>', '<strong>' . icms::$xoopsDB->prefix($table) . '<strong> . '
                        ));
                    } else {
                        $response->msg(sprintf('&nbsp;&nbsp;' . _MD_AM_MOD_TABLE_DELETED, '<strong>' . icms::$xoopsDB->prefix($table) . '</strong>'));
                    }
                } else {
                    $response->error(sprintf('&nbsp;&nbsp;<span style="color:#ff0000;">' . _MD_AM_MOD_TABLE_DELETE_NOTALLOWED . '</span>', '<strong>' . icms::$xoopsDB->prefix($table) . '</strong>'));
                }
            }
        }
    }

}