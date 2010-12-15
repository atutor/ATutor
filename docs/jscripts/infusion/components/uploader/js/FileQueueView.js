/*
Copyright 2008-2009 University of Toronto
Copyright 2008-2009 University of California, Berkeley
Copyright 2008-2009 University of Cambridge
Copyright 2010 OCAD University

Licensed under the Educational Community License (ECL), Version 2.0 or the New
BSD license. You may not use this file except in compliance with one these
Licenses.

You may obtain a copy of the ECL 2.0 License and BSD License at
https://source.fluidproject.org/svn/LICENSE.txt
*/

/*global jQuery, fluid_1_3:true*/

var fluid_1_3 = fluid_1_3 || {};

/*******************
 * File Queue View *
 *******************/

(function ($, fluid) {
    
    // Real data binding would be nice to replace these two pairs.
    var rowForFile = function (that, file) {
        return that.locate("fileQueue").find("#" + file.id);
    };
    
    var errorRowForFile = function (that, file) {
        return $("#" + file.id + "_error", that.container);
    };
    
    var fileForRow = function (that, row) {
        var files = that.model;
        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            if (file.id.toString() === row.attr("id")) {
                return file;
            }
        }
        return null;
    };
    
    var progressorForFile = function (that, file) {
        var progressId = file.id + "_progress";
        return that.fileProgressors[progressId];
    };
    
    var startFileProgress = function (that, file) {
        var fileRowElm = rowForFile(that, file);
        that.scroller.scrollTo(fileRowElm);
         
        // update the progressor and make sure that it's in position
        var fileProgressor = progressorForFile(that, file);
        fileProgressor.refreshView();
        fileProgressor.show();
    };
        
    var updateFileProgress = function (that, file, fileBytesComplete, fileTotalBytes) {
        var filePercent = fluid.uploader.derivePercent(fileBytesComplete, fileTotalBytes);
        var filePercentStr = filePercent + "%";    
        progressorForFile(that, file).update(filePercent, filePercentStr);
    };
    
    var hideFileProgress = function (that, file) {
        var fileRowElm = rowForFile(that, file);
        progressorForFile(that, file).hide();
        if (file.filestatus === fluid.uploader.fileStatusConstants.COMPLETE) {
            that.locate("fileIconBtn", fileRowElm).removeClass(that.options.styles.dim);
        } 
    };
    
    var removeFileProgress = function (that, file) {
        var fileProgressor = progressorForFile(that, file);
        if (!fileProgressor) {
            return;
        }
        var rowProgressor = fileProgressor.displayElement;
        rowProgressor.remove();
    };
 
    var animateRowRemoval = function (that, row) {
        row.fadeOut("fast", function () {
            row.remove();  
            that.refreshView();
        });
    };
    
    var removeFileErrorRow = function (that, file) {
        if (file.filestatus === fluid.uploader.fileStatusConstants.ERROR) {
            animateRowRemoval(that, errorRowForFile(that, file));
        }
    };
   
    var removeFileAndRow = function (that, file, row) {
        // Clean up the stuff associated with a file row.
        removeFileProgress(that, file);
        removeFileErrorRow(that, file);
        
        // Remove the file itself.
        that.events.onFileRemoved.fire(file);
        animateRowRemoval(that, row);
    };
    
    var removeFileForRow = function (that, row) {
        var file = fileForRow(that, row);
        if (!file || file.filestatus === fluid.uploader.fileStatusConstants.COMPLETE) {
            return;
        }
        removeFileAndRow(that, file, row);
    };
    
    var removeRowForFile = function (that, file) {
        var row = rowForFile(that, file);
        removeFileAndRow(that, file, row);
    };
    
    var bindHover = function (row, styles) {
        var over = function () {
            if (row.hasClass(styles.ready) && !row.hasClass(styles.uploading)) {
                row.addClass(styles.hover);
            }
        };
        
        var out = function () {
            if (row.hasClass(styles.ready) && !row.hasClass(styles.uploading)) {
                row.removeClass(styles.hover);
            }   
        };
        row.hover(over, out);
    };
    
    var bindDeleteKey = function (that, row) {
        var deleteHandler = function () {
            removeFileForRow(that, row);
        };
       
        fluid.activatable(row, null, {
            additionalBindings: [{
                key: $.ui.keyCode.DELETE, 
                activateHandler: deleteHandler
            }]
        });
    };
    
    var bindRowHandlers = function (that, row) {
        if ($.browser.msie && $.browser.version < 7) {
            bindHover(row, that.options.styles);
        }
        
        that.locate("fileIconBtn", row).click(function () {
            removeFileForRow(that, row);
        });
        
        bindDeleteKey(that, row);
    };
    
    var renderRowFromTemplate = function (that, file) {
        var row = that.rowTemplate.clone(),
            fileName = file.name,
            fileSize = fluid.uploader.formatFileSize(file.size);
        
        row.removeClass(that.options.styles.hiddenTemplate);
        that.locate("fileName", row).text(fileName);
        that.locate("fileSize", row).text(fileSize);
        that.locate("fileIconBtn", row).addClass(that.options.styles.remove);
        row.attr("id", file.id);
        row.addClass(that.options.styles.ready);
        bindRowHandlers(that, row);
        fluid.updateAriaLabel(row, fileName + " " + fileSize);
        return row;    
    };
    
    var createProgressorFromTemplate = function (that, row) {
        // create a new progress bar for the row and position it
        var rowProgressor = that.rowProgressorTemplate.clone();
        var rowId = row.attr("id");
        var progressId = rowId + "_progress";
        rowProgressor.attr("id", progressId);
        rowProgressor.css("top", row.position().top);
        rowProgressor.height(row.height()).width(5);
        that.container.after(rowProgressor);
       
        that.fileProgressors[progressId] = fluid.progress(that.options.uploaderContainer, {
            selectors: {
                progressBar: "#" + rowId,
                displayElement: "#" + progressId,
                label: "#" + progressId + " .fl-uploader-file-progress-text",
                indicator: "#" + progressId
            }
        });
    };
    
    var addFile = function (that, file) {
        var row = renderRowFromTemplate(that, file);
        /* FLUID-2720 - do not hide the row under IE8 */
        if (!($.browser.msie && ($.browser.version >= 8))) {
            row.hide();
        }
        that.container.append(row);
        row.attr("title", that.options.strings.status.remove);
        row.fadeIn("slow");
        that.scroller.scrollBottom();
        createProgressorFromTemplate(that, row);

        that.refreshView();
    };
    
    var prepareForUpload = function (that) {
        var rowButtons = that.locate("fileIconBtn", that.locate("fileRows"));
        rowButtons.attr("disabled", "disabled");
        rowButtons.addClass(that.options.styles.dim);    
    };

    var refreshAfterUpload = function (that) {
        var rowButtons = that.locate("fileIconBtn", that.locate("fileRows"));
        rowButtons.removeAttr("disabled");
        rowButtons.removeClass(that.options.styles.dim);    
    };
        
    var changeRowState = function (that, row, newState) {
        row.removeClass(that.options.styles.ready).removeClass(that.options.styles.error).addClass(newState);
    };
    
    var markRowAsComplete = function (that, file) {
        // update styles and keyboard bindings for the file row
        var row = rowForFile(that, file);
        changeRowState(that, row, that.options.styles.uploaded);
        row.attr("title", that.options.strings.status.success);
        fluid.enabled(row, false);
        
        // update the click event and the styling for the file delete button
        var removeRowBtn = that.locate("fileIconBtn", row);
        removeRowBtn.unbind("click");
        removeRowBtn.removeClass(that.options.styles.remove);
        removeRowBtn.attr("title", that.options.strings.status.success); 
    };
    
    var renderErrorInfoRowFromTemplate = function (that, fileRow, error) {
        // Render the row by cloning the template and binding its id to the file.
        var errorRow = that.errorInfoRowTemplate.clone();
        errorRow.attr("id", fileRow.attr("id") + "_error");
        
        // Look up the error message and render it.
        var errorType = fluid.keyForValue(fluid.uploader.errorConstants, error);
        var errorMsg = that.options.strings.errors[errorType];
        that.locate("errorText", errorRow).text(errorMsg);
        fileRow.after(errorRow);
        that.scroller.scrollTo(errorRow);
    };
    
    var showErrorForFile = function (that, file, error) {
        hideFileProgress(that, file);
        if (file.filestatus === fluid.uploader.fileStatusConstants.ERROR) {
            var fileRowElm = rowForFile(that, file);
            changeRowState(that, fileRowElm, that.options.styles.error);
            renderErrorInfoRowFromTemplate(that, fileRowElm, error);
        }
    };
    
    var bindModelEvents = function (that) {
        that.returnedOptions = {
            listeners: {
                afterFileQueued: that.addFile,
                onUploadStart: that.prepareForUpload,
                onFileStart: that.showFileProgress,
                onFileProgress: that.updateFileProgress,
                onFileSuccess: that.markFileComplete,
                onFileError: that.showErrorForFile,
                afterFileComplete: that.hideFileProgress,
                afterUploadComplete: that.refreshAfterUpload
            }
        };
    };
    
    var addKeyboardNavigation = function (that) {
        fluid.tabbable(that.container);
        that.selectableContext = fluid.selectable(that.container, {
            selectableSelector: that.options.selectors.fileRows,
            onSelect: function (itemToSelect) {
                $(itemToSelect).addClass(that.options.styles.selected);
            },
            onUnselect: function (selectedItem) {
                $(selectedItem).removeClass(that.options.styles.selected);
            }
        });
    };
    
    var prepareTemplateElements = function (that) {
        // Grab our template elements out of the DOM.  
        that.rowTemplate = that.locate("rowTemplate").remove();
        that.errorInfoRowTemplate = that.locate("errorInfoRowTemplate").remove();
        that.errorInfoRowTemplate.removeClass(that.options.styles.hiddenTemplate);
        that.rowProgressorTemplate = that.locate("rowProgressorTemplate", that.options.uploaderContainer).remove();
    };
    
    var setupFileQueue = function (that) {
        fluid.initDependents(that);
        prepareTemplateElements(that);         
        addKeyboardNavigation(that); 
        bindModelEvents(that);
    };
    
    /**
     * Creates a new File Queue view.
     * 
     * @param {jQuery|selector} container the file queue's container DOM element
     * @param {fileQueue} queue a file queue model instance
     * @param {Object} options configuration options for the view
     */
    fluid.uploader.fileQueueView = function (container, events, options) {
        var that = fluid.initView("fluid.uploader.fileQueueView", container, options);
        that.fileProgressors = {};
        that.model = that.options.model;
        that.events = events;
        
        that.addFile = function (file) {
            addFile(that, file);
        };
        
        that.removeFile = function (file) {
            removeRowForFile(that, file);
        };
        
        that.prepareForUpload = function () {
            prepareForUpload(that);
        };
        
        that.refreshAfterUpload = function () {
            refreshAfterUpload(that);
        };

        that.showFileProgress = function (file) {
            startFileProgress(that, file);
        };
        
        that.updateFileProgress = function (file, fileBytesComplete, fileTotalBytes) {
            updateFileProgress(that, file, fileBytesComplete, fileTotalBytes); 
        };
        
        that.markFileComplete = function (file) {
            progressorForFile(that, file).update(100, "100%");
            markRowAsComplete(that, file);
        };
        
        that.showErrorForFile = function (file, error) {
            showErrorForFile(that, file, error);
        };
        
        that.hideFileProgress = function (file) {
            hideFileProgress(that, file);
        };
        
        that.refreshView = function () {
            that.scroller.refreshView();
            that.selectableContext.refresh();
        };
        
        setupFileQueue(that);     
        return that;
    };
    
    fluid.demands("fluid.uploader.fileQueueView", "fluid.uploader.multiFileUploader", {
        funcName: "fluid.uploader.fileQueueView",
        args: [
            "{multiFileUploader}.dom.fileQueue",
            {
                onFileRemoved: "{multiFileUploader}.events.onFileRemoved"
            },
            fluid.COMPONENT_OPTIONS
        ]
    });
    
    fluid.demands("fluid.scroller", "fluid.uploader.fileQueueView", {
        funcName: "fluid.scroller",
        args: [
            "{fileQueueView}.container"
        ]
    });
    
    fluid.defaults("fluid.uploader.fileQueueView", {
        components: {
            scroller: {
                type: "fluid.scroller"
            }
        },
        
        selectors: {
            fileRows: ".flc-uploader-file",
            fileName: ".flc-uploader-file-name",
            fileSize: ".flc-uploader-file-size",
            fileIconBtn: ".flc-uploader-file-action",      
            errorText: ".flc-uploader-file-error",
            
            rowTemplate: ".flc-uploader-file-tmplt",
            errorInfoRowTemplate: ".flc-uploader-file-error-tmplt",
            rowProgressorTemplate: ".flc-uploader-file-progressor-tmplt"
        },
        
        styles: {
            hover: "fl-uploader-file-hover",
            selected: "fl-uploader-file-focus",
            ready: "fl-uploader-file-state-ready",
            uploading: "fl-uploader-file-state-uploading",
            uploaded: "fl-uploader-file-state-uploaded",
            error: "fl-uploader-file-state-error",
            remove: "fl-uploader-file-action-remove",
            dim: "fl-uploader-dim",
            hiddenTemplate: "fl-uploader-hidden-templates"
        },
        
        strings: {
            progress: {
                toUploadLabel: "To upload: %fileCount %fileLabel (%totalBytes)", 
                singleFile: "file",
                pluralFiles: "files"
            },
            status: {
                success: "File Uploaded",
                error: "File Upload Error",
                remove: "Press Delete key to remove file"
            }, 
            errors: {
                HTTP_ERROR: "File upload error: a network error occured or the file was rejected (reason unknown).",
                IO_ERROR: "File upload error: a network error occured.",
                UPLOAD_LIMIT_EXCEEDED: "File upload error: you have uploaded as many files as you are allowed during this session",
                UPLOAD_FAILED: "File upload error: the upload failed for an unknown reason.",
                QUEUE_LIMIT_EXCEEDED: "You have as many files in the queue as can be added at one time. Removing files from the queue may allow you to add different files.",
                FILE_EXCEEDS_SIZE_LIMIT: "One or more of the files that you attempted to add to the queue exceeded the limit of %fileSizeLimit.",
                ZERO_BYTE_FILE: "One or more of the files that you attempted to add contained no data.",
                INVALID_FILETYPE: "One or more files were not added to the queue because they were of the wrong type."
            }
        },
        
        mergePolicy: {
            model: "preserve",
            events: "preserve"
        }
    });
   
})(jQuery, fluid_1_3);
