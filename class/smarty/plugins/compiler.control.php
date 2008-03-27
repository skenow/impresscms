<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

function smarty_compiler_control($tag_attrs, &$compiler)
{
    $_params = $compiler->_parse_attrs($tag_attrs);

    if (!isset($_params['name'])) {
        $compiler->_syntax_error("assign: missing 'name' parameter", E_USER_WARNING);
        return;
    }

	$_params['name'] = substr($_params['name'], 1, -1);
    $url = ZAR_CONTROLS_PATH.'/'.strtolower($_params['name']).'/control.class.php';

	if (($count = count($_params))>1) {
		$prx = array();
		$last_index = 0;
		foreach ($_params as $name => $value) {
			if ($name === 'name') {
				continue;
			}
			$prx[$name] = $value;			
			if ($name > $last_index) {
				$last_index = $name;
			}
		}
		if (($count-1)<$last_index) {
			for($i=1;$i<($last_index);$i++) {
				if (!isset($prx[$i])) {
					$prx[$i] = 'null';
				}
			}
			ksort($prx);
		}
		return "require_once '$url';
	        \$control = new ZariliaControl_{$_params[name]}(".implode(',', $prx).");
			echo \$control->render();
			unset(\$control);
		   ";
	} else {
		return "require_once '$url';
	        \$control = new ZariliaControl_{$_params[name]}();
			echo \$control->render();
			unset(\$control);
		   ";
	}		
}

/* vim: set expandtab: */

?>