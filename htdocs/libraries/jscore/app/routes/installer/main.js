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

				installer.passwords();
				installer.trustPath();
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
	};

	installer.init();
});
