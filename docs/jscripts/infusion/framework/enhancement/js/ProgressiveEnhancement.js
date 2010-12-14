/*
Copyright 2008-2009 University of Toronto
Copyright 2010 OCAD University

Licensed under the Educational Community License (ECL), Version 2.0 or the New
BSD license. You may not use this file except in compliance with one these
Licenses.

You may obtain a copy of the ECL 2.0 License and BSD License at
https://source.fluidproject.org/svn/LICENSE.txt
*/

/*global window, swfobject, jQuery*/

var fluid_1_3 = fluid_1_3 || {};

(function ($, fluid) {
    
    fluid.browser = fluid.browser || {};
    
    fluid.browser.binaryXHR = function () {
        var canSendBinary = window.FormData || XMLHttpRequest.prototype.sendAsBinary;
        return canSendBinary ? fluid.typeTag("fluid.browser.supportsBinaryXHR") : undefined;
    };
    
    fluid.browser.formData  = function () {
        return window.FormData ? fluid.typeTag("fluid.browser.supportsFormData") : undefined;
    };
    
    fluid.browser.flash = function () {
        var hasModernFlash = (typeof(swfobject) !== "undefined") && (swfobject.getFlashPlayerVersion().major > 8);
        return hasModernFlash ? fluid.typeTag("fluid.browser.supportsFlash") : undefined;
    };
    
    fluid.progressiveChecker = function (options) {
        // TODO: Replace with fluid.makeArray() when merged into trunk.
        var checks = options.checks ? $.makeArray(options.checks) : [];
        for (var x = 0; x < checks.length; x++) {
            var check = checks[x];
                            
            if (check.feature) {
                return fluid.typeTag(check.contextName);
            }

        }
        return options.defaultTypeTag;
    };
    
    fluid.defaults("fluid.progressiveChecker", {
        checks: [], // [{"feature": "{IoC Expression}", "contextName": "context.name"}]
        defaultTypeTag: undefined
    });
    
    
    /**********************************************************
     * This code runs immediately upon inclusion of this file *
     **********************************************************/
    
    // Use JavaScript to hide any markup that is specifically in place for cases when JavaScript is off.
    // Note: the use of fl-ProgEnhance-basic is deprecated, and replaced by fl-progEnhance-basic.
    // It is included here for backward compatibility only.
    $("head").append("<style type='text/css'>.fl-progEnhance-basic, .fl-ProgEnhance-basic { display: none; }</style>");
    
    // Browser feature detection--adds corresponding type tags to the static environment,
    // which can be used to define appropriate demands blocks for components using the IoC system.
    var features = {
        supportsBinaryXHR: fluid.browser.binaryXHR(),
        supportsFormData: fluid.browser.formData(),
        supportsFlash: fluid.browser.flash()
    };
    fluid.merge(null, fluid.staticEnvironment, features);
    
})(jQuery, fluid_1_3);
