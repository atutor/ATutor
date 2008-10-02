/*
Copyright 2007 - 2008 University of Toronto
Copyright 2007 - 2008 University of Cambridge

Licensed under the Educational Community License (ECL), Version 2.0 or the New
BSD license. You may not use this file except in compliance with one these
Licenses.

You may obtain a copy of the ECL 2.0 License and BSD License at
https://source.fluidproject.org/svn/LICENSE.txt
*/

// Declare dependencies.
/*global jQuery*/
/*global fluid_0_5*/

fluid_0_5 = fluid_0_5 || {};

fluid.moduleLayout = fluid.moduleLayout || {};

(function (jQuery, fluid) {

    /**
     * Calculate the location of the item and the column in which it resides.
     * @return  An object with column index and item index (within that column) properties.
     *          These indices are -1 if the item does not exist in the grid.
     */
      var findColumnAndItemIndices = function (item, layout) {
          return fluid.find(layout.columns,
              function(column, colIndex) {
                  var index = jQuery.inArray(item, column.elements);
                  return index === -1? null : {columnIndex: colIndex, itemIndex: index};
                  }, {columnIndex: -1, itemIndex: -1});
        };
        
       var findColIndex = function (item, layout) {
           return fluid.find(layout.columns,
             function(column, colIndex) {
                     return item === column.container? colIndex : null;
            }, -1);
        };

    /**
     * Move an item within the layout object. 
     */
    fluid.moduleLayout.updateLayout = function (item, target, position, layout) {
        item = fluid.unwrap(item);
        target = fluid.unwrap(target);
        var itemIndices = findColumnAndItemIndices(item, layout);
        layout.columns[itemIndices.columnIndex].elements.splice(itemIndices.itemIndex, 1);
        var targetCol;
        if (position === fluid.position.INSIDE) {
            targetCol = layout.columns[findColIndex(target, layout)].elements;
            targetCol.splice(targetCol.length, 0, item);

        } else {
            var relativeItemIndices = findColumnAndItemIndices(target, layout);
            targetCol = layout.columns[relativeItemIndices.columnIndex].elements;
            position = fluid.normalisePosition(position, 
                  itemIndices.columnIndex === relativeItemIndices.columnIndex, 
                  relativeItemIndices.itemIndex, itemIndices.itemIndex);
            var relative = position === fluid.position.BEFORE? 0 : 1;
            targetCol.splice(relativeItemIndices.itemIndex + relative, 0, item);
        }
      };
       
    /**
     * Builds a layout object from a set of columns and modules.
     * @param {jQuery} container
     * @param {jQuery} columns
     * @param {jQuery} portlets
     */
    fluid.moduleLayout.layoutFromFlat = function (container, columns, portlets) {
        var layout = {};
        layout.container = container;
        layout.columns = fluid.transform(columns, 
            function(column) {
                return {
                    container: column,
                    elements: jQuery.makeArray(portlets.filter(function() {
                    	  // is this a bug in filter? would have expected "this" to be 1st arg
                        return fluid.dom.isContainer(column, this);
                    }))
                };
            });
        return layout;
      };
      
    /**
     * Builds a layout object from a serialisable "layout" object consisting of id lists
     */
    fluid.moduleLayout.layoutFromIds = function (idLayout) {
        return {
            container: fluid.byId(idLayout.id),
            columns: fluid.transform(idLayout.columns, 
                function(column) {
                    return {
                        container: fluid.byId(column.id),
                        elements: fluid.transform(column.children, fluid.byId)
                    };
                })
            };
      };
      
    /**
     * Serializes the current layout into a structure of ids
     */
    fluid.moduleLayout.layoutToIds = function (idLayout) {
        return {
            id: fluid.getId(idLayout.container),
            columns: fluid.transform(idLayout.columns, 
                function(column) {
                    return {
                        id: fluid.getId(column.container),
                        children: fluid.transform(column.elements, fluid.getId)
                    };
                })
            };
      };
    
    var defaultOnShowKeyboardDropWarning = function (item, dropWarning) {
        if (dropWarning) {
            var offset = jQuery(item).offset();
            dropWarning = jQuery(dropWarning);
            dropWarning.css("position", "absolute");
            dropWarning.css("top", offset.top);
            dropWarning.css("left", offset.left);
        }
    };
    
    fluid.defaults(true, "fluid.moduleLayoutHandler", 
        {orientation: fluid.orientation.VERTICAL,
         containerRole: fluid.reorderer.roles.REGIONS,
         selectablesTabindex: 0,
         sentinelize:         true
         });
    
    /**
     * Module Layout Handler for reordering content modules.
     * 
     * General movement guidelines:
     * 
     * - Arrowing sideways will always go to the top (moveable) module in the column
     * - Moving sideways will always move to the top available drop target in the column
     * - Wrapping is not necessary at this first pass, but is ok
     */
    fluid.moduleLayoutHandler = function (container, options, dropManager, dom) {
        var that = {};
        var layout;
        
        if (options.selectors.modules) {
            layout = fluid.moduleLayout.layoutFromFlat(container, dom.locate("columns"), dom.locate("modules"));
        }
        if (!layout) {
            var idLayout = fluid.model.getBeanValue(options, "moduleLayout.layout");
            layout = fluid.moduleLayout.layoutFromIds(idLayout);
        }

        function isLocked(item) {
            var lockedModules = options.selectors.lockedModules? dom.fastLocate("lockedModules") : [];
            return jQuery.inArray(item, lockedModules) !== -1;
            }

        that.getRelativePosition  = 
           fluid.reorderer.relativeInfoGetter(options.orientation, 
                 fluid.reorderer.WRAP_LOCKED_STRATEGY, fluid.reorderer.GEOMETRIC_STRATEGY, 
                 dropManager, dom);
                 
        that.getGeometricInfo = function () {
        	  var extents = [];
            var togo = {extents: extents,
                        sentinelize: options.sentinelize};
            togo.elementMapper = function(element) {
                return isLocked(element)? "locked" : null;
                };
            for (var col = 0; col < layout.columns.length; col++) {
                var column = layout.columns[col];
                var thisEls = {
                    orientation: options.orientation,
                    elements: jQuery.makeArray(column.elements),
                    parentElement: column.container
                };
              //  fluid.log("Geometry col " + col + " elements " + fluid.dom.dumpEl(thisEls.elements) + " isLocked [" + 
              //       fluid.transform(thisEls.elements, togo.elementMapper).join(", ") + "]");
                extents.push(thisEls);
            }

            return togo;
        };
        
        function computeModules(all) {
            return function() {
                var modules = fluid.accumulate(layout.columns, function(column, list) {
                    return list.concat(column.elements); // note that concat will not work on a jQuery
                    }, []);
                if (!all) {
                    fluid.remove_if(modules, isLocked);
                }
                return modules;
            };
        }
        
        that.returnedOptions = {
            selectors: {
                movables: computeModules(false),
                dropTargets: computeModules(false),
                selectables: computeModules(true)
            },
            listeners: {
                onMove: function(item, requestedPosition) {
                    fluid.moduleLayout.updateLayout(item, requestedPosition.element, requestedPosition.position, layout);
                    },
                "onShowKeyboardDropWarning.setPosition": defaultOnShowKeyboardDropWarning
             }
        };
        
        that.getModel = function() {
           return fluid.moduleLayout.layoutToIds(layout);
        };
        
        
        return that;
    };
}) (jQuery, fluid_0_5);
