<?

include_once 'mainfile.php';
include_once ICMS_ROOT_PATH . '/header.php';

function read_dir($dir) {
	$dirs = IcmsLists::getDirListAsArray($dir);
	foreach ($dirs as $dir2) {
		print_files($dir . '/' . $dir2);
	}
	print_files($dir);
}

function print_files($dir) {
	$files = IcmsLists::getFileListAsArray($dir);
	foreach ($files as $file) {
		echo str_replace(ICMS_ROOT_PATH, '', $dir) . ',' . $file. '<br />';
	}
}

$path = ICMS_ROOT_PATH . '/kernel';
read_dir($path);

exit;


$path = ICMS_ROOT_PATH . '/kernel';
$files = IcmsLists::getFileListAsArray($path);

foreach ($files as $file) {
	echo str_replace(ICMS_ROOT_PATH, '', $path) . ',' . $file. '<br />';
}


include_once ICMS_ROOT_PATH . '/footer.php';