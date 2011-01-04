/*
Copyright 2007-2009 University of Cambridge
Copyright 2007-2009 University of Toronto
Copyright 2007-2009 University of California, Berkeley

Licensed under the Educational Community License (ECL), Version 2.0 or the New
BSD license. You may not use this file except in compliance with one these
Licenses.

You may obtain a copy of the ECL 2.0 License and BSD License at
https://source.fluidproject.org/svn/LICENSE.txt
*/

/*global jQuery*/
/*global fluid*/
/*global demo*/

var demo = demo || {};
(function (jQuery, fluid) {
	var afterMoveListener = function (thePhotoThatMoved, position, allPhotos) {
		// Loop through each item in the ordered list and update its hidden form field.
		allPhotos.each(function (idx, photo) {
			jQuery(photo).children("a").children("input").val(idx+1);
		});

		//POST it back to the server
		postOrder();
	};

	// Serialize the form and post it back to the server.
	var postOrder = function () {
		var form = jQuery("#reorder-images-form"); // Get the form out of the DOM
		var photoRequest = jQuery(form).serialize(); // Use jQuery to serialize it into a standard form request.

		// Send it back to the server via an AJAX POST request.
		jQuery.ajax({
			type: "post",
			url: form.action, 
			data: photoRequest, 
			complete: function (data, ajaxStatus) {
				// Handle success or failure by being nice to the user.
			}
		});
	};

        
    demo.formBasedImageReorderer = function () {
        var reorderer = fluid.reorderImages("#reorder-images-form", {
            selectors: {
                movables: ".photo_wrapper"
            },
			listeners: {
			   afterMove: afterMoveListener
			}
        });  
    };
})(jQuery, fluid);