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

/*global fluid_1_2, jQuery, SWFUpload, swfobject */

fluid_1_2 = fluid_1_2 || {};

(function ($, fluid) {

    fluid.uploader = fluid.uploader || {};
    
    
    /******************************
     * uploader.swfUploadStrategy *
     ******************************/
     
    var setupSWFUploadStrategy = function (that) {
        that.version = swfobject.getFlashPlayerVersion().major;
        fluid.initDependents(that);
        that.flashContainer = that.setupDOM();
        that.config = that.setupConfig();
        that.swfUploader = new SWFUpload(that.config);
        that.bindEvents();
    };
    
    fluid.uploader.swfUploadStrategy = function (options) {
        var that = fluid.initLittleComponent("fluid.uploader.swfUploadStrategy", options);
        
        that.browse = function () {
            if (that.options.file_queue_limit === 1) {
                that.swfUploader.selectFile();
            } else {
                that.swfUploader.selectFiles();
            }    
        };
        
        that.start = function () {
            that.swfUploader.startUpload();
        };
        
        that.removeFile = function (file) {
            that.swfUploader.cancelUpload(file.id);
        };
        
        that.stop = function () {
            that.swfUploader.stopUpload();
        };
        
        that.enableBrowseButton = function () {
            that.swfUploader.setButtonDisabled(false);
        };
        
        that.disableBrowseButton = function () {
            that.swfUploader.setButtonDisabled(true);
        };
        
        setupSWFUploadStrategy(that);
        return that;
    };
    
    fluid.defaults("fluid.uploader.swfUploadStrategy", {
        invokers: {
            setupDOM: "fluid.uploader.swfUploadStrategy.setupDOM",
            setupConfig: "fluid.uploader.swfUploadStrategy.setupConfig",
            bindEvents: "fluid.uploader.swfUploadStrategy.eventBinder"
        },
     
        // Rename this to "flashSettings" and remove the "flash" prefix from each option
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
    
    fluid.demands("fluid.uploader.swfUploadStrategy", "fluid.uploader", {
        funcName: "fluid.uploader.swfUploadStrategy"
    });
    
    fluid.demands("fluid.uploader.swfUploadStrategy.setupDOM", ["fluid.uploader", "fluid.uploader.swfUploaderStrategy"], {
        funcName: "fluid.uploader.swfUploadStrategy.setupDOM",
        args: [
            "{uploader}.container",
            "{uploader}.dom.browseButton",
            "{swfUploadStrategy}.version",
            "{swfUploadStrategy}.options.styles"
        ]
    });
    
    fluid.demands("fluid.uploader.swfUploadStrategy.setupConfig", ["fluid.uploader", "fluid.uploader.swfUploaderStrategy"], {
        funcName: "fluid.uploader.swfUploadStrategy.setupConfig",
        args: [
            "{uploader}.events",
            "{uploader}.dom.browseButton",
            "{swfUploadStrategy}.flashContainer",
            "{swfUploadStrategy}.version",
            "{uploader}.options.queueSettings",
            "{swfUploadStrategy}.options.flashMovieSettings"
        ]
    });
    
    fluid.demands("fluid.uploader.swfUploadStrategy.eventBinder", ["fluid.uploader", "fluid.uploader.swfUploaderStrategy"], {
        funcName: "fluid.uploader.swfUploadStrategy.eventBinder",
        args: [
            "{uploader}.queue.files",
            "{uploader}.events",
            "{swfUploadStrategy}.version",
            "{swfUploadStrategy}" // TODO: Could narrow this to just the start function.
        ]
    });

    
    /******************************
     * swfUploadStrategy.setupDOM *
     ******************************/

    var createFlash9MovieContainer = function (styles) {
        var container = $("<div><span></span></div>");
        container.addClass(styles.flash9Container);
        $("body").append(container);
        return container;
    };

    var createFlash10MovieContainer = function (uploaderContainer, styles) {        
        // Wrap the whole uploader first.
        uploaderContainer.wrap("<div class='" + styles.uploaderWrapperFlash10 + "'></div>");

        // Then create a container and placeholder for the Flash movie as a sibling to the uploader.
        var flashContainer = $("<div><span></span></div>");
        flashContainer.addClass(styles.browseButtonOverlay);
        uploaderContainer.after(flashContainer);
        return flashContainer;
    };

    var setupDOMForFlash10 = function (container, browseButton, styles) {
        var flashContainer = createFlash10MovieContainer(container, styles);
        browseButton.attr("tabindex", -1);        
        return flashContainer;
    };

    fluid.uploader.swfUploadStrategy.setupDOM = function (container, browseButton, flashVersion, styles) {
        if (flashVersion === 9) {
            return createFlash9MovieContainer(styles);
        } else {
            return setupDOMForFlash10(container, browseButton, styles);
        }         
    };
    
     
    /*********************************
     * swfUploadStrategy.setupConfig *
     *********************************/
      
    // Maps SWFUpload's setting names to our component's setting names.
    var swfUploadOptionsMap = {
        uploadURL: "upload_url",
        flashURL: "flash_url",
        postParams: "post_params",
        fileSizeLimit: "file_size_limit",
        fileTypes: "file_types",
        fileTypesDescription: "file_types_description",
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
    
    var setupButtonOptions = function (config, browseButton, flashContainer, flashVersion) {
        config.flashButtonPeerId = fluid.allocateSimpleId(flashContainer.children().eq(0));
         
        // Setup for Flash 10+
        if (flashVersion > 9) {
            var isTransparent = config.flashButtonAlwaysVisible ? false : (!$.browser.msie || config.flashButtonTransparentEvenInIE);
            config.flashButtonImageURL = isTransparent ? undefined : config.flashButtonImageURL;
            config.flashButtonHeight = config.flashButtonHeight || browseButton.outerHeight();
            config.flashButtonWidth = config.flashButtonWidth || browseButton.outerWidth();
            config.flashButtonWindowMode = isTransparent ? SWFUpload.WINDOW_MODE.TRANSPARENT : SWFUpload.WINDOW_MODE.OPAQUE;        
        }
    };
    
    // TODO: Absurd argument list!
    fluid.uploader.swfUploadStrategy.setupConfig = function (events, browseButton, flashContainer, flashVersion, queueSettings, flashMovieSettings) {
        // Map the event and settings names to SWFUpload's expectations.
        var mergedConfig = $.extend({}, queueSettings, flashMovieSettings);
        setupButtonOptions(mergedConfig, browseButton, flashContainer, flashVersion);
        var convertedConfig = mapNames(swfUploadOptionsMap, mergedConfig);
        return mapSWFUploadEvents(swfUploadEventMap, events, convertedConfig);
    };

     
    /*********************************
     * swfUploadStrategy.eventBinder *
     *********************************/
     
    var unbindSWFUploadSelectFiles = function () {
        // There's a bug in SWFUpload 2.2.0b3 that causes the entire browser to crash 
        // if selectFile() or selectFiles() is invoked. Remove them so no one will accidently crash their browser.
        var emptyFunction = function () {};
        SWFUpload.prototype.selectFile = emptyFunction;
        SWFUpload.prototype.selectFiles = emptyFunction;
    };
    
    var bindFlash10ButtonListeners = function (events, engine) {
        events.onUploadStart.addListener(function () {
            engine.disableBrowseButton();
        });
        
        events.afterUploadComplete.addListener(function () {
            engine.enableBrowseButton();            
        });    
    };
    
    var bindFileEventListeners = function (model, events) {
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
    
    var bindUploadQueueEventListeners = function (events, engine) {
        events.onUploadStart.addListener(function () {
            engine.start();
        });
    };
    
    fluid.uploader.swfUploadStrategy.eventBinder = function (model, events, flashVersion, engine) {
        if (flashVersion > 9) {
            unbindSWFUploadSelectFiles();            
            bindFlash10ButtonListeners(events, engine);
        }
        
        bindFileEventListeners(model, events);
        bindUploadQueueEventListeners(events, engine);
    };
    
})(jQuery, fluid_1_2);
