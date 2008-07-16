/*
Copyright 2007 - 2008 University of Toronto
Copyright 2007 University of Cambridge

Licensed under the Educational Community License (ECL), Version 2.0 or the New
BSD license. You may not use this file except in compliance with one these
Licenses.

You may obtain a copy of the ECL 2.0 License and BSD License at
https://source.fluidproject.org/svn/LICENSE.txt
*/

// Declare dependencies.
/*global jQuery*/
/*global fluid*/

fluid = fluid || {};

(function (jQuery, fluid) {
    var defaultContainerRole = fluid.roles.LIST;
    var defaultInstructionMessageId = "message-bundle:";
    
    var defaultCssClassNames = {
        defaultStyle: "orderable-default",
        selected: "orderable-selected",
        dragging: "orderable-dragging",
        mouseDrag: "orderable-dragging",
        hover: "orderable-hover",
        dropMarker: "orderable-drop-marker",
        avatar: "orderable-avatar"
    };
    
    var defaultAvatarCreator = function(item, cssClass, dropWarning) {
        var avatar = jQuery (item).clone ();
        avatar.removeAttr ("id");
        jQuery ("[id]", avatar).removeAttr ("id");
        jQuery (":hidden", avatar).remove();
        jQuery ("input", avatar).attr ("disabled", "true");
// dropping in the same column fails if the avatar is considered a droppable.
// droppable ("destroy") should take care of this, but it doesn't seem to remove
// the class, which is what is checked, so we remove it manually
// (see http://dev.jquery.com/ticket/2599)
// 2008-05-12: 2599 has been fixed now in trunk
//                    avatar.droppable ("destroy");
        avatar.removeClass ("ui-droppable");
        avatar.addClass (cssClass);
        
        if (dropWarning) {
            // Will a 'div' always be valid in this position?
            var avatarContainer = jQuery (document.createElement("div"));
            avatarContainer.append(avatar);
            avatarContainer.append(dropWarning);
            return avatarContainer;
        } else {
            return avatar;
        }
    };   
    
    function firstSelectable (findItems) {
        var selectables = fluid.wrap (findItems.selectables());
        if (selectables.length <= 0) {
            return null;
        }
        return selectables[0];
    }
    
    function bindHandlersToContainer (container, focusHandler, keyDownHandler, keyUpHandler, mouseMoveHandler) {
        container.focus (focusHandler);
        container.keydown (keyDownHandler);
        container.keyup (keyUpHandler);
        container.mousemove (mouseMoveHandler);
        // FLUID-143. Disable text selection for the reorderer.
        // ondrag() and onselectstart() are Internet Explorer specific functions.
        // Override them so that drag+drop actions don't also select text in IE.
        if (jQuery.browser.msie) {
            container[0].ondrag = function () { return false; }; 
            container[0].onselectstart = function () { return false; };
        } 
    }
    
    function addRolesToContainer (container, findItems, role) {
        var first = firstSelectable(findItems);
        if (first) {
            container.ariaState ("activedescendent", first.id);
        }
        container.ariaRole (role.container);
        container.ariaState ("multiselectable", "false");
        container.ariaState ("readonly", "false");
        container.ariaState ("disabled", "false");
    }
    
    function changeSelectedToDefault (jItem, cssClasses) {
        jItem.removeClass (cssClasses.selected);
        jItem.addClass (cssClasses.defaultStyle);
        jItem.ariaState("selected", "false");
    }
    
    // This is the start of refactoring the drag and drop code out into its own space. 
    // These are the private stateless functions.
    var dndFunctions = {};
    dndFunctions.findTarget = function (element, dropTargets, avatarId, lastTarget) {
        var isAvatar = function (el) {
            return (el && el.id === avatarId);
        };
            
        var isTargetOrAvatar = function (el) {
            return ((dropTargets.index (el) > -1) || isAvatar (el));
        };

        var target = fluid.utils.findAncestor(element, isTargetOrAvatar);
        
        // If the avatar was the target of the event, use the last known drop target instead.
        if (isAvatar(target)) {
            target = lastTarget;        
        }
        return target;
    };
    dndFunctions.createAvatarId = function (parentId) {
        // Generating the avatar's id to be containerId_avatar
        // This is safe since there is only a single avatar at a time
        return parentId + "_avatar";
    };
    
    var setupKeysets = function (defaultKeysets, userKeysets) {
        // Check if the user has given us an array of keysets or a single keyset.
        if (userKeysets && !(userKeysets instanceof Array)) {
            userKeysets = [userKeysets];    
        }
        return userKeysets || defaultKeysets;
    };
    
    /**
     * @param container - the root node of the Reorderer.
     * @param findItems - a function that returns all of the movable elements in the container OR
     *        findItems - an object containing the functions:
     *                    movables - a function that returns all of the movable elements in the container
     *                    selectables (optional) - a function that returns all of the selectable elements
     *                    dropTargets (optional) - a function that returns all of the elements that can be used as drop targets
     *                    grabHandle (optional) - a function that returns the element within the given movable that is to be used as a 'handle' for the mouse-based drag and drop of the movable. 
     * @param layoutHandler - an instance of a Layout Handler.
     * @param options - an object containing any of the available options:
     *                  role - indicates the role, or general use, for this instance of the Reorderer
     *                  instructionMessageId - the ID of the element containing any instructional messages
     *                  keysets - an object containing sets of keycodes to use for directional navigation. Must contain:
     *                            modifier - a function that returns a boolean, indicating whether or not the required modifier(s) are activated
     *                            up
     *                            down
     *                            right
     *                            left
     *                  cssClassNames - an object containing class names for styling the Reorderer
     *                                  defaultStyle
     *                                  selected
     *                                  dragging
     *                                  hover
     *                                  dropMarker
     *                                  mouseDrag
     *                                  avatar
     *                  avatarCreator - a function that returns a valid DOM node to be used as the dragging avatar
     */
    fluid.Reorderer = function (container, findItems, layoutHandler, options) {
        // Reliable 'this'.
        var thisReorderer = this;
        
        // Basic setup
        this.domNode = jQuery (container);
        this.activeItem = undefined;
        findItems = fluid.utils.adaptFindItems (findItems); // For backwards API compatibility

        // Configure default properties.
        options = options || {};
        var role = options.role || defaultContainerRole;
        var instructionMessageId = options.instructionMessageId || defaultInstructionMessageId;
        var keysets = setupKeysets(fluid.defaultKeysets, options.keysets);
        this.cssClasses = fluid.utils.initCssClassNames (defaultCssClassNames, options.cssClassNames);
        var avatarCreator = options.avatarCreator || defaultAvatarCreator;
        var kbDropWarning = fluid.utils.jById(options.dropWarningId);
        var mouseDropWarning;
        if (kbDropWarning) {
            mouseDropWarning = kbDropWarning.clone();
        }
        
        this.focusActiveItem = function (evt) {
            // If the active item has not been set yet, set it to the first selectable.
            if (!thisReorderer.activeItem) {
                var first = firstSelectable(findItems);
                if (!first) {  
                    return evt.stopPropagation();
                }
                jQuery(first).focus ();
            } else {
                jQuery (thisReorderer.activeItem).focus ();
            }
            return evt.stopPropagation();
        };

        var isMove = function (evt) {
            for (var i = 0; i < keysets.length; i++) {
                if (keysets[i].modifier(evt)) {
                    return true;
                }
            }
            return false;
        };
        
        var isActiveItemMovable = function () {
            return (jQuery.inArray (thisReorderer.activeItem, findItems.movables()) >= 0);
        };
        
        var setDropEffects = function (value) {
            var dropTargets = fluid.wrap (findItems.dropTargets());
            dropTargets.ariaState ("dropeffect", value);
        };
        
        this.handleKeyDown = function (evt) {
            if (!thisReorderer.activeItem || (thisReorderer.activeItem !== evt.target)) {
                return true;
            }
            // If the key pressed is ctrl, and the active item is movable we want to restyle the active item.
            var jActiveItem = jQuery (thisReorderer.activeItem);
            if (!jActiveItem.hasClass(thisReorderer.cssClasses.dragging) && isMove(evt)) {
               // Don't treat the active item as dragging unless it is a movable.
                if (isActiveItemMovable ()) {
                    jActiveItem.removeClass (thisReorderer.cssClasses.selected);
                    jActiveItem.addClass (thisReorderer.cssClasses.dragging);
                    jActiveItem.ariaState ("grab", "true");
                    setDropEffects("move");
                }
                return false;
            }
            // The only other keys we listen for are the arrows.
            return thisReorderer.handleDirectionKeyDown(evt);
        };

        this.handleKeyUp = function (evt) {
            if (!thisReorderer.activeItem || (thisReorderer.activeItem !== evt.target)) {
                return true;
            }
            var jActiveItem = jQuery (thisReorderer.activeItem);
            
            // Handle a key up event for the modifier
            if (jActiveItem.hasClass(thisReorderer.cssClasses.dragging) && !isMove(evt)) {
                if (kbDropWarning) {
                    kbDropWarning.hide();
                }
                jActiveItem.removeClass (thisReorderer.cssClasses.dragging);
                jActiveItem.addClass (thisReorderer.cssClasses.selected);
                jActiveItem.ariaState ("grab", "supported");
                setDropEffects("none");
                return false;
            }
            
            return false;
        };

        var moveItem = function (moveFunc){
            if (isActiveItemMovable ()) {
                moveFunc(thisReorderer.activeItem);
                // refocus on the active item because moving places focus on the body
                thisReorderer.activeItem.focus();
                jQuery(thisReorderer.activeItem).removeClass(thisReorderer.cssClasses.selected);
            }
        };
        
        var noModifier = function (evt) {
            return (!evt.ctrlKey && !evt.altKey && !evt.shiftKey && !evt.metaKey);
        };
        
        var moveItemForKeyCode = function (keyCode, keyset, layoutHandler) {
            var didMove = false;
            switch (keyCode) {
                case keyset.up:
                    moveItem (layoutHandler.moveItemUp);
                    didMove = true;
                    break;
                case keyset.down:
                    moveItem (layoutHandler.moveItemDown);
                    didMove = true;
                    break;
                case keyset.left:
                    moveItem (layoutHandler.moveItemLeft);
                    didMove = true;
                    break;
                case keyset.right:
                    moveItem (layoutHandler.moveItemRight);
                    didMove = true;
                    break;
            }
            
            return didMove;
        };
        
        var focusItemForKeyCode = function(keyCode, keyset, layoutHandler, activeItem){
            var didFocus = false;
            var item;
            switch (keyCode) {
                case keyset.up:
                    item = layoutHandler.getItemAbove (activeItem);
                    didFocus = true;
                    break;
                case keyset.down:
                    item = layoutHandler.getItemBelow (activeItem);
                    didFocus = true;
                    break;
                case keyset.left:
                    item = layoutHandler.getLeftSibling (activeItem);
                    didFocus = true;
                    break;
                case keyset.right:
                    item = layoutHandler.getRightSibling (activeItem);
                    didFocus = true;
                    break;
            }
            jQuery (item).focus ();
            
            return didFocus;
        };
        
        this.handleDirectionKeyDown = function (evt) {
            if (!thisReorderer.activeItem) {
                return true;
            }
            
            for (var i = 0; i < keysets.length; i++) {
                var keyset = keysets[i];
                var didProcessKey = false;
                if (keyset.modifier (evt)) {
                    if (kbDropWarning) {
                        kbDropWarning.hide();
                    }
                    didProcessKey = moveItemForKeyCode (evt.keyCode, keyset, layoutHandler);
            
                } else if (noModifier(evt)) {
                    didProcessKey = focusItemForKeyCode (evt.keyCode, keyset, layoutHandler, thisReorderer.activeItem);
                }
                
                // We got the right key press. Bail right away by swallowing the event.
                if (didProcessKey) {
                    return false;
                }
            }
            
            return true;
        };

        // Drag and drop setup code starts here. This needs to be refactored to be better contained.
        var dropMarker;

        var createDropMarker = function (tagName) {
            var dropMarker = jQuery(document.createElement (tagName));
            dropMarker.addClass (thisReorderer.cssClasses.dropMarker);
            dropMarker.hide();
            return dropMarker;
        };

        // Storing the last target that gets an 'over' event to work around the issue where
        // the avatar is below the mouse pointer and blocks events
        var targetOver;
        // Storing the most recent valid target and drop position to implement correct behaviour for locked modules
        var validTargetAndPos;
        
        /**
         * Creates an event handler for mouse move events that moves, shows and hides the drop marker accordingly
         * @param {Object} dropTargets    a list of valid drop targets
         */
        var createTrackMouse = function (dropTargets){
            dropTargets = fluid.wrap (dropTargets);
            var avatarId = dndFunctions.createAvatarId(thisReorderer.domNode.id);
           
            return function (evt){
                // Bail if we are not over a target
                if (!targetOver) {
                    return;
                }
                
                var target = dndFunctions.findTarget (evt.target, dropTargets, avatarId, targetOver);
                
                if (target) {
                    var position = layoutHandler.dropPosition(target, thisReorderer.activeItem, evt.clientX, evt.pageY);
                    if (position === fluid.position.DISALLOWED) {
                        if (mouseDropWarning) {
                            mouseDropWarning.show();
                        }
                    } 
                    else {
                        if (mouseDropWarning) {
                            mouseDropWarning.hide();
                        }
                        if (position !== fluid.position.USE_LAST_KNOWN) {
                            validTargetAndPos = {
                                target: target,
                                position: position
                            };
                            if (validTargetAndPos.position === fluid.position.BEFORE) {
                                jQuery(target).before(dropMarker);
                            }
                            else if (validTargetAndPos.position === fluid.position.AFTER) {
                                jQuery(target).after(dropMarker);
                            }
                            else if (validTargetAndPos.position === fluid.position.INSIDE) {
                                jQuery(target).append(dropMarker);
                            }
                        }
                        dropMarker.show();
                    }
                }
                else {
                    dropMarker.hide();
                    if (mouseDropWarning) {
                        mouseDropWarning.hide();
                    }
                }
            };
        };

        /**
         * Takes a jQuery object and adds 'movable' functionality to it
         */
        function initMovable (item) {
            item.addClass (thisReorderer.cssClasses.defaultStyle);
            item.ariaState ("grab", "supported");

            item.mouseover ( 
                function () {
                    var handle = jQuery (findItems.grabHandle (item[0]));
                    handle.addClass (thisReorderer.cssClasses.hover);
                }
            );
        
            item.mouseout (  
                function () {
                    var handle = jQuery (findItems.grabHandle (item[0]));
                    handle.removeClass (thisReorderer.cssClasses.hover);
                }
            );
        
            item.draggable ({
                refreshPositions: true,
                scroll: true,
                helper: function () {
                    var dropWarningEl;
                    if (mouseDropWarning) {
                        dropWarningEl = mouseDropWarning[0];
                    }
                    var avatar = jQuery (avatarCreator (item[0], thisReorderer.cssClasses.avatar, dropWarningEl));
                    avatar.attr("id", dndFunctions.createAvatarId(thisReorderer.domNode.id));
                    return avatar;
                },
                start: function (e, ui) {
                    item.focus ();
                    item.removeClass (thisReorderer.cssClasses.selected);
                    item.addClass (thisReorderer.cssClasses.mouseDrag);
                    item.ariaState ("grab", "true");
                    setDropEffects ("move");
                },
                stop: function(e, ui) {
                    item.removeClass (thisReorderer.cssClasses.mouseDrag);
                    item.addClass (thisReorderer.cssClasses.selected);
                    jQuery (thisReorderer.activeItem).ariaState ("grab", "supported");
                    dropMarker.hide();
                    ui.helper = null;
                    targetOver = null;
                    validTargetAndPos = null;
                    setDropEffects ("none");
                    
                    // refocus on the active item because moving places focus on the body
                    thisReorderer.activeItem.focus();
                },
                handle: findItems.grabHandle (item[0])
            });
        }   

        /**
         * Takes a jQuery object and a selector that matches movable items
         */
        function initDropTarget (item, selector) {
            item.ariaState ("dropeffect", "none");

            item.droppable ({
                accept: selector,
                greedy: true,
                tolerance: "pointer",
                over: function (e, ui) {
                    // Store the last target for the case when the avatar gets the mouse move instead of the droppable below it.
                    // We do not want to store the value if the position is 'USE_LAST_KNOWN'
                    var position = layoutHandler.dropPosition(item[0], ui.draggable[0], e.clientX, e.pageY);
                    if (position !== fluid.position.USE_LAST_KNOWN) {
                        targetOver = ui.element[0];
                    }
                },
                drop: function (e, ui) {
                    if (validTargetAndPos) {
                        layoutHandler.mouseMoveItem(ui.draggable[0], validTargetAndPos.target, e.clientX, e.pageY, validTargetAndPos.position);
                    }
                }
            });
        }
   
        var initSelectables = function (selectables) {
            var handleBlur = function (evt) {
                changeSelectedToDefault (jQuery(this), thisReorderer.cssClasses);
                return evt.stopPropagation();
            };
        
            var handleFocus = function (evt) {
                thisReorderer.selectItem (this);
                return evt.stopPropagation();
            };
        
            // set up selectables 
            // Remove the selectables from the taborder
            for (var i = 0; i < selectables.length; i++) {
                var item = jQuery(selectables[i]);
                item.tabindex ("-1");
                item.blur (handleBlur);
                item.focus (handleFocus);
            
                item.ariaRole (role.item);
                item.ariaState ("selected", "false");
                item.ariaState ("disabled", "false");
            }
        };
    
        var initItems = function () {
            var i;
            var movables = fluid.wrap (findItems.movables());
            var dropTargets = fluid.wrap (findItems.dropTargets());
            initSelectables (fluid.wrap (findItems.selectables ()));
        
            // Setup movables
            for (i = 0; i < movables.length; i++) {
                var item = movables[i];
                initMovable (jQuery (item));
            }

            // In order to create valid html, the drop marker is the same type as the node being dragged.
            // This creates a confusing UI in cases such as an ordered list. 
            // drop marker functionality should be made pluggable. 
            if (movables.length > 0) {
                dropMarker = createDropMarker(movables[0].tagName);
            }

            // Create a simple predicate function that will identify items that can be dropped.
            var droppablePredicate = function (potentialDroppable) {
                return (movables.index(potentialDroppable[0]) > -1);    
            };
        
            // Setup dropTargets
            for (i = 0; i < dropTargets.length; i++) {
                initDropTarget (jQuery (dropTargets[i]), droppablePredicate);
            }         
        };

        // Final initialization of the Reorderer at the end of the construction process 
        if (this.domNode) {
            bindHandlersToContainer (this.domNode, 
                thisReorderer.focusActiveItem,
                thisReorderer.handleKeyDown,
                thisReorderer.handleKeyUp,
                createTrackMouse (findItems.dropTargets()));
            addRolesToContainer (this.domNode, findItems, role);
            // ensure that the Reorderer container is in the tab order
            if (!this.domNode.hasTabindex() || (this.domNode.tabindex() < 0)) {
                this.domNode.tabindex("0");
            }
            initItems();
        }
    };
    
    fluid.Reorderer.prototype.selectItem = function (anItem) {
        // Set the previous active item back to its default state.
        if (this.activeItem && this.activeItem !== anItem) {
            changeSelectedToDefault (jQuery (this.activeItem), this.cssClasses);
        }
        // Then select the new item.
        this.activeItem = anItem;
        var jItem = jQuery(anItem);
        jItem.removeClass (this.cssClasses.defaultStyle);
        jItem.addClass (this.cssClasses.selected);
        jItem.ariaState ("selected", "true");
        this.domNode.ariaState ("activedescendent", anItem.id);
    };
    
    var buildFnFromSelector = function (selector, container) {
        return function () {
            return jQuery(selector, container);
        };
    };
    
    var buildFindItems = function (itemSelectors, container) {
        // If a single selector has been passed in we just need to wrap it in a function.
        if (typeof itemSelectors === 'string') {
            return buildFnFromSelector(itemSelectors, container);
        } 

        // This code is very similar to fluid.utils.adaptFindItems. 
        // It would be nice if adaptFindItems could take in either functions or selectors. 
        var findItems = {};
        // TODO: We should check if there is no movable and throw an error.
        findItems.movables = buildFnFromSelector(itemSelectors.movables, container);
        if (itemSelectors.selectables) {
            findItems.selectables = buildFnFromSelector(itemSelectors.selectables, container);
        }
        if (itemSelectors.dropTargets) {
            findItems.dropTargets = buildFnFromSelector(itemSelectors.dropTargets, container);
        }
        if (itemSelectors.grabHandle) {
            findItems.grabHandle = function (item) {
                return jQuery(itemSelectors.grabHandle, item);
            };
        }
        return findItems;
    };
    
    // Simplified API for reordering lists and grids.
    var simpleInit = function (containerSelector, itemSelector, layoutHandlerFn, orderChangedCallback, options) {
        var container = jQuery(containerSelector);
        var itemFinder = buildFindItems(itemSelector, container);
        
        var lOptions = options || {};
        lOptions.orderChangedCallback = orderChangedCallback;
        var layoutHandler = new layoutHandlerFn(itemFinder, lOptions);
        
        return new fluid.Reorderer(container, itemFinder, layoutHandler, options);
    };
    
    fluid.reorderList = function (containerSelector, itemSelector, orderChangedCallback, options) {
        return simpleInit(containerSelector, itemSelector, fluid.ListLayoutHandler, orderChangedCallback, options);
    };
    
    fluid.reorderGrid = function (containerSelector, itemSelector, orderChangedCallback, options) {
        return simpleInit(containerSelector, itemSelector, fluid.GridLayoutHandler, orderChangedCallback, options); 
    };
}) (jQuery, fluid);

