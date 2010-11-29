/*
Copyright 2008-2009 University of Toronto
Copyright 2008-2009 University of California, Berkeley
Copyright 2010-2011 OCAD University

Licensed under the Educational Community License (ECL), Version 2.0 or the New
BSD license. You may not use this file except in compliance with one these
Licenses.

You may obtain a copy of the ECL 2.0 License and BSD License at
https://source.fluidproject.org/svn/LICENSE.txt
*/

/*global jQuery*/
/*global fluid_1_2*/

fluid_1_2 = fluid_1_2 || {};

(function ($, fluid) {    
    
    var animateDisplay = function (elm, animation, defaultAnimation) {
        animation = (animation) ? animation : defaultAnimation;
        elm.animate(animation.params, animation.duration, animation.callback);
    };
    
    var animateProgress = function (elm, width, speed) {
        // de-queue any left over animations
        elm.queue("fx", []); 
        
        elm.animate({ 
            width: width,
            queue: false
        }, 
        speed);
    };
    
    var showProgress = function (that, animation) {
        if (animation === false) {
            that.displayElement.show();
        } else {
            animateDisplay(that.displayElement, animation, that.options.showAnimation);
        }
    };
    
    var hideProgress = function (that, delay, animation) {
        
        delay = (delay === null || isNaN(delay)) ? that.options.delay : delay;
        
        if (delay) {
            // use a setTimeout to delay the hide for n millies, note use of recursion
            var timeOut = setTimeout(function () {
                hideProgress(that, 0, animation);
            }, delay);
        } else {
            if (animation === false) {
                that.displayElement.hide();
            } else {
                animateDisplay(that.displayElement, animation, that.options.hideAnimation);
            }
        }   
    };
    
    var updateWidth = function (that, newWidth, dontAnimate) {
        dontAnimate  = dontAnimate || false;
        var currWidth = that.indicator.width();
        var direction = that.options.animate;
        if ((newWidth > currWidth) && (direction === "both" || direction === "forward") && !dontAnimate) {
            animateProgress(that.indicator, newWidth, that.options.speed);
        } else if ((newWidth < currWidth) && (direction === "both" || direction === "backward") && !dontAnimate) {
            animateProgress(that.indicator, newWidth, that.options.speed);
        } else {
            that.indicator.width(newWidth);
        }
    };
         
    var percentToPixels = function (that, percent) {
        // progress does not support percents over 100, also all numbers are rounded to integers
        return Math.round((Math.min(percent, 100) * that.progressBar.width()) / 100);
    };
    
    var refreshRelativeWidth = function (that)  {
        var pixels = Math.max(percentToPixels(that, parseFloat(that.storedPercent)), that.options.minWidth);
        updateWidth(that, pixels, true);
    };
        
    var initARIA = function (ariaElement, ariaBusyText) {
        ariaElement.attr("role", "progressbar");
        ariaElement.attr("aria-valuemin", "0");
        ariaElement.attr("aria-valuemax", "100");
        ariaElement.attr("aria-valuenow", "0");
        //Empty value for ariaBusyText will default to aria-valuenow.
        if (ariaBusyText) {
            ariaElement.attr("aria-valuetext", "");
        }
        ariaElement.attr("aria-busy", "false");
    };
    
    var updateARIA = function (that, percent) {
        var str = that.options.strings;
        var busy = percent < 100 && percent > 0;
        that.ariaElement.attr("aria-busy", busy);
        that.ariaElement.attr("aria-valuenow", percent);   
        //Empty value for ariaBusyText will default to aria-valuenow.
        if (str.ariaBusyText) {
            if (busy) {
                var busyString = fluid.stringTemplate(str.ariaBusyText, {percentComplete : percent});           
                that.ariaElement.attr("aria-valuetext", busyString);
            } else if (percent === 100) {
                // FLUID-2936: JAWS doesn't currently read the "Progress is complete" message to the user, even though we set it here.
                that.ariaElement.attr("aria-valuetext", str.ariaDoneText);
            }
        }
    };
        
    var updateText = function (label, value) {
        label.html(value);
    };
    
    var repositionIndicator = function (that) {
        that.indicator.css("top", that.progressBar.position().top)
            .css("left", 0)
            .height(that.progressBar.height());
        refreshRelativeWidth(that);
    };
        
    var updateProgress = function (that, percent, labelText, animationForShow) {
        
        // show progress before updating, jQuery will handle the case if the object is already displayed
        showProgress(that, animationForShow);
            
        // do not update if the value of percent is falsey
        if (percent !== null) {
            that.storedPercent = percent;
        
            var pixels = Math.max(percentToPixels(that, parseFloat(percent)), that.options.minWidth);   
            updateWidth(that, pixels);
        }
        
        if (labelText !== null) {
            updateText(that.label, labelText);
        }
        
        // update ARIA
        if (that.ariaElement) {
            updateARIA(that, percent);
        }
    };
        
    var setupProgress = function (that) {
        that.displayElement = that.locate("displayElement");

        // hide file progress in case it is showing
        if (that.options.initiallyHidden) {
            that.displayElement.hide();
        }

        that.progressBar = that.locate("progressBar");
        that.label = that.locate("label");
        that.indicator = that.locate("indicator");
        that.ariaElement = that.locate("ariaElement");
        
        that.indicator.width(that.options.minWidth);

        that.storedPercent = 0;
                
        // initialize ARIA
        if (that.ariaElement) {
            initARIA(that.ariaElement, that.options.strings.ariaBusyText);
        }
        
        // afterProgressHidden:  
        // Registering listener with the callback provided by the user and reinitializing
        // the event trigger function. 
        // Note: callback depricated as of 1.5, use afterProgressHidden event
        if (that.options.hideAnimation.callback) {
            that.events.afterProgressHidden.addListener(that.options.hideAnimation.callback);           
        }
        
        // triggers the afterProgressHidden event    
        // Note: callback depricated as of 1.5, use afterProgressHidden event
        that.options.hideAnimation.callback = that.events.afterProgressHidden.fire;

        
        // onProgressBegin:
        // Registering listener with the callback provided by the user and reinitializing
        // the event trigger function.  
        // Note: callback depricated as of 1.5, use onProgressBegin event
        if (that.options.showAnimation.callback) {
            that.events.onProgressBegin.addListener(that.options.showAnimation.callback);                      
        } 
            
        // triggers the onProgressBegin event
        // Note: callback depricated as of 1.5, use onProgressBegin event
        that.options.showAnimation.callback = that.events.onProgressBegin.fire;
    };
           
    /**
    * Instantiates a new Progress component.
    * 
    * @param {jQuery|Selector|Element} container the DOM element in which the Uploader lives
    * @param {Object} options configuration options for the component.
    */
    fluid.progress = function (container, options) {
        var that = fluid.initView("fluid.progress", container, options);
        setupProgress(that);
        
        /**
         * Shows the progress bar if is currently hidden.
         * 
         * @param {Object} animation a custom animation used when showing the progress bar
         */
        that.show = function (animation) {
            showProgress(that, animation);
        };
        
        /**
         * Hides the progress bar if it is visible.
         * 
         * @param {Number} delay the amount of time to wait before hiding
         * @param {Object} animation a custom animation used when hiding the progress bar
         */
        that.hide = function (delay, animation) {
            hideProgress(that, delay, animation);
        };
        
        /**
         * Updates the state of the progress bar.
         * This will automatically show the progress bar if it is currently hidden.
         * Percentage is specified as a decimal value, but will be automatically converted if needed.
         * 
         * 
         * @param {Number|String} percentage the current percentage, specified as a "float-ish" value 
         * @param {String} labelValue the value to set for the label; this can be an HTML string
         * @param {Object} animationForShow the animation to use when showing the progress bar if it is hidden
         */
        that.update = function (percentage, labelValue, animationForShow) {
            updateProgress(that, percentage, labelValue, animationForShow);
        };
        
        that.refreshView = function () {
            repositionIndicator(that);
        };
                        
        return that;  
    };
      
    fluid.defaults("fluid.progress", {  
        selectors: {
            displayElement: ".flc-progress", // required, the element that gets displayed when progress is displayed, could be the indicator or bar or some larger outer wrapper as in an overlay effect
            progressBar: ".flc-progress-bar", //required
            indicator: ".flc-progress-indicator", //required
            label: ".flc-progress-label", //optional
            ariaElement: ".flc-progress-bar" // usually required, except in cases where there are more than one progressor for the same data such as a total and a sub-total
        },
        
        strings: {
            //Empty value for ariaBusyText will default to aria-valuenow.
            ariaBusyText: "Progress is %percentComplete percent complete",
            ariaDoneText: "Progress is complete."
        },
        
        // progress display and hide animations, use the jQuery animation primatives, set to false to use no animation
        // animations must be symetrical (if you hide with width, you'd better show with width) or you get odd effects
        // see jQuery docs about animations to customize
        showAnimation: {
            params: {
                opacity: "show"
            }, 
            duration: "slow",
            //callback has been deprecated and will be removed as of 1.5, instead use onProgressBegin event 
            callback: null 
        }, // equivalent of $().fadeIn("slow")
        
        hideAnimation: {
            params: {
                opacity: "hide"
            }, 
            duration: "slow", 
            //callback has been deprecated and will be removed as of 1.5, instead use afterProgressHidden event 
            callback: null
        }, // equivalent of $().fadeOut("slow")
        
        events: {            
            onProgressBegin: null,
            afterProgressHidden: null            
        },

        minWidth: 5, // 0 length indicators can look broken if there is a long pause between updates
        delay: 0, // the amount to delay the fade out of the progress
        speed: 200, // default speed for animations, pretty fast
        animate: "forward", // suppport "forward", "backward", and "both", any other value is no animation either way
        initiallyHidden: true, // supports progress indicators which may always be present
        updatePosition: false
    });
    
})(jQuery, fluid_1_2);
