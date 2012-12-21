String.prototype.format = function() {
    var formatted = this;    
    for (var i = 0; i < arguments.length; i++) {
        var regexp = new RegExp('\\{'+i+'\\}', 'gi');
        formatted = formatted.replace(regexp, arguments[i]);
    }
    return formatted;
};

if (!(JSON && JSON.stringify)) {
    if (!JSON)
        JSON = {};
    if (jQuery.stringify)
        JSON.stringify = jQuery.stringify;
    else {
        JSON.stringify = function (value) {
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
    }
}