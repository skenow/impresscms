<?php
if (!file_exists("$dest/$file")) {
	mkdir("$dest/$file");
}
if (!file_exists("$dest/$file/index.html")) {
	$handle = fopen("$dest/$file/index.html", "w");
	fwrite($handle, ' <script>history.go(-1);</script>');
	fclose($handle);
}
?>