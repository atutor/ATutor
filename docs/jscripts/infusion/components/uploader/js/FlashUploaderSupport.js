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

/*global jQuery, fluid_1_3:true, SWFUpload, swfobject */

fluid_1_3 = fluid_1_3 || {};

(function ($, fluid) {

    fluid.uploader = fluid.uploader || {};
    
    fluid.demands("fluid.uploader.impl", ["fluid.uploader", "fluid.uploader.swfUpload"], {
        funcName: "fluid.uploader.multiFileUploader"
    });
    
    /**********************
     * uploader.swfUpload *
     **********************/
    
    fluid.uploader.swfUploadStrategy = function (options) {
        var that = fluid.initLittleComponent("fluid.uploader.swfUploadStrategy", options);
        fluid.initDependents(that);
        return that;
    };
    
    fluid.defaults("fluid.uploader.swfUploadStrategy", {
        components: {
            engine: {
                type: "fluid.uploader.swfUploadStrategy.engine",
                options: {
                    queueSettings: "{multiFileUploader}.options.queueSettings",
                    flashMovieSettings: "{swfUploadStrategy}.options.flashMovieSettings"
                }
            },
            
            local: {
                type: "fluid.uploader.swfUploadStrategy.local"
            },
            
            remote: {
                type: "fluid.uploader.remote"
            }
        },
        
        // TODO: Rename this to "flashSettings" and remove the "flash" prefix from each option
        flashMovieSettings: {
            flashURL: "../../../lib/swfupload/flash/swfupload.swf",
            flashButtonPeerId: "",
            flashButtonAlwaysVisible: false,
            flashButtonTransparentEvenInIE: true,
            flashButtonImageURL: "../images/browse.png", // Used only when the Flash movie is visible.
            flashButtonCursorEffect: SWFUpload.CURSOR.HAND,
            debug: false
        },

        styles: {
            browseButtonOverlay: "fl-uploader-browse-overlay",
            flash9Container: "fl-uploader-flash9-container",
            uploaderWrapperFlash10: "fl-uploader-flash10-wrapper"
        }
    });
    
    fluid.demands("fluid.uploader.swfUploadStrategy", "fluid.uploader.multiFileUploader", {
        funcName: "fluid.uploader.swfUploadStrategy",
        args: [
            fluid.COMPONENT_OPTIONS
        ]
    });
    
    fluid.demands("fluid.uploader.progressiveStrategy", "fluid.uploader.swfUpload", {
        funcName: "fluid.uploader.swfUploadStrategy",
        args: [
            fluid.COMPONENT_OPTIONS
        ]
    });
    
    
    fluid.uploader.swfUploadStrategy.remote = function (swfUpload, options) {
        var that = fluid.initLittleComponent("fluid.uploader.swfUploadStrategy.remote", options);
        that.swfUpload = swfUpload;
        
        that.start = function () {
            that.swfUpload.startUpload();
        };
        
        that.stop = function () {
            that.swfUpload.stopUpload();
        };
        return that;
    };
    
    fluid.demands("fluid.uploader.remote", "fluid.uploader.swfUploadStrategy", {
        funcName: "fluid.uploader.swfUploadStrategy.remote",
        args: [
            "{engine}.swfUpload",
            fluid.COMPONENT_OPTIONS
        ]
    });

    
    fluid.uploader.swfUploadStrategy.local = function (swfUpload, options) {
        var that = fluid.initLittleComponent("fluid.uploader.swfUploadStrategy.local", options);
        that.swfUpload = swfUpload;
        
        that.browse = function () {
            if (that.options.file_queue_limit === 1) {
                that.swfUpload.selectFile();
            } else {
                that.swfUpload.selectFiles();
            }    
        };
        
        that.removeFile = function (file) {
            that.swfUpload.cancelUpload(file.id);
        };
        
        that.enableBrowseButton = function () {
            that.swfUpload.setButtonDisabled(false);
        };
        
        that.disableBrowseButton = function () {
            that.swfUpload.setButtonDisabled(true);
        };
        
        return that;
    };
    
    fluid.demands("fluid.uploader.swfUploadStrategy.local", "fluid.uploader.multiFileUploader", {
        funcName: "fluid.uploader.swfUploadStrategy.local",
        args: [
            "{engine}.swfUpload",
            fluid.COMPONENT_OPTIONS
        ]
    });
    
    fluid.uploader.swfUploadStrategy.engine = function (options) {
        var that = fluid.initLittleComponent("fluid.uploader.swfUploadStrategy.engine", options);
        
        // Get the Flash version from swfobject and setup a new context so that the appropriate
        // Flash 9/10 strategies are selected.
        var flashVersion = swfobject.getFlashPlayerVersion().major;
        that.flashVersionContext = fluid.typeTag("fluid.uploader.flash." + flashVersion);
        
        // Merge Uploader's generic queue options with our Flash-specific options.
        that.config = $.extend({}, that.options.queueSettings, that.options.flashMovieSettings);
        
        // Configure the SWFUpload subsystem.
        fluid.initDependents(that);
        that.flashContainer = that.setupDOM();
        that.swfUploadConfig = that.setupConfig();
        that.swfUpload = new SWFUpload(that.swfUploadConfig);
        that.bindEvents();
        
        return that;
    };
    
    fluid.defaults("fluid.uploader.swfUploadStrategy.engine", {
        invokers: {
            setupDOM: "fluid.uploader.swfUploadStrategy.setupDOM",
            setupConfig: "fluid.uploader.swfUploadStrategy.setupConfig",
            bindEvents: "fluid.uploader.swfUploadStrategy.eventBinder"
        }
    });
    
    fluid.demands("fluid.uploader.swfUploadStrategy.engine", "fluid.uploader.swfUploadStrategy", {
        funcName: "fluid.uploader.swfUploadStrategy.engine",
        args: [
            fluid.COMPONENT_OPTIONS
        ]
    });
    
    
    /**********************
     * swfUpload.setupDOM *
     **********************/
    
    fluid.uploader.swfUploadStrategy.flash10SetupDOM = function (uploaderContainer, browseButton, styles) {
        // Wrap the whole uploader first.
        uploaderContainer.wrap("<div class='" + styles.uploaderWrapperFlash10 + "'></div>");

        // Then create a container and placeholder for the Flash movie as a sibling to the uploader.
        var flashContainer = $("<div><span></span></div>");
        flashContainer.addClass(styles.browseButtonOverlay);
        uploaderContainer.after(flashContainer);
        
        browseButton.attr("tabindex", -1);        
        return flashContainer;   
    };
    
    fluid.demands("fluid.uploader.swfUploadStrategy.setupDOM", [
        "fluid.uploader.swfUploadStrategy.engine",
        "fluid.uploader.flash.10"
    ], {
        funcName: "fluid.uploader.swfUploadStrategy.flash10SetupDOM",
        args: [
            "{multiFileUploader}.container",
            "{multiFileUploader}.dom.browseButton",
            "{swfUploadStrategy}.options.styles"
        ]
    });
     
     
    /*********************************
     * swfUpload.setupConfig *
     *********************************/
      
    // Maps SWFUpload's setting names to our component's setting names.
    var swfUploadOptionsMap = {
        uploadURL: "upload_url",
        flashURL: "flash_url",
        postParams: "post_params",
        fileSizeLimit: "file_size_limit",
        fileTypes: "file_types",
        fileUploadLimit: "file_upload_limit",
        fileQueueLimit: "file_queue_limit",
        flashButtonPeerId: "button_placeholder_id",
        flashButtonImageURL: "button_image_url",
        flashButtonHeight: "button_height",
        flashButtonWidth: "button_width",
        flashButtonWindowMode: "button_window_mode",
        flashButtonCursorEffect: "button_cursor",
        debug: "debug"
    };

    // Maps SWFUpload's callback names to our component's callback names.
    var swfUploadEventMap = {
        afterReady: "swfupload_loaded_handler",
        onFileDialog: "file_dialog_start_handler",
        afterFileQueued: "file_queued_handler",
        onQueueError: "file_queue_error_handler",
        afterFileDialog: "file_dialog_complete_handler",
        onFileStart: "upload_start_handler",
        onFileProgress: "upload_progress_handler",
        onFileComplete: "upload_complete_handler",
        onFileError: "upload_error_handler",
        onFileSuccess: "upload_success_handler"
    };
    
    var mapNames = function (nameMap, source, target) {
        var result = target || {};
        for (var key in source) {
            var mappedKey = nameMap[key];
            if (mappedKey) {
                result[mappedKey] = source[key];
            }
        }
        
        return result;
    };
    
    // For each event type, hand the fire function to SWFUpload so it can fire the event at the right time for us.
    // TODO: Refactor out duplication with mapNames()--should be able to use Engage's mapping tool
    var mapSWFUploadEvents = function (nameMap, events, target) {
        var result = target || {};
        for (var eventType in events) {
            var fireFn = events[eventType].fire;
            var mappedName = nameMap[eventType];
            if (mappedName) {
                result[mappedName] = fireFn;
            }   
        }
        return result;
    };
    
    fluid.uploader.swfUploadStrategy.convertConfigForSWFUpload = function (config, events) {
        // Map the event and settings names to SWFUpload's expectations.
        var convertedConfig = mapNames(swfUploadOptionsMap, config);
        return mapSWFUploadEvents(swfUploadEventMap, events, convertedConfig);
    };
    
    fluid.uploader.swfUploadStrategy.flash10SetupConfig = function (config, events, flashContainer, browseButton) {
        config.flashButtonPeerId = fluid.allocateSimpleId(flashContainer.children().eq(0));
        var isTransparent = config.flashButtonAlwaysVisible ? false : (!$.browser.msie || config.flashButtonTransparentEvenInIE);
        config.flashButtonImageURL = isTransparent ? undefined : config.flashButtonImageURL;
        config.flashButtonHeight = config.flashButtonHeight || browseButton.outerHeight();
        config.flashButtonWidth = config.flashButtonWidth || browseButton.outerWidth();
        config.flashButtonWindowMode = isTransparent ? SWFUpload.WINDOW_MODE.TRANSPARENT : SWFUpload.WINDOW_MODE.OPAQUE;
        return fluid.uploader.swfUploadStrategy.convertConfigForSWFUpload(config, events);
    };
    
    fluid.demands("fluid.uploader.swfUploadStrategy.setupConfig", [
        "fluid.uploader.swfUploadStrategy.engine",
        "fluid.uploader.flash.10"
    ], {
        funcName: "fluid.uploader.swfUploadStrategy.flash10SetupConfig",
        args: [
            "{engine}.config",
            "{multiFileUploader}.events",
            "{engine}.flashContainer",
            "{multiFileUploader}.dom.browseButton"
        ]
    });

     
    /*********************************
     * swfUpload.eventBinder *
     *********************************/
     
    var unbindSWFUploadSelectFiles = function () {
        // There's a bug in SWFUpload 2.2.0b3 that causes the entire browser to crash 
        // if selectFile() or selectFiles() is invoked. Remove them so no one will accidently crash their browser.
        var emptyFunction = function () {};
        SWFUpload.prototype.selectFile = emptyFunction;
        SWFUpload.prototype.selectFiles = emptyFunction;
    };
    
    fluid.uploader.swfUploadStrategy.bindFileEventListeners = function (model, events) {
        // Manually update our public model to keep it in sync with SWFUpload's insane,
        // always-changing references to its internal model.        
        var manualModelUpdater = function (file) {
            fluid.find(model, function (potentialMatch) {
                if (potentialMatch.id === file.id) {
                    potentialMatch.filestatus = file.filestatus;
                    return true;
                }
            });
        };
        
        events.onFileStart.addListener(manualModelUpdater);
        events.onFileProgress.addListener(manualModelUpdater);
        events.onFileError.addListener(manualModelUpdater);
        events.onFileSuccess.addListener(manualModelUpdater);
    };
    
    fluid.uploader.swfUploadStrategy.flash10EventBinder = function (model, events, local) {
        unbindSWFUploadSelectFiles();      
              
        events.onUploadStart.addListener(function () {
            local.disableBrowseButton();
        });
        
        events.afterUploadComplete.addListener(function () {
            local.enableBrowseButton();            
        });
        
        fluid.uploader.swfUploadStrategy.bindFileEventListeners(model, events);
    };
    
    fluid.demands("fluid.uploader.swfUploadStrategy.eventBinder", [
        "fluid.uploader.swfUploadStrategy.engine",
        "fluid.uploader.flash.10"
    ], {
        funcName: "fluid.uploader.swfUploadStrategy.flash10EventBinder",
        args: [
            "{multiFileUploader}.queue.files",
            "{multiFileUploader}.events",
            "{swfUploadStrategy}.local"
        ]
    });
})(jQuery, fluid_1_3);
