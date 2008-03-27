<?php


function ZariliaControl_LiveUpdate_Handler($name) {
	$objResponse = new xajaxResponse();
	foreach ($_SESSION['liveUpdate'] as $update_script => $update_data) {
		foreach ($update_data as $update_function => $update_data2) {
			foreach ($update_data2 as $serialized_params => $update_data3) {
				if (abs($update_data3['time'] + $update_data3['interval']) > time()) {
					require_once $update_script;
					$update_data3['time'] = time();
					$data = eval("return $update_function(unserialize('$serialized_params'));");
					if ($data != $update_data3['content']) {
						$update_data3['content'] = $data;
//						$js = '';
						foreach ($update_data3['areas'] as $area_nr => $area_name) {
//							$js .= '\''.$area_nr.'\':'.'\''.$area_name.'\',';
//							$objResponse->script("alert('$area_name');");
							$objResponse->assign($area_name, 'innerHTML', $data);
						}
						$js = substr($js, 0, -1);
						$data = '\''.urlencode($data).'\'';						
				//		$objResponse->script("window.liveUpdate_Update(data = ".'{'.$js.'}'.", '$data');");
//						$objResponse->script("window.liveUpdate_Update(data = ".'{'.$js.'}'.", '52');");
//						$objResponse->script("window.liveUpdate_Test();");
					}
				}
			}
		}
	}
    return $objResponse;
}

?>
