<?php

$protocol = strpos(strtolower($_SERVER['SERVER_PROTOCOL']),'https') === FALSE ? 'http://' : 'https://';
$host     = $_SERVER['HTTP_HOST'];
$script   = $_SERVER['SCRIPT_NAME'];
$params   = $_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '';
 
$currentUrl = $protocol . $host . $script . $params;
$baseUrl = ICMS_URL;
$rootPath = ICMS_ROOT_PATH;
$trustPath = $targetTrustPath;

?>

<!doctype html>
<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="<{$icms_langcode}>"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="<{$icms_langcode}>"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="<{$icms_langcode}>"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="<{$icms_langcode}>"> <!--<![endif]-->
<head>
  <meta charset="utf-8">
  <title>Install ICMS</title>
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width">
  <script type="text/javascript" src="tpl/js/modernizr.js"></script>
  <script type="text/javascript" data-main="" src="tpl/js/jquery.js"></script>
  <script type="text/javascript" data-main="" src="tpl/js/twitter_bootstrap.js"></script>
  <link rel="stylesheet" href="tpl/css/bootstrap.min.css" />
</head>
<body>
  <!-- Prompt IE 6 users to install Chrome Frame. Remove this if you support IE 6.
       chromium.org/developers/how-tos/chrome-frame-getting-started -->
  <!--[if lt IE 7]><p class=chromeframe>Your browser is <em>ancient!</em> <a href="http://browsehappy.com/">Upgrade to a different browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to experience this site.</p><![endif]-->
  
  <div id="fullWrapper" class="fullWrapper">

    <div id="page" class="page">
      <div class="inner">
        <div id="header" class="header">
          <h1><img src="tpl/img/logo.png" alt="ImpressCMS" /></h1>
        </div>
        <div id="installer" class="installer">
          <div class="inner">
            <h3>Welcome, Thanks for Installing ICMS!</h3>
            <p>We need to collect some information from you before we begin...</p>

            <form class="form-horizontal">
              <legend>Site Info</legend>
              <div class="control-group">
                <label class="control-label" for="site_name">Site Name <span class="tip" title="Provide the name of your site." type="button"><i class="icon-info-sign"></i></span></label>
                <div class="controls">
                  <div class="input-prepend">
                    <span class="add-on"><i class="icon-list"></i></span>
                    <input class="input-large" type="text" id="site_name" name="site_name" placeholder="ImpressCMS">
                  </div>
                </div>
              </div>

              <div class="control-group">
                <label class="control-label" for="site_slogan">Site Slogan <span class="tip" title="Provide the slogan of your site." type="button"><i class="icon-info-sign"></i></span></label>
                <div class="controls">
                  <div class="input-prepend">
                    <span class="add-on"><i class="icon-list"></i></span>
                    <input class="input-large" type="text" id="site_slogan" name="site_slogan" placeholder="Make a lasting impression!">
                  </div>
                </div>
              </div>

              <div class="control-group">
                <label class="control-label" for="site_language">Site Language <span class="tip" title="Select the primary language for this site." type="button"><i class="icon-info-sign"></i></span></label>
                <div class="controls">
                  <select id="site_language" name="site_language">
                    <option val="en">US English</option>
                  </select>
                  <span class="help-inline">
                    <small><a href="#" target="_blank">Install another language</a></small>
                  </span>
                </div>
              </div>

              <legend>Your Account</legend>
              <div class="control-group">
                <label class="control-label" for="site_admin_email">Admin Email <span class="tip" title="Provide the Administrator's email." type="button"><i class="icon-info-sign"></i></span></label>
                <div class="controls">
                  <div class="input-prepend">
                    <span class="add-on"><i class="icon-envelope"></i></span>
                    <input class="input-large" type="email" id="site_admin_email" name="site_admin_email">
                  </div>
                </div>
              </div>
            
              <div class="control-group">
                <label class="control-label" for="site_admin_display">Admin Display Name <span class="tip" title="Provide the Administrator's display name." type="button"><i class="icon-info-sign"></i></span></label>
                <div class="controls">
                  <div class="input-prepend">
                    <span class="add-on"><i class="icon-user"></i></span>
                    <input class="input-large" type="text" id="site_admin_display" name="site_admin_display">
                  </div>
                </div>
              </div>

              <div class="control-group">
                <label class="control-label" for="site_admin_uname">Admin Login Name <span class="tip" title="Provide the Administrator's login name." type="button"><i class="icon-info-sign"></i></span></label>
                <div class="controls">
                  <div class="input-prepend">
                    <span class="add-on"><i class="icon-user"></i></span>
                    <input class="input-large" type="text" id="site_admin_uname" name="site_admin_uname">
                  </div>
                </div>
              </div>

              <div class="control-group">
                <label class="control-label" for="site_admin_pass">Password <span class="tip" title="Provide the Administrator's password." type="button"><i class="icon-info-sign"></i></span></label>
                <div class="controls">
                  <div class="input-prepend">
                    <span class="add-on"><i class="icon-lock"></i></span>
                    <input class="input-large" type="password" id="site_admin_pass" name="site_admin_pass">
                  </div>
                </div>
              </div>

              <div class="control-group">
                <label class="control-label" for="site_admin_pass_confirm">Confirm Password <span class="tip" title="Confirm the Administrator's password." type="button"><i class="icon-info-sign"></i></span></label>
                <div class="controls">
                  <div class="input-prepend">
                    <span class="add-on"><i class="icon-lock"></i></span>
                    <input class="input-large" type="password" id="site_admin_pass_confirm" name="site_admin_pass_confirm">
                  </div>
                </div>
              </div>

              <legend>Path Settings</legend>
              <div class="control-group">
                <label class="control-label" for="site_url">Url <span class="tip" title="Confirm the URI for this site." type="button"><i class="icon-info-sign"></i></span></label>
                <div class="controls">
                  <div class="input-prepend">
                    <span class="add-on"><i class="icon-home"></i></span>
                    <input class="input-large" type="text" id="site_url" name="site_url" placeholder="<?php echo $baseUrl ;?>" value="<?php echo $baseUrl ;?>">
                  </div>
                </div>
              </div>

              <div class="control-group">
                <label class="control-label" for="site_path">Physical Path <span class="tip" title="Confirm the physical path of this installation." type="button"><i class="icon-info-sign"></i></span></label>
                <div class="controls">
                  <div class="input-prepend">
                    <span class="add-on"><i class="icon-hdd"></i></span>
                    <input class="input-large" type="text" id="site_path" name="site_path" placeholder="<?php echo $rootPath;?>" value="<?php echo $rootPath;?>">
                  </div>
                </div>
              </div>

              <div class="control-group">
                <label class="control-label" for="site_trust">Trust Path <span class="tip" title="Select a location outside of your webroot for your secure trust path" type="button"><i class="icon-info-sign"></i></span></label>
                <div class="controls">
                  <div class="input-prepend input-append">
                    <span class="add-on"><i class="icon-hdd"></i></span>
                    <input class="input-large" type="text" id="site_trust" named="site_trust" placeholder="<?php echo $trustPath;?>" value="<?php echo $trustPath;?>">
                    <button id="createTrustPath" class="btn btn-warning" type="button">Create Trust Path</button>
                  </div>
                </div>
              </div>

              <legend>Database Connection</legend>
              <div class="control-group">
                <label class="control-label" for="site_db">Databse Type <span class="tip" title="Select the type of database to use." type="button"><i class="icon-info-sign"></i></span></label>
                <div class="controls">
                  <select name="site_db" id="site_db" name="site_db">
                    <option val="mysql">MySql</option>
                  </select>
                  <span class="help-inline">
                    <small><a href="#">Install another database type</a></small>
                  </span>
                </div>
              </div>

              <div class="control-group">
                <label class="control-label" for="site_db_host">Server Hostname <span class="tip" title="Provide the database location." type="button"><i class="icon-info-sign"></i></span></label>
                <div class="controls">
                  <div class="input-prepend">
                    <span class="add-on"><i class="icon-globe"></i></span>
                    <input class="input-large" type="text" id="site_db_host" name="site_db_host" placeholder="<?php echo $site_db_host;?>" value="<?php echo $site_db_host;?>">
                  </div>
                </div>
              </div>

              <div class="control-group">
                <label class="control-label" for="site_db_name">Database Name <span class="tip" title="Provide the name of the database." type="button"><i class="icon-info-sign"></i></span></label>
                <div class="controls">
                  <div class="input-prepend">
                    <span class="add-on"><i class="icon-briefcase"></i></span>
                    <input class="input-large" type="text" id="site_db_name" name="site_db_name" placeholder="<?php echo $site_db_name;?>" value="<?php echo $site_db_name;?>">
                  </div>
                </div>
              </div>

              <div class="control-group">
                <label class="control-label" for="site_db_user">Database User <span class="tip" title="Provide the database user's name." type="button"><i class="icon-info-sign"></i></span></label>
                <div class="controls">
                  <div class="input-prepend">
                    <span class="add-on"><i class="icon-user"></i></span>
                    <input class="input-large" type="text" id="site_db_user" name="site_db_user" placeholder="<?php echo $site_db_user;?>" value="<?php echo $site_db_user;?>">
                  </div>
                </div>
              </div>

              <div class="control-group">
                <label class="control-label" for="site_db_pass">Database Pass <span class="tip" title="Provide the database password." type="button"><i class="icon-info-sign"></i></span></label>
                <div class="controls">
                  <div class="input-prepend">
                    <span class="add-on"><i class="icon-lock"></i></span>
                    <input class="input-large" type="password" id="site_db_pass" name="site_db_pass">
                  </div>
                </div>
              </div>

              <div class="control-group">
                <div class="controls">
                  <a href="#" class="toggle" data-toggle=".advanced_db">
                    <span class="add-on"><i class="icon-resize-full"></i></span> Advanced Database Controls
                  </a>
                </div>
              </div>

              <div class="hidden advanced_db">
                <div class="control-group">
                  <div class="controls">
                    <label class="checkbox inline">
                      <input type="checkbox" name="site_db_persist" id="site_db_persist" value="1"> Use Persistant Connection?
                      <span class="tip" title="Use a persistant connection to your database?"><i class="icon-info-sign"></i></span>
                    </label>

                  </div>
                </div>

                <div class="control-group">
                  <label class="control-label" for="site_db_prefix">Database Prefix <span class="tip" title="Provide the database prefix used for your install."><i class="icon-info-sign"></i></span></label>
                  <div class="controls">
                    <input class="input-xlarge" type="text" id="site_db_prefix" name="site_db_prefix" value="<?php echo $site_db_prefix;?>">
                  </div>
                </div>

                <div class="control-group">
                  <label class="control-label" for="site_pw_salt_key">Password Salt Key <span class="tip" title="Provide the salt used to encrypt your passwords."><i class="icon-info-sign"></i></span></label>
                  <div class="controls">
                    <input class="input-xlarge" type="text" id="site_pw_salt_key" name="site_pw_salt_key" value="<?php echo $site_pw_salt_key;?>">
                  </div>
                </div>

                <div class="control-group">
                  <label class="control-label" for="site_db_charset">Databse Charset <span class="tip" title="Select your database character set.<br />(Default: UTF-8)"><i class="icon-info-sign"></i></span></label>
                  <div class="controls">
                    <select name="site_db_charset" id="site_db_charset" name="site_db_charset">
                      <option val="utf8">UTF-8</option>
                    </select>
                    <small>Do we need all these options?</small>
                  </div>
                </div>

                <div class="control-group">
                  <label class="control-label" for="site_db_collation">Databse Collation <span class="tip" title="Select your database collation.<br />(Default: utf8_general_ci)"><i class="icon-info-sign"></i></span></label>
                  <div class="controls">
                    <select name="site_db_collation" id="site_db_collation" name="site_db_collation">
                      <option val="utf8_general_ci">utf8_general_ci</option>
                    </select>
                    <small>Do we need all these options too?</small>
                  </div>
                </div>
              </div>

              <div class="control-group">
                <div class="controls">
                  <br />
                  <p class="text-info">
                    By Installing you understand that ImpressCMS may collect usage data to help the project better understand adaptation and exposure of the system.<br /><br />
                    <strong class="text-error">No personal or sensitive information will be ever collected by us, and we will never sell your information.</strong><br /><br />
                    <a href="#">View our privacy policy</a>
                  </p>
                  <br />
                  <button type="submit" class="btn btn-primary"><i class="icon-ok icon-white"></i> Let's Go!</button>
                  <button type="button" class="btn btn-danger"><i class="icon-off icon-white"></i> Cancel</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script type="text/javascript">
    var installer = {
      init: function() {
        $(document).ready(function() {
          $('.toggle').on({
            click: function(e) {
              e.preventDefault();
              var ele = $(this).data('toggle');
              $(ele).slideToggle('slow');
            }
          });

          $('.tip').tooltip({
            html: true
          });

          installer.trustPath();
        });
      }

      , trustPath: function() {
        // This is some example js to show states of the createTrustPath button
        $('#createTrustPath').live({
          click: function(e) {
            e.preventDefault();
            var path = $(this).prev('input').val();
            // Error
            $(this).off().attr('id', 'createTrustPathError').addClass('btn-danger').text('Error...').closest('.control-group').append('<div class="createPathAlert"><br /><div class="alert alert-error">Error attepting to create: <strong>' + path + '</strong>.<br />Please manually create folder and try again.</div></div>');
            return false;
          }
        });
        $('#createTrustPathError').live({
          click: function(e) {
            e.preventDefault();
            var _this = $(this)
            , path = _this.prev('input').val();
            $('.createPathAlert').remove();
            _this.off().attr('id', 'createTrustPathSuccess').removeClass('btn-danger').addClass('btn-success').text('Created!');
            _this.closest('.control-group').append('<div class="createPathAlert"><br /><div class="alert alert-success">Created: <strong>' + path + '</strong>.</div></div>');

            return false;
          }
        });
        $('#createTrustPathSuccess').live({
          click: function(e) {
            e.preventDefault();
            $('.createPathAlert').remove();
            $(this).off().removeClass('btn-success').attr('id', 'createTrustPath').text('Create Trust Path');
            return false;
          }
        });
      }
    }

    installer.init();
  </script>
</body>
</html>