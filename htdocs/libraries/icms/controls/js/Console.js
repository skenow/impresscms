window.ImpressCMS.console = new (function () {
    
    this.customHandler = null;
    
    var self = this;
    var defaultHandler = (!window.console)?null:window.console;    
    
    this.log = function (type, msg) {
        if (self.customHandler && self.customHandler.log) {
            self.customHandler.log(type, msg);
        } else if (defaultHandler && defaultHandler.log) {
            defaultHandler.log(type, msg);
        }
    };
    
    this.clear = function () {
        if (self.customHandler && self.customHandler.clear) {
            self.customHandler.clear();
        } else if (defaultHandler && defaultHandler.clear) {
            defaultHandler.clear();
        }
    };
   
})();