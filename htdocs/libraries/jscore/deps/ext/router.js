/* global icms: true */
define(['underscore', 'core'], function(_, Core) {
	return {
		processedRoutes: []

		, initialize: function() {
			var self = this;
			Core.mediator.deferredSubscribe('routeReady', function() {
				self.applyRoute();
			});
		}

		, applyRoute: function(){
			var self = this;
			$.map(icms.router, function(val) {
				if(!_.contains(self.processedRoutes, val)) {
					require([val], function(appRoute) {
						appRoute.initialize();
					});
					self.processedRoutes.push(val);
				}
			});
		}
	};
});