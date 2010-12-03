/*
Copyright 2007-2009 University of Toronto
Copyright 2007-2010 University of Cambridge

Licensed under the Educational Community License (ECL), Version 2.0 or the New
BSD license. You may not use this file except in compliance with one these
Licenses.

You may obtain a copy of the ECL 2.0 License and BSD License at
https://source.fluidproject.org/svn/LICENSE.txt
*/

// Declare dependencies.
/*global jQuery, fluid_1_2, document*/

fluid_1_2 = fluid_1_2 || {};

(function ($, fluid) {
    
    var defaultAvatarCreator = function (item, cssClass, dropWarning) {
        fluid.dom.cleanseScripts(fluid.unwrap(item));
        var avatar = $(item).clone();
        
        fluid.dom.iterateDom(avatar.get(0), function (node) {
            node.removeAttribute("id");
            if (node.tagName.toLowerCase() === "input") {
                node.setAttribute("disabled", "disabled");
            }
        });
        
        avatar.removeAttr("id");
        avatar.removeClass("ui-droppable");
        avatar.addClass(cssClass);
        
        if (dropWarning) {
            // Will a 'div' always be valid in this position?
            var avatarContainer = $(document.createElement("div"));
            avatarContainer.append(avatar);
            avatarContainer.append(dropWarning);
            avatar = avatarContainer;
        }
        $("body").append(avatar);
        if (!$.browser.safari) {
            // FLUID-1597: Safari appears incapable of correctly determining the dimensions of elements
            avatar.css("display", "block").width(item.offsetWidth).height(item.offsetHeight);
        }
        
        if ($.browser.opera) { // FLUID-1490. Without this detect, curCSS explodes on the avatar on Firefox.
            avatar.hide();
        }
        return avatar;
    };
    
    function bindHandlersToContainer(container, keyDownHandler, keyUpHandler, mouseMoveHandler) {
        var actualKeyDown = keyDownHandler;
        var advancedPrevention = false;

        // FLUID-1598 and others: Opera will refuse to honour a "preventDefault" on a keydown.
        // http://forums.devshed.com/javascript-development-115/onkeydown-preventdefault-opera-485371.html
        if ($.browser.opera) {
            container.keypress(function (evt) {
                if (advancedPrevention) {
                    advancedPrevention = false;
                    evt.preventDefault();
                    return false;
                }
            });
            actualKeyDown = function (evt) {
                var oldret = keyDownHandler(evt);
                if (oldret === false) {
                    advancedPrevention = true;
                }
            };
        }
        container.keydown(actualKeyDown);
        container.keyup(keyUpHandler);
    }
    
    function addRolesToContainer(that) {
        that.container.attr("role", that.options.containerRole.container);
        that.container.attr("aria-multiselectable", "false");
        that.container.attr("aria-readonly", "false");
        that.container.attr("aria-disabled", "false");
    }
    
    function createAvatarId(parentId) {
        // Generating the avatar's id to be containerId_avatar
        // This is safe since there is only a single avatar at a time
        return parentId + "_avatar";
    }
    
    var adaptKeysets = function (options) {
        if (options.keysets && !(options.keysets instanceof Array)) {
            options.keysets = [options.keysets];    
        }
    };
    
    /**
     * @param container - A jQueryable designator for the root node of the reorderer (a selector, a DOM node, or a jQuery instance)
     * @param options - an object containing any of the available options:
     *                  containerRole - indicates the role, or general use, for this instance of the Reorderer
     *                  keysets - an object containing sets of keycodes to use for directional navigation. Must contain:
     *                            modifier - a function that returns a boolean, indicating whether or not the required modifier(s) are activated
     *                            up
     *                            down
     *                            right
     *                            left
     *                  styles - an object containing class names for styling the Reorderer
     *                                  defaultStyle
     *                                  selected
     *                                  dragging
     *                                  hover
     *                                  dropMarker
     *                                  mouseDrag
     *                                  avatar
     *                  avatarCreator - a function that returns a valid DOM node to be used as the dragging avatar
     */
    fluid.reorderer = function (container, options) {
        if (!container) {
            fluid.fail("Reorderer initialised with no container");
        }
        var thatReorderer = fluid.initView("fluid.reorderer", container, options);
        options = thatReorderer.options;
        
        var dropManager = fluid.dropManager();
        
        thatReorderer.layoutHandler = fluid.initSubcomponent(thatReorderer,
            "layoutHandler", [thatReorderer.container, options, dropManager, thatReorderer.dom]);
        
        thatReorderer.activeItem = undefined;

        adaptKeysets(options);
 
        var kbDropWarning = thatReorderer.locate("dropWarning");
        var mouseDropWarning;
        if (kbDropWarning) {
            mouseDropWarning = kbDropWarning.clone();
        }

        var isMove = function (evt) {
            var keysets = options.keysets;
            for (var i = 0; i < keysets.length; i++) {
                if (keysets[i].modifier(evt)) {
                    return true;
                }
            }
            return false;
        };
        
        var isActiveItemMovable = function () {
            return $.inArray(thatReorderer.activeItem, thatReorderer.dom.fastLocate("movables")) >= 0;
        };
        
        var setDropEffects = function (value) {
            thatReorderer.dom.fastLocate("dropTargets").attr("aria-dropeffect", value);
        };
        
        var styles = options.styles;
        
        var noModifier = function (evt) {
            return (!evt.ctrlKey && !evt.altKey && !evt.shiftKey && !evt.metaKey);
        };
        
        var handleDirectionKeyDown = function (evt) {
            var item = thatReorderer.activeItem;
            if (!item) {
                return true;
            }
            var keysets = options.keysets;
            for (var i = 0; i < keysets.length; i++) {
                var keyset = keysets[i];
                var keydir = fluid.keyForValue(keyset, evt.keyCode);
                if (!keydir) {
                    continue;
                }
                var isMovement = keyset.modifier(evt);
                
                var dirnum = fluid.keycodeDirection[keydir];
                var relativeItem = thatReorderer.layoutHandler.getRelativePosition(item, dirnum, !isMovement);
                if (!relativeItem) {
                    continue;
                }
                
                if (isMovement) {
                    var prevent = thatReorderer.events.onBeginMove.fire(item);
                    if (prevent === false) {
                        return false;
                    }
                    if (kbDropWarning.length > 0) {
                        if (relativeItem.clazz === "locked") {
                            thatReorderer.events.onShowKeyboardDropWarning.fire(item, kbDropWarning);
                            kbDropWarning.show();                       
                        }
                        else {
                            kbDropWarning.hide();
                        }
                    }
                    if (relativeItem.element) {
                        thatReorderer.requestMovement(relativeItem, item);
                    }
            
                } else if (noModifier(evt)) {
                    item.blur();
                    $(relativeItem.element).focus();
                }
                return false;
            }
            return true;
        };

        thatReorderer.handleKeyDown = function (evt) {
            if (!thatReorderer.activeItem || thatReorderer.activeItem !== evt.target) {
                return true;
            }
            // If the key pressed is ctrl, and the active item is movable we want to restyle the active item.
            var jActiveItem = $(thatReorderer.activeItem);
            if (!jActiveItem.hasClass(styles.dragging) && isMove(evt)) {
               // Don't treat the active item as dragging unless it is a movable.
                if (isActiveItemMovable()) {
                    jActiveItem.removeClass(styles.selected);
                    jActiveItem.addClass(styles.dragging);
                    jActiveItem.attr("aria-grabbed", "true");
                    setDropEffects("move");
                }
                return false;
            }
            // The only other keys we listen for are the arrows.
            return handleDirectionKeyDown(evt);
        };

        thatReorderer.handleKeyUp = function (evt) {
            if (!thatReorderer.activeItem || thatReorderer.activeItem !== evt.target) {
                return true;
            }
            var jActiveItem = $(thatReorderer.activeItem);
            
            // Handle a key up event for the modifier
            if (jActiveItem.hasClass(styles.dragging) && !isMove(evt)) {
                if (kbDropWarning) {
                    kbDropWarning.hide();
                }
                jActiveItem.removeClass(styles.dragging);
                jActiveItem.addClass(styles.selected);
                jActiveItem.attr("aria-grabbed", "false");
                setDropEffects("none");
                return false;
            }
            
            return false;
        };

        var dropMarker;

        var createDropMarker = function (tagName) {
            var dropMarker = $(document.createElement(tagName));
            dropMarker.addClass(options.styles.dropMarker);
            dropMarker.hide();
            return dropMarker;
        };

        fluid.logEnabled = true;

        thatReorderer.requestMovement = function (requestedPosition, item) {
          // Temporary censoring to get around ModuleLayout inability to update relative to self.
            if (!requestedPosition || fluid.unwrap(requestedPosition.element) === fluid.unwrap(item)) {
                return;
            }
            thatReorderer.events.onMove.fire(item, requestedPosition);
            dropManager.geometricMove(item, requestedPosition.element, requestedPosition.position);
            //$(thatReorderer.activeItem).removeClass(options.styles.selected);
           
            // refocus on the active item because moving places focus on the body
            $(thatReorderer.activeItem).focus();
            
            thatReorderer.refresh();
            
            dropManager.updateGeometry(thatReorderer.layoutHandler.getGeometricInfo());

            thatReorderer.events.afterMove.fire(item, requestedPosition, thatReorderer.dom.fastLocate("movables"));
        };

        var hoverStyleHandler = function (item, state) {
            thatReorderer.dom.fastLocate("grabHandle", item)[state?"addClass":"removeClass"](styles.hover);
        };
        /**
         * Takes a $ object and adds 'movable' functionality to it
         */
        function initMovable(item) {
            var styles = options.styles;
            item.attr("aria-grabbed", "false");

            item.mouseover(
                function () {
                    thatReorderer.events.onHover.fire(item, true);
                }
            );
        
            item.mouseout(
                function () {
                    thatReorderer.events.onHover.fire(item, false);
                }
            );
            var avatar;
        
            thatReorderer.dom.fastLocate("grabHandle", item).draggable({
                refreshPositions: false,
                scroll: true,
                helper: function () {
                    var dropWarningEl;
                    if (mouseDropWarning) {
                        dropWarningEl = mouseDropWarning[0];
                    }
                    avatar = $(options.avatarCreator(item[0], styles.avatar, dropWarningEl));
                    avatar.attr("id", createAvatarId(thatReorderer.container.id));
                    return avatar;
                },
                start: function (e, ui) {
                    var prevent = thatReorderer.events.onBeginMove.fire(item);
                    if (prevent === false) {
                        return false;
                    }
                    var handle = thatReorderer.dom.fastLocate("grabHandle", item)[0];
                    var handlePos = fluid.dom.computeAbsolutePosition(handle);
                    var handleWidth = handle.offsetWidth;
                    var handleHeight = handle.offsetHeight;
                    item.focus();
                    item.removeClass(options.styles.selected);
                    item.addClass(options.styles.mouseDrag);
                    item.attr("aria-grabbed", "true");
                    setDropEffects("move");
                    dropManager.startDrag(e, handlePos, handleWidth, handleHeight);
                    avatar.show();
                },
                stop: function (e, ui) {
                    item.removeClass(options.styles.mouseDrag);
                    item.addClass(options.styles.selected);
                    $(thatReorderer.activeItem).attr("aria-grabbed", "false");
                    var markerNode = fluid.unwrap(dropMarker);
                    if (markerNode.parentNode) {
                        markerNode.parentNode.removeChild(markerNode);
                    }
                    avatar.hide();
                    ui.helper = null;
                    setDropEffects("none");
                    dropManager.endDrag();
                    
                    thatReorderer.requestMovement(dropManager.lastPosition(), item);
                    // refocus on the active item because moving places focus on the body
                    thatReorderer.activeItem.focus();
                },
                handle: thatReorderer.dom.fastLocate("grabHandle", item)
            });
        }
           
        function changeSelectedToDefault(jItem, styles) {
            jItem.removeClass(styles.selected);
            jItem.removeClass(styles.dragging);
            jItem.addClass(styles.defaultStyle);
            jItem.attr("aria-selected", "false");
        }
           
        var selectItem = function (anItem) {
            thatReorderer.events.onSelect.fire(anItem);
            var styles = options.styles;
            // Set the previous active item back to its default state.
            if (thatReorderer.activeItem && thatReorderer.activeItem !== anItem) {
                changeSelectedToDefault($(thatReorderer.activeItem), styles);
            }
            // Then select the new item.
            thatReorderer.activeItem = anItem;
            var jItem = $(anItem);
            jItem.removeClass(styles.defaultStyle);
            jItem.addClass(styles.selected);
            jItem.attr("aria-selected", "true");
        };
   
        var initSelectables = function () {
            var handleBlur = function (evt) {
                changeSelectedToDefault($(this), options.styles);
                return evt.stopPropagation();
            };
        
            var handleFocus = function (evt) {
                selectItem(this);
                return evt.stopPropagation();
            };
            
            var selectables = thatReorderer.dom.fastLocate("selectables");
            for (var i = 0; i < selectables.length; ++ i) {
                var selectable = $(selectables[i]);
                if (!$.data(selectable[0], "fluid.reorderer.selectable-initialised")) { 
                    selectable.addClass(styles.defaultStyle);
            
                    selectable.blur(handleBlur);
                    selectable.focus(handleFocus);
                    selectable.click(function (evt) {
                        var handle = fluid.unwrap(thatReorderer.dom.fastLocate("grabHandle", this));
                        if (fluid.dom.isContainer(handle, evt.target)) {
                            $(this).focus();
                        }
                    });
                    
                    selectable.attr("role", options.containerRole.item);
                    selectable.attr("aria-selected", "false");
                    selectable.attr("aria-disabled", "false");
                    $.data(selectable[0], "fluid.reorderer.selectable-initialised", true);
                }
            }
            if (!thatReorderer.selectableContext) {
                thatReorderer.selectableContext = fluid.selectable(thatReorderer.container, {
                    selectableElements: selectables,
                    selectablesTabindex: thatReorderer.options.selectablesTabindex,
                    direction: null
                });
            }
        };
    
        var dropChangeListener = function (dropTarget) {
            fluid.moveDom(dropMarker, dropTarget.element, dropTarget.position);
            dropMarker.css("display", "");
            if (mouseDropWarning) {
                if (dropTarget.lockedelem) {
                    mouseDropWarning.show();
                }
                else {
                    mouseDropWarning.hide();
                }
            }
        };
    
        var initItems = function () {
            var movables = thatReorderer.dom.fastLocate("movables");
            var dropTargets = thatReorderer.dom.fastLocate("dropTargets");
            initSelectables();
        
            // Setup movables
            for (var i = 0; i < movables.length; i++) {
                var item = movables[i];
                if (!$.data(item, "fluid.reorderer.movable-initialised")) { 
                    initMovable($(item));
                    $.data(item, "fluid.reorderer.movable-initialised", true);
                }
            }

            // In order to create valid html, the drop marker is the same type as the node being dragged.
            // This creates a confusing UI in cases such as an ordered list. 
            // drop marker functionality should be made pluggable. 
            if (movables.length > 0 && !dropMarker) {
                dropMarker = createDropMarker(movables[0].tagName);
            }
            
            dropManager.updateGeometry(thatReorderer.layoutHandler.getGeometricInfo());
            
            dropManager.dropChangeFirer.addListener(dropChangeListener, "fluid.Reorderer");
            // Setup dropTargets
            dropTargets.attr("aria-dropeffect", "none");  

        };


        // Final initialization of the Reorderer at the end of the construction process 
        if (thatReorderer.container) {
            bindHandlersToContainer(thatReorderer.container, 
                thatReorderer.handleKeyDown,
                thatReorderer.handleKeyUp);
            addRolesToContainer(thatReorderer);
            fluid.tabbable(thatReorderer.container);
            initItems();
        }

        if (options.afterMoveCallbackUrl) {
            thatReorderer.events.afterMove.addListener(function () {
                var layoutHandler = thatReorderer.layoutHandler;
                var model = layoutHandler.getModel? layoutHandler.getModel():
                     options.acquireModel(thatReorderer);
                $.post(options.afterMoveCallbackUrl, JSON.stringify(model));
            }, "postModel");
        }
        thatReorderer.events.onHover.addListener(hoverStyleHandler, "style");

        thatReorderer.refresh = function () {
            thatReorderer.dom.refresh("movables");
            thatReorderer.dom.refresh("selectables");
            thatReorderer.dom.refresh("grabHandle", thatReorderer.dom.fastLocate("movables"));
            thatReorderer.dom.refresh("stylisticOffset", thatReorderer.dom.fastLocate("movables"));
            thatReorderer.dom.refresh("dropTargets");
            thatReorderer.events.onRefresh.fire();
            initItems();
            thatReorderer.selectableContext.selectables = thatReorderer.dom.fastLocate("selectables");
            thatReorderer.selectableContext.selectablesUpdated(thatReorderer.activeItem);
        };

        thatReorderer.refresh();

        return thatReorderer;
    };
    
    /**
     * Constants for key codes in events.
     */    
    fluid.reorderer.keys = {
        TAB: 9,
        ENTER: 13,
        SHIFT: 16,
        CTRL: 17,
        ALT: 18,
        META: 19,
        SPACE: 32,
        LEFT: 37,
        UP: 38,
        RIGHT: 39,
        DOWN: 40,
        i: 73,
        j: 74,
        k: 75,
        m: 77
    };
    
    /**
     * The default key sets for the Reorderer. Should be moved into the proper component defaults.
     */
    fluid.reorderer.defaultKeysets = [{
        modifier : function (evt) {
            return evt.ctrlKey;
        },
        up : fluid.reorderer.keys.UP,
        down : fluid.reorderer.keys.DOWN,
        right : fluid.reorderer.keys.RIGHT,
        left : fluid.reorderer.keys.LEFT
    },
    {
        modifier : function (evt) {
            return evt.ctrlKey;
        },
        up : fluid.reorderer.keys.i,
        down : fluid.reorderer.keys.m,
        right : fluid.reorderer.keys.k,
        left : fluid.reorderer.keys.j
    }];
    
    /**
     * These roles are used to add ARIA roles to orderable items. This list can be extended as needed,
     * but the values of the container and item roles must match ARIA-specified roles.
     */  
    fluid.reorderer.roles = {
        GRID: { container: "grid", item: "gridcell" },
        LIST: { container: "list", item: "listitem" },
        REGIONS: { container: "main", item: "article" }
    };
    
    // Simplified API for reordering lists and grids.
    var simpleInit = function (container, layoutHandler, options) {
        options = options || {};
        options.layoutHandler = layoutHandler;
        return fluid.reorderer(container, options);
    };
    
    fluid.reorderList = function (container, options) {
        return simpleInit(container, "fluid.listLayoutHandler", options);
    };
    
    fluid.reorderGrid = function (container, options) {
        return simpleInit(container, "fluid.gridLayoutHandler", options); 
    };
    
    fluid.reorderer.SHUFFLE_GEOMETRIC_STRATEGY = "shuffleProjectFrom";
    fluid.reorderer.GEOMETRIC_STRATEGY         = "projectFrom";
    fluid.reorderer.LOGICAL_STRATEGY           = "logicalFrom";
    fluid.reorderer.WRAP_LOCKED_STRATEGY       = "lockedWrapFrom";
    fluid.reorderer.NO_STRATEGY = null;
    
    fluid.reorderer.relativeInfoGetter = function (orientation, coStrategy, contraStrategy, dropManager, dom) {
        return function (item, direction, forSelection) {
            var dirorient = fluid.directionOrientation(direction);
            var strategy = dirorient === orientation? coStrategy: contraStrategy;
            return strategy !== null? dropManager[strategy](item, direction, forSelection) : null;
        };
    };
    
    fluid.defaults("fluid.reorderer", {
        styles: {
            defaultStyle: "fl-reorderer-movable-default",
            selected: "fl-reorderer-movable-selected",
            dragging: "fl-reorderer-movable-dragging",
            mouseDrag: "fl-reorderer-movable-dragging",
            hover: "fl-reorderer-movable-hover",
            dropMarker: "fl-reorderer-dropMarker",
            avatar: "fl-reorderer-avatar"
        },
        selectors: {
            dropWarning: ".flc-reorderer-dropWarning",
            movables: ".flc-reorderer-movable",
            grabHandle: "",
            stylisticOffset: ""
        },
        avatarCreator: defaultAvatarCreator,
        keysets: fluid.reorderer.defaultKeysets,
        layoutHandler: {
            type: "fluid.listLayoutHandler"
        },
        
        events: {
            onShowKeyboardDropWarning: null,
            onSelect: null,
            onBeginMove: "preventable",
            onMove: null,
            afterMove: null,
            onHover: null,
            onRefresh: null
        },
        
        mergePolicy: {
            keysets: "replace",
            "selectors.selectables": "selectors.movables",
            "selectors.dropTargets": "selectors.movables"
        }
    });


    /*******************
     * Layout Handlers *
     *******************/

    function geometricInfoGetter(orientation, sentinelize, dom) {
        return function () {
            return {
                sentinelize: sentinelize,
                extents: [{
                    orientation: orientation,
                    elements: dom.fastLocate("dropTargets")
                }],
                elementMapper: function (element) {
                    return $.inArray(element, dom.fastLocate("movables")) === -1? "locked": null;
                }
            };
        };
    }
    
    fluid.defaults(true, "fluid.listLayoutHandler", 
        {orientation:         fluid.orientation.VERTICAL,
         containerRole:       fluid.reorderer.roles.LIST,
         selectablesTabindex: -1,
         sentinelize:         true
        });
    
    // Public layout handlers.
    fluid.listLayoutHandler = function (container, options, dropManager, dom) {
        var that = {};

        that.getRelativePosition = 
          fluid.reorderer.relativeInfoGetter(options.orientation, 
                fluid.reorderer.LOGICAL_STRATEGY, null, dropManager, dom);
        
        that.getGeometricInfo = geometricInfoGetter(options.orientation, options.sentinelize, dom);
        
        return that;
    }; // End ListLayoutHandler

    fluid.defaults(true, "fluid.gridLayoutHandler", 
        {orientation:         fluid.orientation.HORIZONTAL,
         containerRole:       fluid.reorderer.roles.GRID,
         selectablesTabindex: -1,
         sentinelize:         false
         });
    /*
     * Items in the Lightbox are stored in a list, but they are visually presented as a grid that
     * changes dimensions when the window changes size. As a result, when the user presses the up or
     * down arrow key, what lies above or below depends on the current window size.
     * 
     * The GridLayoutHandler is responsible for handling changes to this virtual 'grid' of items
     * in the window, and of informing the Lightbox of which items surround a given item.
     */
    fluid.gridLayoutHandler = function (container, options, dropManager, dom) {
        var that = {};

        that.getRelativePosition = 
           fluid.reorderer.relativeInfoGetter(options.orientation, 
                 fluid.reorderer.LOGICAL_STRATEGY, fluid.reorderer.SHUFFLE_GEOMETRIC_STRATEGY, 
                 dropManager, dom);
        
        that.getGeometricInfo = geometricInfoGetter(options.orientation, options.sentinelize, dom);
        
        return that;
    }; // End of GridLayoutHandler

})(jQuery, fluid_1_2);
