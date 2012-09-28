 window.ImpressCMS.cookies = {
        set: function (name,value,days) {
            var exdate=new Date();
            exdate.setDate(exdate.getDate() + days);
            document.cookie= name + "=" + escape(value) + ((days==null) ? '' : '; expires='+exdate.toUTCString());
        },
        parseRecord: function (unparsed) {
            var i = unparsed.indexOf("=");
            var name = unparsed.substr(0,i);
            return  {
                name: name.replace(/^\s+|\s+$/g,""),
                value: unescape(unparsed.substr(i+1))
            };
        },
        get: function (name) {
            var cookies=document.cookie.split(";");
            for (i=0;i<cookies.length;i++) {
                var ret = window.ImpressCMS.cookies.parseRecord(cookies[i]);
                if (ret.name == name)
                    return ret.value;
            }
            return null;
        }
    };