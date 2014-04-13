Installation:

1. Kopieren Sie den Inhalt aus htdocs in ihr ICMS_ROOT
   (dort wo die mainfile.php liegt)
2. den Inhalt aus thrust_path entsprechend dorthin wo ihr Thrust_Path liegt
   (gegebenfalls in der mainfile.php nachschauen)
3. öffnen Sie die ICMS_ROOT/header.php
4. Gehen Sie zur Zeile 30 bzw. suchen Sie folgende Zeile
   $xoopsTpl = $icmsTpl =& $xoTheme->template;
5. Fügen Sie unter diesen Eintrag in eine neue Zeile mit folgendem ein :
   $xoopsTpl->load_filter('output', 'xoRewriteModule');
6. Konfiguration des xorewrite:
   a) Konfigurationsdatei /THRUST_PATH/xorewrite/xoRewriteModule.ini.php öffnen
   b) einige Beispiele sind vorgegeben, bitte alles in Kommentaren stehen lassen!!
   c) Sysntax am Beispiel des Modules content welches in /modules/content installiert ist:
      content  = "infos"
      heist
      Modulverzeichnisname  = angezeigter Name in der URL
      wichtig, bitte den Verzeichnisnamen des Modules angeben, nicht den Modulnamen!
7. manuelle Erstellung der .htaccess
   (diese muss in das ICMSS_ROOT)
   Inhalt:

  RewriteEngine on

  #Icms : Start xoRewriteModule
  RewriteRule ^infos/(.*)$ modules/content/$1 [L]
  RewriteRule ^banner/(.*)$ modules/banners/$1 [L]
  RewriteRule ^user/(.*)$ modules/profile/$1 [L]
  #Icms : End xoRewriteModule

   u.s.w
8. automatische Erstellung der .htaccess
   a) Konfigurationsdatei /THRUST_PATH/xorewrite/xoRewriteHtaccess.ini.php öffnen
   b) SERVER_NAME = "localhost"  <- hier statt localhost die Domain eintragen
   c) SERVER_ADDR = "127.0.0.1"  <- hier statt 127.0.0.1 eure IP eintragen
   ACHTUNG! Dies funktioniert nicht auf jedem Server!!!


Fragen und Fehlermeldungen bitte ins Forum bei SIMPLE-XOOPS schreiben
http://www.simple-xoops.de