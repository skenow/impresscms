<?php
  $icmsJsConfigData = array(
    'jscore' => ICMS_LIBRARIES_URL . '/jscore/',
    'url' => ICMS_URL
  );
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
  <link rel="stylesheet" href="<?php echo ICMS_URL; ?>/install/tpl/css/install.css" />
</head>
<body>
  <!-- Prompt IE 6 users to install Chrome Frame. Remove this if you support IE 6.
       chromium.org/developers/how-tos/chrome-frame-getting-started -->
  <!--[if lt IE 7]><p class=chromeframe>Your browser is <em>ancient!</em> <a href="http://browsehappy.com/">Upgrade to a different browser</a> to experience this site.</p><![endif]-->
  
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
              <legend>Your Account</legend>
              <div class="control-group">
                <label class="control-label" for="site_admin_email">Admin Email</label>
                <div class="controls">
                  <div class="input-prepend">
                    <span class="add-on"><i class="icon-envelope"></i></span>
                    <input class="input-large" type="email" id="site_admin_email" name="site_admin_email">
                  </div>
                </div>
              </div>
            
              <div class="control-group">
                <label class="control-label" for="site_admin_display">Admin Display Name <span class="tip" title="Name displayed to public" type="button"><i class="icon-info-sign"></i></span></label>
                <div class="controls">
                  <div class="input-prepend">
                    <span class="add-on"><i class="icon-eye-open"></i></span>
                    <input class="input-large" type="text" id="site_admin_display" name="site_admin_display">
                  </div>
                </div>
              </div>

              <div class="control-group">
                <label class="control-label" for="site_admin_uname">Admin Login Name <span class="tip" title="Must differ from display name" type="button"><i class="icon-info-sign"></i></span></label>
                <div class="controls">
                  <div class="input-prepend">
                    <span class="add-on"><i class="icon-eye-close"></i></span>
                    <input class="input-large" type="text" id="site_admin_uname" name="site_admin_uname">
                  </div>
                </div>
              </div>

              <div class="control-group">
                <label class="control-label" for="site_admin_pass">Password</label>
                <div class="controls">
                  <div class="input-prepend">
                    <span class="add-on"><i class="icon-lock"></i></span>
                    <input class="input-large" type="password" id="site_admin_pass" name="site_admin_pass">
                  </div>
                </div>
              </div>

              <legend>Path Settings</legend>
              <div class="control-group">
                <label class="control-label" for="site_url">Url</label>
                <div class="controls">
                  <div class="input-prepend">
                    <span class="add-on"><i class="icon-home"></i></span>
                    <input class="input-large" type="text" id="site_url" name="site_url" placeholder="<?php echo $siteURI;?>" value="<?php echo $siteURI;?>">
                  </div>
                </div>
              </div>

              <div class="control-group">
                <label class="control-label" for="site_path">Physical Path</label>
                <div class="controls">
                  <div class="input-prepend">
                    <span class="add-on"><i class="icon-hdd"></i></span>
                    <input class="input-large" type="text" id="site_path" name="site_path" placeholder="<?php echo $siteRootPath;?>" value="<?php echo $siteRootPath;?>">
                  </div>
                </div>
              </div>

              <div class="control-group">
                <label class="control-label" for="site_trust">Trust Path <span class="tip" title="Select a location outside of your webroot for your secure trust path" type="button"><i class="icon-info-sign"></i></span></label>
                <div class="controls">
                  <div class="input-prepend input-append">
                    <span class="add-on"><i class="icon-exclamation-sign"></i></span>
                    <input class="input-large" type="text" id="site_trust" named="site_trust" placeholder="<?php echo $targetTrustPath;?>" value="<?php echo $targetTrustPath;?>">
                    <button id="createTrustPath" class="btn btn-warning" type="button">Create Trust Path</button>
                  </div>
                </div>
              </div>

              <legend>Database Connection</legend>
              <div class="control-group">
                <label class="control-label" for="site_db">Databse Type</label>
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
                <label class="control-label" for="site_db_host">Server Hostname</label>
                <div class="controls">
                  <div class="input-prepend">
                    <span class="add-on"><i class="icon-globe"></i></span>
                    <input class="input-large" type="text" id="site_db_host" name="site_db_host" placeholder="<?php echo $site_db_host;?>" value="<?php echo $site_db_host;?>">
                  </div>
                </div>
              </div>

              <div class="control-group">
                <label class="control-label" for="site_db_name">Database Name</label>
                <div class="controls">
                  <div class="input-prepend">
                    <span class="add-on"><i class="icon-briefcase"></i></span>
                    <input class="input-large" type="text" id="site_db_name" name="site_db_name" placeholder="<?php echo $site_db_name;?>" value="<?php echo $site_db_name;?>">
                  </div>
                </div>
              </div>

              <div class="control-group">
                <label class="control-label" for="site_db_user">Database User</label>
                <div class="controls">
                  <div class="input-prepend">
                    <span class="add-on"><i class="icon-user"></i></span>
                    <input class="input-large" type="text" id="site_db_user" name="site_db_user" placeholder="<?php echo $site_db_user;?>" value="<?php echo $site_db_user;?>">
                  </div>
                </div>
              </div>

              <div class="control-group">
                <label class="control-label" for="site_db_pass">Database Pass</label>
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
                  <label class="control-label" for="site_db_prefix">Database Prefix</label>
                  <div class="controls">
                    <input class="input-xlarge" type="text" id="site_db_prefix" name="site_db_prefix" value="<?php echo $site_db_prefix;?>">
                  </div>
                </div>

                <div class="control-group">
                  <label class="control-label" for="site_pw_salt_key">Password Salt Key</label>
                  <div class="controls">
                    <input class="input-xlarge" type="text" id="site_pw_salt_key" name="site_pw_salt_key" value="<?php echo $site_pw_salt_key;?>">
                  </div>
                </div>

                <div class="control-group">
                  <label class="control-label" for="site_db_charset">Database Charset</label>
                  <div class="controls">
                    <select name="site_db_charset" id="site_db_charset" name="site_db_charset">
                      <option val="utf8">UTF-8</option>
                    </select>
                  </div>
                </div>

                <div class="control-group">
                  <label class="control-label" for="site_db_collation">Database Collation <span class="tip" title="Default: utf8_general_ci"><i class="icon-info-sign"></i></span></label>
                  <div class="controls">
                    <select name="site_db_collation" id="site_db_collation" name="site_db_collation">
                      <option val="utf8_general_ci">utf8_general_ci</option>
                    </select>
                  </div>
                </div>
              </div>

              <div class="control-group">
                <div class="controls">
                  <br />
                  <p class="text-info">
                    By Installing you understand that ImpressCMS may collect usage data to help the project better understand adaptation and exposure of the system.<br /><br />
                    <strong class="text-error">No personal or sensitive information will ever be collected by us, and we will never sell your information.</strong><br /><br />
                    <a href="#">View our privacy policy</a>
                  </p>
                  <br />
                  <button id="submitInstall" type="submit" class="btn btn-primary disabled"><i class="icon-ok icon-white"></i> Let's Go!</button>
                  <button id="cancelInstall" type="button" class="btn btn-danger"><i class="icon-off icon-white"></i> Cancel</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    var icms = {
      config: <?php echo json_encode($icmsJsConfigData); ?>
    };
  </script>
  <script src="<?php echo ICMS_LIBRARIES_URL; ?>/jscore/lib/modernizr.js"></script>
  <script data-main="<?php echo ICMS_LIBRARIES_URL; ?>/jscore/app/routes/installer/main.js" data-loaded="icms_install" src="<?php echo ICMS_LIBRARIES_URL; ?>/jscore/lib/require.js"></script>
</body>
</html>