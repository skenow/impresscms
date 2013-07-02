/* global icms: true */
require({
  baseUrl: icms.config.jscore
  ,	paths: {
		util: 'util'
		, lib: 'lib'
		, app: 'app'
		, modules: 'app/modules'
		, mediator: 'app/mediator'
		, locale: 'locale/english/'
	}
}
,[
	'jquery'
	, 'mediator'
	, 'util/core/tools'
	, 'modules/notify/main'
	, 'modules/validator/main'
  , 'plugins/twitter_bootstrap'
  , 'plugins/jquery.ui/jquery.ui'
  , 'plugins/password/passfield'
], function($, mediator, tools, notifier, validator){
	var installForm
	, installer = {
		init: function() {
			tools.loadCSS(icms.config.jscore + 'app/modules/notify/notify.css', 'jquery-notify');
      validator.initialize();

			mediator.subscribe('addNotification', function(message, options) {
				notifier.showMessage(message, options);
			});

			mediator.subscribe('formCallback', function(formName, status, data) {
				console.log(formName, status, data);
			});

			$(document).ready(function() {
				installForm = $('#installForm');

        $('a[rel="external"]').click(function(){
          $(this).attr('target', '_blank');
        });

        $('#submitInstall').on({
					click: function(e) {
						e.preventDefault();
						installForm.submit();
						return false;
					}
        });

				$('#cancelInstall').on({
					click: function(e) {
						e.preventDefault();
						installForm[0].reset();
					}
				});

				$('.tip').tooltip({
					html: true
				});

				installer.passwords();
			});
		}

    , passwords: function() {
      tools.loadCSS(icms.config.jscore + 'plugins/password/passfield.css', 'core-jquery-password');
      $('input[type=password]').passField({
        'showTip': false
        , 'showWarn': false
        , 'showGenerate' : true
      });
    }
	};

	return installer.init();
});
