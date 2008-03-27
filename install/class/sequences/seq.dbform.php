<?php
$javacript = "<script type='text/javascript'>
	<!--//
	function zariliaFormValidate_zariliainstall() {
		myform = window.document.zariliainstall;
		if ( myform.dbhost.value == \"\" ) {
			window.alert(\"Please enter required value for 'Database Hostname'\"); myform.dbhost.focus(); return false;
		}
		if ( myform.dbuname.value ==\"\"  ) {
			window.alert(\"Please enter required value for 'Database Username'\"); myform.dbuname.focus(); return false;
		}
		/*if ( myform.dbpass.value == \"\"  ) {
			window.alert(\"Please enter required value for 'Database Password'\"); myform.dbpass.focus(); return false;
		}*/
		if ( myform.dbname.value == \"\"  ) {
			window.alert(\"Please enter required value for 'Database Name'\"); myform.dbname.focus(); return false;
		}
		if ( myform.prefix.value == \"\"  ) {
			window.alert(\"Please enter required value for 'Table Prefix'\"); myform.prefix.focus(); return false;
		}
		if ( myform.root_path.value == \"\"  ) {
			window.alert(\"Please enter required value for 'Zarilia Physical Path'\"); myform.root_path.focus(); return false;
		}
		if ( myform.zarilia_url.value == \"\"  ) {
			window.alert(\"Please enter required value for 'Zarilia Virtual Path'\"); myform.zarilia_url.focus(); return false;
		}
		return true;
	}
	//--></script>";
$onclick = 'onsubmit="return zariliaFormValidate_zariliainstall();"';

include_once 'class/settingmanager.php';
$installer->setArgs( 'title', _INSTALL_L90 );
$installer->setArgs( 'subtitle', _INSTALL_L90a );
$installer->setArgs( 'javascript', $javacript );
$installer->setArgs( 'onclick', $onclick );

//include '../siteinfo.php';

chdir('..');
include 'mainfile.php';
chdir('install');

$setting_manager = new setting_manager();
$setting_manager->readConstant();
$content = $setting_manager->editform();
$installer->setArgs( 'content', $content );
$installer->render();

?>