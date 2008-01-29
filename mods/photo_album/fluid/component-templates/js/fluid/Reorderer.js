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
var fluid = fluid || {};

fluid.Reorderer = function (container, params) {
    // Reliable 'this'.
    //
    var thisReorderer = this;
    var theAvatar = null;
    
    this.domNode = jQuery (container);
    
    // the reorderable DOM element that is currently active
    this.activeItem = null;
        
    this.layoutHandler = null;
        
    this.messageNamebase = "message-bundle:";
    
    this.orderableFinder = null;
            
    this.cssClasses = {
        defaultStyle: "orderable-default",
        selected: "orderable-selected",
        dragging: "orderable-dragging",
        hover: "orderable-hover",
        focusTarget: "orderable-focus-target",
        dropMarker: "orderable-drop-marker",
        avatar: "orderable-avatar"
    };

    if (params) {
        fluid.mixin (this, params);
    }
    
   /**
    * Return the element within the item that should receive focus. This is determined by 
    * cssClasses.focusTarget. If it is not specified, the item itself is returned.
    * 
    * @param {Object} item
    * @return {Object} The element that should receive focus in the specified item.
    */
    this.findElementToFocus = function (item) {
        var elementToFocus = jQuery ("." + this.cssClasses.focusTarget, item).get (0);
        if (elementToFocus) {
            return elementToFocus;
        }
        return item;
    };
    
    function setupDomNode (domNode) {
        domNode.focus(thisReorderer.handleFocus);
        domNode.blur (thisReorderer.handleBlur);
        domNode.keydown (thisReorderer.handleKeyDown);
        domNode.keyup (thisReorderer.handleKeyUp);
        domNode.removeAttr ("aaa:activedescendent");
    }   
    
    /**
     * Changes the current focus to the specified item.
     * @param {Object} anItem
     */
    this.focusItem = function(anItem) {
        if (this.activeItem !== anItem) {
            this.changeActiveItemToDefaultState();
            this._setActiveItem (anItem);   
        }
        
        var jActiveItem = jQuery (this.activeItem);
        jActiveItem.removeClass (this.cssClasses.defaultStyle);
        jActiveItem.addClass (this.cssClasses.selected);
        
        this.addFocusToElement (this.findElementToFocus (this.activeItem));
    };
    
    this.addFocusToElement = function (anElement) {
        var jElement = jQuery (anElement);
        if (!jElement.hasTabIndex ()) {
            jElement.tabIndex (-1);
        }
        jElement.focus ();
    };
    
    this.removeFocusFromElement = function (anElement) {
        jQuery (anElement).blur ();
    };

    /**
     * Changes focus to the active item.
     */
    this.selectActiveItem = function() {
        if (!this.activeItem) {
            var orderables = this.orderableFinder (this.domNode.get(0));
            if (orderables.length > 0) {
                this._setActiveItem (orderables[0]);
            }
            else {
                return;
            }
        }
        this.focusItem (this.activeItem);
    };
    
    this.handleFocus = function (evt) {
        thisReorderer.selectActiveItem();
        return false;
    };
    
    this.handleBlur = function (evt) {
        // Temporarily disabled blur handling in IE. See FLUID-7 for details.
        if (!jQuery.browser.msie) {
            thisReorderer.changeActiveItemToDefaultState();
        }
    };
            
    this.changeActiveItemToDefaultState = function() {
        if (this.activeItem) {
            var jActiveItem = jQuery (this.activeItem);
            jActiveItem.removeClass (this.cssClasses.selected);
            jActiveItem.addClass (this.cssClasses.defaultStyle);
            this.removeFocusFromElement (jActiveItem);
        }
    };
    
    this.handleKeyDown = function (evt) {
       if (thisReorderer.activeItem && evt.keyCode === fluid.keys.CTRL) {
           jQuery (thisReorderer.activeItem).removeClass (thisReorderer.cssClasses.selected);
           jQuery (thisReorderer.activeItem).addClass (thisReorderer.cssClasses.dragging);
           thisReorderer.activeItem.setAttribute ("aaa:grab", "true");
           return false;
       }

       return thisReorderer.handleArrowKeyPress(evt);
    };

    this.handleKeyUp = function (evt) {
       if (thisReorderer.activeItem && evt.keyCode === fluid.keys.CTRL) {
           jQuery (thisReorderer.activeItem).removeClass (thisReorderer.cssClasses.dragging);
           jQuery (thisReorderer.activeItem).addClass (thisReorderer.cssClasses.selected);
           thisReorderer.activeItem.setAttribute ("aaa:grab", "supported");
           return false;
       } 
    };
        
    this.handleArrowKeyPress = function (evt) {
        if (thisReorderer.activeItem) {
            switch (evt.keyCode) {
                case fluid.keys.DOWN:
                    evt.preventDefault();
                    thisReorderer.handleDownArrow (evt.ctrlKey);                                
                    return false;
                case fluid.keys.UP: 
                    evt.preventDefault();
                    thisReorderer.handleUpArrow (evt.ctrlKey);                              
                    return false;
                case fluid.keys.LEFT: 
                    evt.preventDefault();
                    thisReorderer.handleLeftArrow (evt.ctrlKey);                                
                    return false;
                case fluid.keys.RIGHT: 
                    evt.preventDefault();
                    thisReorderer.handleRightArrow (evt.ctrlKey);                               
                    return false;
                default:
                    return true;
            }
        }
    };
    
    this.handleUpArrow = function (isCtrl) {
        if (isCtrl) {
            this.layoutHandler.moveItemUp (this.activeItem);
            this.findElementToFocus (this.activeItem).focus();
        } else {
            this.focusItem (this.layoutHandler.getItemAbove(this.activeItem));
        }           
    };
    
    this.handleDownArrow = function (isCtrl) {
        if (isCtrl) {
            this.layoutHandler.moveItemDown (this.activeItem);
            this.findElementToFocus (this.activeItem).focus();
        } else {
            this.focusItem (this.layoutHandler.getItemBelow (this.activeItem));
        }
    };
    
    this.handleRightArrow = function (isCtrl) {
        if (isCtrl) {
            this.layoutHandler.moveItemRight (this.activeItem);
            jQuery(this.findElementToFocus (this.activeItem)).focus();
        } else {
            this.focusItem (this.layoutHandler.getRightSibling (this.activeItem));              
        }           
    };
    
    this.handleLeftArrow = function (isCtrl) {
        if (isCtrl) {
            this.layoutHandler.moveItemLeft (this.activeItem);
            this.findElementToFocus (this.activeItem).focus();
        } else {
            this.focusItem (this.layoutHandler.getLeftSibling (this.activeItem));               
        }
    };
            
    this._fetchMessage = function (messagekey) {
        var messageID = this.messageNamebase + messagekey;
        var node = document.getElementById (messageID);
        
        return node? node.innerHTML: "[Message not found at id " + messageID + "]";
    };
    
    this._setActiveItem = function (anItem) {
        this.activeItem = anItem;
        this._updateActiveDescendent();
    };
    
    this._updateActiveDescendent = function() {
        if (this.activeItem) {
            this.domNode.attr ("aaa:activedescendent", this.activeItem.id);
        } else {
            this.domNode.removeAttr ("aaa:activedescendent");
        }
    };

    var dropMarker; // private scratch variable
    
    /**
     * evt.data - the droppable DOM element.
     */
    function trackMouseMovement (evt) {
        if (thisReorderer.layoutHandler.isMouseBefore (evt, evt.data)) {
           jQuery (evt.data).before (dropMarker);
       }        
       else {
           jQuery (evt.data).after (dropMarker);
       }
    }

    /**
     * Given an item, make it draggable.
     */
    function setUpDraggable (anItem) {
        anItem.draggable ({
            helper: function() {
                theAvatar = jQuery (this).clone();
                jQuery (theAvatar).removeAttr ("id");
                jQuery ("[id]", theAvatar).removeAttr ("id");
                jQuery (":hidden", theAvatar).remove(); 
                jQuery ("input", theAvatar).attr ("disabled", "true"); 
                theAvatar.addClass (thisReorderer.cssClasses.avatar);           
                return theAvatar;
            },
            start: function (e, ui) {
                thisReorderer.focusItem (ui.draggable.element);                
                jQuery (ui.draggable.element).addClass (thisReorderer.cssClasses.dragging);
                ui.draggable.element.setAttribute ("aaa:grab", "true");
                
                // In order to create valid html, the drop marker is the same type as the node being dragged.
                // This creates a confusing UI in cases such as an ordered list. 
                // drop marker functionality should be made pluggable. 
                dropMarker = document.createElement (ui.draggable.element.tagName);
                jQuery (dropMarker).addClass (thisReorderer.cssClasses.dropMarker);
                dropMarker.style.visibility = "hidden";
            },
            stop: function(e, ui) {
                jQuery (ui.draggable.element).removeClass (thisReorderer.cssClasses.dragging);
                thisReorderer.activeItem.setAttribute ("aaa:grab", "supported");
                thisReorderer.domNode.focus();
                if (dropMarker.parentNode) {
                    dropMarker.parentNode.removeChild (dropMarker);
                }
                theAvatar = null;
            }
        });
    
    }   // end setUpDraggable()

    /**
     * Make item a drop target.
     */
    function setUpDroppable (anItem, selector) {
        anItem.droppable ({
            accept: selector,
            tolerance: "pointer",
            over: function (e, ui) {
                // the second parameter to bind() can be accessed through the event as event.data
                jQuery (ui.droppable.element).bind ("mousemove", ui.droppable.element, trackMouseMovement);    
                jQuery (theAvatar).bind ("mousemove", ui.droppable.element, trackMouseMovement);            
                dropMarker.style.visibility = "visible";
            },
            out: function (e, ui) {
                dropMarker.style.visibility = "hidden";
                jQuery (ui.droppable.element).unbind ("mousemove", trackMouseMovement);
                jQuery (theAvatar).unbind ("mousemove", trackMouseMovement);            
            },
            drop: function (e, ui) {
                thisReorderer.layoutHandler.mouseMoveItem(e, ui.draggable.element, ui.droppable.element);
                jQuery (ui.droppable.element).unbind ("mousemove", trackMouseMovement);
                jQuery (theAvatar).unbind ("mousemove", trackMouseMovement);            
            }
        });
    
    }   // end setUpDroppable().
 
    /**
     * Given an item, make it a draggable and a droppable with the relevant properties and functions.
     * @param  anItem      The element to make draggable and droppable.
     * @param  selector    The jQuery selector(s) that select all the orderables.
     */ 
    function setUpDnDItem (anItem, selector) {
        anItem.mouseover ( 
            function () {
                jQuery (this).addClass (thisReorderer.cssClasses.hover);
            }
        );
        
        anItem.mouseout (  
            function () {
                jQuery (this).removeClass (thisReorderer.cssClasses.hover);
            }
        );
  
        // Make anItem draggable and a drop target (in the jQuery UI sense).
        setUpDraggable (anItem);
        setUpDroppable (anItem, selector);
            
    }   // end setUpDnDItem()
        
    function initOrderables() {
        var items = thisReorderer.orderableFinder (thisReorderer.domNode.get(0));
        if (items.length === 0) {
            return;
        }
        
        // Create a selector based on the ids of the nodes for use with drag and drop.
        // This should be replaced with using the actual nodes rather then a selector 
        // but will require a patch to jquery's DnD. 
        // See: FLUID-71, FLUID-112
        var selector = "";
        for (var i = 0; i < items.length; i++) {
            var item = items[i];
            selector += "[id=" + item.id + "]";
            if (i !== items.length - 1) {
                selector += ", ";
            }                   
        }

         // Setup orderable item including drag and drop.
         for (i = 0; i < items.length; i++) {
            jQuery (items[i]).addClass (thisReorderer.cssClasses.defaultStyle);
            setUpDnDItem (jQuery (items[i]), selector);
        }
        
        // Add any other drop targets (e.g., any unmoveable ones).
        if ((thisReorderer.droppableFinder) && (thisReorderer.droppableFinder.constructor === Function)) {
            var extraDropTargets = thisReorderer.droppableFinder (thisReorderer.domNode);
            for (i = 0; i < extraDropTargets.length; i++) {
                var jExtraDropTarget = jQuery (extraDropTargets[i]);
                if (!jExtraDropTarget.droppableInstance()) {
                    setUpDroppable (jExtraDropTarget, selector);
                }
            }
        }
    }   // end initOrderables().

    /**
     * Finds the "orderable" parent element given a child element.
     */
    this._findReorderableParent = function (childElement, items) {
        if (!childElement) {
            return null;
        }
        else {
            for (var i=0; i<items.length; i++) {
                if (childElement === items[i]) {
                    return childElement;
                }  
            }
            return this._findReorderableParent (childElement.parentNode, items);
        }
    };

    // Final initialization of the Reorderer at the end of the construction process 
    if (this.domNode) {
        setupDomNode(this.domNode);
        initOrderables();
    }
}; // End Reorderer

