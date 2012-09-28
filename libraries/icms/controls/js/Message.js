window.ImpressCMS.message = {
    type: {
      none: 0,
      info: 1,
      exclamation: 2,
      asterisk: 3,
      error: 4,
      question: 5,
      stop: 6,
      warning: 7
    },
    data: [
        { //0
            image: ''
        },
        { // 1
            image: 'infomsg_icon.gif'
        },
        { // 2
            image: ''
        },
        { // 3
            image: ''
        },
        { // 4
            image: 'errormsg_icon.gif'
        },
        { // 5
            image: 'kfaenza/help.png'
        },
        { // 6
            image: 'kfaenza/stop.png'
        },
        { // 7
            image: ''
        }        
    ],
    show: function (msg, header, type) {
        if (!header)
            header = '';
        if (!type)
            type = window.ImpressCMS.message.type.none;
        var img = window.ImpressCMS.config.url.root + '/images/' + window.ImpressCMS.message.data[type].image;
        var options = {sticky:false};
        options.header = ((window.ImpressCMS.message.data[type].image == '')?'':'<img src="' + img + '" alt="icon" />') + ' ' + header;
        if (type == window.ImpressCMS.message.error)
            options.speed = 'slow';        
        jQuery.jGrowl(msg, options);
    }
}