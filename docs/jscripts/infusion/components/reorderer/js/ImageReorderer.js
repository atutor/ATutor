/*
Copyright 2008-2009 University of Cambridge
Copyright 2008-2009 University of Toronto
Copyright 2010 Lucendo Development Ltd.

Licensed under the Educational Community License (ECL), Version 2.0 or the New
BSD license. You may not use this file except in compliance with one these
Licenses.

You may obtain a copy of the ECL 2.0 License and BSD License at
https://source.fluidproject.org/svn/LICENSE.txt
*/

/*global jQuery*/
/*global fluid_1_3*/

fluid_1_3 = fluid_1_3 || {};

(function ($, fluid) {
    
    var deriveLightboxCellBase = function (namebase, index) {
        return namebase + "lightbox-cell:" + index + ":";
    };
            
    var addThumbnailActivateHandler = function (container) {
        var enterKeyHandler = function (evt) {
            if (evt.which === fluid.reorderer.keys.ENTER) {
                var thumbnailAnchors = $("a", evt.target);
                document.location = thumbnailAnchors.attr("href");
            }
        };
        
        container.keypress(enterKeyHandler);
    };
    
    // Custom query method seeks all tags descended from a given root with a 
    // particular tag name, whose id matches a regex.
    var seekNodesById = function (rootnode, tagname, idmatch) {
        var inputs = rootnode.getElementsByTagName(tagname);
        var togo = [];
        for (var i = 0; i < inputs.length; i += 1) {
            var input = inputs[i];
            var id = input.id;
            if (id && id.match(idmatch)) {
                togo.push(input);
            }
        }
        return togo;
    };
    
    var createImageCellFinder = function (parentNode, containerId) {
        parentNode = fluid.unwrap(parentNode);
        
        var lightboxCellNamePattern = "^" + deriveLightboxCellBase(containerId, "[0-9]+") + "$";
        
        return function () {
            // This orderable finder assumes that the lightbox thumbnails are 'div' elements
            return seekNodesById(parentNode, "div", lightboxCellNamePattern);
        };
    };
    
    var seekForm = function (container) {
        return fluid.findAncestor(container, function (element) {
            return $(element).is("form");
        });
    };
    
    var seekInputs = function (container, reorderform) {
        return seekNodesById(reorderform, 
                             "input", 
                             "^" + deriveLightboxCellBase(container.attr("id"), "[^:]*") + "reorder-index$");
    };
    
    var mapIdsToNames = function (container, reorderform) {
        var inputs = seekInputs(container, reorderform);
        for (var i = 0; i < inputs.length; i++) {
            var input = inputs[i];
            var name = input.name;
            input.name = name || input.id;
        }
    };
    
    /**
     * Returns a default afterMove listener using the id-based, form-driven scheme for communicating with the server.
     * It is implemented by nesting hidden form fields inside each thumbnail container. The value of these form elements
     * represent the order for each image. This default listener submits the form's default 
     * action via AJAX.
     * 
     * @param {jQueryable} container the Image Reorderer's container element 
     */
    var createIDAfterMoveListener = function (container) {
        var reorderform = seekForm(container);
        mapIdsToNames(container, reorderform);
        
        return function () {
            var inputs, i;
            inputs = seekInputs(container, reorderform);
            
            for (i = 0; i < inputs.length; i += 1) {
                inputs[i].value = i;
            }
        
            if (reorderform && reorderform.action) {
                var order = $(reorderform).serialize();
                $.post(reorderform.action, 
                       order,
                       function (type, data, evt) { /* No-op response */ });
            }
        };
    };

    
    var setDefaultValue = function (target, path, value) {
        var previousValue = fluid.get(target, path);
        var valueToSet = previousValue || value;
        fluid.set(target, path, valueToSet);
    };
    
    // Public Lightbox API
    /**
     * Creates a new Lightbox instance from the specified parameters, providing full control over how
     * the Lightbox is configured.
     * 
     * @param {Object} container 
     * @param {Object} options 
     */
    fluid.reorderImages = function (container, options) {
        // Instantiate a mini-Image Reorderer component, then feed its options to the real Reorderer.
        var that = fluid.initView("fluid.reorderImages", container, options);
        
        // If the user didn't specify their own afterMove or movables options,
        // set up defaults for them using the old id-based scheme.
        // Backwards API compatiblity. Remove references to afterMoveCallback by Infusion 1.5.
        setDefaultValue(that, "options.listeners.afterMove", 
                        that.options.afterMoveCallback || createIDAfterMoveListener(that.container));
        setDefaultValue(that, "options.selectors.movables", 
                        createImageCellFinder(that.container, that.container.attr("id")));
        
        var reorderer = fluid.reorderer(that.container, that.options);
        
        fluid.tabindex($("a", that.container), -1);
        addThumbnailActivateHandler(that.container);
        
        return reorderer;
    };
   
    // This function now deprecated. Please use fluid.reorderImages() instead.
    fluid.lightbox = fluid.reorderImages;
    
    fluid.defaults("fluid.reorderImages", {
        layoutHandler: "fluid.gridLayoutHandler",

        selectors: {
            labelSource: ".flc-reorderer-imageTitle"
        }
    });

})(jQuery, fluid_1_3);
