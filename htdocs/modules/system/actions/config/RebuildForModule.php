<?php

class action_system_config_RebuildForModule extends icms_action_base_Module {

    public function __construct($params = array()) {
        $this->initVar('config', self::DTYPE_ARRAY);
        $this->initVar('module_id', self::DTYPE_INTEGER);
        $this->initVar('dirname', self::DTYPE_INTEGER);
        parent::__construct($params);
    }

    public function exec(\icms_action_Response &$response) {
        // first delete all config entries
        $config_handler = icms::handler('icms_config');
        $configs = & $config_handler->getConfigs(new icms_db_criteria_Item('conf_modid', $this->module_id));
        $confcount = count($configs);
        $config_delng = array();
        $config_old = array();
        if ($confcount > 0) {
            $response->msg(_MD_AM_CONFIGOPTION_DELETED);
            for ($i = 0; $i < $confcount; $i++) {
                if (!$config_handler->deleteConfig($configs[$i])) {
                    $response->msg(sprintf('&nbsp;&nbsp;<span style="color:#ff0000;">' . _MD_AM_CONFIGOPTION_DELETE_FAIL . '</span>', '<strong>' . $configs[$i]->getvar('conf_id') . '</strong>'));
                    // save the name of config failed to delete for later use
                    $config_delng[] = $configs[$i]->getvar('conf_name');
                } else {
                    $config_old[$configs[$i]->getvar('conf_name')]['value'] = $configs[$i]->getvar('conf_value', 'N');
                    $config_old[$configs[$i]->getvar('conf_name')]['formtype'] = $configs[$i]->getvar('conf_formtype');
                    $config_old[$configs[$i]->getvar('conf_name')]['valuetype'] = $configs[$i]->getvar('conf_valuetype');
                    $response->msg(sprintf('&nbsp;&nbsp;' . _MD_AM_CONFIGOPTION_DELETED, '<strong>' . $configs[$i]->getVar('conf_id') . '</strong>'));
                }
            }
        }

        if (!empty($this->config)) {
            $response->msg(_MD_AM_CONFIG_ADDING);
            $config_handler = icms::handler('icms_config');
            $order = 0;
            foreach ($this->config as $config) {
                // only insert ones that have been deleted previously with success
                if (!in_array($config['name'], $config_delng)) {
                    $confobj = & $config_handler->createConfig();
                    $confobj->setVar('conf_modid', (int) $newmid);
                    $confobj->setVar('conf_catid', 0);
                    $confobj->setVar('conf_name', $config['name']);
                    $confobj->setVar('conf_title', $config['title'], TRUE);
                    $confobj->setVar('conf_desc', $config['description'], TRUE);
                    $confobj->setVar('conf_formtype', $config['formtype']);
                    $confobj->setVar('conf_valuetype', $config['valuetype']);
                    if (isset($config_old[$config['name']]['value'])
                            && $config_old[$config['name']]['formtype'] == $config['formtype']
                            && $config_old[$config['name']]['valuetype'] == $config['valuetype']
                    ) {
                        // preserve the old value if any
                        // form type and value type must be the same
                        // need to deal with arrays, because getInfo('config') doesn't convert arrays
                        if (is_array($config_old[$config['name']]['value'])) {
                            $confobj->setVar('conf_value', serialize($config_old[$config['name']]['value']), TRUE);
                        } else {
                            $confobj->setVar('conf_value', $config_old[$config['name']]['value'], TRUE);
                        }
                    } else {
                        $confobj->setConfValueForInput($config['default'], TRUE);
                    }
                    $confobj->setVar('conf_order', $order);
                    $confop_msgs = '';
                    if (isset($config['options']) && is_array($config['options'])) {
                        foreach ($config['options'] as $key => $value) {
                            $confop = & $config_handler->createConfigOption();
                            $confop->setVar('confop_name', $key, TRUE);
                            $confop->setVar('confop_value', $value, TRUE);
                            $confobj->setConfOptions($confop);
                            $confop_msgs .= sprintf('<br />&nbsp;&nbsp;&nbsp;&nbsp;' . _MD_AM_CONFIGOPTION_ADDED, '<strong>' . $key . '</strong>', '<strong>' . $value . '</strong>');
                            unset($confop);
                        }
                    }
                    $order++;
                    if (FALSE !== $config_handler->insertConfig($confobj)) {
                        $response->msg(sprintf('&nbsp;&nbsp;' . _MD_AM_CONFIG_ADDED, '<strong>' . $config['name'] . '</strong>. ')
                                . $confop_msgs);
                    } else {
                        $response->error(sprintf('&nbsp;&nbsp;<span style="color:#ff0000;">' . _MD_AM_CONFIG_ADD_FAIL . '</span>', '<strong>' . $config['name'] . '</strong>. '));
                    }
                    unset($confobj);
                }
            }
            unset($configs);
        }
    }

}