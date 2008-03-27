<?php

require_once('dyna.inc.php');

$icon = 'folder.gif';



$menu  = new HTML_TreeMenu("menuLayer", 'images', '_self');



$node1 = new HTML_TreeNode("INBOX", "info.php", $icon);

$foo   = &$node1->addItem(new HTML_TreeNode("deleted-items", "info.php", $icon));

$bar   = &$foo->addItem(new HTML_TreeNode("deleted-items", "info.php", $icon));

$blaat = &$bar->addItem(new HTML_TreeNode("deleted-items", "info.php", $icon));

$blaat->addItem(new HTML_TreeNode("deleted-items", "info.php", $icon));



$node1->addItem(new HTML_TreeNode("sent-items",    "info.php", $icon));

$node1->addItem(new HTML_TreeNode("drafts",        "info.php", $icon));

	

$menu->addItem($node1);

$menu->addItem(new HTML_TreeNode("drafts", "info.php", $icon));

$menu->addItem(new HTML_TreeNode("drafts", "info.php", $icon));//$node1);



printf("<HTML><HEAD>");



printf("<TITLE> Dynamic Tree Test </TITLE>");



printf("<META NAME='Generator' CONTENT='Glimmer'>");

printf("<META NAME='Author' CONTENT='phpDynaTree'>");

printf("<META NAME='Keywords' CONTENT='PHP Dynamic HTML Tree List'>");

printf("<META NAME='Description' CONTENT='PHP and Dynamic HTML'>");



printf("</HEAD><BODY>");

printf("<DIV ID='menuLayer'></DIV>");



$menu->printMenu();



printf("</BODY></HTML>");



?>

