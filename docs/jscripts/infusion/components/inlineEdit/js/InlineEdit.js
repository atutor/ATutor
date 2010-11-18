/*
Copyright 2008-2009 University of Cambridge
Copyright 2008-2010 University of Toronto
Copyright 2008-2009 University of California, Berkeley
Copyright 2010 OCAD University

Licensed under the Educational Community License (ECL), Version 2.0 or the New
BSD license. You may not use this file except in compliance with one these
Licenses.

You may obtain a copy of the ECL 2.0 License and BSD License at
https://source.fluidproject.org/svn/LICENSE.txt
*/

/*global jQuery, fluid_1_2, document, setTimeout*/

fluid_1_2 = fluid_1_2 || {};

(function ($, fluid) {
    
    function sendKey(control, event, virtualCode, charCode) {
        var kE = document.createEvent("KeyEvents");
        kE.initKeyEvent(event, 1, 1, null, 0, 0, 0, 0, virtualCode, charCode);
        control.dispatchEvent(kE);
    }
    
    /** Set the caret position to the end of a text field's value, also taking care
     * to scroll the field so that this position is visible.
     * @param {DOM node} control The control to be scrolled (input, or possibly textarea)
     * @param value The current value of the control
     */
    fluid.setCaretToEnd = function (control, value) {
        var pos = value ? value.length : 0;

        try {
            control.focus();
        // see http://www.quirksmode.org/dom/range_intro.html - in Opera, must detect setSelectionRange first, 
        // since its support for Microsoft TextRange is buggy
            if (control.setSelectionRange) {

                control.setSelectionRange(pos, pos);
                if ($.browser.mozilla && pos > 0) {
                  // ludicrous fix for Firefox failure to scroll to selection position, inspired by
                  // http://bytes.com/forum/thread496726.html
                    sendKey(control, "keypress", 92, 92); // type in a junk character
                    sendKey(control, "keydown", 8, 0); // delete key must be dispatched exactly like this
                    sendKey(control, "keypress", 8, 0);
                }
            }

            else if (control.createTextRange) {
                var range = control.createTextRange();
                range.move("character", pos);
                range.select();
            }
        }
        catch (e) {} 
    };

    var switchToViewMode = function (that) {
        that.editContainer.hide();
        that.displayModeRenderer.show();
    };
    
    var cancel = function (that) {
        if (that.isEditing()) {
            // Roll the edit field back to its old value and close it up.
            // This setTimeout is necessary on Firefox, since any attempt to modify the 
            // input control value during the stack processing the ESCAPE key will be ignored.
            setTimeout(function () {
                that.editView.value(that.model.value);
            }, 1);
            switchToViewMode(that);
            that.events.afterFinishEdit.fire(that.model.value, that.model.value, 
                that.editField[0], that.viewEl[0]);
        }
    };
    
    var finish = function (that) {
        var newValue = that.editView.value();
        var oldValue = that.model.value;

        var viewNode = that.viewEl[0];
        var editNode = that.editField[0];
        var ret = that.events.onFinishEdit.fire(newValue, oldValue, editNode, viewNode);
        if (ret === false) {
            return;
        }
        
        that.updateModelValue(newValue);
        that.events.afterFinishEdit.fire(newValue, oldValue, editNode, viewNode);
        
        switchToViewMode(that);
    };
    
    /** 
     * Do not allow the textEditButton to regain focus upon completion unless
     * the keypress is enter or esc.
     */  
    var bindEditFinish = function (that) {
        if (that.options.submitOnEnter === undefined) {
            that.options.submitOnEnter = "textarea" !== fluid.unwrap(that.editField).nodeName.toLowerCase();
        }
        function keyCode(evt) {
            // Fix for handling arrow key presses. See FLUID-760.
            return evt.keyCode ? evt.keyCode : (evt.which ? evt.which : 0);          
        }
        var escHandler = function (evt) {
            var code = keyCode(evt);
            if (code === $.ui.keyCode.ESCAPE) {
                that.textEditButton.focus(0);
                cancel(that);
                return false;
            }
        };
        var finishHandler = function (evt) {
            var code = keyCode(evt);
            
            if (code !== $.ui.keyCode.ENTER) {
                that.textEditButton.blur();
                return true;
            }
            else {
                finish(that);
                that.textEditButton.focus(0);
            }
            
            return false;
        };
        if (that.options.submitOnEnter) {
            that.editContainer.keypress(finishHandler);
        }
        that.editContainer.keydown(escHandler);
    };

    var bindBlurHandler = function (that) {
        if (that.options.blurHandlerBinder) {
            that.options.blurHandlerBinder(that);
        }
        else {
            var blurHandler = function (evt) {
                if (that.isEditing()) {
                    finish(that);
                }
                return false;
            };
            that.editField.blur(blurHandler);
        }
    };

    var initializeEditView = function (that, initial) {
        if (!that.editInitialized) { 
            fluid.inlineEdit.renderEditContainer(that, !that.options.lazyEditView || !initial);
            
            if (!that.options.lazyEditView || !initial) {
                that.editView = fluid.initSubcomponent(that, "editView", that.editField);
                
                $.extend(true, that.editView, fluid.initSubcomponent(that, "editAccessor", that.editField));
        
                bindEditFinish(that);
                bindBlurHandler(that);
                that.editView.refreshView(that);
                that.editInitialized = true;
            }
        }
    };
    
    var edit = function (that) {
        initializeEditView(that, false);
      
        var viewEl = that.viewEl;
        var displayText = that.displayView.value();
        that.updateModelValue(that.model.value === "" ? "" : displayText);
        if (that.options.applyEditPadding) {
            that.editField.width(Math.max(viewEl.width() + that.options.paddings.edit, that.options.paddings.minimumEdit));
        }

        that.displayModeRenderer.hide();
        that.editContainer.show();                  

        // Work around for FLUID-726
        // Without 'setTimeout' the finish handler gets called with the event and the edit field is inactivated.       
        setTimeout(function () {
            that.editField.focus();
            fluid.setCaretToEnd(that.editField[0], that.editView.value());
            if (that.options.selectOnEdit) {
                that.editField[0].select();
            }
        }, 0);
        that.events.afterBeginEdit.fire();
    };

    var clearEmptyViewStyles = function (textEl, defaultViewStyle, originalViewPadding) {
        textEl.removeClass(defaultViewStyle);
        textEl.css('padding-right', originalViewPadding);
    };
    
    var showDefaultViewText = function (that) {
        that.displayView.value(that.options.defaultViewText);
        that.viewEl.css('padding-right', that.existingPadding);
        that.viewEl.addClass(that.options.styles.defaultViewStyle);
    };

    var showNothing = function (that) {
        that.displayView.value("");
        
        // workaround for FLUID-938:
        // IE can not style an empty inline element, so force element to be display: inline-block
        if ($.browser.msie) {
            if (that.viewEl.css('display') === 'inline') {
                that.viewEl.css('display', "inline-block");
            }
        }
    };

    var showEditedText = function (that) {
        that.displayView.value(that.model.value);
        clearEmptyViewStyles(that.viewEl, that.options.styles.defaultViewStyle, that.existingPadding);
    };
    
    var refreshView = function (that, source) {
        that.displayView.refreshView(that, source);
        if (that.editView) {
            that.editView.refreshView(that, source);
        }
    };
    
    var initModel = function (that, value) {
        that.model.value = value;
        that.refreshView();
    };
    
    var updateModelValue = function (that, newValue, source) {
        var comparator = that.options.modelComparator;
        var unchanged = comparator ? comparator(that.model.value, newValue) : 
            that.model.value === newValue;
        if (!unchanged) {
            var oldModel = $.extend(true, {}, that.model);
            that.model.value = newValue;
            that.events.modelChanged.fire(that.model, oldModel, source);
            that.refreshView(source);
        }
    };
        
    var makeIsEditing = function (that) {
        var isEditing = false;

        that.events.onBeginEdit.addListener(function () {
            isEditing = true;
        });
        that.events.afterFinishEdit.addListener(function () {
            isEditing = false; 
        });
        return function () {
            return isEditing;
        };
    };
    
    var makeEditHandler = function (that) {
        return function () {
            var prevent = that.events.onBeginEdit.fire();
            if (prevent === false) {
                return false;
            }
            edit(that);
            
            return true;
        }; 
    };    
    
    var setTooltipTitle = function (element, title) {
        fluid.wrap(element).attr("title", title);
    };
    
    // Initialize the tooltip once the document is ready.
    // For more details, see http://issues.fluidproject.org/browse/FLUID-1030
    var initTooltips = function (that) {
        var tooltipOptions = {
            delay: that.options.tooltipDelay,
            extraClass: that.options.styles.tooltip,
            bodyHandler: function () { 
                return that.options.tooltipText;
            },
            id: that.options.tooltipId,
            showURL: false                        
        };
        
        that.viewEl.tooltip(tooltipOptions);
        
        if (that.textEditButton) {
            that.textEditButton.tooltip(tooltipOptions);
        }
    };
    
    var calculateInitialPadding = function (viewEl) {
        var padding = viewEl.css("padding-right");
        return padding ? parseFloat(padding) : 0;
    };
    
    var setupInlineEdit = function (componentContainer, that) {
        // Hide the edit container to start
        if (that.editContainer) {
            that.editContainer.hide();
        }
        
        // Add tooltip handler if required and available
        if (that.tooltipEnabled()) {
            initTooltips(that);
        }
        
        // Setup any registered decorators for the component.
        that.decorators = fluid.initSubcomponents(that, "componentDecorators", 
            [that, fluid.COMPONENT_OPTIONS]);
    };
    
    /**
     * Creates a whole list of inline editors.
     */
    var setupInlineEdits = function (editables, options) {
        var editors = [];
        editables.each(function (idx, editable) {
            editors.push(fluid.inlineEdit($(editable), options));
        });
        
        return editors;
    };
    
    /**
     * Instantiates a new Inline Edit component
     * 
     * @param {Object} componentContainer a selector, jquery, or a dom element representing the component's container
     * @param {Object} options a collection of options settings
     */
    fluid.inlineEdit = function (componentContainer, userOptions) {   
        var that = fluid.initView("inlineEdit", componentContainer, userOptions);
        
        that.viewEl = fluid.inlineEdit.setupDisplayView(that);
        
        that.displayView = fluid.initSubcomponent(that, "displayView", that.viewEl);
        $.extend(true, that.displayView, fluid.initSubcomponent(that, "displayAccessor", that.viewEl));

        /**
         * The current value of the inline editable text. The "model" in MVC terms.
         */
        that.model = {value: ""};
       
        /**
         * Switches to edit mode.
         */
        that.edit = makeEditHandler(that);
        
        /**
         * Determines if the component is currently in edit mode.
         * 
         * @return true if edit mode shown, false if view mode is shown
         */
        that.isEditing = makeIsEditing(that);
        
        /**
         * Finishes editing, switching back to view mode.
         */
        that.finish = function () {
            finish(that);
        };

        /**
         * Cancels the in-progress edit and switches back to view mode.
         */
        that.cancel = function () {
            cancel(that);
        };

        /**
         * Determines if the tooltip feature is enabled.
         * 
         * @return true if the tooltip feature is turned on, false if not
         */
        that.tooltipEnabled = function () {
            return that.options.useTooltip && $.fn.tooltip;
        };
        
        /**
         * Updates the state of the inline editor in the DOM, based on changes that may have
         * happened to the model.
         * 
         * @param {Object} source
         */
        that.refreshView = function (source) {
            refreshView(that, source);
        };
        
        /**
         * Pushes external changes to the model into the inline editor, refreshing its
         * rendering in the DOM. The modelChanged event will fire.
         * 
         * @param {String} newValue The bare value of the model, that is, the string being edited
         * @param {Object} source An optional "source" (perhaps a DOM element) which triggered this event
         */
        that.updateModelValue = function (newValue, source) {
            updateModelValue(that, newValue, source);
        };
        
        /**
         * Pushes external changes to the model into the inline editor, refreshing its
         * rendering in the DOM. The modelChanged event will fire.
         * 
         * @param {Object} newValue The full value of the new model, that is, a model object which contains the editable value as the element named "value"
         * @param {Object} source An optional "source" (perhaps a DOM element) which triggered this event
         */
        that.updateModel = function (newModel, source) {
            updateModelValue(that, newModel.value, source);
        };
        
        that.existingPadding = calculateInitialPadding(that.viewEl);
        
        initModel(that, that.displayView.value());
        
        that.displayModeRenderer = that.options.displayModeRenderer(that);  
        initializeEditView(that, true);
        setupInlineEdit(componentContainer, that);
        
        return that;
    };
    
    /**
     * Set up and style the edit field.  If an edit field is not provided,
     * default markup is created for the edit field 
     * 
     * @param {string} editStyle The default styling for the edit field
     * @param {Object} editField The edit field markup provided by the integrator
     * 
     * @return eField The styled edit field   
     */
    fluid.inlineEdit.setupEditField = function (editStyle, editField) {
        var eField = $(editField);
        eField = eField.length ? eField : $("<input type='text' class='flc-inlineEdit-edit'/>");
        eField.addClass(editStyle);
        return eField;
    };

    /**
     * Set up the edit container and append the edit field to the container.  If an edit container
     * is not provided, default markup is created.
     * 
     * @param {Object} displayContainer The display mode container 
     * @param {Object} editField The edit field that is to be appended to the edit container 
     * @param {Object} editContainer The edit container markup provided by the integrator   
     * 
     * @return eContainer The edit container containing the edit field   
     */
    fluid.inlineEdit.setupEditContainer = function (displayContainer, editField, editContainer) {
        var eContainer = $(editContainer);
        eContainer = eContainer.length ? eContainer : $("<span></span>");
        displayContainer.after(eContainer);
        eContainer.append(editField);
        
        return eContainer;
    };
    
    /**
     * Default renderer for the edit mode view.
     * 
     * @return {Object} container The edit container containing the edit field
     *                  field The styled edit field  
     */
    fluid.inlineEdit.defaultEditModeRenderer = function (that) {
        var editField = fluid.inlineEdit.setupEditField(that.options.styles.edit, that.editField);
        var editContainer = fluid.inlineEdit.setupEditContainer(that.displayModeRenderer, editField, that.editContainer);
        var editModeInstruction = fluid.inlineEdit.setupEditModeInstruction(that.options.styles.editModeInstruction, that.options.strings.editModeInstruction);
        
        var id = fluid.allocateSimpleId(editModeInstruction);
        editField.attr("aria-describedby", id);

        fluid.inlineEdit.positionEditModeInstruction(editModeInstruction, editContainer, editField);
              
        // Package up the container and field for the component.
        return {
            container: editContainer,
            field: editField 
        };
    };
    
    /**
     * Configures the edit container and view, and uses the component's editModeRenderer to render
     * the edit container.
     *  
     * @param {boolean} lazyEditView If true, will delay rendering of the edit container;
     *                                            Default is false 
     */
    fluid.inlineEdit.renderEditContainer = function (that, lazyEditView) {
        that.editContainer = that.locate("editContainer");
        that.editField = that.locate("edit");
        if (that.editContainer.length !== 1) {
            if (that.editContainer.length > 1) {
                fluid.fail("InlineEdit did not find a unique container for selector " + that.options.selectors.editContainer +
                   ": " + fluid.dumpEl(that.editContainer));
            }
        }
        
        if (!lazyEditView) {
            return; 
        } // do not invoke the renderer, unless this is the "final" effective time
        
        var editElms = that.options.editModeRenderer(that);
        if (editElms) {
            that.editContainer = editElms.container;
            that.editField = editElms.field;
        }
    };

    /**
     * Set up the edit mode instruction with aria in edit mode
     * 
     * @param {String} editModeInstructionStyle The default styling for the instruction
     * @param {String} editModeInstructionText The default instruction text
     * 
     * @return {jQuery} The displayed instruction in edit mode
     */
    fluid.inlineEdit.setupEditModeInstruction = function (editModeInstructionStyle, editModeInstructionText) {
        var editModeInstruction = $("<p></p>");
        editModeInstruction.addClass(editModeInstructionStyle);
        editModeInstruction.text(editModeInstructionText);

        return editModeInstruction;
    };

    /**
     * Positions the edit mode instruction directly beneath the edit container
     * 
     * @param {Object} editModeInstruction The displayed instruction in edit mode
     * @param {Object} editContainer The edit container in edit mode
     * @param {Object} editField The edit field in edit mode
     */    
    fluid.inlineEdit.positionEditModeInstruction = function (editModeInstruction, editContainer, editField) {
        editContainer.append(editModeInstruction);
        
        editField.focus(function () {
            editModeInstruction.show();

            var editFieldPosition = editField.offset();
            editModeInstruction.css({left: editFieldPosition.left});
            editModeInstruction.css({top: editFieldPosition.top + editField.height() + 5});
        });
    };  
    
    /**
     * Set up and style the display mode container for the viewEl and the textEditButton 
     * 
     * @param {Object} styles The default styling for the display mode container
     * @param {Object} displayModeWrapper The markup used to generate the display mode container
     * 
     * @return {jQuery} The styled display mode container
     */
    fluid.inlineEdit.setupDisplayModeContainer = function (styles, displayModeWrapper) {
        var displayModeContainer = $(displayModeWrapper);  
        displayModeContainer = displayModeContainer.length ? displayModeContainer : $("<span></span>");  
        displayModeContainer.addClass(styles.displayView);
        
        return displayModeContainer;
    };
    
    /**
     * Retrieve the display text from the DOM.  
     * 
     * @return {jQuery} The display text
     */
    fluid.inlineEdit.setupDisplayView = function (that) {
        var viewEl = that.locate("text");

        /*
         *  Remove the display from the tab order to prevent users to think they
         *  are able to access the inline edit field, but they cannot since the 
         *  keyboard event binding is only on the button.
         */
        viewEl.attr("tabindex", "-1");
        viewEl.addClass(that.options.styles.text);
        setTooltipTitle(viewEl, that.options.tooltipText);
        
        return viewEl;
    };
    
    /**
     * Set up the textEditButton.  Append a background image with appropriate
     * descriptive text to the button.
     * 
     * @return {jQuery} The accessible button located after the display text
     */
    fluid.inlineEdit.setupTextEditButton = function (that) {
        var opts = that.options;
        var textEditButton = that.locate("textEditButton");
        
        if  (textEditButton.length === 0) {
            var markup = $("<a href='#_' class='flc-inlineEdit-textEditButton'></a>");
            markup.addClass(opts.styles.textEditButton);
            markup.text(opts.tooltipText);            
            setTooltipTitle(markup, that.options.tooltipText);            
            
            /**
             * Set text for the button and listen
             * for modelChanged to keep it updated
             */ 
            fluid.inlineEdit.updateTextEditButton(markup, that.model.value || opts.defaultViewText, opts.strings.textEditButton);
            that.events.modelChanged.addListener(function () {
                fluid.inlineEdit.updateTextEditButton(markup, that.model.value || opts.defaultViewText, opts.strings.textEditButton);
            });        
            
            that.locate("text").after(markup);
            
            // Refresh the textEditButton with the newly appended options
            textEditButton = that.locate("textEditButton");
        } 
        return textEditButton;
    };    

    /**
     * Update the textEditButton text with the current value of the field.
     * 
     * @param {Object} textEditButton the textEditButton
     * @param {String} model The current value of the inline editable text
     * @param {Object} strings Text option for the textEditButton
     */
    fluid.inlineEdit.updateTextEditButton = function (textEditButton, value, stringTemplate) {
        var buttonText = fluid.stringTemplate(stringTemplate, {
            text: value
        });
        textEditButton.text(buttonText);
    };
    
    /**
     * Bind mouse hover event handler to the display mode container.  
     * 
     * @param {Object} displayModeRenderer The display mode container
     * @param {String} invitationStyle The default styling for the display mode container on mouse hover
     */
    fluid.inlineEdit.bindHoverHandlers = function (displayModeRenderer, invitationStyle) {
        var over = function (evt) {
            displayModeRenderer.addClass(invitationStyle);
        };     
        var out = function (evt) {
            displayModeRenderer.removeClass(invitationStyle);
        };
        displayModeRenderer.hover(over, out);
    };    
    
    /**
     * Bind keyboard focus and blur event handlers to an element
     * 
     * @param {Object} element The element to which the event handlers are bound
     * @param {Object} displayModeRenderer The display mode container
     * @param {Ojbect} styles The default styling for the display mode container on mouse hover
     */    
    fluid.inlineEdit.bindHighlightHandler = function (element, displayModeRenderer, styles) {
        element = $(element);
        
        var focusOn = function () {
            displayModeRenderer.addClass(styles.focus);
            displayModeRenderer.addClass(styles.invitation);
        };
        var focusOff = function () {
            displayModeRenderer.removeClass(styles.focus);
            displayModeRenderer.removeClass(styles.invitation);
        };
        
        element.focus(focusOn);
        element.blur(focusOff);
    };        
    
    /**
     * Bind mouse click handler to an element
     * 
     * @param {Object} element The element to which the event handler is bound
     * @param {Object} edit Function to invoke the edit mode
     * 
     * @return {boolean} Returns false if entering edit mode
     */
    fluid.inlineEdit.bindMouseHandlers = function (element, edit) {
        element = $(element);
        
        var triggerGuard = fluid.inlineEdit.makeEditTriggerGuard(element, edit);
        element.click(function (e) {
            triggerGuard(e);
            return false;
        });
    };

    /**
     * Bind keyboard press handler to an element
     * 
     * @param {Object} element The element to which the event handler is bound
     * @param {Object} edit Function to invoke the edit mode
     * 
     * @return {boolean} Returns false if entering edit mode
     */    
    fluid.inlineEdit.bindKeyboardHandlers = function (element, edit) {
        element = $(element);
        element.attr("role", "button");
        
        var guard = fluid.inlineEdit.makeEditTriggerGuard(element, edit);
        fluid.activatable(element, function (event) {
            return guard(event);
        });
    };
    
    /**
     * Creates an event handler that will trigger the edit mode if caused by something other
     * than standard HTML controls. The event handler will return false if entering edit mode.
     * 
     * @param {Object} element The element to trigger the edit mode
     * @param {Object} edit Function to invoke the edit mode
     * 
     * @return {function} The event handler function
     */    
    fluid.inlineEdit.makeEditTriggerGuard = function (element, edit) {
        var selector = fluid.unwrap(element);
        return function (event) {
            // FLUID-2017 - avoid triggering edit mode when operating standard HTML controls. Ultimately this
            // might need to be extensible, in more complex authouring scenarios.
            var outer = fluid.findAncestor(event.target, function (elem) {
                if (/input|select|textarea|button|a/i.test(elem.nodeName) || elem === selector) {
                    return true; 
                }
            });
            if (outer === selector) {
                edit();
                return false;
            }
        };
    };
    
    /**
     * Render the display mode view.  
     * 
     * @return {jQuery} The display container containing the display text and 
     *                             textEditbutton for display mode view
     */
    fluid.inlineEdit.defaultDisplayModeRenderer = function (that) {
        var styles = that.options.styles;
        
        var displayModeWrapper = fluid.inlineEdit.setupDisplayModeContainer(styles);
        var displayModeRenderer = that.viewEl.wrap(displayModeWrapper).parent();
        
        that.textEditButton = fluid.inlineEdit.setupTextEditButton(that);
        displayModeRenderer.append(that.textEditButton);
        
        // Add event handlers.
        fluid.inlineEdit.bindHoverHandlers(displayModeRenderer, styles.invitation);
        fluid.inlineEdit.bindMouseHandlers(that.viewEl, that.edit);
        fluid.inlineEdit.bindMouseHandlers(that.textEditButton, that.edit);
        fluid.inlineEdit.bindKeyboardHandlers(that.textEditButton, that.edit);
        fluid.inlineEdit.bindHighlightHandler(that.viewEl, displayModeRenderer, styles);
        fluid.inlineEdit.bindHighlightHandler(that.textEditButton, displayModeRenderer, styles);
        
        return displayModeRenderer;
    };    
    
    fluid.inlineEdit.standardAccessor = function (element) {
        var nodeName = element.nodeName.toLowerCase();
        var func = "input" === nodeName || "textarea" === nodeName ? "val" : "text";
        return {
            value: function (newValue) {
                return $(element)[func](newValue);
            }
        };
    };
    
    fluid.inlineEdit.richTextViewAccessor = function (element) {
        return {
            value: function (newValue) {
                return $(element).html(newValue);
            }
        };
    };
    
    fluid.inlineEdit.standardDisplayView = function (viewEl) {
        var that = {
            refreshView: function (componentThat, source) {
                if (componentThat.model.value) {
                    showEditedText(componentThat);
                } else if (componentThat.options.defaultViewText) {
                    showDefaultViewText(componentThat);
                } else {
                    showNothing(componentThat);
                }
                // If necessary, pad the view element enough that it will be evident to the user.
                if (($.trim(componentThat.viewEl.text()).length === 0) &&
                    (componentThat.existingPadding < componentThat.options.paddings.minimumView)) {
                    componentThat.viewEl.css('padding-right', componentThat.options.paddings.minimumView);
                }
            }
        };
        return that;
    };
    
    fluid.inlineEdit.standardEditView = function (editField) {
        var that = {
            refreshView: function (componentThat, source) {
                if (!source || componentThat.editField && componentThat.editField.index(source) === -1) {
                    componentThat.editView.value(componentThat.model.value);
                }
            }
        };
        $.extend(true, that, fluid.inlineEdit.standardAccessor(editField));
        return that;
    };
    
    /**
     * Instantiates a list of InlineEdit components.
     * 
     * @param {Object} componentContainer the element containing the inline editors
     * @param {Object} options configuration options for the components
     */
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
            text: ".flc-inlineEdit-text",
            editContainer: ".flc-inlineEdit-editContainer",
            edit: ".flc-inlineEdit-edit",
            textEditButton: ".flc-inlineEdit-textEditButton"
        },
        
        styles: {
            text: "fl-inlineEdit-text",
            edit: "fl-inlineEdit-edit",
            invitation: "fl-inlineEdit-invitation",
            defaultViewStyle: "fl-inlineEdit-invitation-text",
            focus: "fl-inlineEdit-focus",
            tooltip: "fl-inlineEdit-tooltip",
            editModeInstruction: "fl-inlineEdit-editModeInstruction",
            displayView: "fl-inlineEdit-underline fl-inlineEdit-inlineBlock",
            textEditButton: "fl-offScreen-hidden"
        },
        
        events: {
            modelChanged: null,
            onBeginEdit: "preventable",
            afterBeginEdit: null,
            onFinishEdit: "preventable",
            afterFinishEdit: null,
            afterInitEdit: null
        },

        strings: {
            textEditButton: "Edit text %text",
            editModeInstruction: "Escape to cancel, Enter or Tab when finished"
        },
        
        paddings: {
            edit: 10,
            minimumEdit: 80,
            minimumView: 60
        },
        
        applyEditPadding: true,
        
        blurHandlerBinder: null,
        
        // set this to true or false to cause unconditional submission, otherwise it will
        // be inferred from the edit element tag type.
        submitOnEnter: undefined,
        
        modelComparator: null,
        
        displayAccessor: {
            type: "fluid.inlineEdit.standardAccessor"
        },
        
        displayView: {
            type: "fluid.inlineEdit.standardDisplayView"
        },
        
        editAccessor: {
            type: "fluid.inlineEdit.standardAccessor"
        },
        
        editView: {
            type: "fluid.inlineEdit.standardEditView"
        },
        
        displayModeRenderer: fluid.inlineEdit.defaultDisplayModeRenderer,
            
        editModeRenderer: fluid.inlineEdit.defaultEditModeRenderer,
        
        lazyEditView: false,
        
        // this is here for backwards API compatibility, but should be in the strings block
        defaultViewText: "Click here to edit",

        /** View Mode Tooltip Settings **/
        useTooltip: true,
        
        // this is here for backwards API compatibility, but should be in the strings block
        tooltipText: "Select or press Enter to edit",
        
        tooltipId: "tooltip",
        
        tooltipDelay: 1000,

        selectOnEdit: false        
    });
    
    fluid.defaults("inlineEdits", {
        selectors: {
            editables: ".flc-inlineEditable"
        }
    });
})(jQuery, fluid_1_2);
