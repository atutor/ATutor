/*
Copyright 2008 University of Cambridge
Copyright 2008 University of Toronto

Licensed under the GNU General Public License or the MIT license.
You may not use this file except in compliance with one these
Licenses.

You may obtain a copy of the GPL and MIT License at
https://source.fluidproject.org/svn/sandbox/tabindex/trunk/LICENSE.txt
*/

/*global jQuery*/

// Tabindex normalization
(function($) {
    // -- Private functions --
    
    var normalizeTabindexName = function() {
        return $.browser.msie ? "tabIndex" : "tabindex";
    };

    var canHaveDefaultTabindex = function(elements) {
       if (elements.length <= 0) {
           return false;
       }

       return jQuery(elements[0]).is("a, input, button, select, area, textarea, object");
    };
    
    var getValue = function(elements) {
        if (elements.length <= 0) {
            return undefined;
        }

        if (!elements.hasTabindexAttr()) {
            return canHaveDefaultTabindex(elements) ? Number(0) : undefined;
        }

        // Get the attribute and return it as a number value.
        var value = elements.attr(normalizeTabindexName());
        return Number(value);
    };

    var setValue = function(elements, toIndex) {
        return elements.each(function(i, item) {
            $(item).attr(normalizeTabindexName(), toIndex);
        });
    };
    
    // -- Public API --
    
    /**
     * Gets the value of the tabindex attribute for the first item, or sets the tabindex value of all elements
     * if toIndex is specified.
     * 
     * @param {String|Number} toIndex
     */
    $.fn.tabindex = function(toIndex) {
        if (toIndex !== null && toIndex !== undefined) {
            return setValue(this, toIndex);
        } else {
            return getValue(this);
        }
    };

    /**
     * Removes the tabindex attribute altogether from each element.
     */
    $.fn.removeTabindex = function() {
        return this.each(function(i, item) {
            $(item).removeAttr(normalizeTabindexName());
        });
    };

    /**
     * Determines if an element actually has a tabindex attribute present.
     */
    $.fn.hasTabindexAttr = function() {
        if (this.length <= 0) {
            return false;
        }

        var attributeNode = this[0].getAttributeNode(normalizeTabindexName());
        return attributeNode ? attributeNode.specified : false;
    };

    /**
     * Determines if an element either has a tabindex attribute or is naturally tab-focussable.
     */
    $.fn.hasTabindex = function() {
        return this.hasTabindexAttr() || canHaveDefaultTabindex(this);
    };
})(jQuery);


