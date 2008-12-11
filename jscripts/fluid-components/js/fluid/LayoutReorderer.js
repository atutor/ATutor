/*
Copyright 2007 - 2008 University of Cambridge
Copyright 2007 - 2008 University of Toronto

Licensed under the Educational Community License (ECL), Version 2.0 or the New
BSD license. You may not use this file except in compliance with one these
Licenses.

You may obtain a copy of the ECL 2.0 License and BSD License at
https://source.fluidproject.org/svn/LICENSE.txt
*/

/*global jQuery*/
/*global fluid_0_5*/
fluid_0_5 = fluid_0_5 || {};

(function (fluid) {
 
    /**
     * Simple way to create a layout reorderer.
     * @param {selector} a selector for the layout container
     * @param {Object} a map of selectors for columns and modules within the layout
     * @param {Function} a function to be called when the order changes 
     * @param {Object} additional configuration options
     */
    fluid.reorderLayout = function (container, userOptions) {
        var assembleOptions = {
            layoutHandler: "fluid.moduleLayoutHandler"
        };
        var options = jQuery.extend(true, assembleOptions, userOptions);
        return fluid.reorderer(container, options);
    };    
})(fluid_0_5);
