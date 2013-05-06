<?php

class action_system_autotasks_RebuildForModule extends icms_action_base_Module {

    public function __construct($params = array()) {
        $this->initVar('autotasks', self::DTYPE_ARRAY);
        $this->initVar('module_id', self::DTYPE_INTEGER);
        $this->initVar('dirname', self::DTYPE_INTEGER);
        parent::__construct($params);
    }

    public function exec(\icms_action_Response &$response) {
        $response->msg(_MD_AM_AUTOTASK_UPDATE);
        $atasks_handler = &icms_getModuleHandler('autotasks', 'system');
        $criteria = new icms_db_criteria_Compo();
        $criteria->add(new icms_db_criteria_Item('sat_type', 'addon/' . $this->dirname));
        $items_atasks = $atasks_handler->getObjects($criteria, FALSE);
        foreach ($items_atasks as $task) {
            $taskID = (int) $task->getVar('sat_addon_id');
            $atasks[$taskID]['enabled'] = $task->getVar('sat_enabled');
            $atasks[$taskID]['repeat'] = $task->getVar('sat_repeat');
            $atasks[$taskID]['interval'] = $task->getVar('sat_interval');
            $atasks[$taskID]['name'] = $task->getVar('sat_name');
        }
        $atasks_handler->deleteAll($criteria);
        foreach ($this->autotasks as $taskID => $taskData) {
            if (!isset($taskData['code']) || trim($taskData['code']) == '')
                continue;
            $task = &$atasks_handler->create();
            if (isset($taskData['enabled']))
                $task->setVar('sat_enabled', $taskData['enabled']);
            if (isset($taskData['repeat']))
                $task->setVar('sat_repeat', $taskData['repeat']);
            if (isset($taskData['interval']))
                $task->setVar('sat_interval', $taskData['interval']);
            if (isset($taskData['onfinish']))
                $task->setVar('sat_onfinish', $taskData['onfinish']);
            $task->setVar('sat_name', $taskData['name']);
            $task->setVar('sat_code', $taskData['code']);
            $task->setVar('sat_type', 'addon/' . $this->dirname);
            $task->setVar('sat_addon_id', (int) $taskID);
            if (!($atasks_handler->insert($task))) {
                $response->msg(sprintf('&nbsp;&nbsp;<span style="color:#ff0000;">' . _MD_AM_AUTOTASK_FAIL . '</span>', '<strong>' . $taskData['name'] . '</strong>'));
            } else {
                $response->msg(sprintf('&nbsp;&nbsp;' . _MD_AM_AUTOTASK_ADDED, '<strong>' . $taskData['name'] . '</strong>'));
            }
        }
    }

}