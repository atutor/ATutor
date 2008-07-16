/*
Copyright 2007 University of Toronto

Licensed under the GNU General Public License or the MIT license.
You may not use this file except in compliance with one these
Licenses.

You may obtain a copy of the GPL and MIT License at
https://source.fluidproject.org/svn/sandbox/tabindex/trunk/LICENSE.txt
*/

// Tabindex normalization
(function ($) {
    // -- Private functions --
    
    var normalizeTabindexName = function () {
	    return $.browser.msie ? "tabIndex" : "tabindex";
	};

	var getValue = function (elements) {
        if (elements.length <= 0) {
            return undefined;
        }

		if (!elements.hasTabindexAttr ()) {
		    return canHaveDefaultTabindex (elements) ? Number (0) : undefined;
		}

        // Get the attribute (.attr () doesn't work for tabIndex in IE) and return it as a number value.
		var value = elements[0].getAttribute (normalizeTabindexName ());
		return Number (value);
	};

	var setValue = function (elements, toIndex) {
		return elements.each (function (i, item) {
			$ (item).attr (normalizeTabindexName (), toIndex);
		});
	};

	var canHaveDefaultTabindex = function (elements) {
       if (elements.length <= 0) {
           return false;
       }

	   return jQuery (elements[0]).is ("a, input, button, select, area, textarea, object");
	};
    
    // -- Public API --
    
    /**
     * Gets the value of the tabindex attribute for the first item, or sets the tabindex value of all elements
     * if toIndex is specified.
     * 
     * @param {String|Number} toIndex
     */
    $.fn.tabindex = function (toIndex) {
		if (toIndex !== null && toIndex !== undefined) {
			return setValue (this, toIndex);
		} else {
			return getValue (this);
		}
	};

    /**
     * Removes the tabindex attribute altogether from each element.
     */
	$.fn.removeTabindex = function () {
		return this.each(function (i, item) {
			$ (item).removeAttr (normalizeTabindexName ());
		});
	};

    /**
     * Determines if an element actually has a tabindex attribute present.
     */
	$.fn.hasTabindexAttr = function () {
	    if (this.length <= 0) {
	        return false;
	    }

	    var attributeNode = this[0].getAttributeNode (normalizeTabindexName ());
        return attributeNode ? attributeNode.specified : false;
	};

    /**
     * Determines if an element either has a tabindex attribute or is naturally tab-focussable.
     */
	$.fn.hasTabindex = function () {
        return this.hasTabindexAttr () || canHaveDefaultTabindex (this);
	};
})(jQuery);


