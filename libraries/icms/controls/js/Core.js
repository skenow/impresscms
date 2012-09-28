window.ImpressCMS.core.controls = {
        isKnownControl: function (obj) {
            var obj2 = jQuery(obj);
			if (!obj2.hasClass(window.ImpressCMS.config.controls['class']) || !obj2.attr('data-icms-control'))
                return false;
            return true;
        },
        getSelector: function () {
            return '.' + window.ImpressCMS.config.controls['class'];
        },
        actionType: {
            control:0, 
            module:1
        },
        getControl: function (id) {
            return window.ImpressCMS.controls[id];
        },
        controlExists: function (id) {
            if (!this.getControl(id))
                return false;
            return true;
        },
        renderType: {
            data: 0,
            attribute: 1,
            style: 2,
            state: 3,
            tag: 4
        },
        setState: function (state) {
            window.location.reload();
            // TODO: implement correct way
            /*var ret = Base64.btou(RawDeflate.inflate(Base64.fromBase64(state)));
            ret = jQuery.parseJSON(ret);
            window.ImpressCMS.console.log("Setting state", ret);
            var ctl_types = {};
            for (var x in ret) {
                ctl_types[x] = ret[x]['data-icms-control'];
                delete ret[x]['data-icms-control'];
            }
            for (var x in ret) {
                var ctl = window.ImpressCMS.core.controls.getControl(x);                
                if (!ctl || ctl.isSameControlType(ctl_types[x]))
                    window.location.reload();
                
                ctl.importArray(ret[x]);
            }               */     
        },
        getMainControls: function () {            
            var rez = {};
            var skipControls = [];
            for (var x in window.ImpressCMS.controls) {
                if (!window.ImpressCMS.controls[x])
                    continue;
                rez[x] = window.ImpressCMS.controls[x];
                window.ImpressCMS.controls[x].getChildren().each(
					function (index) {
						var obj = jQuery(this);
						skipControls.push(obj.attr('id'));
					}
				);
            }
            for(var i=0; i<skipControls.length;i++)
				if (rez[skipControls[i]])
					delete rez[skipControls[i]];
            return rez;
        },
        getState: function (noCompress) {
            var rez = {};
			var controls = this.getMainControls();
            for (var x in controls) {
                var type = window.ImpressCMS.controls[x].getControlType();
                if (typeof rez[type] == "undefined")
                    rez[type] = {};
                rez[type][x] = window.ImpressCMS.controls[x].getNotDefaultVars();
            }
                        
            var ret = JSON.stringify(rez);
            
            if (!noCompress)
                ret = Base64.toBase64(RawDeflate.deflate(escape(ret)));
            
            return ret;
        },
        waitUntilControlExists: function (control_id, func) {
            var self = this;
            var exist = function() {
                if (!self.controlExists(control_id))
                    setTimeout(exist, 100);
                else
                    func();
            };
            exist();
        },
        update: function (context) {
            this.replaceMagicLinks(context);
            
            var sel = window.ImpressCMS.core.controls.getSelector();
            for(var x in window.ImpressCMS.controls)
                if ((jQuery('#' + x).length == 0) || (jQuery('#' + x).data('events') == undefined))
                    delete window.ImpressCMS.controls[x];
            var obj = (context)?jQuery(sel, jQuery(context)):jQuery(sel);
            obj.getControl();
        },
        replaceMagicLinks: function (context) {
            var prot = [];            
            jQuery.each(window.ImpressCMS.URLHandler.specialProtocols, 
                    function( intIndex, objValue ) { 
                         prot.push('[href^="' + objValue + '"]'); 
                    });
            var sel = prot.join(',');
            jQuery(sel, context).each(
                function () {
                    var obj = jQuery(this);
                    var href = obj.attr('href');
                    obj.attr('href', 'javascript:alert("'+window.ImpressCMS.language.controls.link_in_same_window_error+'"); if (window.close) window.close();');
                    obj.bind('click', function (e) {
                                         var ctl = window.ImpressCMS.core.controls.findControlWhereTagBelongs(this);
                                         window.ImpressCMS.actionQueue.processURL(href, null, null, ctl);
                                         e.preventDefault();
                                     });                    
                }
            );
        },
        findControlWhereTagBelongs: function (tag) {
            if (this.isKnownControl(tag))
                return jQuery(tag).getControl();
            var ctl = jQuery(tag).closest(this.getSelector()).get(0);
            return window.ImpressCMS.core.controls.getControl(ctl.id);
        }
    };