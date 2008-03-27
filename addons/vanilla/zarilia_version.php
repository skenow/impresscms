<?php
$addonversion['name'] = 'Vanilla';
$addonversion['version'] = 0.1;
$addonversion['description'] = '-||-';
$addonversion['author'] = "MekDrop";
$addonversion['credits'] = "Zarilia";
$addonversion['license'] = "GPL see LICENSE";
$addonversion['official'] = 1;

// Admin things
$addonversion['hasMain'] = 1;
//$addonversion['adminindex'] = "admin/index.php";

$addonversion['onInstall'] = 'addon.setup.php';
$addonversion['onUpdate'] = 'addon.setup.php';
$addonversion['onUninstall'] = 'addon.setup.php';

$addonversion['sqlfile']['mysql'] = "sql/mysql.sql";

$addonversion['tables'] = array('vanilla_category', 'vanilla_categoryblock', 'vanilla_categoryroleblock', 'vanilla_comment', 'vanilla_discussion', 'vanilla_discussionuserwhisperfrom', 'vanilla_discussionuserwhisperto', 'vanilla_iphistory', 'vanilla_role', 'vanilla_style', 'vanilla_user' ,'vanilla_userbookmark', 'vanilla_userdiscussionwatch', 'vanilla_userrolehistory');

$addonversion['globaltables'] = &$addonversion['tables'];


?>