/*
Copyright 2007 - 2008 University of Toronto

Licensed under the Educational Community License (ECL), Version 2.0 or the New
BSD license. You may not use this file except in compliance with one these
Licenses.

You may obtain a copy of the ECL 2.0 License and BSD License at
https://source.fluidproject.org/svn/LICENSE.txt
*/

var fluid = fluid || {};

(function (jQuery, document) {
    var deriveLightboxCellBase = function (namebase, index) {
        return namebase + "lightbox-cell:" + index + ":";
    };
            
    var addThumbnailActivateHandler = function (lightboxContainer) {
        var enterKeyHandler = function (evt) {
            if (evt.which === fluid.keys.ENTER) {
                var thumbnailAnchors = jQuery ("a", evt.target);
                document.location = thumbnailAnchors.attr ('href');
            }
        };
        
        jQuery (lightboxContainer).keypress (enterKeyHandler);
    };
    
    var createItemFinder = function (parentNode, containerId) {
        // This orderable finder knows that the lightbox thumbnails are 'div' elements
        var lightboxCellNamePattern = "^" + deriveLightboxCellBase (containerId, "[0-9]+") +"$";
        
        return function () {
            return fluid.utils.seekNodesById (parentNode, "div", lightboxCellNamePattern);
        };
    };
    
    // Public Lightbox API
    fluid.lightbox = {
        /**
         * Returns the default Lightbox order change callback. This callback is used by the Lightbox
         * to send any changes in image order back to the server. It is implemented by nesting
         * a form and set of hidden fields within the Lightbox container which contain the order value
         * for each image displayed in the Lightbox. The default callback submits the form's default 
         * action via AJAX.
         * 
         * @param {Element} lightboxContainer The DOM element containing the form that is POSTed back to the server upon order change 
         */
        defaultOrderChangedCallback: function (lightboxContainer) {
            var reorderform = fluid.utils.findForm (lightboxContainer);
            
            return function () {
                var inputs = fluid.utils.seekNodesById(
                    reorderform, 
                    "input", 
                    "^" + deriveLightboxCellBase (lightboxContainer.id, "[^:]*") + "reorder-index$");
                
                for (var i = 0; i < inputs.length; i = i+1) {
                    inputs[i].value = i;
                }
            
                if (reorderform && reorderform.action) {
                    jQuery.post(reorderform.action, 
                    jQuery(reorderform).serialize(),
                    function (type, data, evt) { /* No-op response */ });
                }
            };
        },
    	
        /**
         * Creates a new Lightbox instance from the specified parameters, providing full control over how
         * the Lightbox is configured.
         * 
         * @param {Element} container The DOM element that represents the Lightbox
         * @param {Function} itemFinderFn A function that returns a list of orderable images
         * @param {Function} orderChangedFn A function that is called when the image order is changed by the user
         * @param {String} instructionMessageId The id of the DOM element containing instructional text for Lightbox users
         * @param {Object} options (optional) extra options for the Reorderer
         */
        createLightbox: function (container, itemFinderFn, options) {
            options = options || {};
            // Remove the anchors from the taborder.
            jQuery ("a", container).tabindex (-1);
            addThumbnailActivateHandler (container);
            
            var orderChangedFn = options.orderChangedCallback || fluid.lightbox.defaultOrderChangedCallback (fluid.unwrap(container));

            var layoutHandler = new fluid.GridLayoutHandler (itemFinderFn, {
                orderChangedCallback: orderChangedFn
            });

            var reordererOptions = {
                role : fluid.roles.GRID
            };            
            fluid.mixin (reordererOptions, options);
            
            return new fluid.Reorderer (container, itemFinderFn, layoutHandler, reordererOptions);
        },
        
        /**
         * Creates a new Lightbox by binding to element ids in the DOM.
         * This provides a convenient way of constructing a Lightbox with the default configuration.
         * 
         * @param {String} containerId The id of the DOM element that represents the Lightbox
         * @param {String} instructionMessageId The id of the DOM element containing instructional text for Lightbox users
         */
        createLightboxFromId: function (containerId, options) {
            var parentNode = document.getElementById (containerId);
            var itemFinder = createItemFinder(parentNode, containerId);
            
            return fluid.lightbox.createLightbox (parentNode, itemFinder, options);
        }
    };
}) (jQuery, document);
