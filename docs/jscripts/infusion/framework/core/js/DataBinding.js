/*
Copyright 2008-2010 University of Cambridge
Copyright 2008-2009 University of Toronto

Licensed under the Educational Community License (ECL), Version 2.0 or the New
BSD license. You may not use this file except in compliance with one these
Licenses.

You may obtain a copy of the ECL 2.0 License and BSD License at
https://source.fluidproject.org/svn/LICENSE.txt
*/

/*global jQuery*/
/*global fluid_1_2*/

fluid_1_2 = fluid_1_2 || {};

(function ($, fluid) {
    
    fluid.VALUE = {};
    
    fluid.BINDING_ROOT_KEY = "fluid-binding-root";
    
    /** Recursively find any data stored under a given name from a node upwards
     * in its DOM hierarchy **/
     
    fluid.findData = function(elem, name) {
        while (elem) {
            var data = $.data(elem, name);
            if (data) {return data;}
            elem = elem.parentNode;
            }
        };
  
    fluid.bindFossils = function(node, data, fossils) {
        $.data(node, fluid.BINDING_ROOT_KEY, {data: data, fossils: fossils});
        };
  
    fluid.findForm = function (node) {
      return fluid.findAncestor(node, 
          function(element) {return element.nodeName.toLowerCase() === "form";});
    };
    
    /** A generalisation of jQuery.val to correctly handle the case of acquiring and
     * setting the value of clustered radio button/checkbox sets, potentially, given
     * a node corresponding to just one element.
     */
    fluid.value = function (nodeIn, newValue) {
        var node = fluid.unwrap(nodeIn);
        var multiple = false;
        if (node.nodeType === undefined && node.length > 1) {
            node = node[0];
            multiple = true;
        }
        var jNode = $(node);
        if ("input" !== node.nodeName.toLowerCase()
           || ! /radio|checkbox/.test(node.type)) {return $(node).val(newValue);}
        var name = node.name;
        if (name === undefined) {
            fluid.fail("Cannot acquire value from node " + fluid.dumpEl(node) + " which does not have name attribute set");
        }
        var elements;
        if (multiple) {
            elements = nodeIn;
        }
        else {
            var elements = document.getElementsByName(name);
            var scope = fluid.findForm(node);
            elements = $.grep(elements, 
              function(element) {
                if (element.name !== name) {return false;}
                return !scope || fluid.dom.isContainer(scope, element);
              });
        }
        if (newValue !== undefined) {
            if (typeof(newValue) === "boolean") {
                newValue = (newValue? "true" : "false");
            }
          // jQuery gets this partially right, but when dealing with radio button array will
          // set all of their values to "newValue" rather than setting the checked property
          // of the corresponding control. 
            $.each(elements, function() {
               this.checked = (newValue instanceof Array? 
                 $.inArray(this.value, newValue) !== -1 : newValue === this.value);
            });
        }
        else { // this part jQuery will not do - extracting value from <input> array
            var checked = $.map(elements, function(element) {
                return element.checked? element.value : null;
            });
            return node.type === "radio"? checked[0] : checked;
            }
       };
    
    /** "Automatically" apply to whatever part of the data model is
     * relevant, the changed value received at the given DOM node*/
    fluid.applyChange = function(node, newValue, applier) {
        node = fluid.unwrap(node);
        if (newValue === undefined) {
            newValue = fluid.value(node);
        }
        if (node.nodeType === undefined && node.length > 0) {node = node[0];} // assume here that they share name and parent
        var root = fluid.findData(node, fluid.BINDING_ROOT_KEY);
        if (!root) {
            fluid.fail("Bound data could not be discovered in any node above " + fluid.dumpEl(node));
        }
        var name = node.name;
        var fossil = root.fossils[name];
        if (!fossil) {
            fluid.fail("No fossil discovered for name " + name + " in fossil record above " + fluid.dumpEl(node));
        }
        if (typeof(fossil.oldvalue) === "boolean") { // deal with the case of an "isolated checkbox"
            newValue = newValue[0]? true: false;
        }
        var EL = root.fossils[name].EL;
        if (applier) {
            applier.fireChangeRequest({path: EL, value: newValue, source: node.id});
        }
        else {
            fluid.model.setBeanValue(root.data, EL, newValue);
        }    
        };
   
    fluid.pathUtil = {};
   
    var getPathSegmentImpl = function(accept, path, i) {
        var segment = null; // TODO: rewrite this with regexes and replaces
        if (accept) {
            segment = "";
        }
        var escaped = false;
        var limit = path.length;
        for (; i < limit; ++i) {
            var c = path.charAt(i);
            if (!escaped) {
                if (c === '.') {
                    break;
                    }
                else if (c === '\\') {
                    escaped = true;
                    }
                else if (segment !== null) {
                    segment += c;
                }
            }
            else {
                escaped = false;
                if (segment !== null)
                    accept += c;
                }
            }
        if (segment !== null) {
            accept[0] = segment;
        }
        return i;
        };
    
    var globalAccept = []; // reentrancy risk
    
    fluid.pathUtil.getPathSegment = function(path, i) {
        getPathSegmentImpl(globalAccept, path, i);
        return globalAccept[0];
        }; 
  
    fluid.pathUtil.getHeadPath = function(path) {
        return fluid.pathUtil.getPathSegment(path, 0);
        };
  
    fluid.pathUtil.getFromHeadPath = function(path) {
        var firstdot = getPathSegmentImpl(null, path, 0);
        return firstdot === path.length ? null
            : path.substring(firstdot + 1);
        };
    
    function lastDotIndex(path) {
        // TODO: proper escaping rules
        return path.lastIndexOf(".");
        }
    
    fluid.pathUtil.getToTailPath = function(path) {
        var lastdot = lastDotIndex(path);
        return lastdot == -1 ? null : path.substring(0, lastdot);
        };

  /** Returns the very last path component of a bean path */
    fluid.pathUtil.getTailPath = function(path) {
        var lastdot = lastDotIndex(path);
        return fluid.pathUtil.getPathSegment(path, lastdot + 1);
        };
    
    var composeSegment = function(prefix, toappend) {
        for (var i = 0; i < toappend.length; ++i) {
            var c = toappend.charAt(i);
            if (c === '.' || c === '\\' || c === '}') {
                prefix += '\\';
            }
            prefix += c;
        }
        return prefix;
    };
    
    /**
     * Compose a prefix and suffix EL path, where the prefix is already escaped.
     * Prefix may be empty, but not null. The suffix will become escaped.
     */
    fluid.pathUtil.composePath = function(prefix, suffix) {
        if (prefix.length !== 0) {
            prefix += '.';
        }
        return composeSegment(prefix, suffix);
        };    
   
    fluid.pathUtil.matchPath = function(spec, path) {
        var togo = "";
        while (true) {
          if (!spec) {break;}
          if (!path) {return null;}
          var spechead = fluid.pathUtil.getHeadPath(spec);
          var pathhead = fluid.pathUtil.getHeadPath(path);
          // if we fail to match on a specific component, fail.
          if (spechead !== "*" && spechead !== pathhead) {
              return null;
          }
          togo = fluid.pathUtil.composePath(togo, pathhead);
          spec = fluid.pathUtil.getFromHeadPath(spec);
          path = fluid.pathUtil.getFromHeadPath(path);
        }
        return togo;
      };
  
  
    fluid.model.applyChangeRequest = function(model, request) {
        if (request.type === "ADD") {
            fluid.model.setBeanValue(model, request.path, request.value);
            }
        else if (request.type === "DELETE") {
            var totail = fluid.pathUtil.getToTailPath(request.path);
            var tail = fluid.pathUtil.getTailPath(request.path);
            var penult = fluid.model.getBeanValue(model, penult);
            delete penult[tail];
        }
    };
  
    fluid.model.bindRequestChange = function(that) {
        that.requestChange = function(path, value, type) {
            var changeRequest = {
                path: path,
                value: value,
                type: type
            };
        that.fireChangeRequest(changeRequest);
        }
    };
  
    fluid.makeChangeApplier = function(model) {
        var baseEvents = {
            guards: fluid.event.getEventFirer(false, true),
            modelChanged: fluid.event.getEventFirer(false, false)
        };
        var that = {
            model: model
        };
        function makePredicate(listenerMember, requestIndex) {
          return function(listener, args) {
                var changeRequest = args[requestIndex];
                return fluid.pathUtil.matchPath(listener[listenerMember], changeRequest.path);
            };
        }
        function adaptListener(that, name, listenerMember, requestIndex) {
            var predicate = makePredicate(listenerMember, requestIndex);
            that[name] = {
                addListener: function(pathSpec, listener, namespace) {
                    listener[listenerMember] = pathSpec;
                    baseEvents[name].addListener(listener, namespace, predicate);
                },
                removeListener: function(listener) {
                    baseEvents[name].removeListener(listener);
                }
            };
        }
        
        adaptListener(that, "guards", "guardedPathSpec", 1);
        adaptListener(that, "modelChanged", "triggerPathSpec", 2);
        that.fireChangeRequest = function(changeRequest) {
            if (!changeRequest.type) {
                changeRequest.type = "ADD";
            }
            var prevent = baseEvents.guards.fire(model, changeRequest);
            if (prevent === false) {
                return;
            }
            var oldModel = {};
            fluid.model.copyModel(oldModel, model);
            fluid.model.applyChangeRequest(model, changeRequest);
            baseEvents.modelChanged.fire(model, oldModel, changeRequest);
        };
        fluid.model.bindRequestChange(that);
        
        return that;
    };
    
    fluid.makeSuperApplier = function() {
        var subAppliers = [];
        var that = {};
        that.addSubApplier = function(path, subApplier) {
            subAppliers.push({path: path, subApplier: subApplier});
        };
        that.fireChangeRequest = function(request) {
            for (var i = 0; i < subAppliers.length; ++ i) {
                var path = subAppliers[i].path;
                if (request.path.indexOf(path) === 0) {
                    var subpath = request.path.substring(path.length + 1);
                    var subRequest = fluid.copy(request);
                    subRequest.path = subpath;
                    // TODO: Deal with the as yet unsupported case of an EL rvalue DAR
                    subAppliers[i].subApplier.fireChangeRequest(subRequest);
                }
            }
        };
        return that;
    };
    
    fluid.attachModel = function(baseModel, path, model) {
        var segs = fluid.model.parseEL(path);
        for (var i = 0; i < segs.length - 1; ++ i) {
            var seg = segs[i];
            var subModel = baseModel[seg];
            if (!subModel) {
                baseModel[seg] = subModel = {};
            }
            baseModel = subModel;
        }
        baseModel[segs[segs.length - 1]] = model;
    };
    
    fluid.assembleModel = function (modelSpec) {
       var model = {};
       var superApplier = fluid.makeSuperApplier();
       var togo = {model: model, applier: superApplier};
       for (path in modelSpec) {
           var rec = modelSpec[path];
           fluid.attachModel(model, path, rec.model);
           if (rec.applier) {
              superApplier.addSubApplier(path, rec.applier);
           }
       }
       return togo;
    };

})(jQuery, fluid_1_2);
