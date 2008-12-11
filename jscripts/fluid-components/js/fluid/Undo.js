/*

Copyright 2008 University of Cambridge
Copyright 2008 University of Toronto

Licensed under the Educational Community License (ECL), Version 2.0 or the New
BSD license. You may not use this file except in compliance with one these
Licenses.

You may obtain a copy of the ECL 2.0 License and BSD License at
https://source.fluidproject.org/svn/LICENSE.txt
*/

/*global jQuery*/
/*global fluid_0_5*/

fluid_0_5 = fluid_0_5 || {};

(function ($, fluid) {
    
  // The three states of the undo component
    var STATE_INITIAL = "state_initial", 
        STATE_CHANGED = "state_changed",
        STATE_REVERTED = "state_reverted";
  
    function defaultRenderer(that, targetContainer) {
        var markup = "<span class='fluid-undo'>" + 
          "<span class='undoContainer'>[<a href='#' class='undoControl'>undo</a>]</span>" + 
          "<span class='redoContainer'>[<a href='#' class='redoControl'>redo</a>]</span>" + 
        "</span>";
        var markupNode = $(markup);
        targetContainer.append(markupNode);
        return markupNode;
    }
  
    function refreshView(that) {
        if (that.state === STATE_INITIAL) {
            that.locate("undoContainer").hide();
            that.locate("redoContainer").hide();
        }
        else if (that.state === STATE_CHANGED) {
            that.locate("undoContainer").show();
            that.locate("redoContainer").hide();
        }
        else if (that.state === STATE_REVERTED) {
            that.locate("undoContainer").hide();
            that.locate("redoContainer").show();          
        }
    }
   
    
    var bindHandlers = function (that) { 
        that.locate("undoControl").click( 
            function () {
                fluid.model.copyModel(that.extremalModel, that.component.model);
                fluid.model.copyModel(that.component.model, that.initialModel);
                that.component.refreshView();
                that.state = STATE_REVERTED;
                refreshView(that);
                that.locate("redoControl").focus();
            }
        );
        that.locate("redoControl").click( 
            function () {
                fluid.model.copyModel(that.component.model, that.extremalModel);
                that.component.refreshView();
                that.state = STATE_CHANGED;
                refreshView(that);
                that.locate("undoControl").focus();
            }
        );
        return {
            modelChanged: function () {
                that.state = STATE_CHANGED;
                refreshView(that);
            },
            onFinish: function () {
                if (that.component.model === that.extremalModel) {
                    fluid.model.copyModel(that.initialModel, that.component.model);
                }
            }
        };
    };
    
    /**
     * Decorates a target component with the function of "undoability"
     * 
     * @param {Object} component a "model-bearing" standard Fluid component to receive the "undo" functionality
     * @param {Object} options a collection of options settings
     */
    fluid.undoDecorator = function (component, userOptions) {
        var that = fluid.initView("undo", null, userOptions);
        that.container = that.options.renderer(that, component.container);
        fluid.initDomBinder(that);
        
        that.component = component;
        that.initialModel = {};
        that.extremalModel = {};
        fluid.model.copyModel(that.initialModel, component.model);
        
        that.state = STATE_INITIAL;
        refreshView(that);
        var listeners = bindHandlers(that);
        
        that.returnedOptions = {
            listeners: listeners
        };
        return that;
    };
  
    fluid.defaults("undo", {  
        selectors: {
            undoContainer: ".undoContainer",
            undoControl: ".undoControl",
            redoContainer: ".redoContainer",
            redoControl: ".redoControl"
        },
                    
        renderer: defaultRenderer
    });
        
})(jQuery, fluid_0_5);
