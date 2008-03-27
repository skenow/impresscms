<?php
require("detect.inc.php");

printf("<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.0 Transitional//EN'>");
printf("<HTML><HEAD>");
printf("<TITLE> Browser Test </TITLE>");
printf("<META NAME='Generator' CONTENT='Glimmer'>");
printf("<META NAME='Author' CONTENT='phpDynaTree'>");
printf("<META NAME='Keywords' CONTENT='PHP Dynamic HTML Tree List'>");
printf("<META NAME='Description' CONTENT='PHP and Dynamic HTML'>");

printf("</HEAD><BODY>");
printf("<script language='javascript'>");

// Konqueror does not appear to handle setting object values properly on Linux, may work 
// better on other systems.

printf("Detect_Capability();");

printf("document.write('This Operating System is: ' + navigator.OS);");
printf("document.write('<BR>This Browser is: ' + navigator.Browser_Family);");
printf("document.write('<BR>This Browser Organization is: ' + navigator.Browser_Organization);");

printf("</script>");
printf("</BODY></HTML>");

?>