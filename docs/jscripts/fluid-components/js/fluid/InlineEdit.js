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
    function setCaretToStart(control) {
        if (control.createTextRange) {
            var range = control.createTextRange();
            range.collapse(true);
            range.select();
        } else if (control.setSelectionRange) {
            control.focus();
            control.setSelectionRange(0, 0);
        }
    }
    function setCaretToEnd(control) {
        var pos = control.value.length;
        if (control.createTextRange) {
            var range = control.createTextRange();
            range.move("character", pos);
            range.select();
        } else if (control.setSelectionRange) {
            control.focus();
            control.setSelectionRange(pos, pos);
        }
    }
    
    // Is paddings doing what we want? Should it be in the CSS file instead?
    function edit(that) {
        var viewEl = that.viewEl;
        var displayText = viewEl.text();
        that.updateModel(displayText === that.options.defaultViewText? "" : displayText);
        that.editField.width(Math.max(viewEl.width() + that.options.paddings.edit, that.options.paddings.minimumEdit));

        viewEl.removeClass(that.options.styles.invitation);
        viewEl.removeClass(that.options.styles.focus);
        viewEl.hide();
        that.editContainer.show();
        if (that.tooltipEnabled()) {
            $("#" + that.options.tooltipId).hide();
        }

        // Work around for FLUID-726
        // Without 'setTimeout' the finish handler gets called with the event and the edit field is inactivated.       
        setTimeout(function () {
            that.editField.focus();
            if (that.options.selectOnEdit) {
                that.editField[0].select();
            }
            else {
                setCaretToEnd(that.editField[0]);
            }
        }, 0);
        that.events.afterBeginEdit.fire();
    }



    function clearEmptyViewStyles(textEl, defaultViewStyle, originalViewPadding) {
        textEl.removeClass(defaultViewStyle);
        textEl.css('padding-right', originalViewPadding);
    }
    
    
    function showDefaultViewText(that) {
        that.viewEl.text(that.options.defaultViewText);
        that.viewEl.addClass(that.options.styles.defaultViewText);
    }
    

    function showNothing(that) {
        that.viewEl.text("");
       // workaround for FLUID-938, IE can not style an empty inline element, so force element to be display: inline-block
       
        if ($.browser.msie) {
            if (that.viewEl.css('display') === 'inline') {
                that.viewEl.css('display', "inline-block");
            }
        }
        
        // If necessary, pad the view element enough that it will be evident to the user.
        if (that.existingPadding < that.options.paddings.minimumView) {
            that.viewEl.css('padding-right',  that.options.paddings.minimumView);
        }
    }

    function showEditedText(that) {
        that.viewEl.text(that.model.value);
        clearEmptyViewStyles(that.viewEl, that.options.defaultViewStyle, that.existingPadding);
    }

    function finish(that) {
        that.events.onFinish.fire();
        if (that.options.finishedEditing) {
            that.options.finishedEditing(that.editField[0], that.viewEl[0]);
        }
        that.updateModel(that.editField.val());
        
        that.editContainer.hide();
        that.viewEl.show();
    }
        
    function makeEditHandler(that) {
        return function () {
            var prevent = that.events.onBeginEdit.fire();
            if (prevent) return true;
            edit(that);
            return false;
        }; 
    }
    
    function bindHoverHandlers(viewEl, invitationStyle) {
        var over = function (evt) {
            viewEl.addClass(invitationStyle);
        };     
        var out = function (evt) {
            viewEl.removeClass(invitationStyle);
        };

        viewEl.hover(over, out);
    }
    
    function bindMouseHandlers(that) {
        bindHoverHandlers(that.viewEl, that.options.styles.invitation);
        that.viewEl.click(makeEditHandler(that));
    }
    
    function bindKeyHighlight(viewEl, focusStyle, invitationStyle) {
        var focusOn = function () {
            viewEl.addClass(focusStyle);
            viewEl.addClass(invitationStyle); 
        };
        var focusOff = function () {
            viewEl.removeClass(focusStyle);
            viewEl.removeClass(invitationStyle);
        };
        viewEl.focus(focusOn);
        viewEl.blur(focusOff);
    }
    
    function bindKeyboardHandlers(that) {
        that.viewEl.tabbable();
        bindKeyHighlight(that.viewEl, that.options.styles.focus, that.options.styles.invitation);
        that.viewEl.activatable(makeEditHandler(that));
    } 
    
    function bindEditFinish(that) {
        var finishHandler = function (evt) {
            // Fix for handling arrow key presses see FLUID-760
            var code = (evt.keyCode ? evt.keyCode : (evt.which ? evt.which : 0));
            if (code !== $.a11y.keys.ENTER) {
                return true;
            }
            
            finish(that);
            that.viewEl.focus();  // Moved here from inside "finish" to fix FLUID-857
            return false;
        };
        that.editContainer.keypress(finishHandler);
    }
    
    function bindBlurHandler(that) {
        var blurHandler = function (evt) {
            finish(that);
            return false;
        };
        that.editField.blur(blurHandler);
    }
    
    function aria(viewEl, editContainer) {
        viewEl.ariaRole("button");
    }
    
    var bindToDom = function (that, container) {
        // Bind to the DOM.
        that.viewEl = that.locate("text");

        // If an edit container is found in the markup, use it. Otherwise generate one based on the view text.
        that.editContainer = $(that.options.selectors.editContainer, that.container);
        if (that.editContainer.length >= 1) {
            var isEditSameAsContainer = that.editContainer.is(that.options.selectors.edit);
            var containerConstraint = isEditSameAsContainer ? that.container : that.editContainer;
            that.editField =  $(that.options.selectors.edit, containerConstraint);
        } else {
            var editElms = that.options.editModeRenderer(that);
            that.editContainer = editElms.container;
            that.editField = editElms.field;
        }
    };
    
    var defaultEditModeRenderer = function (that) {
        // Template strings.
        var editModeTemplate = "<span><input type='text' class='edit'/></span>";

        // Create the edit container and pull out the textfield.
        var editContainer = $(editModeTemplate);
        var editField = jQuery("input", editContainer);
        
        var componentContainerId = that.container.attr("id");
        // Give the container and textfield a reasonable set of ids if necessary.
        if (componentContainerId) {
            var editContainerId = componentContainerId + "-edit-container";
            var editFieldId = componentContainerId + "-edit";   
            editContainer.attr("id", editContainerId);
            editField.attr("id", editFieldId);
        }
        
        editField.val(that.model.value);
        
        // Inject it into the DOM.
        that.viewEl.after(editContainer);
        
        // Package up the container and field for the component.
        return {
            container: editContainer,
            field: editField
        };
    };
    
    
    var setupInlineEdit = function (componentContainer, that) {
        bindToDom(that, componentContainer);
        var padding = that.viewEl.css("padding-right");
        that.existingPadding = padding? parseFloat(padding) : 0;
        that.updateModel(that.viewEl.text());
        
        // Add event handlers.
        bindMouseHandlers(that);
        bindKeyboardHandlers(that);
        bindEditFinish(that);
        bindBlurHandler(that);
        
        // Add ARIA support.
        aria(that.viewEl, that.editContainer);
                
        // Hide the edit container to start
        that.editContainer.hide();
        
        var initTooltip = function () {
            // Add tooltip handler if required and available
            if (that.tooltipEnabled()) {
                $(componentContainer).tooltip({
                    delay: that.options.tooltipDelay,
                    extraClass: that.options.styles.tooltip,
                    bodyHandler: function () { 
                        return that.options.tooltipText; 
                    },
                    id: that.options.tooltipId
                });
            }
        };

        // when the document is ready, initialize the tooltip
        // see http://issues.fluidproject.org/browse/FLUID-1030
        jQuery(initTooltip);
    };
    
    
    /**
     * Instantiates a new Inline Edit component
     * 
     * @param {Object} componentContainer a selector, jquery, or a dom element representing the component's container
     * @param {Object} options a collection of options settings
     */
    fluid.inlineEdit = function (componentContainer, userOptions) {
      
        var that = fluid.initView("inlineEdit", componentContainer, userOptions);
       
        that.model = {value: ""};
       
        that.edit = function () {
            edit(that);
        };
        
        that.finish = function () {
            finish(that);
        };
            
        that.tooltipEnabled = function () {
            return that.options.useTooltip && $.fn.tooltip;
        };
        
        that.refreshView = function (source) {
            if (that.model.value) {
                showEditedText(that);
            } else if (that.options.defaultViewText) {
                showDefaultViewText(that);
            } else {
                showNothing(that);
            }
          
            if (that.editField && that.editField.index(source) === -1) {
                that.editField.val(that.model.value);
            }
        };
        
        that.updateModel = function (newValue, source) {
            var change = that.model.value !== newValue;
            if (change) {
                that.model.value = newValue;
                that.events.modelChanged.fire(newValue);
            }
            that.refreshView(source); // Always render, because of possibility of initial event
        };

        setupInlineEdit(componentContainer, that);
        
        that.decorators = fluid.initSubcomponents(that, "componentDecorators", 
            [that, fluid.COMPONENT_OPTIONS]);
        
        return that;
    };
    
    /**
     * A set of inline edit fields.
     */
    var setupInlineEdits = function (editables, options) {
        var editors = [];
        editables.each(function (idx, editable) {
            editors.push(fluid.inlineEdit(jQuery(editable), options));
        });
        
        return editors;
    };

    fluid.inlineEdits = function (componentContainer, options) {
        options = options || {};
        var selectors = $.extend({}, fluid.defaults("inlineEdits").selectors, options.selectors);
        
        // Bind to the DOM.
        var container = fluid.container(componentContainer);
        var editables = $(selectors.editables, container);
        
        return setupInlineEdits(editables, options);
    };
    
    fluid.defaults("inlineEdit", {  
        selectors: {
            text: ".text",
            editContainer: ".editContainer",
            edit: ".edit"
        },
        
        styles: {
            invitation: "inlineEdit-invitation",
            defaultViewText: "inlineEdit-invitation-text",
            tooltip: "inlineEdit-tooltip",
            focus: "inlineEdit-focus"
        },
        
        events: {
            modelChanged: null,
            onBeginEdit: "preventable",
            afterBeginEdit: null,
            onFinish: null
        },
        
        paddings: {
            edit: 10,
            minimumEdit: 80,
            minimumView: 60
        },
        
        editModeRenderer: defaultEditModeRenderer,
        
        defaultViewText: "Click here to edit",
        
        tooltipText: "Click item to edit",
        
        tooltipId: "tooltip",
        
        useTooltip: false,
        
        tooltipDelay: 2000,
        
        selectOnEdit: false
    });
    
    
    fluid.defaults("inlineEdits", {
        selectors: {
            editables: ".inlineEditable"
        }
    });
})(jQuery, fluid_0_5);
