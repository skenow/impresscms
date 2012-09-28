window.ImpressCMS.actionQueue = new (function () {
        
        var list = {actions: {}, func:[], index: 0};
        var state = 0; // 0 - stopped, 1 - waiting, 2 - running
        var self = this;
        
        this.cacheInterval = 100;
        
        var processQueue = function () {
            state = 2;
            var timestamp = {};
            timestamp[window.ImpressCMS.config.special_param.dummy] = (new Date()).getTime();            
           // timestamp['logging_enabled'] = 1;
           // timestamp['show_headers'] = 1;
            var clist = jQuery.extend({}, list);
            list = {actions: {}, func:[], index: 0};
            var act_type = window.ImpressCMS.session.get('ImpressCMS/ActionQueue/Type','post');
            var send_data = jQuery.extend({}, clist.actions, timestamp);
            var true_url = window.ImpressCMS.URLHandler.makeRootUrl('process.php');
            if (act_type == 'get') {
                true_url += '?icms:1:' + escape(Base64.toBase64(RawDeflate.deflate(Base64.utob(JSON.stringify(send_data))))); 
            }
            jQuery.ajax({
			url: true_url,
			data: (act_type != 'get')?send_data:'',
			dataType: 'json',
			type: act_type,
			success: function (data) {
                            if (data.system_log) {
                                for (var x in data.system_log)
                                    for (var i = 0; i < data.system_log[x].length; i++)
                                        window.ImpressCMS.console.log(x, data.system_log[x][i]);
                            }                    
                            var rez = [];
                            for (var x in data) {
                                if (parseInt(x).toString() != x)
                                    continue;
                                rez.push(data[x]);
                            }
                            data = rez;
                            for(var i = 0; i < data.length; i++) {
                                var ret = data[i];
                                if (jQuery.isFunction(clist.func[i]))
                                    clist.func[i](ret);
                            }   
                            delete clist;
                                
                            if (list.index > 0) {
                                state = 1;
                                setTimeout(processQueue, self.cacheInterval);
                            } else {
                                state = 0;
                            }
			}			
			});
        };
        
        var enqueue = function (params, func) {
            for(var x in params) {
                var name = x + '[' + list.index + ']';
                list.actions[name] = params[x];
            }
            list.func[list.index] = func;
            if (state == 0) {
                state = 1;
                setTimeout(processQueue, self.cacheInterval);
            }
            list.index++;
        };
        
        var loadCSSFromLocation = function (url, func) {         
            // Idea: user406905 @ http://stackoverflow.com/questions/805384/how-to-apply-inline-and-or-external-css-loaded-dynamically-with-jquery
            if(document.createStyleSheet) {
                try { 
                    document.createStyleSheet(url);                     
                } catch (e) {
                    // Do Nothing
                }
            } else {
                var css = document.createElement('link');
                css.rel = 'stylesheet';
                css.type = 'text/css';
                css.media = "all";
                css.href = url;
                document.getElementsByTagName("head")[0].appendChild(css);
            }
            if (func)
                func();
        };                                  
        
        this.module = {
            addAction: function (module, action, params, func) {
                var p2 = jQuery.extend({}, params);
                p2[window.ImpressCMS.config.special_param.action] = action;
                p2[window.ImpressCMS.config.special_param.module] = module;
                enqueue(p2, func);
            },
            addCSS: function (module, cssfile, func) {
                var url = window.ImpressCMS.URLHandler.makeModuleUrl(module, cssfile);
                loadCSSFromLocation(url, func);
            },
            addJS: function (module, jsfile, func) {
                var url = window.ImpressCMS.URLHandler.makeModuleUrl(module, jsfile);                
                jQuery.getScript(url, func);
            }
        };
        
        this.control = {
            addAction: function (control_type, action, params, config, func) { 
                var p2 = jQuery.extend({logging_enabled:1}, params);
                p2[window.ImpressCMS.config.special_param.action] = action;
                p2[window.ImpressCMS.config.special_param.control] = control_type;
                p2[window.ImpressCMS.config.special_param.params] = config;
                enqueue(p2, func);
            },
            addCSS: function (control_type, cssfile, func) {
                var url = window.ImpressCMS.URLHandler.makeControlUrl(control_type, cssfile);
                loadCSSFromLocation(url, func);
            },
            addJS: function (control_type, jsfile, func) {
                var url = window.ImpressCMS.URLHandler.makeControlUrl(control_type, jsfile);             
                jQuery.getScript(url, func);
            }
        };
        
        this.processURL = function (url, params, func, handler_ctl) {
            var location = window.ImpressCMS.URLHandler.parseURL(url);
            if (!params)
                params = {};
            jQuery.extend(true, params, location.query);
            switch (location.scheme) {
                case 'control':
                    var type = window.ImpressCMS.core.controls.actionType[location.scheme];
                    var ctl = window.ImpressCMS.core.controls.getControl(location.host);
                    if (!ctl)
                        throw new window.ImpressCMS.language.controls.control_not_found_error.format(location.host);
                    if (func)
                        ctl.bind( 'execFinished', func );
                    ctl.exec(location.path[0], params, ctl.getControlType(), type);
                return false;
                case 'module':
                    var type = window.ImpressCMS.core.controls.actionType[location.scheme];
                    if (func)
                        handler_ctl.bind( 'execFinished', func );
                    handler_ctl.exec(location.path[0], params, location.host, type);
                   // var type = window.ImpressCMS.core.controls.actionType[location.scheme];                    
                 //   self.module.addAction(location.host, location.path[0], params, func);
                return false;
            }
            return true;
        };
        
        this.count = function () {
            return list.length;
        };
        
        this.isRunning = function () {
            return state == 2;
        };
        
        this.isWaiting = function () {
            return state == 1;
        };
        
        this.isStopped = function () {
            return state == 0;
        };
        
    })();