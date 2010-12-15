/*
Copyright 2008-2009 University of Cambridge
Copyright 2008-2010 University of Toronto
Copyright 2010 OCAD University

Licensed under the Educational Community License (ECL), Version 2.0 or the New
BSD license. You may not use this file except in compliance with one these
Licenses.

You may obtain a copy of the ECL 2.0 License and BSD License at
https://source.fluidproject.org/svn/LICENSE.txt
*/

/*global setTimeout*/
/*global jQuery, fluid_1_3:true, fluid*/
/*global tinyMCE, FCKeditor, FCKeditorAPI, CKEDITOR*/

var fluid_1_3 = fluid_1_3 || {};

(function ($, fluid) {

    /*************************************
     * Shared Rich Text Editor functions *
     *************************************/
     
    fluid.inlineEdit.makeViewAccessor = function (editorGetFn, setValueFn, getValueFn) {
        return function (editField) {
            return {
                value: function (newValue) {
                    var editor = editorGetFn(editField);
                    if (!editor) {
                        if (newValue) {
                            $(editField).val(newValue);
                        }
                        return "";
                    }
                    if (newValue) {
                        setValueFn(editField, editor, newValue);
                    }
                    else {
                        return getValueFn(editor);
                    }
                }
            };
        };
    };
    
    fluid.inlineEdit.richTextViewAccessor = function (element) {
        return {
            value: function (newValue) {
                return $(element).html(newValue);
            }
        };
    };        
    
    var configureInlineEdit = function (configurationName, container, options) {
        var defaults = fluid.defaults(configurationName); 
        var assembleOptions = fluid.merge(defaults ? defaults.mergePolicy: null, {}, defaults, options);
        return fluid.inlineEdit(container, assembleOptions);
    };

    fluid.inlineEdit.normalizeHTML = function (value) {
        var togo = $.trim(value.replace(/\s+/g, " "));
        togo = togo.replace(/\s+<\//g, "</");
        togo = togo.replace(/\<(\S+)[^\>\s]*\>/g, function (match) {
            return match.toLowerCase();
        });
        return togo;
    };
    
    fluid.inlineEdit.htmlComparator = function (el1, el2) {
        return fluid.inlineEdit.normalizeHTML(el1) ===
           fluid.inlineEdit.normalizeHTML(el2);
    };
    
    fluid.inlineEdit.bindRichTextHighlightHandler = function (element, displayModeRenderer, invitationStyle) {
        element = $(element);
        
        var focusOn = function () {
            displayModeRenderer.addClass(invitationStyle);
        };
        var focusOff = function () {
            displayModeRenderer.removeClass(invitationStyle);
        };
        
        element.focus(focusOn);
        element.blur(focusOff);
    };        
    
    fluid.inlineEdit.setupRichTextEditButton = function (that) {
        var opts = that.options;
        var textEditButton = that.locate("textEditButton");
        
        if  (textEditButton.length === 0) {
            var markup = $("<a href='#_' class='flc-inlineEdit-textEditButton'></a>");
            markup.text(opts.strings.textEditButton);
            
            that.locate("text").after(markup);
            
            // Refresh the textEditButton with the newly appended options
            textEditButton = that.locate("textEditButton");
        } 
        return textEditButton;
    };    
    
    /**
     * Wrap the display text and the textEditButton with the display mode container  
     * for better style control.
     */
    fluid.inlineEdit.richTextDisplayModeRenderer = function (that) {
        var styles = that.options.styles;
        
        var displayModeWrapper = fluid.inlineEdit.setupDisplayModeContainer(styles);
        var displayModeRenderer = that.viewEl.wrap(displayModeWrapper).parent();
        
        that.textEditButton = fluid.inlineEdit.setupRichTextEditButton(that);
        displayModeRenderer.append(that.textEditButton);
        displayModeRenderer.addClass(styles.focus);
        
        // Add event handlers.
        fluid.inlineEdit.bindHoverHandlers(displayModeRenderer, styles.invitation);
        fluid.inlineEdit.bindMouseHandlers(that.textEditButton, that.edit);
        fluid.inlineEdit.bindKeyboardHandlers(that.textEditButton, that.edit);
        fluid.inlineEdit.bindRichTextHighlightHandler(that.viewEl, displayModeRenderer, styles.invitation);
        fluid.inlineEdit.bindRichTextHighlightHandler(that.textEditButton, displayModeRenderer, styles.invitation);
        
        return displayModeRenderer;
    };        

   
    /************************
     * Tiny MCE Integration *
     ************************/
    
    /**
     * Instantiate a rich-text InlineEdit component that uses an instance of TinyMCE.
     * 
     * @param {Object} componentContainer the element containing the inline editors
     * @param {Object} options configuration options for the components
     */
    fluid.inlineEdit.tinyMCE = function (container, options) {
        var inlineEditor = configureInlineEdit("fluid.inlineEdit.tinyMCE", container, options);
        tinyMCE.init(inlineEditor.options.tinyMCE);
        return inlineEditor;
    };
        
    fluid.inlineEdit.tinyMCE.getEditor = function (editField) {
        return tinyMCE.get(editField.id);
    };
    
    fluid.inlineEdit.tinyMCE.setValue = function (editField, editor, value) {
        // without this, there is an intermittent race condition if the editor has been created on this event.
        $(editField).val(value); 
        editor.setContent(value, {format : 'raw'});
    };
    
    fluid.inlineEdit.tinyMCE.getValue = function (editor) {
        return editor.getContent();
    };
    
    var flTinyMCE = fluid.inlineEdit.tinyMCE; // Shorter alias for awfully long fully-qualified names.
    flTinyMCE.viewAccessor = fluid.inlineEdit.makeViewAccessor(flTinyMCE.getEditor, 
                                                               flTinyMCE.setValue,
                                                               flTinyMCE.getValue);
   
    fluid.inlineEdit.tinyMCE.blurHandlerBinder = function (that) {
        function focusEditor(editor) {
            setTimeout(function () {
                tinyMCE.execCommand('mceFocus', false, that.editField[0].id);
                if ($.browser.mozilla && $.browser.version.substring(0, 3) === "1.8") {
                    // Have not yet found any way to make this work on FF2.x - best to do nothing,
                    // for FLUID-2206
                    //var body = editor.getBody();
                    //fluid.setCaretToEnd(body.firstChild, "");
                    return;
                }
                editor.selection.select(editor.getBody(), 1);
                editor.selection.collapse(0);
            }, 10);
        }
        
        that.events.afterInitEdit.addListener(function (editor) {
            focusEditor(editor);
            var editorBody = editor.getBody();

            // NB - this section has no effect - on most browsers no focus events
            // are delivered to the actual body
            fluid.deadMansBlur(that.editField, 
                {exclusions: {body: $(editorBody)}, 
                    handler: function () {
                        that.cancel();
                    }
                });
        });
            
        that.events.afterBeginEdit.addListener(function () {
            var editor = tinyMCE.get(that.editField[0].id);
            if (editor) {
                focusEditor(editor);
            } 
        });
    };
   
    fluid.inlineEdit.tinyMCE.editModeRenderer = function (that) {
        var options = that.options.tinyMCE;
        options.elements = fluid.allocateSimpleId(that.editField);
        var oldinit = options.init_instance_callback;
        
        options.init_instance_callback = function (instance) {
            that.events.afterInitEdit.fire(instance);
            if (oldinit) {
                oldinit();
            }
        };
        
        tinyMCE.init(options);
    };
    
    fluid.defaults("fluid.inlineEdit.tinyMCE", {
        tinyMCE : {
            mode: "exact", 
            theme: "simple"
        },
        useTooltip: true,
        selectors: {
            edit: "textarea" 
        },
        styles: {
            invitation: "fl-inlineEdit-richText-invitation",
            displayView: "fl-inlineEdit-textContainer",
            text: ""
                
        },
        strings: {
            textEditButton: "Edit"
        },
        displayAccessor: {
            type: "fluid.inlineEdit.richTextViewAccessor"
        },
        editAccessor: {
            type: "fluid.inlineEdit.tinyMCE.viewAccessor"
        },
        lazyEditView: true,
        defaultViewText: "Click Edit",
        modelComparator: fluid.inlineEdit.htmlComparator,
        blurHandlerBinder: fluid.inlineEdit.tinyMCE.blurHandlerBinder,
        displayModeRenderer: fluid.inlineEdit.richTextDisplayModeRenderer,
        editModeRenderer: fluid.inlineEdit.tinyMCE.editModeRenderer
    });
    
    
    /*****************************
     * FCKEditor 2.x Integration *
     *****************************/
         
    /**
     * Instantiate a rich-text InlineEdit component that uses an instance of FCKeditor.
     * Support for FCKEditor 2.x is now deprecated. We recommend the use of the simpler and more
     * accessible CKEditor 3 instead.
     * 
     * @param {Object} componentContainer the element containing the inline editors
     * @param {Object} options configuration options for the components
     */
    fluid.inlineEdit.FCKEditor = function (container, options) {
        return configureInlineEdit("fluid.inlineEdit.FCKEditor", container, options);
    };
    
    fluid.inlineEdit.FCKEditor.getEditor = function (editField) {
        var editor = typeof(FCKeditorAPI) === "undefined" ? null: FCKeditorAPI.GetInstance(editField.id);
        return editor;
    };
    
    fluid.inlineEdit.FCKEditor.complete = fluid.event.getEventFirer();
    
    fluid.inlineEdit.FCKEditor.complete.addListener(function (editor) {
        var editField = editor.LinkedField;
        var that = $.data(editField, "fluid.inlineEdit.FCKEditor");
        if (that && that.events) {
            that.events.afterInitEdit.fire(editor);
        }
    });
    
    fluid.inlineEdit.FCKEditor.blurHandlerBinder = function (that) {
        function focusEditor(editor) {
            editor.Focus(); 
        }
        
        that.events.afterInitEdit.addListener(
            function (editor) {
                focusEditor(editor);
            }
        );
        that.events.afterBeginEdit.addListener(function () {
            var editor = fluid.inlineEdit.FCKEditor.getEditor(that.editField[0]);
            if (editor) {
                focusEditor(editor);
            } 
        });

    };
    
    fluid.inlineEdit.FCKEditor.editModeRenderer = function (that) {
        var id = fluid.allocateSimpleId(that.editField);
        $.data(fluid.unwrap(that.editField), "fluid.inlineEdit.FCKEditor", that);
        var oFCKeditor = new FCKeditor(id);
        // The Config object and the FCKEditor object itself expose different configuration sets,
        // which possess a member "BasePath" with different meanings. Solve FLUID-2452, FLUID-2438
        // by auto-inferring the inner path for Config (method from http://drupal.org/node/344230 )
        var opcopy = fluid.copy(that.options.FCKEditor);
        opcopy.BasePath = opcopy.BasePath + "editor/";
        $.extend(true, oFCKeditor.Config, opcopy);
        // somehow, some properties like Width and Height are set on the object itself

        $.extend(true, oFCKeditor, that.options.FCKEditor);
        oFCKeditor.Config.fluidInstance = that;
        oFCKeditor.ReplaceTextarea();
    };

    fluid.inlineEdit.FCKEditor.setValue = function (editField, editor, value) {
        editor.SetHTML(value);
    };
    
    fluid.inlineEdit.FCKEditor.getValue = function (editor) {
        return editor.GetHTML();
    };
    
    var flFCKEditor = fluid.inlineEdit.FCKEditor;
    
    flFCKEditor.viewAccessor = fluid.inlineEdit.makeViewAccessor(flFCKEditor.getEditor,
                                                                 flFCKEditor.setValue,
                                                                 flFCKEditor.getValue);
    
    fluid.defaults("fluid.inlineEdit.FCKEditor", {
        selectors: {
            edit: "textarea" 
        },
        styles: {
            invitation: "fl-inlineEdit-richText-invitation",
            displayView: "fl-inlineEdit-textContainer",
            text: ""
        },
        strings: {
            textEditButton: "Edit"
        },        
        displayAccessor: {
            type: "fluid.inlineEdit.richTextViewAccessor"
        },
        editAccessor: {
            type: "fluid.inlineEdit.FCKEditor.viewAccessor"
        },
        lazyEditView: true,
        defaultViewText: "Click Edit",
        modelComparator: fluid.inlineEdit.htmlComparator,
        blurHandlerBinder: fluid.inlineEdit.FCKEditor.blurHandlerBinder,
        displayModeRenderer: fluid.inlineEdit.richTextDisplayModeRenderer,
        editModeRenderer: fluid.inlineEdit.FCKEditor.editModeRenderer,
        FCKEditor: {
            BasePath: "fckeditor/"    
        }
    });
    
    
    /****************************
     * CKEditor 3.x Integration *
     ****************************/
    
    fluid.inlineEdit.CKEditor = function (container, options) {
        return configureInlineEdit("fluid.inlineEdit.CKEditor", container, options);
    };
    
    fluid.inlineEdit.CKEditor.getEditor = function (editField) {
        return CKEDITOR.instances[editField.id];
    };
    
    fluid.inlineEdit.CKEditor.setValue = function (editField, editor, value) {
        editor.setData(value);
    };
    
    fluid.inlineEdit.CKEditor.getValue = function (editor) {
        return editor.getData();
    };
    
    var flCKEditor = fluid.inlineEdit.CKEditor;
    flCKEditor.viewAccessor = fluid.inlineEdit.makeViewAccessor(flCKEditor.getEditor,
                                                                flCKEditor.setValue,
                                                                flCKEditor.getValue);
                             
    fluid.inlineEdit.CKEditor.focus = function (editor) {
        setTimeout(function () {
            // CKEditor won't focus itself except in a timeout.
            editor.focus();
        }, 0);
    };
    
    // Special hacked HTML normalisation for CKEditor which spuriously inserts whitespace
    // just after the first opening tag
    fluid.inlineEdit.CKEditor.normalizeHTML = function (value) {
        var togo = fluid.inlineEdit.normalizeHTML(value);
        var angpos = togo.indexOf(">");
        if (angpos !== -1 && angpos < togo.length - 1) {
            if (togo.charAt(angpos + 1) !== " ") {
                togo = togo.substring(0, angpos + 1) + " " + togo.substring(angpos + 1);
            }
        }
        return togo;
    };
    
    fluid.inlineEdit.CKEditor.htmlComparator = function (el1, el2) {
        return fluid.inlineEdit.CKEditor.normalizeHTML(el1) ===
           fluid.inlineEdit.CKEditor.normalizeHTML(el2);
    };
                                    
    fluid.inlineEdit.CKEditor.blurHandlerBinder = function (that) {
        that.events.afterInitEdit.addListener(fluid.inlineEdit.CKEditor.focus);
        that.events.afterBeginEdit.addListener(function () {
            var editor = fluid.inlineEdit.CKEditor.getEditor(that.editField[0]);
            if (editor) {
                fluid.inlineEdit.CKEditor.focus(editor);
            }
        });
    };
    
    fluid.inlineEdit.CKEditor.editModeRenderer = function (that) {
        var id = fluid.allocateSimpleId(that.editField);
        $.data(fluid.unwrap(that.editField), "fluid.inlineEdit.CKEditor", that);
        var editor = CKEDITOR.replace(id, that.options.CKEditor);
        editor.on("instanceReady", function (e) {
            fluid.inlineEdit.CKEditor.focus(e.editor);
            that.events.afterInitEdit.fire(e.editor);
        });
    };                                                     
    
    fluid.defaults("fluid.inlineEdit.CKEditor", {
        selectors: {
            edit: "textarea" 
        },
        styles: {
            invitation: "fl-inlineEdit-richText-invitation",
            displayView: "fl-inlineEdit-textContainer",
            text: ""
        },
        strings: {
            textEditButton: "Edit"
        },        
        displayAccessor: {
            type: "fluid.inlineEdit.richTextViewAccessor"
        },
        editAccessor: {
            type: "fluid.inlineEdit.CKEditor.viewAccessor"
        },
        lazyEditView: true,
        defaultViewText: "Click Edit",
        modelComparator: fluid.inlineEdit.CKEditor.htmlComparator,
        blurHandlerBinder: fluid.inlineEdit.CKEditor.blurHandlerBinder,
        displayModeRenderer: fluid.inlineEdit.richTextDisplayModeRenderer,
        editModeRenderer: fluid.inlineEdit.CKEditor.editModeRenderer,
        CKEditor: {
            // CKEditor-specific configuration goes here.
        }
    });
 
    
    /************************
     * Dropdown Integration *
     ************************/    
    /**
     * Instantiate a drop-down InlineEdit component
     * 
     * @param {Object} container
     * @param {Object} options
     */
    fluid.inlineEdit.dropdown = function (container, options) {
        return configureInlineEdit("fluid.inlineEdit.dropdown", container, options);
    };

    fluid.inlineEdit.dropdown.editModeRenderer = function (that) {
        var id = fluid.allocateSimpleId(that.editField);
        that.editField.selectbox({
            finishHandler: function () {
                that.finish();
            }
        });
        return {
            container: that.editContainer,
            field: $("input.selectbox", that.editContainer) 
        };
    };
   
    fluid.inlineEdit.dropdown.blurHandlerBinder = function (that) {
        fluid.deadMansBlur(that.editField, {
            exclusions: {selectBox: $("div.selectbox-wrapper", that.editContainer)},
            handler: function () {
                that.cancel();
            }
        });
    };
    
    fluid.defaults("fluid.inlineEdit.dropdown", {
        applyEditPadding: false,
        blurHandlerBinder: fluid.inlineEdit.dropdown.blurHandlerBinder,
        editModeRenderer: fluid.inlineEdit.dropdown.editModeRenderer
    });
})(jQuery, fluid_1_3);


// This must be written outside any scope as a result of the FCKEditor event model.
// Do not overwrite this function, if you wish to add your own listener to FCK completion,
// register it with the standard fluid event firer at fluid.inlineEdit.FCKEditor.complete
function FCKeditor_OnComplete(editorInstance) {
    fluid.inlineEdit.FCKEditor.complete.fire(editorInstance);
}