/*******************
 * Layout Handlers *
 *******************/
(function (jQuery, fluid) {
    // Shared private functions.
    var moveItem = function (item, relatedItemInfo, position, wrappedPosition) {
        var itemPlacement = position;
        if (relatedItemInfo.hasWrapped) {
            itemPlacement = wrappedPosition;
        }
        
        if (itemPlacement === fluid.position.AFTER) {
            jQuery (relatedItemInfo.item).after (item);
        } else {
            jQuery (relatedItemInfo.item).before (item);
        } 
    };
    
    /**
     * For drag-and-drop during the drag:  is the mouse over the "before" half
     * of the droppable?  In the case of a vertically oriented set of orderables,
     * "before" means "above".  For a horizontally oriented set, "before" means
     * "left of".
     */
    var mousePosition = function (droppableEl, orientation, x, y) {    	
        var mid;
        var isBefore;
        if (orientation === fluid.orientation.VERTICAL) {
            mid = jQuery (droppableEl).offset().top + (droppableEl.offsetHeight / 2);
            isBefore = y < mid;
        } else {
            mid = jQuery (droppableEl).offset().left + (droppableEl.offsetWidth / 2);
            isBefore = x < mid;
        }
        
        return (isBefore ? fluid.position.BEFORE : fluid.position.AFTER);
    };    
    
    var itemInfoFinders = {
        /*
         * A general get{Left|Right}SiblingInfo() given an item, a list of orderables and a direction.
         * The direction is encoded by either a +1 to move right, or a -1 to
         * move left, and that value is used internally as an increment or
         * decrement, respectively, of the index of the given item.
         */
        getSiblingInfo: function (item, orderables, /* NEXT, PREVIOUS */ direction) {
            var index = jQuery (orderables).index (item) + direction;
            var hasWrapped = false;
                
            // Handle wrapping to 'before' the beginning. 
            if (index === -1) {
                index = orderables.length - 1;
                hasWrapped = true;
            }
            // Handle wrapping to 'after' the end.
            else if (index === orderables.length) {
                index = 0;
                hasWrapped = true;
            } 
            // Handle case where the passed-in item is *not* an "orderable"
            // (or other undefined error).
            //
            else if (index < -1 || index > orderables.length) {
                index = 0;
            }
            
            return {item: orderables[index], hasWrapped: hasWrapped};
        },

        /*
         * Returns an object containing the item that is to the right of the given item
         * and a flag indicating whether or not the process has 'wrapped' around the end of
         * the row that the given item is in
         */
        getRightSiblingInfo: function (item, orderables) {
            return this.getSiblingInfo (item, orderables, fluid.direction.NEXT);
        },
        
        /*
         * Returns an object containing the item that is to the left of the given item
         * and a flag indicating whether or not the process has 'wrapped' around the end of
         * the row that the given item is in
         */
        getLeftSiblingInfo: function (item, orderables) {
            return this.getSiblingInfo (item, orderables, fluid.direction.PREVIOUS);
        },
        
        /*
         * Returns an object containing the item that is below the given item in the current grid
         * and a flag indicating whether or not the process has 'wrapped' around the end of
         * the column that the given item is in. The flag is necessary because when an image is being
         * moved to the resulting item location, the decision of whether or not to insert before or
         * after the item changes if the process wrapped around the column.
         */
        getItemInfoBelow: function (inItem, orderables) {
            var curCoords = jQuery (inItem).offset();
            var i, iCoords;
            var firstItemInColumn, currentItem;
            
            for (i = 0; i < orderables.length; i++) {
                currentItem = orderables [i];
                iCoords = jQuery (orderables[i]).offset();
                if (iCoords.left === curCoords.left) {
                    firstItemInColumn = firstItemInColumn || currentItem;
                    if (iCoords.top > curCoords.top) {
                        return {item: currentItem, hasWrapped: false};
                    }
                }
            }
    
            firstItemInColumn = firstItemInColumn || orderables [0];
            return {item: firstItemInColumn, hasWrapped: true};
        },
        
        /*
         * Returns an object containing the item that is above the given item in the current grid
         * and a flag indicating whether or not the process has 'wrapped' around the end of
         * the column that the given item is in. The flag is necessary because when an image is being
         * moved to the resulting item location, the decision of whether or not to insert before or
         * after the item changes if the process wrapped around the column.
         */
         getItemInfoAbove: function (inItem, orderables) {
            var curCoords = jQuery (inItem).offset();
            var i, iCoords;
            var lastItemInColumn, currentItem;
            
            for (i = orderables.length - 1; i > -1; i--) {
                currentItem = orderables [i];
                iCoords = jQuery (orderables[i]).offset();
                if (iCoords.left === curCoords.left) {
                    lastItemInColumn = lastItemInColumn || currentItem;
                    if (curCoords.top > iCoords.top) {
                        return {item: currentItem, hasWrapped: false};
                    }
                }
            }
    
            lastItemInColumn = lastItemInColumn || orderables [0];
            return {item: lastItemInColumn, hasWrapped: true};
        }
    
    };
    
    // Public layout handlers.
    fluid.ListLayoutHandler = function (findItems, options) {
        findItems = fluid.utils.adaptFindItems (findItems);
        var orderChangedCallback = function () {};
        var orientation = fluid.orientation.VERTICAL;
        if (options) {
            orderChangedCallback = options.orderChangedCallback || orderChangedCallback;
            orientation = options.orientation || orientation;
        }
                
        this.getRightSibling = function (item) {
            return itemInfoFinders.getRightSiblingInfo (item, findItems.selectables ()).item;
        };
        
        this.moveItemRight = function (item) {
        	var rightSiblingInfo = itemInfoFinders.getRightSiblingInfo (item, findItems.movables ());
            moveItem (item, rightSiblingInfo, fluid.position.AFTER, fluid.position.BEFORE);
            orderChangedCallback();
        };
    
        this.getLeftSibling = function (item) {
            return itemInfoFinders.getLeftSiblingInfo(item, findItems.selectables ()).item;
        };
    
        this.moveItemLeft = function (item) {
         	var leftSiblingInfo = itemInfoFinders.getLeftSiblingInfo (item, findItems.movables ());
            moveItem (item, leftSiblingInfo, fluid.position.BEFORE, fluid.position.AFTER);
            orderChangedCallback();
        };
    
        this.getItemBelow = this.getRightSibling;
    
        this.getItemAbove = this.getLeftSibling;
        
        this.moveItemUp = this.moveItemLeft;
        
        this.moveItemDown = this.moveItemRight;
    
        this.dropPosition = function (target, moving, x, y) {
            return mousePosition (target, orientation, x, y);
        };
        
        this.mouseMoveItem = function (moving, target, x, y) {
            var whereTo = this.dropPosition (target, moving, x, y);
            if (whereTo === fluid.position.BEFORE) {
                jQuery (target).before (moving);
            } else if (whereTo === fluid.position.AFTER) {
                jQuery (target).after (moving);
            }
            orderChangedCallback();
        };
        
    }; // End ListLayoutHandler
    
	/*
	 * Items in the Lightbox are stored in a list, but they are visually presented as a grid that
	 * changes dimensions when the window changes size. As a result, when the user presses the up or
	 * down arrow key, what lies above or below depends on the current window size.
	 * 
	 * The GridLayoutHandler is responsible for handling changes to this virtual 'grid' of items
	 * in the window, and of informing the Lightbox of which items surround a given item.
	 */
	fluid.GridLayoutHandler = function (findItems, options) {
        fluid.ListLayoutHandler.call (this, findItems, options);

        findItems = fluid.utils.adaptFindItems (findItems);
        
        var orderChangedCallback = function () {};
        if (options) {
            orderChangedCallback = options.orderChangedCallback || orderChangedCallback;
        }
        
        var orientation = fluid.orientation.HORIZONTAL;
                
	    this.getItemBelow = function(item) {
	        return itemInfoFinders.getItemInfoBelow (item, findItems.selectables ()).item;
	    };
	
	    this.moveItemDown = function (item) {
	    	var itemBelow = itemInfoFinders.getItemInfoBelow (item, findItems.movables ());
	        moveItem (item, itemBelow, fluid.position.AFTER, fluid.position.BEFORE);
            orderChangedCallback(); 
	    };
	            
	    this.getItemAbove = function (item) {
	        return itemInfoFinders.getItemInfoAbove (item, findItems.selectables ()).item;   
	    }; 
	    
	    this.moveItemUp = function (item) {
	    	var itemAbove = itemInfoFinders.getItemInfoAbove (item, findItems.movables ());
	        moveItem (item, itemAbove, fluid.position.BEFORE, fluid.position.AFTER);
            orderChangedCallback(); 
	    };
	                
	    // We need to override ListLayoutHandler.dropPosition to ensure that the local private
	    // orientation is used.
        this.dropPosition = function (target, moving, x, y) {
            return mousePosition (target, orientation, x, y);
        };
        
	}; // End of GridLayoutHandler
    
    var defaultWillShowKBDropWarning = function (item, dropWarning) {
        if (dropWarning) {
            var offset = jQuery(item).offset();
            dropWarning = jQuery(dropWarning);
            dropWarning.css("position", "absolute");
            dropWarning.css("top", offset.top);
            dropWarning.css("left", offset.left);
        }
    };

    /*
     * Module Layout Handler for reordering content modules.
     * 
     * General movement guidelines:
     * 
     * - Arrowing sideways will always go to the top (moveable) module in the column
     * - Moving sideways will always move to the top available drop target in the column
     * - Wrapping is not necessary at this first pass, but is ok
     */
    fluid.ModuleLayoutHandler = function (layout, targetPerms, options) {
        var orientation = fluid.orientation.VERTICAL;
        
        // Configure optional parameters
        targetPerms = targetPerms || fluid.moduleLayout.buildEmptyPerms(layout);
        options = options || {};
        var orderChangedCallback = options.orderChangedCallback || function () {};
        if (options.orderChangedCallbackUrl) {
            // Create the orderChangedCallback function
            orderChangedCallback = function () {
                jQuery.post (options.orderChangedCallbackUrl, 
                    JSON.stringify (layout),
                    function (data, textStatus) { 
                        targetPerms = data; 
                    }, 
                    "json");
            };
        } 
        var dropWarning = fluid.utils.jById(options.dropWarningId);
        var willShowKBDropWarning = options.willShowKBDropWarning || defaultWillShowKBDropWarning;
        
        // Private Methods.
        /*
	     * Find an item's sibling in the vertical direction based on the
	     * layout.  This assumes that there is no wrapping the top and
	     * bottom of the columns, and returns the given item if at top
	     * and seeking the previous item, or at the bottom and seeking
	     * the next item.
	     */
	    var getVerticalSibling = function (item, /* NEXT, PREVIOUS */ direction) {
	    	var siblingId = fluid.moduleLayout.itemAboveBelow (item.id, direction, layout);
            return fluid.utils.jById (siblingId)[0];
	    };
	
	    /*
	     * Find an item's sibling in the horizontal direction based on the
	     * layout.  This assumes that there is no wrapping the ends of
	     * the rows, and returns the given item if left most and
	     * seeking the previous item, or if right most and seeking
	     * the next item.
	     */
	    var getHorizontalSibling = function (item, /* NEXT, PREVIOUS */ direction) {
	        var itemId = fluid.moduleLayout.firstItemInAdjacentColumn (item.id, direction, layout);
	        return fluid.utils.jById (itemId)[0];
        };
	    	    
        // This should probably be part of the public API so it can be configured.
        var move = function (item, relatedItem, position /* BEFORE, AFTER or INSIDE */) {
            if (!item || !relatedItem) {
                return;
            }           
            if (position === fluid.position.BEFORE) {
                jQuery(relatedItem).before(item);
            } else if (position === fluid.position.AFTER) {
                jQuery(relatedItem).after(item);
            } else if (position === fluid.position.INSIDE) {
                jQuery(relatedItem).append(item);
            }  // otherwise it's either DISALLOWED or USE_LAST_KNOWN
            
            fluid.moduleLayout.updateLayout (item.id, relatedItem.id, position, layout);
            orderChangedCallback (); 
        };
        
        var moveHorizontally = function (item, direction /* PREVIOUS, NEXT */) {
            var targetInfo = fluid.moduleLayout.findTarget (item.id, direction, layout, targetPerms);
            var targetItem = fluid.utils.jById (targetInfo.id)[0];
            move (item, targetItem, targetInfo.position);
        };
        
        var moveVertically = function (item, targetFunc) {
            var targetAndPos = targetFunc(item.id, layout, targetPerms);
            var target = fluid.utils.jById(targetAndPos.id)[0]; 
            if (targetAndPos.position === fluid.position.DISALLOWED) {
                if (dropWarning) {
                    willShowKBDropWarning(item, dropWarning[0]);
                    dropWarning.show();
                }
            } else if (targetAndPos.position !== fluid.position.USE_LAST_KNOWN) {
                move(item, target, targetAndPos.position);
            }
        };
        
        // Public Methods
	    this.getRightSibling = function (item) {
	        return getHorizontalSibling (item, fluid.direction.NEXT);
	    };
	    
	    this.moveItemRight = function (item) {
	    	moveHorizontally (item, fluid.direction.NEXT);
	    };
	
	    this.getLeftSibling = function (item) {
	        return getHorizontalSibling (item, fluid.direction.PREVIOUS);
	    };
	
	    this.moveItemLeft = function (item) {
            moveHorizontally (item, fluid.direction.PREVIOUS);
	    };
	
	    this.getItemAbove = function (item) {
	    	return getVerticalSibling (item, fluid.direction.PREVIOUS);
	    };
	    
	    this.moveItemUp = function (item) {
            moveVertically(item, fluid.moduleLayout.targetAndPositionAbove);
	    };
	        
	    this.getItemBelow = function (item) {
	    	return getVerticalSibling (item, fluid.direction.NEXT);
	    };
	
	    this.moveItemDown = function (item) {
            moveVertically(item, fluid.moduleLayout.targetAndPositionBelow);
	    };
	    
        this.dropPosition = function (target, moving, x, y) {
            if (fluid.moduleLayout.isColumn (target.id, layout)) {
                var lastItemInColId = fluid.moduleLayout.lastItemInCol(target.id, layout);
                if (lastItemInColId === undefined) {
                    return fluid.position.INSIDE;
                }
                var lastItem = fluid.utils.jById(lastItemInColId);
                var topOfEmptySpace = lastItem.offset().top + lastItem.height();
                
                if (y > topOfEmptySpace) {
                    return fluid.position.INSIDE;
                } else {
                    return fluid.position.USE_LAST_KNOWN;
                }
            }
            
            var position = mousePosition (target, orientation, x, y);
            var canDrop = fluid.moduleLayout.canMove (moving.id, target.id, position, layout, targetPerms);
	    	if (canDrop) {
                return position;
	    	}
	    	else {
	    		return fluid.position.DISALLOWED;
	    	}
        };

        this.mouseMoveItem = function (moving, target, x, y, position) {
            move(moving, target, position);
        };
        
    }; // End ModuleLayoutHandler
}) (jQuery, fluid);

