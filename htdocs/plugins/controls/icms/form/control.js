m = {
    controlInitialized: function () {
        var obj = jQuery(this);
        var self = this;       
        jQuery('input', obj).keypress(function (e) {
            if ( e.which == 13 ) {
                e.preventDefault();
                obj.submit();
            }
        });
        var buttons = jQuery('button[type=submit]', obj);
        buttons.keypress(function (e) {
            if ( e.which == 13 ) {
                e.preventDefault();
                obj.submit();
            }
        });
        buttons.click(
            function (e) {
                e.preventDefault();
                obj.submit();
            }
        );
        obj.submit(
            function (e) {        
                var notreadonly = jQuery('input:not([disabled]), select:not([disabled]), button:not([disabled])', obj);
                var correct_readonly = function () {
                                notreadonly.removeAttr('disabled');
                               // self.unbind('execFinished', correct_readonly);
                            };
                notreadonly.attr('disabled', 'disabled');
                if (!window.ImpressCMS.actionQueue.processURL(self.getVar('action'), self.getFormData(), correct_readonly)) {
                    window.ImpressCMS.URLHandler.updateCurrentURL();
                    e.preventDefault();
                    return false;
                }
                return true;
            }
        );
    },
    getLocation: function () {
        var action = this.getVar('action');
        var location = window.ImpressCMS.URLHandler.parseURL(action);
        return location;
    },
    getFormData: function () {
        var obj = jQuery(this);
        var ret = {};
        jQuery('input', obj).each(
            function () {
                var obj = jQuery(this);
                var name = obj.attr('name');
                if (!name)
                    return;
                switch (obj.attr('type')) {
                    case 'reset':
                    case 'button':
                    case 'submit':
                        // skip these 
                    break;
                    case 'checkbox':
                    case 'radio':
                        if (obj.hasAttr('checked'))
                            ret[name] = obj.val();
                    break;
                    default:
                        ret[name] = obj.val();
                }                
            }
        );
        jQuery('select', obj).each(
            function () {
                var obj = jQuery(this);
                var name = obj.attr('name');
                if (!name)
                    return;
                ret[name] = obj.val();               
            }
        );
        return ret;
    }
}