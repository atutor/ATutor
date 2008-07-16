/*
Copyright 2007 - 2008 University of Toronto

Licensed under the Educational Community License (ECL), Version 2.0 or the New
BSD license. You may not use this file except in compliance with one these
Licenses.

You may obtain a copy of the ECL 2.0 License and BSD License at
https://source.fluidproject.org/svn/LICENSE.txt
*/

var fluid = fluid || {};

(function (fluid) {
    var createLayoutCustomizer = function (layout, perms, orderChangedCallbackUrl, options) {
        // Configure options
        options = options || {};
        var rOptions = options;
        rOptions.role = rOptions.role || fluid.roles.REGIONS;

        var lhOptions = {};
        lhOptions.orderChangedCallbackUrl = orderChangedCallbackUrl;
        lhOptions.orderChangedCallback = options.orderChangedCallback;
        lhOptions.dropWarningId = options.dropWarningId;

        var reordererRoot = fluid.utils.jById (fluid.moduleLayout.containerId (layout));
        var items = fluid.moduleLayout.createFindItems (layout, perms, rOptions.grabHandle);    
        var layoutHandler = new fluid.ModuleLayoutHandler (layout, perms, lhOptions);

        return new fluid.Reorderer (reordererRoot, items, layoutHandler, rOptions);
    };
    

    /**
     * Creates a layout customizer from a combination of a layout and permissions object.
     * @param {Object} layout a layout object. See http://wiki.fluidproject.org/x/FYsk for more details
     * @param {Object} perms a permissions data structure. See the above documentation
     */
    fluid.initLayoutCustomizer = function (layout, perms, orderChangedCallbackUrl, options) {        
        return createLayoutCustomizer (layout, perms, orderChangedCallbackUrl, options);
    };

    /**
     * Simple way to create a layout customizer.
     * @param {selector} a selector for the layout container
     * @param {Object} a map of selectors for columns and modules within the layout
     * @param {Function} a function to be called when the order changes 
     * @param {Object} additional configuration options
     */
    fluid.reorderLayout = function(containerSelector, layoutSelectors, orderChangedCallback, options) {
        options = options || {};
        options.orderChangedCallback = orderChangedCallback;
        
        var container = jQuery(containerSelector);
        var columns = jQuery(layoutSelectors.columns, container);
        var modules = jQuery(layoutSelectors.modules, container);
        
        var layout = fluid.moduleLayout.buildLayout(container, columns, modules);
        
        return fluid.initLayoutCustomizer(layout, null, null, options);
    };    
}) (fluid);
