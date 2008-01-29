var fluid = fluid || {};

fluid.Lightbox = {
	addThumbnailActivateHandler: function (lightbox) {
		var lightboxContainerElement = lightbox.domNode;
		var enterKeyHandler = function (evt) {
			if (evt.which == fluid.keys.ENTER) {
				var thumbnailAnchors = jQuery ("a", lightbox.activeItem);
				document.location = thumbnailAnchors.attr ('href');
			}
		};
		
		jQuery (lightboxContainerElement).keypress (enterKeyHandler);
	}
};