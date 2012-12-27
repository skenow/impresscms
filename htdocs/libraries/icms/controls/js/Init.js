jQuery(
    function () {
        jQuery(window.ImpressCMS.core.controls.getSelector()).getControl();
        jQuery(window).bind('popstate', function () {
            var state = window.ImpressCMS.URLHandler.getParamsArray();
            if (state.icms_page_state == undefined)
                return;
            if (jQuery.isArray(state.icms_page_state))                
                state = state.icms_page_state[0];
            else
                state = state.icms_page_state;
            window.ImpressCMS.core.controls.setState(unescape(state));
        });
    }
);