// Keyboard navigation
(function ($) {    
    // Public, static constants needed by the rest of the library.
    $.a11y = $.a11y || {};

    $.a11y.keys = {
        UP: 38,
        DOWN: 40,
        LEFT: 37,
        RIGHT: 39,
        SPACE: 32,
        ENTER: 13,
        TAB: 9,
        CTRL: 17,
        SHIFT: 16,
        ALT: 18
    };

    $.a11y.orientation = {
        HORIZONTAL: 0,
        VERTICAL: 1,
        BOTH: 2
    };

    // Private constants.
    var NAMESPACE_KEY = "keyboard-a11y";
    var CONTEXT_KEY = "selectionContext";
    var HANDLERS_KEY = "userHandlers";
    var ACTIVATE_KEY = "defaultActivate";

    var UP_DOWN_KEYMAP = {
        next: $.a11y.keys.DOWN,
        previous: $.a11y.keys.UP
    };

    var LEFT_RIGHT_KEYMAP = {
        next: $.a11y.keys.RIGHT,
        previous: $.a11y.keys.LEFT
    };

    // Private functions.
    var unwrap = function (element) {
        return (element.jquery) ? element[0] : element; // Unwrap the element if it's a jQuery.
    };

    var cleanUpWhenLeavingContainer = function (userHandlers, selectionContext, shouldRememberSelectionState) {
        if (userHandlers.willLeaveContainer) {
            userHandlers.willLeaveContainer (selectionContext.activeItem);
        } else if (userHandlers.willUnselect) {
            userHandlers.willUnselect (selectionContext.activeItem);
        }

        if (!shouldRememberSelectionState) {
            selectionContext.activeItem = null;
        }
    };

    var checkForModifier = function (binding, evt) {
        // If no modifier was specified, just return true.
        if (!binding.modifier) {
            return true;
        }

        var modifierKey = binding.modifier;
        var isCtrlKeyPresent = (modifierKey && evt.ctrlKey);
        var isAltKeyPresent = (modifierKey && evt.altKey);
        var isShiftKeyPresent = (modifierKey && evt.shiftKey);

        return (isCtrlKeyPresent || isAltKeyPresent || isShiftKeyPresent);
    };

    var activationHandler = function (binding) {
        return function (evt) {
            if (evt.which === binding.key && binding.activateHandler && checkForModifier (binding, evt)) {
                binding.activateHandler (evt.target, evt);
                evt.preventDefault ();
            }
        };
    };

    /**
     * Does the work of selecting an element and delegating to the client handler.
     */
    var drawSelection = function (elementToSelect, handler) {
        if (handler) {
            handler (elementToSelect);
        }
    };

    /**
     * Does does the work of unselecting an element and delegating to the client handler.
     */
    var eraseSelection = function (selectedElement, handler) {
        if (handler) {
            handler (selectedElement);
        }
    };

    var unselectElement = function (selectedElement, selectionContext, userHandlers) {
        eraseSelection (selectedElement, userHandlers.willUnselect);
    };

    var selectElement = function (elementToSelect, selectionContext, userHandlers) {
        // It's possible that we're being called programmatically, in which case we should clear any previous selection.
        if (selectionContext.activeItem) {
            unselectElement (selectionContext.activeItem, selectionContext, userHandlers);
        }

        elementToSelect = unwrap (elementToSelect);

        // Next check if the element is a known selectable. If not, do nothing.
        if (selectionContext.selectables.index(elementToSelect) === -1) {
           return;
        }

        // Select the new element.
        selectionContext.activeItem = elementToSelect;
        drawSelection (elementToSelect, userHandlers.willSelect);
    };

    var selectableFocusHandler = function (selectionContext, userHandlers) {
        return function (evt) {
            selectElement (evt.target, selectionContext, userHandlers);

            // Force focus not to bubble on some browsers.
            return evt.stopPropagation ();
        };
    };

    var selectableBlurHandler = function (selectionContext, userHandlers) {
        return function (evt) {
            unselectElement (evt.target, selectionContext, userHandlers);

            // Force blur not to bubble on some browsers.
            return evt.stopPropagation ();
        };
    };

    var focusNextElement = function (selectionContext) {
        var elements = selectionContext.selectables;
        var activeItem = selectionContext.activeItem;

        var currentSelectionIdx = (!activeItem) ? -1 : elements.index (activeItem);
        var nextIndex = currentSelectionIdx + 1;
        nextIndex = (nextIndex >= elements.length) ? nextIndex = 0 : nextIndex; // Wrap around to the beginning if needed.

        elements.eq (nextIndex).focus ();
    };

    var focusPreviousElement = function (selectionContext) {
        var elements = selectionContext.selectables;
        var activeItem = selectionContext.activeItem;

        var currentSelectionIdx = (!activeItem) ? 0 : elements.index (activeItem);
        var previousIndex = currentSelectionIdx - 1;
        previousIndex = (previousIndex < 0) ? elements.length - 1 : previousIndex; // Wrap around to the end if necessary.

        elements.eq (previousIndex).focus ();
    };

    var arrowKeyHandler = function (selectionContext, keyMap, userHandlers) {
        return function (evt) {
            if (evt.which === keyMap.next) {
                focusNextElement (selectionContext);
                evt.preventDefault ();
            } else if (evt.which === keyMap.previous) {
                focusPreviousElement (selectionContext);
                evt.preventDefault ();
            }
        };
    };

    var getKeyMapForDirection = function (direction) {
        // Determine the appropriate mapping for next and previous based on the specified direction.
        var keyMap;
        if (direction === $.a11y.orientation.HORIZONTAL) {
            keyMap = LEFT_RIGHT_KEYMAP;
        } else {
            // Assume vertical in any other case.
            keyMap = UP_DOWN_KEYMAP;
        }

        return keyMap;
    };

    var containerFocusHandler = function (selectionContext, container, shouldAutoSelectFirstChild) {
        return function (evt) {
            var shouldSelect = (shouldAutoSelectFirstChild.constructor === Function) ? shouldAutoSelectFirstChild () : shouldAutoSelectFirstChild;

            // Override the autoselection if we're on the way out of the container.
            if (selectionContext.focusIsLeavingContainer) {
                shouldSelect = false;
            }

            // This target check works around the fact that sometimes focus bubbles, even though it shouldn't.
            if (shouldSelect && evt.target === container.get(0)) {
                if (!selectionContext.activeItem) {
                    focusNextElement (selectionContext);
                } else {
                    jQuery (selectionContext.activeItem).focus ();
                }
            }

           // Force focus not to bubble on some browsers.
           return evt.stopPropagation ();
        };
    };

    var containerBlurHandler = function (selectionContext) {
        return function (evt) {
            selectionContext.focusIsLeavingContainer = false;

            // Force blur not to bubble on some browsers.
            return evt.stopPropagation ();
        };
    };

    var makeElementsTabFocussable = function (elements) {
        // If each element doesn't have a tabindex, or has one set to a negative value, set it to 0.
        elements.each (function (idx, item) {
            item = $ (item);
            if (!item.hasTabindex () || (item.tabindex () < 0)) {
                item.tabindex (0);
            }
        });
    };

    var makeElementsActivatable = function (elements, onActivateHandler, defaultKeys, options) {
        // Create bindings for each default key.
        var bindings = [];
        $ (defaultKeys).each (function (index, key) {
            bindings.push ({
                modifier: null,
                key: key,
                activateHandler: onActivateHandler
            });
        });

        // Merge with any additional key bindings.
        if (options && options.additionalBindings) {
            bindings = bindings.concat (options.additionalBindings);
        }

        // Add listeners for each key binding.
        for (var i = 0; i < bindings.length; i = i + 1) {
            var binding = bindings[i];
            elements.keydown (activationHandler (binding));
        }
    };

    var tabKeyHandler = function (userHandlers, selectionContext, shouldRememberSelectionState) {
        return function (evt) {
            if (evt.which !== $.a11y.keys.TAB) {
                return;
            }

            cleanUpWhenLeavingContainer (userHandlers, selectionContext, shouldRememberSelectionState);

            // Catch Shift-Tab and note that focus is on its way out of the container.
            if (evt.shiftKey) {
                selectionContext.focusIsLeavingContainer = true;
            }
        };
    };

    var makeElementsSelectable = function (container, selectableElements, handlers, defaults, options) {
        // Create empty an handlers and use default options where not specified.
        handlers = handlers || {};
        var mergedOptions = $.extend ({}, defaults, options);

        var keyMap = getKeyMapForDirection (mergedOptions.direction);

        // Context stores the currently active item (undefined to start) and list of selectables.
        var selectionContext = {
            activeItem: undefined,
            selectables: selectableElements,
            focusIsLeavingContainer: false
        };

        // Add various handlers to the container.
        container.keydown (arrowKeyHandler (selectionContext, keyMap, handlers));
        container.keydown (tabKeyHandler (handlers, selectionContext, mergedOptions.rememberSelectionState));
        container.focus (containerFocusHandler (selectionContext, container, mergedOptions.autoSelectFirstItem));
        container.blur (containerBlurHandler (selectionContext));

        // Remove selectables from the tab order and add focus/blur handlers
        selectableElements.tabindex(-1);
        selectableElements.focus (selectableFocusHandler (selectionContext, handlers));
        selectableElements.blur (selectableBlurHandler (selectionContext, handlers));

        return selectionContext;
    };

    var createDefaultActivationHandler = function (activatables, userActivateHandler) {
        return function (elementToActivate) {
            if (!userActivateHandler) {
                return;
            }

            elementToActivate = unwrap (elementToActivate);
            if (activatables.index (elementToActivate) === -1) {
                return;
            }

            userActivateHandler (elementToActivate);
        };
    };

    /**
     * Gets stored state from the jQuery instance's data map.
     */
    var getData = function (aJQuery, key) {
        var data = aJQuery.data (NAMESPACE_KEY);
        return data ? data[key] : undefined;
    };

    /**
     * Stores state in the jQuery instance's data map.
     */
    var setData = function (aJQuery, key, value) {
        var data = aJQuery.data (NAMESPACE_KEY) || {};
        data[key] = value;
        aJQuery.data (NAMESPACE_KEY, data);
    };

    // Public API.
    /**
     * Makes all matched elements available in the tab order by setting their tabindices to "0".
     */
    $.fn.tabbable = function () {
        makeElementsTabFocussable (this);
        return this;
    };

    /**
     * Makes all matched elements selectable with the arrow keys.
     * Supply your own handlers object with willSelect: and willUnselect: properties for custom behaviour.
     * Options provide configurability, including direction: and autoSelectFirstItem:
     * Currently supported directions are jQuery.a11y.directions.HORIZONTAL and VERTICAL.
     */
    $.fn.selectable = function (container, handlers, options) {
        var ctx = makeElementsSelectable ($ (container), this, handlers, this.selectable.defaults, options);
        setData (this, CONTEXT_KEY, ctx);
        setData (this, HANDLERS_KEY, handlers);
        return this;
    };

    /**
     * Makes all matched elements activatable with the Space and Enter keys.
     * Provide your own hanlder function for custom behaviour.
     * Options allow you to provide a list of additionalActivationKeys.
     */
    $.fn.activatable = function (fn, options) {
        makeElementsActivatable (this, fn, this.activatable.defaults.keys, options);
        setData (this, ACTIVATE_KEY, createDefaultActivationHandler (this, fn));
        return this;
    };

    /**
     * Selects the specified element.
     */
    $.fn.select = function (elementToSelect) {
        elementToSelect.focus ();
        return this;
    };

    /**
     * Selects the next matched element.
     */
    $.fn.selectNext = function () {
        focusNextElement (getData (this, CONTEXT_KEY));
        return this;
    };

    /**
     * Selects the previous matched element.
     */
    $.fn.selectPrevious = function () {
        focusPreviousElement (getData (this, CONTEXT_KEY));
        return this;
    };

    /**
     * Returns the currently selected item wrapped as a jQuery object.
     */
    $.fn.currentSelection = function () {
        return $ (getData (this, CONTEXT_KEY).activeItem);
    };

    /**
     * Activates the specified element.
     */
    $.fn.activate = function (elementToActivate) {
        var handler = getData (this, ACTIVATE_KEY);
        handler (elementToActivate);
        return this;
    };

    // Public Defaults.
    $.fn.activatable.defaults = {
        keys: [$.a11y.keys.ENTER, $.a11y.keys.SPACE]
    };

    $.fn.selectable.defaults = {
        direction: this.VERTICAL,
        autoSelectFirstItem: true,
        rememberSelectionState: true
    };
}) (jQuery);
