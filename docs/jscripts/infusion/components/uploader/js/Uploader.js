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

/*global window, swfobject, jQuery, fluid_1_2*/

fluid_1_2 = fluid_1_2 || {};

/************
 * Uploader *
 ************/

(function ($, fluid) {
    
    var fileOrFiles = function (that, numFiles) {
        return (numFiles === 1) ? that.options.strings.progress.singleFile : 
                                  that.options.strings.progress.pluralFiles;
    };
    
    var enableElement = function (that, elm) {
        elm.removeAttr("disabled");
        elm.removeClass(that.options.styles.dim);
    };
    
    var disableElement = function (that, elm) {
        elm.attr("disabled", "disabled");
        elm.addClass(that.options.styles.dim);
    };
    
    var showElement = function (that, elm) {
        elm.removeClass(that.options.styles.hidden);
    };
     
    var hideElement = function (that, elm) {
        elm.addClass(that.options.styles.hidden);
    };
    
    var setTotalProgressStyle = function (that, didError) {
        didError = didError || false;
        var indicator = that.totalProgress.indicator;
        indicator.toggleClass(that.options.styles.totalProgress, !didError);
        indicator.toggleClass(that.options.styles.totalProgressError, didError);
    };
    
    var setStateEmpty = function (that) {
        disableElement(that, that.locate("uploadButton"));
        
        // If the queue is totally empty, treat it specially.
        if (that.queue.files.length === 0) { 
            that.locate("browseButton").text(that.options.strings.buttons.browse);
            showElement(that, that.locate("instructions"));
        }
    };
    
    var setStateDone = function (that) {
        disableElement(that, that.locate("uploadButton"));
        enableElement(that, that.locate("browseButton"));
        hideElement(that, that.locate("pauseButton"));
        showElement(that, that.locate("uploadButton"));
    };

    var setStateLoaded = function (that) {
        that.locate("browseButton").text(that.options.strings.buttons.addMore);
        hideElement(that, that.locate("pauseButton"));
        showElement(that, that.locate("uploadButton"));
        enableElement(that, that.locate("uploadButton"));
        enableElement(that, that.locate("browseButton"));
        hideElement(that, that.locate("instructions"));
        that.totalProgress.hide();
    };
    
    var setStateUploading = function (that) {
        that.totalProgress.hide(false, false);
        setTotalProgressStyle(that);
        hideElement(that, that.locate("uploadButton"));
        disableElement(that, that.locate("browseButton"));
        enableElement(that, that.locate("pauseButton"));
        showElement(that, that.locate("pauseButton"));
        that.locate(that.options.focusWithEvent.afterUploadStart).focus();
    };    
    
    var renderUploadTotalMessage = function (that) {
        // Render template for the total file status message.
        var numReadyFiles = that.queue.getReadyFiles().length;
        var bytesReadyFiles = that.queue.sizeOfReadyFiles();
        var fileLabelStr = fileOrFiles(that, numReadyFiles);
                                                   
        var totalStateStr = fluid.stringTemplate(that.options.strings.progress.toUploadLabel, {
            fileCount: numReadyFiles, 
            fileLabel: fileLabelStr, 
            totalBytes: fluid.uploader.formatFileSize(bytesReadyFiles)
        });
        that.locate("totalFileStatusText").html(totalStateStr);
    };
        
    var updateTotalProgress = function (that) {
        var batch = that.queue.currentBatch;
        var totalPercent = fluid.uploader.derivePercent(batch.totalBytesUploaded, batch.totalBytes);
        var numFilesInBatch = batch.files.length;
        var fileLabelStr = fileOrFiles(that, numFilesInBatch);
        
        var totalProgressStr = fluid.stringTemplate(that.options.strings.progress.totalProgressLabel, {
            curFileN: batch.fileIdx + 1, 
            totalFilesN: numFilesInBatch, 
            fileLabel: fileLabelStr,
            currBytes: fluid.uploader.formatFileSize(batch.totalBytesUploaded), 
            totalBytes: fluid.uploader.formatFileSize(batch.totalBytes)
        });  
        that.totalProgress.update(totalPercent, totalProgressStr);
    };
    
    var updateTotalAtCompletion = function (that) {
        var numErroredFiles = that.queue.getErroredFiles().length;
        var numTotalFiles = that.queue.files.length;
        var fileLabelStr = fileOrFiles(that, numTotalFiles);
        
        var errorStr = "";
        
        // if there are errors then change the total progress bar
        // and set up the errorStr so that we can use it in the totalProgressStr
        if (numErroredFiles > 0) {
            var errorLabelString = (numErroredFiles === 1) ? that.options.strings.progress.singleError : 
                                                             that.options.strings.progress.pluralErrors;
            setTotalProgressStyle(that, true);
            errorStr = fluid.stringTemplate(that.options.strings.progress.numberOfErrors, {
                errorsN: numErroredFiles,
                errorLabel: errorLabelString
            });
        }
        
        var totalProgressStr = fluid.stringTemplate(that.options.strings.progress.completedLabel, {
            curFileN: that.queue.getUploadedFiles().length, 
            totalFilesN: numTotalFiles,
            errorString: errorStr,
            fileLabel: fileLabelStr,
            totalCurrBytes: fluid.uploader.formatFileSize(that.queue.sizeOfUploadedFiles())
        });
        
        that.totalProgress.update(100, totalProgressStr);
    };
   
    var bindDOMEvents = function (that) {
        that.locate("browseButton").click(function (evnt) {        
            that.browse();
            evnt.preventDefault();
        });
        
        that.locate("uploadButton").click(function () {
            that.start();
        });

        that.locate("pauseButton").click(function () {
            that.stop();
        });
    };

    var updateStateAfterFileDialog = function (that) {
        if (that.queue.getReadyFiles().length > 0) {
            setStateLoaded(that);
            renderUploadTotalMessage(that);
            that.locate(that.options.focusWithEvent.afterFileDialog).focus();  
        }
    };
    
    var updateStateAfterFileRemoval = function (that) {
        if (that.queue.getReadyFiles().length === 0) {
            setStateEmpty(that);
        }
        renderUploadTotalMessage(that);
    };
    
    var updateStateAfterCompletion = function (that) {
        if (that.queue.getReadyFiles().length === 0) {
            setStateDone(that);
        } else {
            setStateLoaded(that);
        }
        updateTotalAtCompletion(that);
    };
    
    var bindEvents = function (that) {        
        that.events.afterFileDialog.addListener(function () {
            updateStateAfterFileDialog(that);
        });
        
        that.events.afterFileQueued.addListener(function (file) {
            that.queue.addFile(file); 
        });
        
        that.events.onFileRemoved.addListener(function (file) {
            that.removeFile(file);
        });
        
        that.events.afterFileRemoved.addListener(function () {
            updateStateAfterFileRemoval(that);
        });
        
        that.events.onUploadStart.addListener(function () {
            setStateUploading(that);
        });
        
        that.events.onUploadStop.addListener(function () {
            that.locate(that.options.focusWithEvent.afterUploadStop).focus();
        });
        
        that.events.onFileStart.addListener(function (file) {
            that.queue.startFile();
        });
        
        that.events.onFileProgress.addListener(function (file, currentBytes, totalBytes) {
            that.queue.updateBatchStatus(currentBytes);
            updateTotalProgress(that); 
        });
        
        that.events.onFileComplete.addListener(function (file) {
            that.queue.finishFile(file);
            that.events.afterFileComplete.fire(file); 
            
            if (that.queue.shouldUploadNextFile()) {
                that.uploadStrategy.start();
            } else {
                if (that.queue.shouldStop) {
                    that.uploadStrategy.stop();
                }
                
                that.events.afterUploadComplete.fire(that.queue.currentBatch.files);
                that.queue.clearCurrentBatch();                
            }
        });
        
        that.events.onFileSuccess.addListener(function (file) {
            if (that.queue.currentBatch.bytesUploadedForFile === 0) {
                that.queue.currentBatch.totalBytesUploaded += file.size;
            }
            
            updateTotalProgress(that); 
        });
        
        that.events.onFileError.addListener(function (file, error) {
            if (error === fluid.uploader.errorConstants.UPLOAD_STOPPED) {
                that.queue.isUploading = false;
            } else if (that.queue.isUploading) {
                that.queue.currentBatch.totalBytesUploaded += file.size;
                that.queue.currentBatch.numFilesErrored++;
            }
        });

        that.events.afterUploadComplete.addListener(function () {
            that.queue.isUploading = false;
            updateStateAfterCompletion(that);
        });
    };
    
    var setupUploader = function (that) {
        // Setup the environment appropriate if we're in demo mode.
        if (that.options.demo) {
            that.demo = fluid.typeTag("fluid.uploader.demo");
        }
        
        fluid.initDependents(that);                 
        
        // Upload button should not be enabled until there are files to upload
        disableElement(that, that.locate("uploadButton"));
        bindDOMEvents(that);
        bindEvents(that);
    };
    
    /**
     * Instantiates a new Uploader component.
     * 
     * @param {Object} container the DOM element in which the Uploader lives
     * @param {Object} options configuration options for the component.
     */
    fluid.uploader = function (container, options) {
        var that = fluid.initView("fluid.uploader", container, options);
        that.queue = fluid.uploader.fileQueue();
        
        /**
         * Opens the native OS browse file dialog.
         */
        that.browse = function () {
            if (!that.queue.isUploading) {
                that.uploadStrategy.browse();
            }
        };
        
        /**
         * Removes the specified file from the upload queue.
         * 
         * @param {File} file the file to remove
         */
        that.removeFile = function (file) {
            that.queue.removeFile(file);
            that.uploadStrategy.removeFile(file);
            that.events.afterFileRemoved.fire(file);
        };
        
        /**
         * Starts uploading all queued files to the server.
         */
        that.start = function () {
            that.queue.start();
            that.events.onUploadStart.fire(that.queue.currentBatch.files); 
        };
        
        /**
         * Cancels an in-progress upload.
         */
        that.stop = function () {
            /* FLUID-822: while stopping the upload cycle while a file is in mid-upload should be possible
             * in practice, it sets up a state where when the upload cycle is restarted SWFUpload will get stuck
             * therefore we only stop the upload after a file has completed but before the next file begins. 
             */
            that.queue.shouldStop = true;
            that.events.onUploadStop.fire();
        };
        
        setupUploader(that);
        return that;  
    };
    
    /**
     * Instantiates a new Uploader component in the progressive enhancement style.
     * This mode requires another DOM element to be present, the element that is to be enhanced.
     * This method checks to see if the correct version of Flash is present, and will only
     * create the Uploader component if so.
     * 
     * @param {Object} container the DOM element in which the Uploader component lives
     * @param {Object} enhanceable the DOM element to show if the system requirements aren't met
     * @param {Object} options configuration options for the component
     */
    fluid.progressiveEnhanceableUploader = function (container, enhanceable, options) {
        enhanceable = fluid.container(enhanceable);
        container = fluid.container(container);
              
        // TODO: Flash-specific code. Move this elsewhere.
        if (swfobject.getFlashPlayerVersion().major < 9) {
            // Degrade gracefully.
            enhanceable.show();
        } else {
            // Instantiate the component.
            container.show();
            return fluid.uploader(container, options);
        }
    };
    
    /**
     * Pretty prints a file's size, converting from bytes to kilobytes or megabytes.
     * 
     * @param {Number} bytes the files size, specified as in number bytes.
     */
    fluid.uploader.formatFileSize = function (bytes) {
        if (typeof bytes === "number") {
            if (bytes === 0) {
                return "0.0 KB";
            } else if (bytes > 0) {
                if (bytes < 1048576) {
                    return (Math.ceil(bytes / 1024 * 10) / 10).toFixed(1) + " KB";
                }
                else {
                    return (Math.ceil(bytes / 1048576 * 10) / 10).toFixed(1) + " MB";
                }
            }
        }
        return "";
    };
    
    fluid.uploader.derivePercent = function (num, total) {
        return Math.round((num * 100) / total);
    };

    fluid.defaults("fluid.uploader", {
        components: {
            uploadStrategy: {
                type: "fluid.uploader.swfUploadStrategy"
            },
            
            fileQueueView: {
                type: "fluid.uploader.fileQueueView"
            },
            
            totalProgress: {
                type: "fluid.uploader.totalProgressBar",
                options: {
                    selectors: {
                        progressBar: ".flc-uploader-queue-footer",
                        displayElement: ".flc-uploader-total-progress", 
                        label: ".flc-uploader-total-progress-text",
                        indicator: ".flc-uploader-total-progress",
                        ariaElement: ".flc-uploader-total-progress"
                    }
                }
            },
            
            manuallyDegrade: {
                type: "fluid.manuallyDegrade",
                options: {
                    selectors: {
                        enhanceable: ".fl-uploader.fl-progEnhance-basic"
                    }
                }
            }
        },
        
        queueSettings: {
            uploadURL: "",
            postParams: {},
            fileSizeLimit: "20480",
            fileTypes: "*",
            fileTypesDescription: null,
            fileUploadLimit: 0,
            fileQueueLimit: 0
        },

        demo: false,
        
        selectors: {
            fileQueue: ".flc-uploader-queue",
            browseButton: ".flc-uploader-button-browse",
            uploadButton: ".flc-uploader-button-upload",
            pauseButton: ".flc-uploader-button-pause",
            totalFileStatusText: ".flc-uploader-total-progress-text",
            instructions: ".flc-uploader-browse-instructions"
        },

        // Specifies a selector name to move keyboard focus to when a particular event fires.
        // Event listeners must already be implemented to use these options.
        focusWithEvent: {
            afterFileDialog: "uploadButton",
            afterUploadStart: "pauseButton",
            afterUploadStop: "uploadButton"
        },
        
        styles: {
            disabled: "fl-uploader-disabled",
            hidden: "fl-uploader-hidden",
            dim: "fl-uploader-dim",
            totalProgress: "fl-uploader-total-progress-okay",
            totalProgressError: "fl-uploader-total-progress-errored"
        },
        
        events: {
            afterReady: null,
            onFileDialog: null,
            afterFileQueued: null,
            onFileRemoved: null,
            afterFileRemoved: null,
            onQueueError: null,
            afterFileDialog: null,
            onUploadStart: null,
            onUploadStop: null,
            onFileStart: null,
            onFileProgress: null,
            onFileError: null,
            onFileSuccess: null,
            onFileComplete: null,
            afterFileComplete: null,
            afterUploadComplete: null
        },

        strings: {
            progress: {
                toUploadLabel: "To upload: %fileCount %fileLabel (%totalBytes)", 
                totalProgressLabel: "Uploading: %curFileN of %totalFilesN %fileLabel (%currBytes of %totalBytes)", 
                completedLabel: "Uploaded: %curFileN of %totalFilesN %fileLabel (%totalCurrBytes)%errorString",
                numberOfErrors: ", %errorsN %errorLabel",
                singleFile: "file",
                pluralFiles: "files",
                singleError: "error",
                pluralErrors: "errors"
            },
            buttons: {
                browse: "Browse Files",
                addMore: "Add More",
                stopUpload: "Stop Upload",
                cancelRemaning: "Cancel remaining Uploads",
                resumeUpload: "Resume Upload"
            }
        }
    });
    
    fluid.demands("fluid.uploader.totalProgressBar", "fluid.uploader", {
        funcName: "fluid.progress",
        args: [
            "{uploader}.container",
            fluid.COMPONENT_OPTIONS
        ]
    });
    
    
    fluid.demands("fluid.manuallyDegrade", "fluid.uploader", {
	    funcName: "fluid.manuallyDegrade",
	    args: [
	        "{uploader}.container",
	        fluid.COMPONENT_OPTIONS
	    ]
	});
    
    
    /**************************************************
     * Error constants for the Uploader               *
     * TODO: These are SWFUpload-specific error codes *
     **************************************************/
     
    fluid.uploader.errorConstants = {
        HTTP_ERROR: -200,
        MISSING_UPLOAD_URL: -210,
        IO_ERROR: -220,
        SECURITY_ERROR: -230,
        UPLOAD_LIMIT_EXCEEDED: -240,
        UPLOAD_FAILED: -250,
        SPECIFIED_FILE_ID_NOT_FOUND: -260,
        FILE_VALIDATION_FAILED: -270,
        FILE_CANCELLED: -280,
        UPLOAD_STOPPED: -290
    };
    
    fluid.uploader.fileStatusConstants = {
        QUEUED: -1,
        IN_PROGRESS: -2,
        ERROR: -3,
        COMPLETE: -4,
        CANCELLED: -5
    };

    
    /*******************
     * ManuallyDegrade *
     *******************/
    
    var renderLink = function (renderLocation, text, classes, appendBeside) {
        var link = $("<a href='#'>" + text + "</a>");
        link.addClass(classes);
        
        if (renderLocation === "before") {
            appendBeside.before(link);
        } else {
            appendBeside.after(link);
        }
        
        return link;
    };

    var toggleVisibility = function (toShow, toHide) {
        // For FLUID-2789: hide() doesn't work in Opera, so this check
        // uses a style to hide if the browser is Opera
        if (window.opera) { 
            toShow.show().removeClass("hideUploaderForOpera");
            toHide.show().addClass("hideUploaderForOpera");
        } else {
            toShow.show();
            toHide.hide();
        }
    };
    
    var defaultControlRenderer = function (that) {
        var degradeLink = renderLink(that.options.defaultRenderLocation,
                   that.options.strings.degradeLinkText,
                   that.options.styles.degradeLinkClass,
                   that.enhancedContainer);
        degradeLink.addClass("flc-manuallyDegrade-degrade");
        
        var enhanceLink = renderLink(that.options.defaultRenderLocation,
                   that.options.strings.enhanceLinkText,
                   that.options.styles.enhanceLinkClass,
                   that.degradedContainer);
        enhanceLink.addClass("flc-manuallyDegrade-enhance");
    };
    
    var fetchControls = function (that) {
        that.degradeControl = that.locate("degradeControl");
        that.enhanceControl = that.locate("enhanceControl");
    };
    
    var setupManuallyDegrade = function (that) {
        // If we don't have anything to degrade to, stop right here.
        if (!that.degradedContainer.length) {
            return;
        }
        
        // Render the controls if they're not already there.
        fetchControls(that);
        if (!that.degradeControl.length && !that.enhanceControl.length) {
            that.options.controlRenderer(that);
            fetchControls(that);
        }
        
        // Bind click handlers to them.
        that.degradeControl.click(that.degrade);
        that.enhanceControl.click(that.enhance);
        
        // Hide the enhance link to start.
        that.enhanceControl.hide();
    };
    
    var determineContainer = function (options) {
        var defaults = fluid.defaults("fluid.manuallyDegrade");
        return (options && options.container) ? options.container : defaults.container;
    };
    
    fluid.manuallyDegrade = function (componentContainer, options) {
        var container = determineContainer(options);

        var that = fluid.initView("fluid.manuallyDegrade", container, options);
        var isDegraded = false;
        that.enhancedContainer = componentContainer;
        that.degradedContainer = that.locate("enhanceable");
  
        
        that.degrade = function () {
            toggleVisibility(that.enhanceControl, that.degradeControl);
            toggleVisibility(that.degradedContainer, that.enhancedContainer);
            isDegraded = true;
        };
         
        that.enhance = function () {
            toggleVisibility(that.degradeControl, that.enhanceControl);
            toggleVisibility(that.enhancedContainer, that.degradedContainer);
            isDegraded = false;
        };
         
        that.isDegraded = function () {
            return isDegraded;
        };
         
        setupManuallyDegrade(that);
        return that;
    };
     
    fluid.defaults("fluid.manuallyDegrade", {
        container: "body",
        
        controlRenderer: defaultControlRenderer,
        
        defaultRenderLocation: "before",
        strings: {
            degradeLinkText: "Switch to the standard single-file Uploader",
            enhanceLinkText: "Switch to the Flash-based multi-file Uploader"
        },
        selectors: {
            enhanceable: ".fl-progEnhance-basic",
            degradeControl: ".flc-manuallyDegrade-degrade",
            enhanceControl: ".flc-manuallyDegrade-enhance"
        },
        
        styles: {
            degradeLinkClass: "fl-uploader-manually-degrade",
            enhanceLinkClass: "fl-uploader-manually-enhance"
        }
    });

})(jQuery, fluid_1_2);
