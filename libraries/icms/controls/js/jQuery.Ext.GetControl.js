jQuery.fn.getControl = function() {
    
  return this.filter(
    function (index) {
        return window.ImpressCMS.core.controls.isKnownControl(this);
    }
  ).map(function() {
          
     var obj = jQuery(this);
     var id = obj.attr('id');
     var type = obj.attr('data-icms-control');
     
     var hData = {};

     if (!window.ImpressCMS.controls[id]) {                  
         
         if (!window.ImpressCMS.baseControls[type] || !window.ImpressCMS.baseControls[type].configuration) {
             window.ImpressCMS.console.log("init", window.ImpressCMS.language.controls.undefined_control_error.format(type));
             return null;
         }
         
         jQuery.extend(true, obj, window.ImpressCMS.baseControls[type], {
			 getControlType: function() {
					return this.attr('data-icms-control');
			 },             
             toActionParams: function () {
                 var vars = this.toArray();
                 for(var name in vars)
                 if (!this.configuration.fields[name])
                    continue;
                 else
                    switch (this.configuration.fields[name][window.ImpressCMS.config.var_param.type]) {
                        case window.ImpressCMS.config.var_type.data_source:
                            vars[name] = vars[name][0];
                        break;
                    }
                return vars;
             },
             exec: function (action, params, objName, type, noAnimation) {
                 
                if (arguments.length == 1) {
                    if (jQuery.isArray(action)) {
                        for (var i = 0; i < action.length; i++)
                            this.exec(action[i]);
                        return;
                    } else if (action.action) {
                        noAnimation = action.noAnimation;
                        type = action.type;
                        objName = action.objName;
                        params = action.params;
                        action = action.action;
                    }
                }                
                 
                if (!noAnimation)
                    this.showGlobalAnimation(window.ImpressCMS.core.animations.predefined.loading);
                 
                if (!type)
                    type = window.ImpressCMS.core.controls.actionType.control;
                if (!objName)
                    objName = this.getControlType();
                
                var f2 = function (data) {
                    obj.trigger('execFinished', data, {action:action, params:params, objName:objName, type:type});
                };
                
                if (type == window.ImpressCMS.core.controls.actionType.control) {                    
                    window.ImpressCMS.actionQueue.control.addAction(objName, action, params, this.toActionParams(), f2);
                } else {
                    window.ImpressCMS.actionQueue.module.addAction(objName, action, params, f2);
                }
             },             
             getChildren: function () {
                 var bad = [];
                 var current = jQuery(window.ImpressCMS.core.controls.getSelector(), obj);
                 current.each(
                    function () {
                        jQuery(window.ImpressCMS.core.controls.getSelector(), jQuery(this)).each(
                            function () {
                                bad.push(jQuery(this).attr('id'));
                            }
                        );                        
                    }
                 );
                 return current.filter(
                    function () {
                        var obj = jQuery(this);
                        for(var i = 0; i < bad.length; i++)
                            if (obj.attr('id') == bad[i])
                                return false;
                        return true;
                    }
                 ).getControl();
                //return .getControl();
             },
			 parseAttrValue: function (name, value) {
					if (value == undefined)
						value = "";
					switch (this.configuration.fields[name][window.ImpressCMS.config.var_param.type]) {
						case window.ImpressCMS.config.var_type.string:						
						case window.ImpressCMS.config.var_type.criteria:
							return value.toString();
                        case window.ImpressCMS.config.var_type.data_source:
                            var dt = Base64.btou(RawDeflate.inflate(Base64.fromBase64(value)));
                            if (!dt)
                                return value.toString();
                            dt = jQuery.parseJSON(dt);
                            if (dt.length != 2)
                                return null;
                            return dt;
						case window.ImpressCMS.config.var_type.integer:
							return parseInt(value);
						case window.ImpressCMS.config.var_type['float']:
							return parseFloat(value);
						case window.ImpressCMS.config.var_type['boolean']:
							if (typeof value == 'boolean')
								return value;
							if (typeof value == 'number')
								return parseInt(value) != 0;
							if (typeof value == 'object')
								return false;
							value = value.toString();
							value = value.toLowerCase().replace(/^[\s]+/,'').replace(/[\s]+$/,'').replace(/[\s]{2,}/,' ');
							return (value == 'yes') || (value == 'true') || (value == '1');
						case window.ImpressCMS.config.var_type.file:
							return value;
							//throw "Undefined file type";
						break;
						case window.ImpressCMS.config.var_type.datetime:
							var dt = new Date();
							dt.setTime(value);
							return dt;
						case window.ImpressCMS.config.var_type.array:
                            switch (typeof value) {
                                case 'boolean':
                                case 'number':
                                    return [value];
                                case 'string':
                                    if (value == '')
                                        return [];
                                    try {
                                        var rt = jQuery.parseJSON(value);
                                        if (typeof rt != 'object')
                                            return [rt];
                                        return rt;
                                    } catch (e) {
                                        return [value];
                                    }
                                case 'object':
                                    return value;
                                default:
                                    alert(typeof value);
                            }
							return value;
							//throw "Undefined array type";
						break;
						case window.ImpressCMS.config.var_type.list:
							return value.split(';');
							//throw "Undefined list type";
						break;
					}
			 },
             getVar: function (name) {
                if (!this.configuration.fields[name])
                    return null;
                var val;
                switch (this.configuration.fields[name].renderType) {
                    case window.ImpressCMS.core.controls.renderType.state:
                        if (this.hasAttr(name))
                            val = (this.attr(name) == name);
                        else
                            val = false;
                    break;
                    case window.ImpressCMS.core.controls.renderType.attribute:
                        if (this.hasAttr(name))
                            val = this.attr(name);
                        else
                            val = this.configuration.baseValues[name];
                    break;
                    case window.ImpressCMS.core.controls.renderType.style:
                        val = this.css(name);
                    break;
                    case window.ImpressCMS.core.controls.renderType.tag:
                        val = this.get(0).nodeName.toLowerCase();
                    break;
                    case window.ImpressCMS.core.controls.renderType.data:
                        var cname = 'data-' + name;
                        if (this.hasAttr(cname))
                            val = this.attr(cname);
                        else
                            val = this.configuration.baseValues[name];
                    break;
                }
                return this.parseAttrValue(name,  val);
            },
            hasAttr: function (name) {
                return this.get(0).hasAttribute(name);
            },
            encodeAttrValue: function (name, value) {
                if (value == undefined)
                    value = "";
                switch (this.configuration.fields[name][window.ImpressCMS.config.var_param.type]) {
                    case window.ImpressCMS.config.var_type.string:						
                    case window.ImpressCMS.config.var_type.criteria:
                        return value.toString();
                    case window.ImpressCMS.config.var_type.data_source:
                        throw window.ImpressCMS.language.controls.unsupported_source_changing_error;
                        break;
                    case window.ImpressCMS.config.var_type.integer:
                        return parseInt(value);
                    case window.ImpressCMS.config.var_type['float']:
                        return parseFloat(value);
                    case window.ImpressCMS.config.var_type['boolean']:
                        return value?'1':'0';
                    case window.ImpressCMS.config.var_type.file:
                        return value;
                        //throw "Undefined file type";
                    break;
                    case window.ImpressCMS.config.var_type.datetime:
                        return value.toString();                        
                    case window.ImpressCMS.config.var_type.array:
                        if (typeof value != 'object')
                            value = [value];
                        if (jQuery.stringify)
                            return jQuery.stringify(value);
                        else if (JSON && JSON.stringify)
                            return JSON.stringify(value);
                        else {
                            function toJSON(data) {
                                switch (typeof data) {
                                    case 'number':
                                        return data;
                                    case 'string':
                                        return '"' + data.replace(/\\/g,'\\\\').replace(/\'/g,'\\\'').replace(/\"/g,'\\"').replace(/\0/g,'\\0') + '"';
                                    case 'boolean':
                                        return data?'true':'false';
                                    case 'undefined':
                                        return 'null';
                                    case 'object':
                                        if (data === null)
                                            return 'null';
                                        else {
                                            var ret = [];
                                            for (var x in data)
                                                ret.push('"' + x + '":' + toJSON(data[x]));
                                            return '{' + ret.join(',') + '}';
                                        }                                            
                                    default:
                                        window.ImpressCMS.language.controls.unknown_data_in_core_error.format(typeof data);
                                } 
                            }
                            return toJSON(value);
                        }                        
                    break;
                    case window.ImpressCMS.config.var_type.list:
                        return value.join(';');
                    break;
                }
            },
            setVar: function (name, value) {
                if (!this.configuration.fields[name] || (this.getVar(name) === value))
                    return;
                switch (this.configuration.fields[name].renderType) {
                    case window.ImpressCMS.core.controls.renderType.attribute:
                        this.attr(name, this.encodeAttrValue(name, value));
                    break;
                    case window.ImpressCMS.core.controls.renderType.state:
						if (value) {
							this.attr(name);
						} else {
							this.removeAttr(name);
						}
                    break;
                    case window.ImpressCMS.core.controls.renderType.style:
                        this.css(name, value);
                    break;
                    case window.ImpressCMS.core.controls.renderType.tag:
                        throw window.ImpressCMS.language.controls.tag_cant_be_modified_error;
                    break;
                    case window.ImpressCMS.core.controls.renderType.data:
                        this.attr('data-' + name, this.encodeAttrValue(name, value));
                    break;
                }
                this.trigger('changedVar', [name, value]);
            },
            getNotDefaultVars: function (getAllVars) {
                var rez = {};
                for(var x in this.configuration.fields) {
                    if (x == '') 
                        continue;
                    var val = this.getVar(x);
                    if (this.configuration.baseValues[x] == val)
                        continue;
                    if (x == 'class' || x == 'style' || x == 'id') 
                        continue;
                    switch (this.configuration.fields[x].renderType) {
                        case window.ImpressCMS.core.controls.renderType.tag:
                            continue;
                    }
                    switch (this.configuration.fields[x][window.ImpressCMS.config.var_param.type]) {
                        case window.ImpressCMS.config.var_type.data_source:
                            if (!getAllVars) continue;
                            rez[x] = val;
                        break;
                        case window.ImpressCMS.config.var_type.tag:
                            continue;
                        default:
                            rez[x] = val;
                    }                    
                }
                return rez;
            },
            isSameControlType: function (type) {
                return type != this.getControlType();
            },
            importArray: function (arr) {
                var set = {};
                for(var x in this.configuration.fields) {
                    if (x == 'class' || x == 'style' || x == 'id') 
                        continue;
                    switch (this.configuration.fields[x].renderType) {
                        case window.ImpressCMS.core.controls.renderType.tag:
                            continue;
                    }
                    switch (this.configuration.fields[x][window.ImpressCMS.config.var_param.type]) {
                        case window.ImpressCMS.config.var_type.data_source:
                        case window.ImpressCMS.config.var_type.tag:
                            continue;
                    }
                    set[x] = (typeof arr[x] == "undefined")?this.configuration.baseValues[x]:arr[x];
                }
                console.log(set);
                for(var x in set)
                    this.setVar(x, set[x]);
            },
            toArray: function () {
                var rez = {};
                for(var x in this.configuration.fields) {
                    if (x == '') 
                        continue;
                    rez[x] = this.getVar(x);
                    switch (this.configuration.fields[x][window.ImpressCMS.config.var_param.type]) {
                        case window.ImpressCMS.config.var_type.list:
                            rez[x] = rez[x].join(';');
                        break;                        
                    }
                }
                return rez;
            },
            getCurrentState: function () {     
                var rez = this.getNotDefaultVars();
                var ret = jQuery.param(rez, false);
                return ret;
            },
            setCurrentState: function (state) {
                var data = window.ImpressCMS.URLHandler.getParamsArray(state);
                for(var x in this.configuration.fields) { 
                    if (!data[x])
                        this.setVar(x, this.configuration.baseValues[x]);
                    else 
                        this.setVar(x, data[x]);                    
                }
            },
            showGlobalAnimation: function (animation) {
                if (!jQuery.isFunction(animation))
                    animation = window.ImpressCMS.core.animations.predefined.loading;                
                
                if (hData.animation)
                    hData.animation.stop();
                    
                hData.animation = new window.ImpressCMS.core.animations.init.asOverlay(this, animation);
            },
            hideGlobalAnimation: function () {
                if (hData.animation && hData.animation.stop) {
                    hData.animation.stop();     
                    delete hData.animation;
                }
            }
         });

         if (obj.configuration.parentType) {
             obj.parentControl = jQuery.extend(true, {}, obj, obj.parentControl);
         }
         
        /* for(var x in obj.configuration.fields) {   
             switch (obj.configuration.fields[x].renderType) {
                 case window.ImpressCMS.core.controls.renderType.style:
                     if (obj.css(x).length < 1)
                        obj.css(x, obj.configuration.baseValues[x]);
                 break;
                 case window.ImpressCMS.core.controls.renderType.state:
                     // do nothing
                 break;
                 case window.ImpressCMS.core.controls.renderType.data:
                     x = 'data-' + x;
                 case window.ImpressCMS.core.controls.renderType.attribute:
                     //if (obj.attr(x) === undefined)
                     //   obj.attr(x, obj.configuration.baseValues[x]);
                 break;
             }
         }*/
         
         obj.bind({
            ajaxError: function (e, x, settings, exception) {
                var message;
                if (x.status) {
                    message = window.ImpressCMS.language.server[x.status];
                    if(!message){
                        message = window.ImpressCMS.language.request.unknown_error;
                    }
                }else if(e=='parsererror'){
                    message=window.ImpressCMS.language.request.parse_error;
                }else if(e=='timeout'){
                    message=window.ImpressCMS.language.request.timeout;
                }else if(e=='abort'){
                    message=window.ImpressCMS.language.request.abort;
                }else {
                    message=window.ImpressCMS.language.request.unknown_error;
                }               
                window.ImpressCMS.message.show(message, window.ImpressCMS.language.controls.error, window.ImpressCMS.message.type.error);
                window.ImpressCMS.controls[id].hideGlobalAnimation();                
            },
            DOMAttrModified: function (event) {
                var obj = jQuery(event.target);
                switch (event.attrChange) {
                    case MutationEvent.MODIFICATION:
                        if (event.attrName == 'id')
                            event.preventDefault();
                        else
                            obj.trigger('changedAttr', [event.attrName, event.prevValue, event.newValue]);
                    break;
                    case MutationEvent.ADDITION:
                        obj.trigger('addedAttr', [event.attrName, event.newValue]);
                    break;
                    case MutationEvent.REMOVAL:
                        if (event.attrName == 'id')
                            event.preventDefault();
                        else
                            obj.trigger('removedAttr', [event.attrName, event.prevValue]);                        
                    break;
               }
            },
            propertychange: function (event) {
                 if (event.propertyName == 'id')
                     throw window.ImpressCMS.language.controls.id_attr_cant_be_modified_error;
                 obj.trigger('changedAttr', [event.propertyName, undefined, obj.attr(event.propertyName)]);
            },
            DOMNodeRemoved: function (event) {
                var obj = jQuery(event.target);
                obj.trigger('removeNode');
            },
            DOMNodeRemovedFromDocument: function (event) {
                var obj = jQuery(event.target);
                obj.trigger('removeNode', event.target);
            },
            changedAttr: function (event, name, oldValue, newValue) {
                var ctl = window.ImpressCMS.controls[jQuery(this).attr('id')];
                if (!ctl || !ctl.configuration || !ctl.configuration.serverEvents)
                    return;
                if ('propertyChanged' in ctl.configuration.serverEvents) {
                    if (name in ctl.configuration.fields)
                        window.ImpressCMS.ActionQueue.module.addAction(ctl.attr('id'), 'propertyChanged', {name:name, newValue:newValue, oldValue:oldValue});
                }
            },
            removedAttr: function (event, name, oldValue) {
                var ctl = window.ImpressCMS.controls[jQuery(this).attr('id')];
                if (!ctl || !ctl.configuration || !ctl.configuration.serverEvents)
                    return;
                if ('propertyChanged' in ctl.configuration.serverEvents) {
                    if (name in ctl.configuration.fields)
                        window.ImpressCMS.ActionQueue.module.addAction(ctl.attr('id'), 'propertyChanged', {name:name, newValue:'', oldValue:oldValue});
                }
            },
            addedAttr: function (event, name, newValue) {
                var ctl = window.ImpressCMS.controls[jQuery(this).attr('id')];
                if (!ctl || !ctl.configuration || !ctl.configuration.serverEvents)
                    return;
                if ('propertyChanged' in ctl.configuration.serverEvents) {
                    if (name in ctl.configuration.fields)
                        window.ImpressCMS.ActionQueue.module.addAction(ctl.attr('id'), 'propertyChanged', {name:name, oldValue:'', newValue:newValue});
                }
            },
            execFinished: function (event, data) {                
                if (data.error) {
                    window.ImpressCMS.message.show(data.error, window.ImpressCMS.language.controls.error, window.ImpressCMS.message.type.error);
                } else if (data.errors) {
                    for (var i = 0; i < data.errors.length; i++)
                        window.ImpressCMS.message.show(data.errors[i], window.ImpressCMS.language.controls.error, window.ImpressCMS.message.type.error);
                } else {
                    if (data[window.ImpressCMS.config.response_special_key.inner_html] != undefined) {
                        var ctl = obj;
                        if (data[window.ImpressCMS.config.response_special_key.selector])
                            ctl = jQuery(data[window.ImpressCMS.config.response_special_key.selector], ctl);
                        ctl.html(data[window.ImpressCMS.config.response_special_key.inner_html]);                                        
                    }
                    
                    if (data[window.ImpressCMS.config.response_special_key.changed_properties] != undefined) {
                        for(var x in data[window.ImpressCMS.config.response_special_key.changed_properties]) {
                            var sel_obj = window.ImpressCMS.core.controls.getControl(x);
                            for (var y in data[window.ImpressCMS.config.response_special_key.changed_properties][x])
                                sel_obj.setVar(y, data[window.ImpressCMS.config.response_special_key.changed_properties][x][y]);
                        }                                                        
                    }
                    if (data.message)
                        window.ImpressCMS.message.show(data.message, window.ImpressCMS.language.controls.info, window.ImpressCMS.message.type.info);
                    
                    var self = this;
                    setTimeout(function() {
                        window.ImpressCMS.core.controls.update(self);
                    }, 500);
                }
                window.ImpressCMS.controls[id].hideGlobalAnimation();
            },
            controlInitialized: function (e) {
                e.stopPropagation();
                if (typeof(obj.controlInitialized) == 'function')
                    obj.controlInitialized();
                         
            }
         });         
         
         window.ImpressCMS.controls[id] = obj;         
         window.ImpressCMS.controls[id].trigger('controlInitialized');
         
     }
     return window.ImpressCMS.controls[id];
  });  

};