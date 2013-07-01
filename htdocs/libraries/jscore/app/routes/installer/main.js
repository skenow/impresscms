/* global icms: true */
require({
  baseUrl: icms.config.jscore
  ,	paths: {
		util: 'util'
		, lib: 'lib'
		, app: 'app'
		, modules: 'app/modules'
		, mediator: 'app/mediator'
	}
}
,[
	'jquery'
	, 'util/core/tools'
  , 'plugins/twitter_bootstrap'
  , 'plugins/jquery.ui/jquery.ui'
  , 'plugins/password/passfield'
], function($, tools){
	var installer = {
		init: function() {
			$(document).ready(function() {
				$('#cancelInstall').on({
					click: function(e) {
						e.preventDefault();
						$('form')[0].reset();
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

		// , trustPath: function() {
		// 	// This is some example js to show states of the createTrustPath button
		// 	$('#createTrustPath').on({
		// 		click: function(e) {
		// 			e.preventDefault();
		// 			var $this = $(this)
		// 			, path = $this.prev('input').val()
		// 			, alert = $this.closest('.control-group').children('.createPathAlert').children('.alert');

		// 			// Show worker
		// 			alert.slideDown('slow');
		// 			$.ajax({
		// 				url: window.location.href
		// 				, success: function() {
		// 					$this.attr('class', 'btn btn-success disabled').text('Success!');
		// 					alert.attr('class', 'alert alert-success').html('Created: <strong>' + path + '</strong>.');
		// 					$('#submitInstall').attr('class','btn btn-primary');
		// 				}
		// 				, error: function() {
		// 					$this.attr('class', 'btn btn-danger disabled').text('Error!');
		// 					alert.attr('class', 'alert alert-error').html('Error Creating: <strong>' + path + '</strong>.<br />Please create manually and try again.');
		// 				}
		// 			});
		// 			return false;
		// 		}
		// 	});
		// }
	};

	return installer.init();
});
