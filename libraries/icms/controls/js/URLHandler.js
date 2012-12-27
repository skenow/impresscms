window.ImpressCMS.URLHandler = {
        specialProtocols: [
            'module://',
            'control://'
        ],
        isSpecialProtocol: function (url) {
            for (var i = 0; i < this.specialProtocols.length; i++)
                if (url.substr(0, this.specialProtocols[i]) == this.specialProtocols[i])
                    return true;
            return false;
        },
        getCurrentURL: function () {
            var url = location.protocol + '//' + location.host + location.pathname;
            var params = window.ImpressCMS.URLHandler.getParamsArray();            
            params['icms_page_state'] = window.ImpressCMS.core.controls.getState();
            url += '?' + window.ImpressCMS.URLHandler.makeGetRequestString(params) + location.hash;
            return url;
        },
        parseURL: function (str, component) {
            // Parse a URL and return its components  
            // 
            // version: 1109.2015
            // discuss at: http://phpjs.org/functions/parse_url
            // +      original by: Steven Levithan (http://blog.stevenlevithan.com)
            // + reimplemented by: Brett Zamir (http://brett-zamir.me)
            // + input by: Lorenzo Pisani
            // + input by: Tony
            // + improved by: Brett Zamir (http://brett-zamir.me)
            // %          note: Based on http://stevenlevithan.com/demo/parseuri/js/assets/parseuri.js
            // %          note: blog post at http://blog.stevenlevithan.com/archives/parseuri
            // %          note: demo at http://stevenlevithan.com/demo/parseuri/js/assets/parseuri.js
            // %          note: Does not replace invalid characters with '_' as in PHP, nor does it return false with
            // %          note: a seriously malformed URL.
            // %          note: Besides function name, is essentially the same as parseUri as well as our allowing
            // %          note: an extra slash after the scheme/protocol (to allow file:/// as in PHP)
            // *     example 1: parse_url('http://username:password@hostname/path?arg=value#anchor');
            // *     returns 1: {scheme: 'http', host: 'hostname', user: 'username', pass: 'password', path: '/path', query: 'arg=value', fragment: 'anchor'}
            var key = ['source', 'scheme', 'authority', 'userInfo', 'user', 'pass', 'host', 'port', 
                                'relative', 'path', 'directory', 'file', 'query', 'fragment'],
                ini = (this.php_js && this.php_js.ini) || {},
                mode = (ini['phpjs.parse_url.mode'] && 
                    ini['phpjs.parse_url.mode'].local_value) || 'php',
                parser = {
                    php: /^(?:([^:\/?#]+):)?(?:\/\/()(?:(?:()(?:([^:@]*):?([^:@]*))?@)?([^:\/?#]*)(?::(\d*))?))?()(?:(()(?:(?:[^?#\/]*\/)*)()(?:[^?#]*))(?:\?([^#]*))?(?:#(.*))?)/,
                    strict: /^(?:([^:\/?#]+):)?(?:\/\/((?:(([^:@]*):?([^:@]*))?@)?([^:\/?#]*)(?::(\d*))?))?((((?:[^?#\/]*\/)*)([^?#]*))(?:\?([^#]*))?(?:#(.*))?)/,
                    loose: /^(?:(?![^:@]+:[^:@\/]*@)([^:\/?#.]+):)?(?:\/\/\/?)?((?:(([^:@]*):?([^:@]*))?@)?([^:\/?#]*)(?::(\d*))?)(((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[?#]|$)))*\/?)?([^?#\/]*))(?:\?([^#]*))?(?:#(.*))?)/ // Added one optional slash to post-scheme to catch file:/// (should restrict this)
                };

            var m = parser[mode].exec(str),
                uri = {},
                i = 14;
            while (i--) {
                if (m[i]) {
                  uri[key[i]] = m[i];  
                }
            }
            
            uri.path = uri.path.split('/');
            if (!uri.path[0])
                uri.path.splice(0, 1);
            
            if (uri.query === undefined) {
                uri.query = {};
            } else {
                var doRpl = function (text) {                
                    var ret = {},
                        seg = text.replace(/^\?/,'').split('&'),
                        len = seg.length, i = 0, s;
                    for (;i<len;i++) {
                        if (!seg[i]) { continue; }
                        s = seg[i].split('=');
                        ret[unescape(s[0])] = unescape(s[1]);
                    }
                    return ret;
                };
            
                uri.query = doRpl(uri.query);
            }                                    

            if (component) {
                return uri[component.replace('PHP_URL_', '').toLowerCase()];
            }
            if (mode !== 'php') {
                var name = (ini['phpjs.parse_url.queryKey'] && 
                        ini['phpjs.parse_url.queryKey'].local_value) || 'queryKey';
                parser = /(?:^|&)([^&=]*)=?([^&]*)/g;
                uri[name] = {};
                uri[key[12]].replace(parser, function ($0, $1, $2) {
                    if ($1) {uri[name][$1] = $2;}
                });
            }
            delete uri.source;
            return uri;
        },
       /* parseURL: function (url, component) {
            var a =  document.createElement('a');
            a.href = url;     
            var ret = {
                scheme: a.protocol.replace(':','').toLowerCase(),
                host: a.hostname,
                port: a.port,
                user: null, 
                pass: null, 
                path: a.pathname.split('/'), 
                query: (function(){
                    var ret = {},
                        seg = a.search.replace(/^\?/,'').split('&'),
                        len = seg.length, i = 0, s;
                    for (;i<len;i++) {
                        if (!seg[i]) { continue; }
                        s = seg[i].split('=');
                        ret[unescape(s[0])] = unescape(s[1]);
                    }
                    return ret;
                })(),
                fragment: a.hash
            };
            
            for(var i = 0; i < ret.path.length; i++) {
                if (!ret.path[i]) 
                    continue;
                break;
            }
            
            if (i > 0)
                ret.path.splice(0,i); 
            
            if ((!ret.host) && (url.match(/\:\/\//g))) {
                ret.host = ret.path[0];
                ret.path.splice(0,1);
            }
            
            if (component)
                return ret[component];
            else
                return ret;
        },*/
        makeGetRequestString: function(params) {
            var rez = '';
            for (var x in params)
                rez += encodeURIComponent(x) + '=' + encodeURIComponent(params[x]) + '&';
            if (rez.length > 1)
               rez = rez.substr(0, rez.length - 1);
            return rez;
        },
        makeControlUrl: function (control_type, file, params) {
           var url = window.ImpressCMS.config.url.control;
           if (url.indexOf('%1')) {
               url = url.replace('%1',control_type);
               url = url.replace('%2',file);
           } else {
               url += '/' + control_type + '/' + file;
           }
           if (params)
               url += '?' + window.ImpressCMS.URLHandler.makeGetRequestString(params);           
           return url;
        },
        makeModuleUrl: function (module, file, params) {
           var url = window.ImpressCMS.config.url.module;
           if (url.indexOf('%1') > 0) {
               url = url.replace('%1',module);
               url = url.replace('%2',file);
           } else {
               url += '/' + module + '/' + file;
           }
           if (params)
               url += '?' + window.ImpressCMS.URLHandler.makeGetRequestString(params);           
           return url;
        },
        makeRootUrl: function (file, params) {
           var url = window.ImpressCMS.config.url.root;
           if (url.indexOf('%1') > 0) {
               url = url.replace('%1',file);
           } else {
               url += '/' + file;
           }
           if (params)
               url += '?' + window.ImpressCMS.URLHandler.makeGetRequestString(params); 
           return url;
        },
        convertParamsArrayToParams: function (pArray) {
            var ret = {};
            for(i = 0; i < pArray.length; i++)
                for (var k in pArray[i]) 
                    ret[k + '[' + i.toString() + ']'] = pArray[i][k];
            return ret;
        },
        getParamsArray: function () {
            // based on http://snipplr.com/view/11455/parse-locationsearch/
            var parameters, cx, query;
            
            if (!arguments[0])
                query = location.search;
            else
                query = arguments[0];

            parameters = query.split(/[&?]/);

            for (cx = 0; cx < parameters.length; cx++) {
                parameters[cx] = parameters[cx].split("=");
                if (parameters[cx].length < 2) //Drop "" or /[A-Za-z]\w*=$/
                    parameters.splice(cx--, 1);
            }

            while (parameters.length) {
                parameter = parameters.shift();
                if (!parameters[parameter[0]])
                    parameters[parameter[0]] = [];
                parameters[parameter.shift()].push( parameter.shift() ); //p=1&p=2&p=3 -> parameters.p = [1, 2, 3]
            }
            return parameters;
        },
        updateCurrentURL: function () {
            var title = jQuery(jQuery('title').get(0)).text();
            history.pushState({}, title, window.ImpressCMS.URLHandler.getCurrentURL());
        }
    }    