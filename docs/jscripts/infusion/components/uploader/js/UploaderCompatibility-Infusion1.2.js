/*
Copyright 2010 OCAD University

Licensed under the Educational Community License (ECL), Version 2.0 or the New
BSD license. You may not use this file except in compliance with one these
Licenses.

You may obtain a copy of the ECL 2.0 License and BSD License at
https://source.fluidproject.org/svn/LICENSE.txt
*/

/*global jQuery, fluid_1_3:true*/

var fluid_1_3 = fluid_1_3 || {};

/*********************************************************************************************
 * Note: this file should not be included in any Infusion build.                             *
 * Instead, users can choose to add this file manually if they need backwards compatibility. *
 *********************************************************************************************/
 
(function ($, fluid) {
    
    fluid.registerNamespace("fluid.compat.fluid_1_2.uploader");
    fluid.staticEnvironment.uploaderCompatibility = fluid.typeTag("fluid.uploader.fluid_1_2");

    fluid.compat.fluid_1_2.uploader.optionsRules = {
        "components": {
            expander: {
                type: "fluid.model.transform.firstValue",
                values: [
                    {
                        expander: {
                            type: "fluid.model.transform.value",
                            path: "components"
                        }
                    },
                    {
                        expander: {
                            type: "fluid.model.transform.value",
                            value: {
                                "strategy": {
                                    "options": {
                                        "flashMovieSettings": {
                                            expander: {
                                                type: "fluid.model.transform.value",
                                                value: {
                                                    "flashURL": "uploadManager.options.flashURL",
                                                    "flashButtonPeerId": "decorators.0.options.flashButtonPeerId",
                                                    "flashButtonAlwaysVisible": "decorators.0.options.flashButtonAlwaysVisible",
                                                    "flashButtonTransparentEvenInIE": "decorators.0.options.flashButtonTransparentEvenInIE",
                                                    "flashButtonImageURL": "decorators.0.options.flashButtonImageURL",
                                                    "flashButtonCursorEffect": "decorators.0.options.flashButtonCursorEffect",
                                                    "debug": "decorators.0.options.debug"
                                                }
                                            }
                                        },
                                        "styles": "decorators.0.options.styles"
                                    }
                                },
                                "fileQueueView": "fileQueueView",
                                "totalProgressBar": "totalProgressBar"
                            }
                        }
                    }
                ]
            }
        },
        "invokers": "invokers",
        "queueSettings": "uploadManager.options",
        "demo": "demo",
        "selectors": "selectors",
        "focusWithEvent": "focusWithEvent",
        "styles": "styles",
        "listeners": "listeners",
        "strings": "strings",
        "mergePolicy": "mergePolicy"
    };
    
    // Monkey patch fluid.uploader with an options-chewing wrapper.
    // TODO: Replace this with an IoC-resolved solution.
    var multiFileImpl = fluid.uploader.multiFileUploader;
    fluid.uploader.multiFileUploader = function (container, options) {
        options = fluid.model.transformWithRules(options, fluid.compat.fluid_1_2.uploader.optionsRules);
        return multiFileImpl(container, options);
    };
    
})(jQuery, fluid_1_3);
