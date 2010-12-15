/*
Copyright 2010 OCAD University

Licensed under the Educational Community License (ECL), Version 2.0 or the New
BSD license. You may not use this file except in compliance with one these
Licenses.

You may obtain a copy of the ECL 2.0 License and BSD License at
https://source.fluidproject.org/svn/LICENSE.txt
*/

/*global jQuery, fluid_1_3:true*/

var fluid_1_3 = fluid_1_3 || {};

(function ($, fluid) {
    
    var createContentFunc = function (content) {
        return typeof content === "function" ? content : function () {
            return content;
        };
    };

    var setup = function (that) {
        that.container.tooltip({
            content: createContentFunc(that.options.content),
            position: that.options.position,
            items: that.options.items,
            open: function (event) {
                var tt = $(event.target).tooltip("widget");
                tt.stop(false, true);
                tt.hide();
                if (that.options.delay) {
                    tt.delay(that.options.delay).fadeIn("default", that.events.afterOpen.fire());
                } else {
                    tt.show();
                    that.events.afterOpen.fire();
                }
            },
            close: function (event) {
                var tt = $(event.target).tooltip("widget");
                tt.stop(false, true);
                tt.hide();
                tt.clearQueue();
                that.events.afterClose.fire();
            } 
        });
        
        that.elm = that.container.tooltip("widget");
        
        that.elm.addClass(that.options.styles.tooltip);
    };

    fluid.tooltip = function (container, options) {
        var that = fluid.initView("fluid.tooltip", container, options);
        
        /**
         * Updates the contents displayed in the tooltip
         * 
         * @param {Object} content, the content to be displayed in the tooltip
         */
        that.updateContent = function (content) {
            that.container.tooltip("option", "content", createContentFunc(content));
        };
        
        /**
         * Destroys the underlying jquery ui tooltip
         */
        that.destroy = function () {
            that.container.tooltip("destroy");
        };
        
        /**
         * Manually displays the tooltip
         */
        that.open = function () {
            that.container.tooltip("open");
        };
        
        /**
         * Manually hides the tooltip
         */
        that.close = function () {
            that.container.tooltip("close");
        };
        
        setup(that);
        
        return that;
    };
    
    fluid.defaults("fluid.tooltip", {
        styles: {
            tooltip: ""
        },
        
        events: {
            afterOpen: null,
            afterClose: null  
        },
        
        content: "",
        
        position: {
            my: "left top",
            at: "left bottom",
            offset: "0 5"
        },
        
        items: "*",
        
        delay: 300
    });

})(jQuery, fluid_1_3);