/*******************
 * Layout Handlers *
 *******************/
(function () {
    // Shared private functions.
    var moveItem = function (item, relatedItemInfo, defaultPlacement, wrappedPlacement) {
        var itemPlacement = defaultPlacement;
        if (relatedItemInfo.hasWrapped) {
            itemPlacement = wrappedPlacement;
        }
        
        // The "after" and "before" strings are an artifact of using dojo.place. 
        // This should be refactored. 
        if (itemPlacement === "after") {
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
    var isMouseBefore = function (evt, droppableEl, orientation) {
        var mid;
        if (orientation === fluid.orientation.VERTICAL) {
            mid = jQuery (droppableEl).offset().top + (droppableEl.offsetHeight / 2);
            return (evt.pageY < mid);
        } else {
            mid = jQuery (droppableEl).offset().left + (droppableEl.offsetWidth / 2);
            return (evt.clientX < mid);
        }
    };    

    var itemInfoFinders = {
        /*
         * A general get{Left|Right}SiblingInfo() given an item, a list of orderables and a direction.
         * The direction is encoded by either a +1 to move right, or a -1 to
         * move left, and that value is used internally as an increment or
         * decrement, respectively, of the index of the given item.
         */
        getSiblingInfo: function (item, orderables, /* +1, -1 */ incDecrement) {
            var index = jQuery (orderables).index (item) + incDecrement;
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
            return this.getSiblingInfo (item, orderables, 1);
        },
        
        /*
         * Returns an object containing the item that is to the left of the given item
         * and a flag indicating whether or not the process has 'wrapped' around the end of
         * the row that the given item is in
         */
        getLeftSiblingInfo: function (item, orderables) {
            return this.getSiblingInfo (item, orderables, -1);
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
    fluid.ListLayoutHandler = function (params) {
        var orderableFinder = params.orderableFinder;
        var container = params.container;
        var orderChangedCallback = params.orderChangedCallback;
        var orientation = params.orientation;
        
        // Unwrap the container if it's a jQuery.
        container = (container.jquery) ? container.get(0) : container;
        
        orderChangedCallback = orderChangedCallback || function () {};
        
        orientation = orientation || fluid.orientation.VERTICAL;    // default
                
        this.getRightSibling = function (item) {
            return itemInfoFinders.getRightSiblingInfo(item, orderableFinder(container)).item;
        };
        
        this.moveItemRight = function (item) {
            moveItem (item, itemInfoFinders.getRightSiblingInfo(item, orderableFinder(container)), "after", "before");
            orderChangedCallback();
        };
    
        this.getLeftSibling = function (item) {
            return itemInfoFinders.getLeftSiblingInfo(item, orderableFinder(container)).item;
        };
    
        this.moveItemLeft = function (item) {
            moveItem (item, itemInfoFinders.getLeftSiblingInfo(item, orderableFinder(container)), "before", "after");
            orderChangedCallback();
        };
    
        this.getItemBelow = this.getRightSibling;
    
        this.getItemAbove = this.getLeftSibling;
        
        this.moveItemUp = this.moveItemLeft;
        
        this.moveItemDown = this.moveItemRight;
    
        this.isMouseBefore = function(evt, droppableEl) {
            return isMouseBefore(evt, droppableEl, orientation);
        };
        
        this.mouseMoveItem = function (e, item, relatedItem) {
            if (this.isMouseBefore (e, relatedItem)) {
                jQuery (relatedItem).before (item);
            } else {
                jQuery (relatedItem).after (item);
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
	fluid.GridLayoutHandler = function (params) {
        fluid.ListLayoutHandler.call (this, params);

        var orderableFinder = params.orderableFinder;
        var container = params.container;
        var orderChangedCallback = params.orderChangedCallback;
        var orientation = fluid.orientation.HORIZONTAL;
        
		// Unwrap the container if it's a jQuery.
        container = (container.jquery) ? container.get(0) : container;
        
        orderChangedCallback = orderChangedCallback || function () {};
        
	    this.getItemBelow = function(item) {
	        return itemInfoFinders.getItemInfoBelow (item, orderableFinder(container)).item;
	    };
	
	    this.moveItemDown = function (item) {
	        moveItem (item, itemInfoFinders.getItemInfoBelow (item, orderableFinder(container)), "after", "before");
            orderChangedCallback(); 
	    };
	            
	    this.getItemAbove = function (item) {
	        return itemInfoFinders.getItemInfoAbove (item, orderableFinder(container)).item;   
	    }; 
	    
	    this.moveItemUp = function (item) {
	        moveItem (item, itemInfoFinders.getItemInfoAbove (item, orderableFinder(container)), "before", "after");
            orderChangedCallback(); 
	    };
	                
	    // We need to override ListLayoutHandler.isMouseBefore to ensure that the local private
	    // orientation is used.
        this.isMouseBefore = function(evt, droppableEl) {
            return isMouseBefore(evt, droppableEl, orientation);
        };
        
	}; // End of GridLayoutHandler

    fluid.portletPerms = fluid.portletPerms || {};
    fluid.portletPerms.canDrop = function (perms, indexOfItem, indexOfTarget, position) {
        return (!!perms[indexOfItem][indexOfTarget][position]); 
    };
    
    /*
     * PortletLayout helper object.
     */
    fluid.PortletLayout = function() {
    	        
        /**
         * Calculate the location of the item and the column in which it resides.
         * @return  An object with column index and item index (within that column) properties.
         *          These indices are -1 if the item does not exist in the grid.
         */
        this.calcColumnAndItemIndex = function (item, portletLayoutJSON) {
        	var indices = { columnIndex: -1, itemIndex: -1 };
            for (var col = 0; col < portletLayoutJSON.columns.length; col++) {
            	var itemIndex = jQuery (portletLayoutJSON.columns[col].children).index (item.id);
                if (itemIndex !== -1) {
                    indices.columnIndex = col;
                    indices.itemIndex = itemIndex;
                    break;
                }                
            }
            return indices;
        };
                        
        /**
         * Return the first orderable item in the given column.
         */
        this.findFirstOrderableSiblingInColumn = function (columnIndex, orderableItems, portletLayoutJSON) {
            // Pull out the portlet id of the top-most sibling in the column.
            var topMostOrderableSibling = null;
            var itemIndex = 0;
            var column = portletLayoutJSON.columns[columnIndex];
            if (column) {
                var id = column.children[itemIndex];
                topMostOrderableSibling = jQuery ("#" + id)[0];
                // loop down the column looking for first orderable portlet (i.e. skip over non-movable portlets)
                while (orderableItems.index (topMostOrderableSibling) === -1) {
                    itemIndex += 1;
                    id = column.children[itemIndex];
                    topMostOrderableSibling = jQuery ("#" + id).get (0);
                }
            }
            return topMostOrderableSibling;
        };
        
        /**
         * Return the number of items in the given column.  If the column index
         * is out of bounds, this returns -1.
         */
        this.numItemsInColumn = function (columnIndex, portletLayoutJSON) {
        	if ((columnIndex < 0) || (columnIndex > portletLayoutJSON.columns.length)) {
        		return -1;
        	}
        	else {
        	   return portletLayoutJSON.columns[columnIndex].children.length;
        	}
        };
        
        this.numColumns = function (layout) {
            return layout.columns.length;
        };
        
        this.findLinearIndex = function (itemId, layout) {
            var columns = layout.columns;
            var linearIndex = 0;
            
            for (var i = 0; i < columns.length; i++) {
            	var idsInCol = columns[i].children;
            	for (var j = 0; j < idsInCol.length; j++) {
            		if (idsInCol[j] === itemId) {
            			return linearIndex;
            		}
            		linearIndex++;
            	}
            }
            
            return -1;
        };
        
        /**
         * Move an item within the portletLayoutJSON object. 
         */
        this.updateLayout = function (item, relativeItem, placement, portletLayoutJSON) {
            if (!item || !relativeItem) { return; }
            var itemIndices = this.calcColumnAndItemIndex (item, portletLayoutJSON);

            var itemId = portletLayoutJSON.columns[itemIndices.columnIndex].children[itemIndices.itemIndex];
            portletLayoutJSON.columns[itemIndices.columnIndex].children.splice (itemIndices.itemIndex, 1);

            var relativeItemIndices = this.calcColumnAndItemIndex (relativeItem, portletLayoutJSON);
            var targetCol = portletLayoutJSON.columns[relativeItemIndices.columnIndex].children;
            targetCol.splice (relativeItemIndices.itemIndex + (placement === "before"? 0 : 1), 0, itemId);
        };
    };   // End of PortletLayout.
    
    /*
     * Portlet Layout Handler for reordering portlet-type markup.
     * 
     * General movement guidelines:
     * 
     * - Arrowing sideways will always go to the top (moveable) portlet in the column
     * - Moving sideways will always move to the top available drop target in the column
     * - Wrapping is not necessary at this first pass, but is ok
     */
    fluid.PortletLayoutHandler = function (params) {
        var orderableFinder = params.orderableFinder;
        var container = params.container;
        var orderChangedCallback = params.orderChangedCallback;
        var portletLayoutJSON = params.portletLayout;
        var targetPerms = params.dropTargetPermissions;
        var orientation = fluid.orientation.VERTICAL;
        
        var portletLayout = new fluid.PortletLayout();
        
        // Unwrap the container if it's a jQuery.
        container = (container.jquery) ? container.get(0) : container;
        
        orderChangedCallback = orderChangedCallback || function () {};

        // Private Methods.
        /*
	     * A general get{Above|Below}Sibling() given an item and a direction.
	     * The direction is encoded by either a +1 to move down, or a -1 to
	     * move up, and that value is used internally as an increment or
	     * decrement, respectively, of the index of the given item.
	     * This implementation does not wrap around. 
	     */
	    var getVerticalSibling = function (item, /* +1, -1 */ incDecrement) {
	        var orderables = orderableFinder (container);
	        var index = jQuery(orderables).index(item) + incDecrement;
	            
	        // If we wrap, backup 
	        if ((index === -1) || (index === orderables.length)) {
	            return null;
	        }
	        // Handle case where the passed-in item is *not* an "orderable"
	        // (or other undefined error).
	        //
	        else if (index < -1 || index > orderables.length) {
	            index = 0;
	        }
	        
	        return orderables[index];
	    };
	
	    /*
	     * A general get{Beside}SiblingInfo() given an item and a direction.
	     * The direction is encoded by either a +1 to move right, or a -1 to
	     * move left.
	     * Currently, the horizontal sibling defaults to the top orderable item in the
	     * neighboring column.
	     */
	    var getHorizontalSibling = function (item, /* +1, -1 */ incDecrement) {
	        var orderables = orderableFinder (container);
	            
            // go through all the children and find which column the passed in item is located in.
            // Save that column if found.
            var colIndex = portletLayout.calcColumnAndItemIndex (item, portletLayoutJSON).columnIndex;
	        if (colIndex === -1) {
	            return null;
	        }
            var sibling = portletLayout.findFirstOrderableSiblingInColumn (colIndex + incDecrement, orderables, portletLayoutJSON);
	        return sibling;
	
	    };
	    	    
	    // These methods are public only for our unit tests. They need to be refactored into a portletlayout object.
         var isFirstInColumn = function (item) {
            var position = portletLayout.calcColumnAndItemIndex (item, portletLayoutJSON);
            return (position.itemIndex === 0) ? true : false;
        };
    
        var isLastInColumn = function (item) {
            var position = portletLayout.calcColumnAndItemIndex (item, portletLayoutJSON);
            return (position.itemIndex === portletLayout.numItemsInColumn (position.columnIndex, portletLayoutJSON)-1) ? true : false;
        };
        
        var isInLeftmostColumn = function (item) {
            if (portletLayout.calcColumnAndItemIndex (item, portletLayoutJSON).columnIndex === 0) {
                return true;
            } else {
                return false;
            }
        };
        
        var isInRightmostColumn = function (item) {
            if (portletLayout.calcColumnAndItemIndex (item, portletLayoutJSON).columnIndex === portletLayout.numColumns (portletLayoutJSON) - 1) {
                return true;
            } else {
                return false;
            }
        };
            
        var canDrop = function (itemId, relatedItemId, position) {
            return fluid.portletPerms.canDrop(
                targetPerms, 
                portletLayout.findLinearIndex(itemId, portletLayoutJSON), 
                portletLayout.findLinearIndex(relatedItemId, portletLayoutJSON), 
                position);
        };
        
        var moveBefore = function (item, relatedItem) {
            if (!canDrop (item.id, relatedItem.id, fluid.position.BEFORE)) {
                return;
            } 
                
            jQuery (relatedItem).before (item);
            portletLayout.updateLayout (item, relatedItem, "before", portletLayoutJSON);
            portletLayoutJSON = orderChangedCallback() || portletLayoutJSON; 
        };
        
        var moveAfter = function (item, relatedItem) {
            if (!canDrop (item.id, relatedItem.id, fluid.position.AFTER)) {
                return;
            } 
                
            jQuery (relatedItem).after (item);
            portletLayout.updateLayout (item, relatedItem, "after", portletLayoutJSON);
            portletLayoutJSON = orderChangedCallback() || portletLayoutJSON; 
        };	   

        // Public Methods
        
	    this.getRightSibling = function (item) {
            if (isInRightmostColumn(item)) {
                return item;
            } else {
                return getHorizontalSibling(item, 1);
            }
	    };
	    
	    this.moveItemRight = function (item) {
            var rightSibling = this.getRightSibling (item);
            jQuery (rightSibling).before (item);
            portletLayout.updateLayout (item, rightSibling, "before", portletLayoutJSON);
            // first, stringify the json before sending it
            portletLayoutJSON = orderChangedCallback() || portletLayoutJSON; 
            // the return value will actually be a big json object with two parts
            // we'll have to parse it, and separate the two parts
	    };
	
	    this.getLeftSibling = function (item) {
            if (isInLeftmostColumn(item)) {
                return item;
            } else {
                return getHorizontalSibling(item, -1);
            }
	    };
	
	    this.moveItemLeft = function (item) {
	        var leftSibling = this.getLeftSibling(item);
            jQuery (leftSibling).before (item);
            portletLayout.updateLayout (item, leftSibling, "before", portletLayoutJSON);
            portletLayoutJSON = orderChangedCallback() || portletLayoutJSON; 
	    };
	
	    this.getItemAbove = function (item) {
	    	if (isFirstInColumn(item)) {
                return item;
            } else {
                return getVerticalSibling (item, -1);
            }
	    };
	    
	    this.moveItemUp = function (item) {
	        var siblingAbove = this.getItemAbove(item);
	        moveBefore (item, siblingAbove);
	    };
	        
	    this.getItemBelow = function (item) {
	    	if (isLastInColumn(item)) {
                return item;
            } else {
                return getVerticalSibling (item, 1);
            }
	    };
	
	    this.moveItemDown = function (item) {
	        var siblingBelow = this.getItemBelow(item);
	        moveAfter (item, siblingBelow);
	    };
	    
	    this.isMouseBefore = function(evt, droppableEl) {
            return isMouseBefore(evt, droppableEl, orientation);
        };

        this.mouseMoveItem = function (e, item, relatedItem) {
            if (this.isMouseBefore (e, relatedItem)) {
                moveBefore (item, relatedItem);
            } else {
                moveAfter (item, relatedItem);
            }
        };
        
    }; // End PortalLayoutHandler
}) ();

