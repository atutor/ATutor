/*
Copyright 2008-2009 University of Toronto
Copyright 2008-2009 University of California, Berkeley
Copyright 2010 OCAD University

Licensed under the Educational Community License (ECL), Version 2.0 or the New
BSD license. You may not use this file except in compliance with one these
Licenses.

You may obtain a copy of the ECL 2.0 License and BSD License at
https://source.fluidproject.org/svn/LICENSE.txt
*/

/*global jQuery, fluid_1_3:true, window, swfobject*/

var fluid_1_3 = fluid_1_3 || {};

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
            that.locate("browseButtonText").text(that.options.strings.buttons.browse);
            that.locate("browseButton").removeClass(that.options.styles.browseButton);
            showElement(that, that.locate("instructions"));
        }
    };
    
    var setStateDone = function (that) {
        disableElement(that, that.locate("uploadButton"));
        enableElement(that, that.locate("browseButton"));
        that.strategy.local.enableBrowseButton();
        hideElement(that, that.locate("pauseButton"));
        showElement(that, that.locate("uploadButton"));
    };

    var setStateLoaded = function (that) {
        that.locate("browseButtonText").text(that.options.strings.buttons.addMore);
        that.locate("browseButton").addClass(that.options.styles.browseButton);
        hideElement(that, that.locate("pauseButton"));
        showElement(that, that.locate("uploadButton"));
        enableElement(that, that.locate("uploadButton"));
        enableElement(that, that.locate("browseButton"));
        that.strategy.local.enableBrowseButton();
        hideElement(that, that.locate("instructions"));
        that.totalProgress.hide();
    };
    
    var setStateUploading = function (that) {
        that.totalProgress.hide(false, false);
        setTotalProgressStyle(that);
        hideElement(that, that.locate("uploadButton"));
        disableElement(that, that.locate("browseButton"));
        that.strategy.local.disableBrowseButton();
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

    /*
     * Summarizes the status of all the files in the file queue.  
     */
    var updateQueueSummaryText = function (that) {
        var fileQueueTable = that.locate("fileQueue");
        
        if (that.queue.files.length === 0) {
            fileQueueTable.attr("summary", that.options.strings.queue.emptyQueue);
        }
        else {
            var queueSummary = fluid.stringTemplate(that.options.strings.queue.queueSummary, {
                totalUploaded: that.queue.getUploadedFiles().length, 
                totalInUploadQueue: that.queue.files.length - that.queue.getUploadedFiles().length
            });        
            
            fileQueueTable.attr("summary", queueSummary);
        }
    };
    
    var bindDOMEvents = function (that) {
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
            updateQueueSummaryText(that);
        }
    };
    
    var updateStateAfterFileRemoval = function (that) {
        if (that.queue.getReadyFiles().length === 0) {
            setStateEmpty(that);
        }
        renderUploadTotalMessage(that);
        updateQueueSummaryText(that);
    };
    
    var updateStateAfterCompletion = function (that) {
        if (that.queue.getReadyFiles().length === 0) {
            setStateDone(that);
        } else {
            setStateLoaded(that);
        }
        updateTotalAtCompletion(that);
        updateQueueSummaryText(that);
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
            file.filestatus = fluid.uploader.fileStatusConstants.IN_PROGRESS;
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
                that.strategy.remote.start();
            } else {
                if (that.queue.shouldStop) {
                    that.strategy.remote.stop();
                }

                that.events.afterUploadComplete.fire(that.queue.currentBatch.files);
                that.queue.clearCurrentBatch();
            }
        });
        
        that.events.onFileSuccess.addListener(function (file) {
            file.filestatus = fluid.uploader.fileStatusConstants.COMPLETE;
            if (that.queue.currentBatch.bytesUploadedForFile === 0) {
                that.queue.currentBatch.totalBytesUploaded += file.size;
            }
            
            updateTotalProgress(that); 
        });
        
        that.events.onFileError.addListener(function (file, error) {
            file.filestatus = fluid.uploader.fileStatusConstants.ERROR;
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
        
        updateQueueSummaryText(that);
        that.statusUpdater();
        
        // Uploader uses application-style keyboard conventions, so give it a suitable role.
        that.container.attr("role", "application");
    };
    
    /**
     * Instantiates a new Uploader component.
     * 
     * @param {Object} container the DOM element in which the Uploader lives
     * @param {Object} options configuration options for the component.
     */
    fluid.uploader = function (container, options) {
        var that = fluid.typeTag("fluid.uploader");
        // Set up the environment for progressive enhancement.
        if (fluid.progressiveChecker) {
            fluid.staticEnvironment.uploaderContext = fluid.invoke("fluid.progressiveChecker", 
                                                                   null, 
                                                                   that);
        }
        
        // Invoke an Uploader implementation, which will be specifically resolved using IoC 
        // based on the static environment configured by the progressiveChecker above.
        return fluid.invoke("fluid.uploader.impl", [container, options], that);
    };
    
    fluid.demands("fluid.progressiveChecker", "fluid.uploader", {
        funcName: "fluid.progressiveChecker",
        args: [{
            checks: [
                {
                    feature: "{fluid.browser.supportsBinaryXHR}",
                    contextName: "fluid.uploader.html5"
                },
                {
                    feature: "{fluid.browser.supportsFlash}",
                    contextName: "fluid.uploader.swfUpload"
                }
            ],

            defaultTypeTag: fluid.typeTag("fluid.uploader.singleFile")
        }]
    });
    
    // This method has been deprecated as of Infusion 1.3. Use fluid.uploader() instead, 
    // which now includes built-in support for progressive enhancement.
    fluid.progressiveEnhanceableUploader = function (container, enhanceable, options) {
        return fluid.uploader(container, options);
    };

    /**
     * Multiple file Uploader implementation. Use fluid.uploader() for IoC-resolved, progressively
     * enhanceable Uploader, or call this directly if you don't want support for old-style single uploads
     *
     * @param {jQueryable} container the component's container
     * @param {Object} options configuration options
     */
    fluid.uploader.multiFileUploader = function (container, options) {
        var that = fluid.initView("fluid.uploader.multiFileUploader", container, options);
        that.queue = fluid.uploader.fileQueue();
        
        /**
         * Opens the native OS browse file dialog.
         */
        that.browse = function () {
            if (!that.queue.isUploading) {
                that.strategy.local.browse();
            }
        };
        
        /**
         * Removes the specified file from the upload queue.
         * 
         * @param {File} file the file to remove
         */
        that.removeFile = function (file) {
            that.queue.removeFile(file);
            that.strategy.local.removeFile(file);
            that.events.afterFileRemoved.fire(file);
        };
        
        /**
         * Starts uploading all queued files to the server.
         */
        that.start = function () {
            that.queue.start();
            that.events.onUploadStart.fire(that.queue.currentBatch.files); 
            that.strategy.remote.start();
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
    
    fluid.defaults("fluid.uploader.multiFileUploader", {
        components: {
            strategy: {
                type: "fluid.uploader.progressiveStrategy"
            },
            
            fileQueueView: {
                type: "fluid.uploader.fileQueueView",
                options: {
                    model: "{multiFileUploader}.queue.files",
                    uploaderContainer: "{multiFileUploader}.container",
                    events: "{multiFileUploader}.events"
                }
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
            }
        },
        
        invokers: {
            statusUpdater: "fluid.uploader.ariaLiveRegionUpdater"
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
            browseButtonText: ".flc-uploader-button-browse-text",
            uploadButton: ".flc-uploader-button-upload",
            pauseButton: ".flc-uploader-button-pause",
            totalFileStatusText: ".flc-uploader-total-progress-text",
            instructions: ".flc-uploader-browse-instructions",
            statusRegion: ".flc-uploader-status-region"
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
            totalProgressError: "fl-uploader-total-progress-errored",
            browseButton: "fl-uploader-browseMore"
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
            },
            queue: {
                emptyQueue: "File list: No files waiting to be uploaded.",
                queueSummary: "File list:  %totalUploaded files uploaded, %totalInUploadQueue file waiting to be uploaded." 
            }
        },
        
        mergePolicy: {
            model: "preserve"
        }
    });
    
    fluid.demands("fluid.uploader.impl", "fluid.uploader", {
        funcName: "fluid.uploader.multiFileUploader"
    });
    
    fluid.demands("fluid.uploader.totalProgressBar", "fluid.uploader.multiFileUploader", {
        funcName: "fluid.progress",
        args: [
            "{multiFileUploader}.container",
            fluid.COMPONENT_OPTIONS
        ]
    });
    
        
   /**
    * Pretty prints a file's size, converting from bytes to kilobytes or megabytes.
    * 
    * @param {Number} bytes the files size, specified as in number bytes.
    */
    fluid.uploader.formatFileSize = function (bytes) {
        if (typeof(bytes) === "number") {
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
     
    // TODO: Refactor this to be a general ARIA utility
    fluid.uploader.ariaLiveRegionUpdater = function (statusRegion, totalFileStatusText, events) {
        statusRegion.attr("role", "log");     
        statusRegion.attr("aria-live", "assertive");
        statusRegion.attr("aria-relevant", "text");
        statusRegion.attr("aria-atomic", "true");

        var regionUpdater = function () {
            statusRegion.text(totalFileStatusText.text());
        };

        events.afterFileDialog.addListener(regionUpdater);
        events.afterFileRemoved.addListener(regionUpdater);
        events.afterUploadComplete.addListener(regionUpdater);
    };
    
    fluid.demands("fluid.uploader.ariaLiveRegionUpdater", "fluid.uploader.multiFileUploader", {
        funcName: "fluid.uploader.ariaLiveRegionUpdater",
        args: [
            "{multiFileUploader}.dom.statusRegion",
            "{multiFileUploader}.dom.totalFileStatusText",
            "{multiFileUploader}.events"
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


    var toggleVisibility = function (toShow, toHide) {
        // For FLUID-2789: hide() doesn't work in Opera
        if (window.opera) { 
            toShow.show().removeClass("hideUploaderForOpera");
            toHide.show().addClass("hideUploaderForOpera");
        } else {
            toShow.show();
            toHide.hide();
        }
    };

    // TODO: Need to resolve the issue of the gracefully degraded view being outside of the component's
    // container. Perhaps we can embed a standard HTML 5 file input element right in the template, 
    // and hide everything else?
    var determineContainer = function (options) {
        var defaults = fluid.defaults("fluid.uploader.singleFileStrategy");
        return (options && options.container) ? options.container : defaults.container;
    };


    /**
     * Single file Uploader implementation. Use fluid.uploader() for IoC-resolved, progressively
     * enhanceable Uploader, or call this directly if you only want a standard single file uploader.
     * But why would you want that?
     *
     * @param {jQueryable} container the component's container
     * @param {Object} options configuration options
     */
    fluid.uploader.singleFileUploader = function (container, options) {
        var that = fluid.initView("fluid.uploader.singleFileUploader", container, options);
        // TODO: direct DOM fascism that will fail with multiple uploaders on a single page.
        toggleVisibility($(that.options.selectors.basicUpload), that.container);
        return that;
    };

    fluid.defaults("fluid.uploader.singleFileUploader", {
        selectors: {
            basicUpload: ".fl-progEnhance-basic"
        }
    });

    fluid.demands("fluid.uploader.impl", ["fluid.uploader", "fluid.uploader.singleFile"], {
        funcName: "fluid.uploader.singleFileUploader"
    });
    
})(jQuery, fluid_1_3);
