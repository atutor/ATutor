/*
Copyright 2008-2009 University of Toronto
Copyright 2010 OCAD University

Licensed under the Educational Community License (ECL), Version 2.0 or the New
BSD license. You may not use this file except in compliance with one these
Licenses.

You may obtain a copy of the ECL 2.0 License and BSD License at
https://source.fluidproject.org/svn/LICENSE.txt
*/

/*global jQuery, fluid_1_3:true*/

var fluid_1_3 = fluid_1_3 || {};

(function ($, fluid) {

    fluid.uploader = fluid.uploader || {};
    fluid.uploader.swfUploadStrategy = fluid.uploader.swfUploadStrategy || {};
    
    /**********************************************************************************
     * The functions in this file, which provide support for Flash 9 in the Uploader, *
     * have been deprecated as of Infusion 1.3.                                       * 
     **********************************************************************************/
    
    fluid.uploader.swfUploadStrategy.flash9SetupDOM = function (styles) {
        var container = $("<div><span></span></div>");
        container.addClass(styles.flash9Container);
        $("body").append(container);
        return container;       
    };

    fluid.demands("fluid.uploader.swfUploadStrategy.setupDOM", [
        "fluid.uploader.swfUploadStrategy.engine",
        "fluid.uploader.flash.9"
    ], {
        funcName: "fluid.uploader.swfUploadStrategy.flash9SetupDOM",
        args: [
            "{engine}.options.styles"
        ]
    });

    fluid.uploader.swfUploadStrategy.flash9SetupConfig = function (config, events) {
        return fluid.uploader.swfUploadStrategy.convertConfigForSWFUpload(config, events);
    };

    fluid.demands("fluid.uploader.swfUploadStrategy.setupConfig", [
        "fluid.uploader.swfUploadStrategy.engine",
        "fluid.uploader.flash.9"
    ], {
        funcName: "fluid.uploader.swfUploadStrategy.flash9SetupConfig",
        args: [
            "{engine}.config",
            "{multiFileUploader}.events"
        ]
    });

    fluid.uploader.swfUploadStrategy.flash9EventBinder = function (model, events, local, browseButton) {
        browseButton.click(function (e) {        
            local.browse();
            e.preventDefault();
        });
        fluid.uploader.swfUploadStrategy.bindFileEventListeners(model, events);
    };

    fluid.demands("fluid.uploader.swfUploadStrategy.eventBinder", [
        "fluid.uploader.swfUploadStrategy.engine",
        "fluid.uploader.flash.9"
    ], {
        funcName: "fluid.uploader.swfUploadStrategy.flash9EventBinder",
        args: [
            "{multiFileUploader}.queue.files",
            "{multiFileUploader}.events",
            "{local}",
            "{multiFileUploader}.dom.browseButton"
        ]
    });

})(jQuery, fluid_1_3);
