/*
Copyright 2007 University of Cambridge
Copyright 2007-2008 University of Toronto

Licensed under the Educational Community License (ECL), Version 2.0 or the New
BSD license. You may not use this file except in compliance with one these
Licenses.

You may obtain a copy of the ECL 2.0 License and BSD License at
https://source.fluidproject.org/svn/LICENSE.txt
*/

// Declare dependencies.
/*global jQuery*/

/*global fluid*/
var fluid = fluid || {};

(function (jQuery, fluid) {
    fluid.keys = {
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
     * These roles are used to add ARIA roles to orderable items. This list can be extended as needed,
     * but the values of the container and item roles must match ARIA-specified roles.
     */  
    fluid.roles = {
        GRID: { container: "grid", item: "gridcell" },
        LIST: { container: "list", item: "listitem" },
        REGIONS: { container: "main", item: "article" }
    };
    
    fluid.orientation = {
    	HORIZONTAL: "horiz",
    	VERTICAL: "vert"
    };
    
    /**
     * This is the position, relative to a given drop target, that a dragged item should be dropped.
     */
    fluid.position = {
    	BEFORE: 0, 
    	AFTER: 1,
    	INSIDE: 2,
    	USE_LAST_KNOWN: 3,  // given configuration meaningless, use last known drop target
        DISALLOWED: -1      // cannot drop in given configuration
    };
    
    /**
     * For incrementing/decrementing a count or index.
     */
    fluid.direction = {
        NEXT: 1,
        PREVIOUS: -1
    };
    
    fluid.defaultKeysets = [{
        modifier : function (evt) {
	        return evt.ctrlKey;
        },
        up : fluid.keys.UP,
        down : fluid.keys.DOWN,
        right : fluid.keys.RIGHT,
        left : fluid.keys.LEFT
    },
    {
        modifier : function (evt) {
	        return evt.ctrlKey;
        },
        up : fluid.keys.i,
        down : fluid.keys.m,
        right : fluid.keys.k,
        left : fluid.keys.j
    }];
    
    fluid.mixin = function (target, args) {
        for (var arg in args) {
            if (args.hasOwnProperty(arg)) {
                target[arg] = args[arg];
            }
        }
    };
    
    fluid.wrap = function (obj) {
        return ((!obj || obj.jquery) ? obj : jQuery(obj)); 
    };
    
    fluid.unwrap = function (obj) {
        return obj.jquery ? obj[0] : obj; // Unwrap the element if it's a jQuery.
    };
    
    /*
     * Utilities object for providing various general convenience functions
     */
    fluid.utils = {};
    
    // Custom query method seeks all tags descended from a given root with a 
    // particular tag name, whose id matches a regex. The Dojo query parser
    // is broken http://trac.dojotoolkit.org/ticket/3520#preview, this is all
    // it might do anyway, and this will be plenty fast.
    fluid.utils.seekNodesById = function (rootnode, tagname, idmatch) {
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
          
    fluid.utils.escapeSelector = function (id) {
        return id.replace(/\:/g, "\\:");
    };
      
    fluid.utils.findForm = function (element) {
        while (element) {
            if (element.nodeName.toLowerCase() === "form") {
                return element;
            }
            element = element.parentNode;
        }
    };
    
    /**
     * Adapt 'findItems' object given either a 'findItems' object or a 'findMovables' function 
     **/
    fluid.utils.adaptFindItems = function (finder) {
        var finderFn = function () {};
        var findItems = {};
        
        if (typeof finder === 'function') {
            finderFn = finder;
        } else {
            findItems = finder;
        }
    
        findItems.movables = findItems.movables || finderFn;
        findItems.selectables = findItems.selectables || findItems.movables;
        findItems.dropTargets = findItems.dropTargets || findItems.movables;
        findItems.grabHandle = findItems.grabHandle ||
            function (item) {
                return item;
            };
            
        return findItems;
    };
    
    /**
     * Returns a jQuery object given the id of a DOM node
     */
    fluid.utils.jById = function (id) {
        var el = jQuery("[id=" + id + "]");
        if (el[0] && el[0].id === id) {
            return el;        
        }       
        
        return null;
    };

    fluid.utils.debug = function (str) {
    	if (window.console) {
            if (console.debug) {
                console.debug(str);
            } else {
                console.log(str);
            }
    	}
    };

	fluid.utils.derivePercent = function (num, total) {
		return Math.round((num * 100) / total);
	};

	// simple function for return kbytes and megabytes from a number of bytes
	// probably should do something fancy that shows MBs if the number is huge
	fluid.utils.filesizeStr = function (bytes) {
		/*
		if (bytes < 1024){
			return bytes + " bytes";
		} else
		*/
		if (typeof bytes === "number") {
			if (bytes === 0) {
				return "0.0 KB";
			} else if (bytes > 0) {
				if (bytes < 1048576) {
					return (Math.ceil(bytes / 1024 * 10) / 10).toFixed(1) + ' KB';
				}
				else {
					return (Math.ceil(bytes / 1048576 * 10) / 10).toFixed(1) + ' MB';
				}
			}
		}
		return '';
	};
	
    fluid.utils.initCssClassNames = function (defaultNames, classNames) {
        if (!classNames) {
            return defaultNames;
        }
        var cssClassNames = {};
        for (var className in defaultNames) {
            if (defaultNames.hasOwnProperty(className)) {
                cssClassNames[className] = classNames[className] || defaultNames[className];
            }
        }

        return cssClassNames;
    };
	
    /**
     * Simple string template system. 
     * Takes a template string containing tokens in the form of "%value".
     * Returns a new string with the tokens replaced by the specified values.
     * Keys and values can be of any data type that can be coerced into a string. Arrays will work here as well.
     * 
     * @param {String}	template	a string (can be HTML) that contains tokens embedded into it
     * @param {object}	values		a collection of token keys and values
	 */
    fluid.utils.stringTemplate = function (template, values) {
	    var newString = template;
		for (var key in values) {
            if (values.hasOwnProperty(key)) {
    			var searchStr = "%" + key;
                newString = newString.replace(searchStr, values[key]);
            }
		}
		return newString;
	};

    /**
     * Finds the ancestor of the element that passes the test
     * @param {Element} element DOM element
     * @param {Function} test A function which takes an element as a parameter and return true or false for some test
     */
    fluid.utils.findAncestor = function (element, test) {
        return test(element) ? element : jQuery.grep(jQuery(element).parents(), test)[0];
    };
    
})(jQuery, fluid);
