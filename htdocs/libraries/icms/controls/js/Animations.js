window.ImpressCMS.core.animations = {
    current: {},
    prepareForAnimation: function(canvas, update_interval) {
        var id = this.getCanvasID(canvas);
        var obj = jQuery(canvas);
        if (!update_interval)
            update_interval = 250;
        this.current[id] = {
            context: obj[0].getContext('2d'),
            interval: setInterval(function () {
                obj.trigger('updateCanvas', window.ImpressCMS.core.animations.current[id]);
            }, update_interval)
        };
        obj.trigger('animationStarted', this.current[id]);
        return id;
    },    
    predefined: {
        loading: function (canvas) {
            var id = window.ImpressCMS.core.animations.prepareForAnimation(canvas);
            var obj = jQuery(canvas);
            window.ImpressCMS.core.animations.current[id].colors = ['black'];
            for(var i = 0; i < 5; i++) {
                var c = Math.floor(255 / i);
                window.ImpressCMS.core.animations.current[id].colors.push('rgb(' + c + ',' + c + ',' + c + ')');
            }
            window.ImpressCMS.core.animations.current[id].center = {
               x: obj.width() / 2,
               y: obj.height() / 2
            };
            window.ImpressCMS.core.animations.current[id].context.translate(window.ImpressCMS.core.animations.current[id].center.x, window.ImpressCMS.core.animations.current[id].center.y);
            window.ImpressCMS.core.animations.current[id].angle = {
                all: 30 * Math.PI / 180,
                item: 360 / window.ImpressCMS.core.animations.current[id].colors.length * Math.PI / 180
            }
            obj.bind('updateCanvas', function (e, current) {
                current.context.rotate(current.angle.all);
                for(var i = 0; i < current.colors.length; i++) {
                    current.context.rotate(current.angle.item);
                    current.context.fillStyle=current.colors[i];
                    current.context.fillRect(0, 0, 20, 10);
                }
            });
            var current = window.ImpressCMS.core.animations.current[id];
            for(var c = 0; c < 9; c++) {
                current.context.rotate(current.angle.all);
                for(var i = 0; i < current.colors.length; i++) {
                    current.context.rotate(current.angle.item);
                    current.context.fillStyle=current.colors[i];
                    current.context.fillRect(0, 0, 20, 10);
                }
            }
            //obj.trigger('updateCanvas', window.ImpressCMS.core.animations.current[id]);
        }        
    },
    getCanvasID: function (canvas) {
        var obj = jQuery(canvas);
        var id = obj.attr('id');
        if (!id) {
            var dt = new Date();
            while (jQuery(id = Base64.encode(dt.toDateString() + ' ' + Math.random())).length > 0);                
            obj.attr('id', id); 
        }
        return id;
    },
    stop: function (canvas) {
            var id = this.getCanvasID(canvas);
            if (!window.ImpressCMS.core.animations.current[id])
                return;
            
            clearInterval(window.ImpressCMS.core.animations.current[id].interval);
            window.ImpressCMS.core.animations.current[id].interval = null;
            
            var obj = jQuery(canvas);   
            obj.trigger('animationFinished', window.ImpressCMS.core.animations.current[id]);
            
            delete window.ImpressCMS.core.animations.current[id];                     
    },
    style: {
       overlay: {
            'text-align': 'center',
            'vertical-align': 'middle',
            'font-size': '2em',
            overflow: 'hidden',
            position: 'absolute',
            opacity: 0.6,
            'background-color': 'grey',
            'font-weight': 'bold',
            color: 'white',            
            'z-index': 2012
      }
    },
    init: {
        asOverlay: function (baseObj, animation) {
            
            if (!jQuery.isFunction(animation))
                animation = window.ImpressCMS.core.animations.predefined.loading;
            
            var obj = jQuery(baseObj);
            
            var canvas = jQuery('<canvas></canvas>');
            canvas.attr({
                        width: obj.width(),
                        height: obj.height(),
                        id: obj.attr('id') + '_animation'
                       });
            canvas.css(window.ImpressCMS.core.animations.style.overlay);
            canvas.css({
                        left: obj.offset().left,
                        top: obj.offset().top                        
                      });             
            
            this.stop = function () {
                window.ImpressCMS.core.animations.stop(canvas);
            };
            
            canvas.bind('animationFinished', function () {
                canvas.remove();
            });
            
            jQuery('body').append(canvas);
            
            canvas.show();
            
            animation(canvas);
        }
    }
};