fluid.moduleLayout = function (jQuery, fluid) {
    var internals = {
        layoutWalker: function (fn, layout) {
            for (var col = 0; col < layout.columns.length; col++) {
                var idsInCol = layout.columns[col].children;
                for (var i = 0; i < idsInCol.length; i++) {
                    var fnReturn = fn (idsInCol, i, col);
                    if (fnReturn) {
                        return fnReturn;
                    }
                }
            }
        },
        
        /**
         * Calculate the location of the item and the column in which it resides.
         * @return  An object with column index and item index (within that column) properties.
         *          These indices are -1 if the item does not exist in the grid.
         */
        findColumnAndItemIndices: function (itemId, layout) {
            var findIndices = function (idsInCol, index, col) {
                if (idsInCol[index] === itemId) {
                    return {columnIndex: col, itemIndex: index};
                }  
            };
            
            var indices = internals.layoutWalker (findIndices, layout);
            return indices || { columnIndex: -1, itemIndex: -1 };
        },
        
        findColIndex: function (colId, layout) {
            for (var col = 0; col < layout.columns.length; col++ ) {
                if (colId === layout.columns[col].id) {
                    return col;
                }
            }
            return -1;
        },
        
        findItemIndex: function (itemId, layout) {
            return internals.findColumnAndItemIndices (itemId, layout).itemIndex;
        },
        
        numColumns: function (layout) {
            return layout.columns.length;
        },
        
        numModules: function (layout) {
            var numModules = 0;
            for (var col = 0; col < layout.columns.length; col++) {
                numModules += layout.columns[col].children.length;
            }
            return numModules;
        },
        
        isColumnIndex: function (index, layout) {
            return (index < layout.columns.length) && (index >= 0);
        },
        
        /**
         * Returns targetIndex
         * This could have been written in two functions for clarity however it gets called a lot and 
         * the two functions were considerably less performant then this single function.
         * 
         * Item index is the row in the permissions object pertaining to the item.
         * Target index is the column in the permission object refering to the postion before or after the target.
         */
        findItemAndTargetIndices: function (itemId, targetId, position, layout) {
            var columns = layout.columns;
            
            // Default to not found.
            var foundIndices = {
                itemIndex: -1,
                targetIndex: -1
            };
            
            // If the ids are invalid, bail immediately.
            if (!itemId || !targetId) {            
                return foundIndices;
            }

            var itemIndexCounter = 0;
            var targetIndexCounter = position;
            
            for (var i = 0; i < columns.length; i++) {
                var idsInCol = columns[i].children;
                for (var j = 0; j < idsInCol.length; j++) {
                    var currId = idsInCol[j];                    
                    if (currId === itemId) {
                        foundIndices.itemIndex = itemIndexCounter; 
                    }
                    if (currId === targetId) {
                        foundIndices.targetIndex = targetIndexCounter; 
                    }
                    
                    // Check if we're done, and if so, bail early.
                    if (foundIndices.itemIndex >= 0 && foundIndices.targetIndex >= 0) {
                        return foundIndices;
                    }
                    
                    // Increment our index counters and keep searching.
                    itemIndexCounter++;
                    targetIndexCounter++;
                }
                
                // Make sure we account for the additional drop target at the end of a column.
                targetIndexCounter++;
            }

            return foundIndices;     
        },
        
        /**
         * Return the item in the given column (index) and at the given position (index)
         * in that column.  If either of the column or item index is out of bounds, this
         * returns null.
         */
        getItemAt: function (columnIndex, itemIndex, layout) {
            var itemId = null;
            var cols = layout.columns;
            
            if (columnIndex >= 0 && columnIndex < cols.length) {
                var idsInCol = cols[columnIndex].children;
                if (itemIndex >= 0 && itemIndex < idsInCol.length) {
                    itemId = idsInCol[itemIndex];
                }
            }
            
            return itemId;
        },
        
        canItemMove: function (itemIndex, perms) {
            var itemPerms = perms[itemIndex];
            for (var i = 0; i < itemPerms.length; i++) {
                if (itemPerms[i] === 1) {
                    return true;
                }
            }
            return false;
        }, 
        
        isDropTarget: function (beforeTargetIndex, perms) {
            for (var i = 0; i < perms.length; i++) {
                if (perms[i][beforeTargetIndex] === 1 || perms[i][beforeTargetIndex + 1] === 1) {
                    return true;
                }
            }
            return false;
        },
        
        targetAndPos: function(itemId, position, layout, perms){
            var inc = (position === fluid.position.BEFORE) ? -1 : 1;            
            var startCoords = internals.findColumnAndItemIndices (itemId, layout);
            var defaultTarg = {
                    id: itemId,
                    position: fluid.position.USE_LAST_KNOWN
                };
            
            // If invalid column, return USE_LAST_KNOWN
            if (startCoords.columnIndex < 0) {
                return defaultTarg;
            }
            
            // Loop thru the target column's items, starting with the item adjacent to the given item,
            // looking for an item that can be moved to.
            var idsInCol = layout.columns[startCoords.columnIndex].children;
            var firstTarg;
            for (var i = startCoords.itemIndex + inc; i > -1 && i < idsInCol.length; i = i + inc) {
                var targetId = idsInCol[i];
                if (fluid.moduleLayout.canMove (itemId, targetId, position, layout, perms)) {
                    // Found a valid move - return
                    return {
                        id: targetId,
                        position: position
                    };
                } else if (!firstTarg) {
                    firstTarg = { id: targetId, position: fluid.position.DISALLOWED};
                }
            }
        
            // Didn't find a valid move so return the first target
            return firstTarg || defaultTarg;                        
        },
            
        findPortletsInColumn: function (portlets, column) {
            var portletsForColumn = [];
            portlets.each(function (idx, portlet) {
                if (jQuery("[id=" + portlet.id + "]", column)[0]) {
                    portletsForColumn.push(portlet);
                }
            });
            
            return portletsForColumn;
        },
    
    	columnStructure: function (column, portletsInColumn) {
            var structure = {};
            structure.id = column.id;
            structure.children = [];
            jQuery(portletsInColumn).each(function (idx, portlet) {
                structure.children.push(portlet.id);
            });
            
            return structure;
        }

    };   
    
	// Public API.
    return {
        internals: internals,

        isColumn: function (id, layout) {
            var colIndex = internals.findColIndex(id, layout);
            return (colIndex > -1);
        },
        
       /**
        * Determine if a given item can move before or after the given target position, given the
        * permissions information.
        */
    	canMove: function (itemId, targetItemId, position, layout, perms) {
    	    if ((position === fluid.position.USE_LAST_KNOWN) || (position === fluid.position.DISALLOWED)) {
    	        return false;
    	    }
    	    if (position === fluid.position.INSIDE) {
    	        return true;
    	    }
    		var indices = internals.findItemAndTargetIndices (itemId, targetItemId, position, layout);
            return (!!perms[indices.itemIndex][indices.targetIndex]); 
        },
        
        /**
         * Given an item id, and a direction, find the top item in the next/previous column.
         */
        firstItemInAdjacentColumn: function (itemId, /* PREVIOUS, NEXT */ direction, layout) {
            var findItemInAdjacentCol = function (idsInCol, index, col) {
                var id = idsInCol[index];
                if (id === itemId) {
                    var adjacentCol = col + direction;
                    var adjacentItem = internals.getItemAt (adjacentCol, 0, layout);
                    // if there are no items in the adjacent column, keep checking further columns
                    while (!adjacentItem) {
                        adjacentCol = adjacentCol + direction;
                        if (internals.isColumnIndex(adjacentCol, layout)) {
                            adjacentItem = internals.getItemAt (adjacentCol, 0, layout);
                        } else {
                            adjacentItem = itemId;
                        }
                    }
                    return adjacentItem; 
                //    return internals.getItemAt (adjacentCol, 0, layout);
                }
            };
            
            return internals.layoutWalker (findItemInAdjacentCol, layout) || itemId; 
        }, 
        
        /**
         * Return the item above/below the given item within that item's column.  If at
         * bottom of column or at top, return the item itelf (no wrapping).
         */
        itemAboveBelow: function (itemId, /*PREVIOUS, NEXT*/ direction, layout) {
            var findItemAboveBelow = function (idsInCol, index) {
                if (idsInCol[index] === itemId) {
                    var siblingIndex = index + direction;
                    if ((siblingIndex < 0) || (siblingIndex >= idsInCol.length)) {
                        return itemId;
                    } else {
                        return idsInCol[siblingIndex];
                    }
                }
        	};

            return internals.layoutWalker (findItemAboveBelow, layout) || itemId;
        },
        
        /**
         * Move an item within the layout object. 
         */
        updateLayout: function (itemId, targetId, position, layout) {
            if (!itemId || !targetId || itemId === targetId) { 
                return; 
            }
            var itemIndices = internals.findColumnAndItemIndices (itemId, layout);
            layout.columns[itemIndices.columnIndex].children.splice (itemIndices.itemIndex, 1);
            var targetCol;
            if (position === fluid.position.INSIDE) {
                targetCol = layout.columns[internals.findColIndex (targetId, layout)].children;
                targetCol.splice (targetCol.length, 0, itemId);

            } else {
                var relativeItemIndices = internals.findColumnAndItemIndices (targetId, layout);
                targetCol = layout.columns[relativeItemIndices.columnIndex].children;
                targetCol.splice (relativeItemIndices.itemIndex + position, 0, itemId);
            }

        },
        
        /**
         * Find the first target that can be moved to in the given column, possibly moving to the end
         * of the column if there are no valid drop targets. 
         * @return Object containing id (the id of the target) and position (relative to the target)
         */
        findTarget: function (itemId, /* NEXT, PREVIOUS */ direction, layout, perms) {
            var targetColIndex = internals.findColumnAndItemIndices (itemId, layout).columnIndex + direction;
            var targetCol = layout.columns[targetColIndex];
			
            // If column is invalid, bail returning the current position.
            if (targetColIndex < 0 || targetColIndex >= internals.numColumns (layout)) {
                return { id: itemId, position: fluid.position.BEFORE };               
            }
            
            // Loop thru the target column's items, looking for the first item that can be moved to.
            var idsInCol = targetCol.children;
            for (var i = 0; (i < idsInCol.length); i++) {
                var targetId = idsInCol[i];
                if (fluid.moduleLayout.canMove (itemId, targetId, fluid.position.BEFORE, layout, perms)) {
                    return { id: targetId, position: fluid.position.BEFORE };
                }
                else if (fluid.moduleLayout.canMove (itemId, targetId, fluid.position.AFTER, layout, perms)) {
                    return { id: targetId, position: fluid.position.AFTER };
                }
            }
			
            // no valid modules found, so target is the column itself
            return { id: targetCol.id, position: fluid.position.INSIDE };
        },

        /**
         * Returns a valid drop target and position above the item being moved.
         * @param {Object} itemId The id of the item being moved
         * @param {Object} layout 
         * @param {Object} perms
         * @returns {Object} id: the target id, position: a 'fluid.position' value relative to the target
         */
        targetAndPositionAbove: function (itemId, layout, perms) {
            return internals.targetAndPos (itemId, fluid.position.BEFORE, layout, perms);
        },
        
        /**
         * Returns a valid drop target and position below the item being moved.
         * @param {Object} itemId The id of the item being moved
         * @param {Object} layout 
         * @param {Object} perms
         * @returns {Object} id: the target id, position: a 'fluid.position' value relative to the target
         */
        targetAndPositionBelow: function (itemId, layout, perms) {
            return internals.targetAndPos (itemId, fluid.position.AFTER, layout, perms);
        },
        
        /**
         * Determine the moveables, selectables, and drop targets based on the information
         * in the layout and permission objects.
         */
        createFindItems: function (layout, perms, grabHandle) {
            perms = perms || fluid.moduleLayout.buildEmptyPerms(layout);
            var findItems = {};
            findItems.grabHandle = grabHandle;
            
            var selectablesSelector;
            var movablesSelector;
            var dropTargets;
            
            var cols = layout.columns;
            for (var i = 0; i < cols.length; i++) {
                var idsInCol = cols[i].children;
                for (var j = 0; j < idsInCol.length; j++) {
                    var itemId = idsInCol[j];
                    var idSelector = "[id=" + itemId  + "]";
                    selectablesSelector = selectablesSelector ? selectablesSelector + "," + idSelector : idSelector;
                    
                    var indices = internals.findItemAndTargetIndices (itemId, itemId, fluid.position.BEFORE, layout);
                    if (internals.canItemMove (indices.itemIndex, perms)) {
                        movablesSelector = movablesSelector ? movablesSelector + "," + idSelector : idSelector; 
                    }
                    if (internals.isDropTarget (indices.targetIndex, perms)) {
                    	dropTargets = dropTargets ? dropTargets + "," + idSelector : idSelector;
                    }
                }
                // now add the column itself
                var colIdSelector = "[id=" + cols[i].id  + "]";
                dropTargets = dropTargets ? dropTargets + "," + colIdSelector : colIdSelector;
            }
            
            findItems.selectables = function () {
                return jQuery (selectablesSelector);
            };
            
            findItems.movables = function () {
                return jQuery (movablesSelector);
            };

            findItems.dropTargets = function() {
                return jQuery (dropTargets);
            };
                      
            return findItems;
        },
        
        containerId: function (layout) {
            return layout.id;
        },
        
        lastItemInCol: function (colId, layout) {
            var colIndex = internals.findColIndex(colId, layout);
            var col = layout.columns[colIndex];
            var numChildren = col.children.length;
            if (numChildren > 0) {
                return col.children[numChildren-1];                
            }
            return undefined;
        },
        
        /**
         * Builds a fake permission object stuffed with 1s.
         * @param {Object} layout
         */
        buildEmptyPerms: function (layout) {
            var numCols = internals.numColumns(layout);
            var numModules = internals.numModules(layout);
            
            var permsStructure = [];
            // Each column has a drop target at its top.
            // Each portlet has a drop target below it.
            var numItemsInBitmap = numCols + numModules;
            for (var i = 0; i < numModules; i++) {
                var rowForPortlet = [];
                // Stuff the whole structure with 1s to dispense with permissions altogether.
                for (var j = 0; j < numItemsInBitmap; j++) {
                    rowForPortlet.push(1);
                }
                permsStructure.push(rowForPortlet);                
            }
            
            return permsStructure;
        },
    
        /**
         * Builds a layout object from a set of columns and portlets.
         * @param {jQuery} container
         * @param {jQuery} columns
         * @param {jQuery} portlets
         */
        buildLayout: function (container, columns, portlets) {
            var layoutStructure = {};
            layoutStructure.id = container[0].id;
            layoutStructure.columns = [];
            columns.each(function (idx, column) {
                var portletsInColumn = internals.findPortletsInColumn(portlets, column);
                layoutStructure.columns.push(internals.columnStructure(column, portletsInColumn));
            });
            
            return layoutStructure;
        }
    };	
} (jQuery, fluid);
