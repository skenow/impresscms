// Controls Animation module
// Provides abstracted animation functions for controller

define(function(require) {
  var $ = require('jquery')
  , current = {}
  , app = {

    style: {
      overlay: {
        'text-align': 'center'
        , 'vertical-align': 'middle'
        , 'font-size': '2em'
        , 'overflow': 'hidden'
        , 'position': 'absolute'
        , 'opacity': 0.6
        , 'background-color': 'grey'
        , 'font-weight': 'bold'
        , 'color': 'white'
        , 'z-index': 2012
      }
    }

    , prepareForAnimation: function(canvas, update_interval) {
      var id = app.getCanvasID(canvas)
      , obj = $(canvas);

      if (typeof update_interval === 'undefined') {
        update_interval = 250;
      }

      this.current[id] = {
        context: obj[0].getContext('2d')
        , interval: setInterval(function () {
          obj.trigger('updateCanvas', current[id]);
        }
        , update_interval
      )};

      obj.trigger('animationStarted', current[id]);

      return id;
    }

    , predefined: {
      loading: function (canvas) {
        var id = app.prepareForAnimation(canvas)
        , obj = jQuery(canvas)
        , i, c, current;
      
        current[id].colors = ['black'];
        
        for(i = 0; i < 5; i++) {
          c = Math.floor(255 / i);
          current[id].colors.push('rgb(' + c + ',' + c + ',' + c + ')');
        }
        
        app.current[id].center = {
          x: obj.width() / 2,
          y: obj.height() / 2
        };
        
        current[id].context.translate(current[id].center.x, current[id].center.y);
        current[id].angle = {
          all: 30 * Math.PI / 180
          , item: 360 / current[id].colors.length * Math.PI / 180
        };
        
        obj.bind('updateCanvas', function (e, current) {
          current.context.rotate(current.angle.all);
          
          for(i = 0; i < current.colors.length; i++) {
            current.context.rotate(current.angle.item);
            current.context.fillStyle=current.colors[i];
            current.context.fillRect(0, 0, 20, 10);
          }
        });
        
        current = current[id];
        for(c = 0; c < 9; c++) {
          current.context.rotate(current.angle.all);
          for(i = 0; i < current.colors.length; i++) {
            current.context.rotate(current.angle.item);
            current.context.fillStyle=current.colors[i];
            current.context.fillRect(0, 0, 20, 10);
          }
        }
      }
    }

    , getCanvasID: function (canvas) {
      var obj = jQuery(canvas)
      , id = obj.attr('id')
      , dt;
      
      if (typeof id === 'undefined') {
        dt = new Date();
        id = Base64.encode(dt.toDateString() + ' ' + Math.random());
        obj.attr('id', id);
      }
      return id;
    }

    , stop: function (canvas) {
      var id = this.getCanvasID(canvas)
      , obj;
      if (typeof current[id] === 'undefined') { return; }
            
      clearInterval(current[id].interval);
      current[id].interval = null;
            
      obj = $(canvas);
      obj.trigger('animationFinished', current[id]);
            
      delete current[id];
    }

    , init: {
      asOverlay: function (baseObj, animation) {
        if (!$.isFunction(animation)) {
          animation = app.predefined.loading;
        }
        var obj = jQuery(baseObj)
        , canvas = jQuery('<canvas></canvas>');
        
        canvas.attr({
          width: obj.width(),
          height: obj.height(),
          id: obj.attr('id') + '_animation'
        })
        .css(app.style.overlay)
        .css({
          left: obj.offset().left
          , top: obj.offset().top
        });
            
        this.stop = function () {
          app.stop(canvas);
        };
            
        canvas.bind('animationFinished', function () {
          canvas.remove();
        });
            
        $('body').append(canvas);
            
        canvas.show();
        animation(canvas);
      }
    }

  };
  return app;
});