/**
 * Sample Build Config
 */

({
	baseUrl: '../'
  , out: 'app/main-built.js'
  , name: 'app/main'
	, paths: {
		// ICMS Paths
		bootstrap: 'deps/libs/bootstrap/src'

		// Script Paths
		, modules: 'app/modules'
		, widgets: 'app/widgets'

		// dependencies
		, plugins : 'deps/plugins'
		, libs: 'deps/libs'
		, ext: 'deps/ext'

		, nls: 'nls'
		, core: 'deps/ext/core'
		, configs: 'configs'

		// Common Libraries
		, jquery: 'deps/libs/jquery'
		, backbone: 'deps/libs/backbone'
		, underscore: 'deps/libs/underscore'

		// Marionette & Deps
    , marionette : 'deps/libs/backbone.marionette'
    , 'backbone.wreqr' : 'deps/libs/backbone.wreqr'
    // 'backbone.eventbinder' : 'deps/libs/backbone.eventbinder'
    , 'backbone.babysitter' : 'deps/libs/backbone.babysitter'

		// Common Utils
		, text: 'deps/libs/text'
		, handlebars: 'deps/libs/handlebars.runtime'
		, hb: 'deps/libs/hb'
	}
	, uglify: {
		max_line_length: 1000
	}
	, inlineText: true
	// Shims
	, shim: {
		jquery:{
			exports:'$'
		}

		, underscore: {
			exports: '_'
		}

		, backbone: {
			deps: [
				'underscore'
				, 'jquery'
			]
			, exports: 'Backbone'
		}

		, 'handlebars': {
			exports: 'Handlebars'
		}
	}
	, map: {
		'*': {
			'css': 'libs/require-css/css'
			, 'i18n': 'libs/i18n'
		}
	}
	// Define timeout for require
	, waitSeconds: 60

})