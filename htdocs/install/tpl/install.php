<?php
  header('X-UA-Compatible: IE=edge,chrome=1');
  $icmsJsConfigData = array(
    'jscore' => ICMS_LIBRARIES_URL . '/jscore/',
    'url' => ICMS_URL
  );
  $arrSize = count($debugMsgs);
  if($arrSize > 0) {
    $showMsg = '<div id="msgWrapper">';
    foreach ($debugMsgs as $msg) {
      $showMsg .= $msg;
    }
    $showMsg .= '</div>';
  }
?>

<!doctype html>
<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
  <meta charset="utf-8">
  <title>Install ICMS</title>
  <meta name="viewport" content="width=device-width">
  <link rel="stylesheet" href="<?php echo ICMS_URL; ?>/install/tpl/css/install.css" />
</head>
<body>
  <!-- Prompt IE 6 users to install Chrome Frame. Remove this if you support IE 6.
       chromium.org/developers/how-tos/chrome-frame-getting-started -->
  <!--[if lt IE 7]><p class=chromeframe>Your browser is <em>ancient!</em> <a href="http://browsehappy.com/">Upgrade to a different browser</a> to experience this site.</p><![endif]-->
  
  <div id="fullWrapper" class="fullWrapper">
    <div id="page" class="page">
      <div id="installer" class="installer">
        <div class="inner">

          <div id="header" class="header">
            <h1><img src="<?php echo ICMS_URL; ?>/themes/core/img/logo.png" alt="ImpressCMS" /></h1>
          </div>

          <?php
            if(isset($showMsg)) {
              echo $showMsg;
            }

            if($reload) {
          ?>
            <p>
              <a href="?op=reload" class="btn btn-primary"><i class="icon-refresh icon-white"></i> Try Again</a>
            </p>
          <?php
            } else {
          ?>

          <h3>Welcome, Thanks for Installing ICMS!</h3>
          <p>We need to collect some information from you before we begin...</p>

          <form id="installForm" class="form-horizontal ajax" method="post" action="?op=go" name="installer">
            <h4>Admin Account</h4>
            <div class="control-group">
              <label class="control-label" for="site_admin_email">Email</label>
              <div class="controls">
                <div class="input-prepend">
                  <span class="add-on"><i class="icon-envelope"></i></span>
                  <input class="input-xlarge required" type="email" id="site_admin_email" name="site_admin_email" value="<?php echo $site_admin_email; ?>">
                </div>
              </div>
            </div>
          
            <div class="control-group">
              <label class="control-label" for="site_admin_display">Display Name <span class="tip" title="Name displayed to public"><i class="icon-question"></i></span></label>
              <div class="controls">
                <div class="input-prepend">
                  <span class="add-on"><i class="icon-eye-open"></i></span>
                  <input class="input-xlarge required" type="text" id="site_admin_display" name="site_admin_display" value="<?php echo $site_admin_display; ?>">
                </div>
              </div>
            </div>

            <!--

            <div class="control-group">
              <label class="control-label" for="site_admin_uname">Login Name <span class="tip" title="Must differ from display name"><i class="icon-question"></i></span></label>
              <div class="controls">
                <div class="input-prepend">
                  <span class="add-on"><i class="icon-eye-close"></i></span>
                  <input class="input-xlarge" type="text" id="site_admin_uname" name="site_admin_uname">
                </div>
              </div>
            </div> -->

            <div class="control-group">
              <label class="control-label" for="site_admin_pass">Password</label>
              <div class="controls">
                <div class="input-prepend">
                  <span class="add-on"><i class="icon-lock"></i></span>
                  <input class="input-xlarge required" type="password" id="site_admin_pass" name="site_admin_pass">
                </div>
              </div>
            </div>

            <h4>Path Settings</h4>
            <div class="control-group">
              <label class="control-label" for="site_trust">Trust Path <span class="tip" title="Select a location outside of your webroot for your secure trust path"><i class="icon-question"></i></span></label>
              <div class="controls">
                <div class="input-prepend">
                  <span class="add-on"><i class="icon-exclamation-sign"></i></span>
                  <input class="input-xlarge" type="text" id="site_trust" name="site_trust" placeholder="<?php echo $targetTrustPath;?>" value="<?php echo $targetTrustPath;?>">
                </div>
              </div>
            </div>

            <h4>Database Settings</h4>
            <div class="control-group">
              <label class="control-label" for="site_db_host">Hostname</label>
              <div class="controls">
                <div class="input-prepend">
                  <span class="add-on"><i class="icon-globe"></i></span>
                  <input class="input-xlarge required" type="text" id="site_db_host" name="site_db_host" placeholder="<?php echo $site_db_host;?>" value="<?php echo $site_db_host;?>">
                </div>
              </div>
            </div>

            <div class="control-group">
              <label class="control-label" for="site_db_name">Name</label>
              <div class="controls">
                <div class="input-prepend">
                  <span class="add-on"><i class="icon-briefcase"></i></span>
                  <input class="input-xlarge required" type="text" id="site_db_name" name="site_db_name" placeholder="<?php echo $site_db_name;?>" value="<?php echo $site_db_name;?>">
                </div>
              </div>
            </div>

            <div class="control-group">
              <label class="control-label" for="site_db_user">User</label>
              <div class="controls">
                <div class="input-prepend">
                  <span class="add-on"><i class="icon-user"></i></span>
                  <input class="input-xlarge required" type="text" id="site_db_user" name="site_db_user" placeholder="<?php echo $site_db_user;?>" value="<?php echo $site_db_user;?>">
                </div>
              </div>
            </div>

            <div class="control-group">
              <label class="control-label" for="site_db_pass">Password</label>
              <div class="controls">
                <div class="input-prepend">
                  <span class="add-on"><i class="icon-lock"></i></span>
                  <input class="input-xlarge required" type="password" id="site_db_pass" name="site_db_pass">
                </div>
              </div>
            </div>

            <div class="control-group">
              <label class="control-label" for="site_db_prefix">Prefix</label>
              <div class="controls">
                <div class="input-prepend">
                  <span class="add-on"><i class="icon-arrow-left"></i></span>
                  <input class="input-xlarge" type="text" id="site_db_prefix" name="site_db_prefix" value="<?php echo $site_db_prefix;?>">
                </div>
              </div>
            </div>


            <div class="control-group">
              <div class="controls">
                <input type="hidden" id="site_pw_salt_key" name="site_pw_salt_key" value="<?php echo $site_pw_salt_key;?>">
                <br />
                <p class="text-info">
                  By Installing you understand that ImpressCMS may collect usage data that will be used to communicate global adaptation of our system as well as metrics on usage of addons. This information allows us to measure which components are most successful and focus our future efforts to deliver a better product to you.<br /><br />
                  <strong class="text-error">These metrics will never contain sensitive information.</strong><br /><br />
                  <a href="#">View our privacy policy</a>
                </p>
                <br />
                <button id="submitInstall" type="submit" class="btn btn-primary"><i class="icon-ok icon-white"></i> Let's Go!</button>
                <button id="cancelInstall" type="button" class="btn btn-danger"><i class="icon-off icon-white"></i> Clear Form</button>
              </div>
            </div>
          </form>
          <?php } // Ends reload if ?>
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