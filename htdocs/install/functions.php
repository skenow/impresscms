<?php
function toNumeric($val) {
	$val = trim($val);

	switch (strtolower(substr($val, -1))) {
		case 'm':
			$val = (int) substr($val, 0, -1) * 1048576;
			break;

		case 'k':
			$val = (int) substr($val, 0, -1) * 1024;
			break;

		case 'g':
			$val = (int) substr($val, 0, -1) * 1073741824;
			break;

		case 'b':
			switch (strtolower(substr($val, -2, 1))) {
				case 'm':
					$val = (int) substr($val, 0, -2) * 1048576;
					break;

				case 'k':
					$val = (int) substr($val, 0, -2) * 1024;
					break;

				case 'g':
					$val = (int) substr($val, 0, -2) * 1073741824;
					break;

				default :
					break;
			}
			break;

		default:
			break;
	}
	return $val;
}

function compare_values($a, $b, $operator = '=') {
	return version_compare(toNumeric($a), toNumeric($b), $operator);
}