// Keyboard navigation
(function($) {    
    // Public, static constants needed by the rest of the library.
    $.a11y = $.a11y || {};

    $.a11y.keys = {
        UP: 38,
        DOWN: 40,
        LEFT: 37,
        RIGHT: 39,
        SPACE: 32,
        ENTER: 13,
        DELETE: 46,
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
    
    var NO_SELECTION = -32768;

    var UP_DOWN_KEYMAP = {
        next: $.a11y.keys.DOWN,
        previous: $.a11y.keys.UP
    };

    var LEFT_RIGHT_KEYMAP = {
        next: $.a11y.keys.RIGHT,
        previous: $.a11y.keys.LEFT
    };

    // Private functions.
    var unwrap = function(element) {
        return element.jquery ? element[0] : element; // Unwrap the element if it's a jQuery.
    };

    var cleanUpWhenLeavingContainer = function(selectionContext) {
        if (selectionContext.onLeaveContainer) {
            selectionContext.onLeaveContainer(
              selectionContext.selectables[selectionContext.activeItemIndex]);
        } else if (selectionContext.onUnselect) {
            selectionContext.onUnselect(
            selectionContext.selectables[selectionContext.activeItemIndex]);
        }

        if (!selectionContext.options.rememberSelectionState) {
            selectionContext.activeItemIndex = NO_SELECTION;
        }
    };

    var checkForModifier = function(binding, evt) {
        // If no modifier was specified, just return true.
        if (!binding.modifier) {
            return true;
        }

        var modifierKey = binding.modifier;
        var isCtrlKeyPresent = modifierKey && evt.ctrlKey;
        var isAltKeyPresent = modifierKey && evt.altKey;
        var isShiftKeyPresent = modifierKey && evt.shiftKey;

        return isCtrlKeyPresent || isAltKeyPresent || isShiftKeyPresent;
    };

    var activationHandler = function(binding) {
        return function(evt) {
// The following 'if' clause works in the real world, but there's a bug in the jQuery simulation
// that causes keyboard simulation to fail in Safari, causing our tests to fail:
//     http://ui.jquery.com/bugs/ticket/3229
// The replacement 'if' clause works around this bug.
// When this issue is resolved, we should revert to the original clause.
//            if (evt.which === binding.key && binding.activateHandler && checkForModifier(binding, evt)) {
            var code = evt.which? evt.which : evt.keyCode;
            if (code === binding.key && binding.activateHandler && checkForModifier(binding, evt)) {
                binding.activateHandler(evt.target, evt);
                evt.preventDefault();
            }
        };
    };

    /**
     * Does the work of selecting an element and delegating to the client handler.
     */
    var drawSelection = function(elementToSelect, handler) {
        if (handler) {
            handler(elementToSelect);
        }
    };

    /**
     * Does does the work of unselecting an element and delegating to the client handler.
     */
    var eraseSelection = function(selectedElement, handler) {
        if (handler && selectedElement) {
            handler(selectedElement);
        }
    };

    var unselectElement = function(selectedElement, selectionContext) {
        eraseSelection(selectedElement, selectionContext.options.onUnselect);
    };

    var selectElement = function(elementToSelect, selectionContext) {
        // It's possible that we're being called programmatically, in which case we should clear any previous selection.
        unselectElement(selectionContext.selectedElement(), selectionContext);

        elementToSelect = unwrap(elementToSelect);
        var newIndex = selectionContext.selectables.index(elementToSelect);

        // Next check if the element is a known selectable. If not, do nothing.
        if (newIndex === -1) {
           return;
        }

        // Select the new element.
        selectionContext.activeItemIndex = newIndex;
        drawSelection(elementToSelect, selectionContext.options.onSelect);
    };

    var selectableFocusHandler = function(selectionContext) {
        return function(evt) {
            selectElement(evt.target, selectionContext);

            // Force focus not to bubble on some browsers.
            return evt.stopPropagation();
        };
    };

    var selectableBlurHandler = function(selectionContext) {
        return function(evt) {
            unselectElement(evt.target, selectionContext);

            // Force blur not to bubble on some browsers.
            return evt.stopPropagation();
        };
    };

    var reifyIndex = function(sc_that) {
        var elements = sc_that.selectables;
        if (sc_that.activeItemIndex >= elements.length) {
            sc_that.activeItemIndex = 0;
        }
        if (sc_that.activeItemIndex < 0 && sc_that.activeItemIndex !== NO_SELECTION) {
            sc_that.activeItemIndex = elements.length - 1;
        }
        if (sc_that.activeItemIndex >= 0) {
            $(elements[sc_that.activeItemIndex]).focus();
        }
    };

    var prepareShift = function(selectionContext) {
        unselectElement(selectionContext.selectedElement(), selectionContext);
        if (selectionContext.activeItemIndex === NO_SELECTION) {
          selectionContext.activeItemIndex = -1;
        }
    };

    var focusNextElement = function(selectionContext) {
        prepareShift(selectionContext);
        ++selectionContext.activeItemIndex;
        reifyIndex(selectionContext);
    };

    var focusPreviousElement = function(selectionContext) {
        prepareShift(selectionContext);
        --selectionContext.activeItemIndex;
        reifyIndex(selectionContext);
    };

    var arrowKeyHandler = function(selectionContext, keyMap, userHandlers) {
        return function(evt) {
            if (evt.which === keyMap.next) {
                focusNextElement(selectionContext);
                evt.preventDefault();
            } else if (evt.which === keyMap.previous) {
                focusPreviousElement(selectionContext);
                evt.preventDefault();
            }
        };
    };

    var getKeyMapForDirection = function(direction) {
        // Determine the appropriate mapping for next and previous based on the specified direction.
        var keyMap;
        if (direction === $.a11y.orientation.HORIZONTAL) {
            keyMap = LEFT_RIGHT_KEYMAP;
        } 
        else if (direction === $.a11y.orientation.VERTICAL) {
            // Assume vertical in any other case.
            keyMap = UP_DOWN_KEYMAP;
        }

        return keyMap;
    };

    var containerFocusHandler = function(selectionContext) {
        return function(evt) {
            var shouldOrig = selectionContext.options.autoSelectFirstItem;
            var shouldSelect = typeof(shouldOrig) === "function" ? 
                 shouldOrig() : shouldOrig;

            // Override the autoselection if we're on the way out of the container.
            if (selectionContext.focusIsLeavingContainer) {
                shouldSelect = false;
            }

            // This target check works around the fact that sometimes focus bubbles, even though it shouldn't.
            if (shouldSelect && evt.target === selectionContext.container.get(0)) {
                if (selectionContext.activeItemIndex === NO_SELECTION) {
                    selectionContext.activeItemIndex = 0;
                }
                $(selectionContext.selectables[selectionContext.activeItemIndex]).focus();
            }

           // Force focus not to bubble on some browsers.
           return evt.stopPropagation();
        };
    };

    var containerBlurHandler = function(selectionContext) {
        return function(evt) {
            selectionContext.focusIsLeavingContainer = false;

            // Force blur not to bubble on some browsers.
            return evt.stopPropagation();
        };
    };

    var makeElementsTabFocussable = function(elements) {
        // If each element doesn't have a tabindex, or has one set to a negative value, set it to 0.
        elements.each(function(idx, item) {
            item = $(item);
            if (!item.hasTabindex() || item.tabindex() < 0) {
                item.tabindex(0);
            }
        });
    };

    var makeElementsActivatable = function(elements, onActivateHandler, defaultKeys, options) {
        // Create bindings for each default key.
        var bindings = [];
        $(defaultKeys).each(function(index, key) {
            bindings.push({
                modifier: null,
                key: key,
                activateHandler: onActivateHandler
            });
        });

        // Merge with any additional key bindings.
        if (options && options.additionalBindings) {
            bindings = bindings.concat(options.additionalBindings);
        }

        // Add listeners for each key binding.
        for (var i = 0; i < bindings.length; ++ i) {
            var binding = bindings[i];
            elements.keydown(activationHandler(binding));
        }
    };

    var tabKeyHandler = function(selectionContext) {
        return function(evt) {
            if (evt.which !== $.a11y.keys.TAB) {
                return;
            }

            cleanUpWhenLeavingContainer(selectionContext);

            // Catch Shift-Tab and note that focus is on its way out of the container.
            if (evt.shiftKey) {
                selectionContext.focusIsLeavingContainer = true;
            }
        };
    };

    var makeElementsSelectable = function(container, defaults, userOptions) {

        var options = $.extend(true, {}, defaults, userOptions);

        var keyMap = getKeyMapForDirection(options.direction);

        var selectableElements = options.selectableElements? options.selectableElements :
              container.find(options.selectableSelector);
          
        // Context stores the currently active item(undefined to start) and list of selectables.
        var that = {
            container: container,
            activeItemIndex: NO_SELECTION,
            selectables: selectableElements,
            focusIsLeavingContainer: false,
            options: options
        };

        that.selectablesUpdated = function(focusedItem) {
          // Remove selectables from the tab order and add focus/blur handlers
            if (typeof(that.options.selectablesTabindex) === "number") {
                that.selectables.tabindex(that.options.selectablesTabindex);
            }
            that.selectables.unbind("focus." + NAMESPACE_KEY);
            that.selectables.unbind("blur." + NAMESPACE_KEY);
            that.selectables.bind("focus."+ NAMESPACE_KEY, selectableFocusHandler(that));
            that.selectables.bind("blur." + NAMESPACE_KEY, selectableBlurHandler(that));
            if (focusedItem) {
                selectElement(focusedItem, that);
            }
            else {
                reifyIndex(that);
            }
        };

        that.refresh = function() {
            if (!that.options.selectableSelector) {
                throw("Cannot refresh selectable context which was not initialised by a selector");
            }
            that.selectables = container.find(options.selectableSelector);
            that.selectablesUpdated();
        };
        
        that.selectedElement = function() {
            return that.activeItemIndex < 0? null : that.selectables[that.activeItemIndex];
        };
        
        // Add various handlers to the container.
        if (keyMap) {
            container.keydown(arrowKeyHandler(that, keyMap));
        }
        container.keydown(tabKeyHandler(that));
        container.focus(containerFocusHandler(that));
        container.blur(containerBlurHandler(that));
        
        that.selectablesUpdated();

        return that;
    };

    var createDefaultActivationHandler = function(activatables, userActivateHandler) {
        return function(elementToActivate) {
            if (!userActivateHandler) {
                return;
            }

            elementToActivate = unwrap(elementToActivate);
            if (activatables.index(elementToActivate) === -1) {
                return;
            }

            userActivateHandler(elementToActivate);
        };
    };

    /**
     * Gets stored state from the jQuery instance's data map.
     */
    var getData = function(aJQuery, key) {
        var data = aJQuery.data(NAMESPACE_KEY);
        return data ? data[key] : undefined;
    };

    /**
     * Stores state in the jQuery instance's data map.
     */
    var setData = function(aJQuery, key, value) {
        var data = aJQuery.data(NAMESPACE_KEY) || {};
        data[key] = value;
        aJQuery.data(NAMESPACE_KEY, data);
    };

    // Public API.
    /**
     * Makes all matched elements available in the tab order by setting their tabindices to "0".
     */
    $.fn.tabbable = function() {
        makeElementsTabFocussable(this);
        return this;
    };

    /**
     * Makes all matched elements selectable with the arrow keys.
     * Supply your own handlers object with onSelect: and onUnselect: properties for custom behaviour.
     * Options provide configurability, including direction: and autoSelectFirstItem:
     * Currently supported directions are jQuery.a11y.directions.HORIZONTAL and VERTICAL.
     */
    $.fn.selectable = function(options) {
        var that = makeElementsSelectable(this, this.selectable.defaults, options);
        setData(this, CONTEXT_KEY, that);
        return this;
    };
    
    $.fn.getSelectableContext = function() {
        return getData(this, CONTEXT_KEY);
    };

    /**
     * Makes all matched elements activatable with the Space and Enter keys.
     * Provide your own handler function for custom behaviour.
     * Options allow you to provide a list of additionalActivationKeys.
     */
    $.fn.activatable = function(fn, options) {
        makeElementsActivatable(this, fn, this.activatable.defaults.keys, options);
        setData(this, ACTIVATE_KEY, createDefaultActivationHandler(this, fn));
        return this;
    };

    /**
     * Selects the specified element.
     */
    $.fn.select = function(elementToSelect) {
        elementToSelect.focus();
        return this;
    };

    /**
     * Selects the next matched element.
     */
    $.fn.selectNext = function() {
        focusNextElement(getData(this, CONTEXT_KEY));
        return this;
    };

    /**
     * Selects the previous matched element.
     */
    $.fn.selectPrevious = function() {
        focusPreviousElement(getData(this, CONTEXT_KEY));
        return this;
    };

    /**
     * Returns the currently selected item wrapped as a jQuery object.
     */
    $.fn.currentSelection = function() {
        var that = getData(this, CONTEXT_KEY);
        return $(that.selectedElement());
    };

    /**
     * Activates the specified element.
     */
    $.fn.activate = function(elementToActivate) {
        var handler = getData(this, ACTIVATE_KEY);
        handler(elementToActivate);
        return this;
    };

    // Public Defaults.
    $.fn.activatable.defaults = {
        keys: [$.a11y.keys.ENTER, $.a11y.keys.SPACE]
    };

    $.fn.selectable.defaults = {
        direction: $.a11y.orientation.VERTICAL,
        selectablesTabindex: -1,
        autoSelectFirstItem: true,
        rememberSelectionState: true,
        selectableSelector: ".selectable",
        selectableElements: null,
        onSelect: null,
        onUnselect: null,
        onLeaveContainer: null
    };
})(jQuery);
