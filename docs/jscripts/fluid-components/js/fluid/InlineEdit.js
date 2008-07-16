/*
Copyright 2008 University of Toronto

Licensed under the Educational Community License (ECL), Version 2.0 or the New
BSD license. You may not use this file except in compliance with one these
Licenses.

You may obtain a copy of the ECL 2.0 License and BSD License at
https://source.fluidproject.org/svn/LICENSE.txt
*/

/*global jQuery*/

/*global fluid*/
fluid = fluid || {};

(function ($, fluid) {
    
    // Is paddings doing what we want? Should it be in the CSS file instead?
    function edit(text, editContainer, editField, invitationStyle, focusStyle, paddings) {
		editField.val(text.text());
		editField.width(Math.max(text.width() + paddings.add, paddings.minimum));
        text.removeClass(invitationStyle);
        text.removeClass(focusStyle);
        text.hide();
        editContainer.show();

        // Work around for FLUID-726
        // Without 'setTimeout' the finish handler gets called with the event and the edit field is inactivated.       
        setTimeout(function () {
            editField.focus();    
        }, 0);
        
    }
    
    function view(editContainer, text) {
        editContainer.hide();
        text.show();
    }

    function finish(editContainer, editField, text, finishedFn) {
        finishedFn(editField);
        text.text(editField.val());
        view(editContainer, text);
        text.focus();
    }
        
    function editHandler(text, editContainer, editField, invitationStyle, focusStyle, paddings) {
        return function () {
            edit(text, editContainer, editField, invitationStyle, focusStyle, paddings);
            return false;
        }; 
    }
    
    function bindHoverHandlers(text, invitationStyle) {
        var over = function (evt) {
            text.addClass(invitationStyle);
        };     
        var out = function (evt) {
            text.removeClass(invitationStyle);
        };

        text.hover(over, out);
    }
    
    function mouse(text, editContainer, editField, styles, paddings, finishFn) {
        bindHoverHandlers(text, styles.invitation);
        text.click(editHandler(text, editContainer, editField, styles.invitation, styles.focus, paddings));
    }
    
    function bindKeyHighlight(text, focusStyle) {
        var focusOn = function () {
            text.addClass(focusStyle);    
        };
        var focusOff = function () {
            text.removeClass(focusStyle);    
        };
        
        text.focus(focusOn);
        text.blur(focusOff);
    }
    
    function keyNav(text, editContainer, editField, styles, paddings) {
        text.tabbable();
        bindKeyHighlight(text, styles.focus);
        text.activatable(editHandler(text, editContainer, editField, styles.invitation, styles.focus, paddings));
    } 
    
    function bindEditFinish(editContainer, editField, text, finishedFn) {
        var finishHandler = function (evt) {
            // Fix for handling arrow key presses see FLUID-760
            var code = (evt.keyCode ? evt.keyCode : (evt.which ? evt.which : 0));
            if (code !== $.a11y.keys.ENTER) {
                return true;
            }
            
            finish(editContainer, editField, text, finishedFn);
            return false;
        };

        editContainer.keypress(finishHandler);
    }
    
    function bindBlurHandler(editContainer, editField, text, finishedFn) {
        var blurHandler = function (evt) {
            finish(editContainer, editField, text, finishedFn);
            return false;
        };

        editField.blur(blurHandler);        
    }
    
    function aria(text, editContainer) {
        // Need to add ARIA roles and states.
    }
    
    fluid.InlineEdit = function (componentContainerId, options) {
        // Mix in the user's configuration options.
        options = options || {};
        var selectors = $.extend({}, this.defaults.selectors, options.selectors);
        this.styles = $.extend({}, this.defaults.styles, options.styles);
        this.paddings = $.extend({}, this.defaults.paddings, options.paddings);
		this.finishedEditing = options.finishedEditing || function () {};
        
        // Bind to the DOM.
        this.container = fluid.utils.jById(componentContainerId);
        this.text = $(selectors.text, this.container);
        this.editContainer = $(selectors.editContainer, this.container);
        this.editField = $(selectors.edit, this.editContainer);
        
        // Add event handlers.
        mouse(this.text, this.editContainer, this.editField, this.styles, this.paddings, this.finishedEditing);
        keyNav(this.text, this.editContainer, this.editField, this.styles, this.paddings);
        bindEditFinish(this.editContainer, this.editField, this.text, this.finishedEditing);
        bindBlurHandler(this.editContainer, this.editField, this.text, this.finishedEditing);
        
        // Add ARIA support.
        aria(this.text, this.editContainer);
        
        // Hide the edit container to start
        this.editContainer.hide();
    };
    
    // Seems a bit strange that we put edit and finish on the prototype but internally we just use the private functions
    fluid.InlineEdit.prototype.edit = function () {
        edit(this.text, this.editContainer, this.editField, this.styles.invitation, this.styles.focus, this.paddings);
    };
    
    fluid.InlineEdit.prototype.finish = function () {
        finish(this.editContainer, this.editField, this.text, this.finishedEditing);
    };
    
    fluid.InlineEdit.prototype.defaults = {
        selectors: {
            text: ".text",
            editContainer: ".editContainer",
            edit: ".edit"
        },
        
        styles: {
            invitation: "invitation",
            focus: "focus"
        },
		
		paddings: {
			add: 10,
			minimum: 80
		}
    };
        
})(jQuery, fluid);
