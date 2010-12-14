/*
Copyright 2010 University of Toronto
Copyright 2010 OCAD University

Licensed under the Educational Community License (ECL), Version 2.0 or the New
BSD license. You may not use this file except in compliance with one these
Licenses.

You may obtain a copy of the ECL 2.0 License and BSD License at
https://source.fluidproject.org/svn/LICENSE.txt
*/

/*global fluid, mccord, jQuery*/

var fluid_1_3 = fluid_1_3 || {};
var fluid = fluid || fluid_1_3;

(function ($) {

    fluid.model = fluid.model || {};
    fluid.model.transform = fluid.model.transform || {};
    
    
    /******************************
     * General Model Transformers *
     ******************************/
    
    fluid.model.transform.value = function (model, expandSpec, recurse) {
        var val;
        if (expandSpec.path) {
            val = fluid.get(model, expandSpec.path);
            
            if (typeof(val) !== "undefined") {
                return val;
            }
        }
        
        return typeof(expandSpec.value) === "object" ? recurse(model, expandSpec.value) : expandSpec.value;    
    };
    
    fluid.model.transform.arrayValue = function (model, expandSpec, recurse) {
        return fluid.makeArray(fluid.model.transform.value(model, expandSpec));
    };
     
    fluid.model.transform.count = function (model, expandSpec, recurse) {
        var value = fluid.get(model, expandSpec.path);
        return fluid.makeArray(value).length;
    };
     
    fluid.model.transform.firstValue = function (model, expandSpec, recurse) {
        var result;
        for (var i = 0; i < expandSpec.values.length; i++) {
            var value = expandSpec.values[i];
            if (typeof(value) === "string") {
                value = fixupExpandSpec(value);
            }
            result = fluid.model.transform.value(model, value.expander, recurse);
            if (typeof(result) !== "undefined") {
                break;
            }
        }
        return result;
    };
    
    var getOrRecurse = function (model, value, recurse) {
        return typeof(value) === "string" ? fluid.get(model, value) : recurse(model, value, recurse);
    };
    
    fluid.model.transform.merge = function (model, expandSpec, recurse) {
        var left = getOrRecurse(model, expandSpec.left, recurse);
        var right = getOrRecurse(model, expandSpec.right, recurse);
        
        if (typeof(left) !== "object" || typeof(right) !== "object") {
            return left;
        }
        
        return fluid.merge(expandSpec.policy ? expandSpec.policy : null, {}, left, right);
    };
     
    var fixupExpandSpec = function (expandSpec) {
        return {
            expander: {
                type: "fluid.model.transform.value",
                path: expandSpec
            }
        };
    };
    
    var expandRule = function (model, targetPath, rule) {
        var expanded = {};
        for (var key in rule) {
            var value = rule[key];
            if (key === "expander") {
                var expanderFn = fluid.getGlobalValue(value.type);
                if (expanderFn) {
                    expanded = expanderFn.call(null, model, value, fluid.model.transformWithRules);
                }
            } else {
                expanded[key] = fluid.model.transformWithRules(model, value);
            }
        }
        return expanded;
    };
    
    /**
     * Transforms a model based on a specified expansion rules objects.
     * Rules objects take the form of:
     *   {
     *       "target.path": "value.el.path" || {
     *          expander: {
     *              type: "expander.function.path",
     *               ...
     *           }
     *       }
     *   }
     *
     * @param {Object} model the model to transform
     * @param {Object} rules a rules object containing instructions on how to transform the model
     */
    fluid.model.transformWithRules = function (model, rules) {
        var transformed = {};
        for (var targetPath in rules) {
            var rule = rules[targetPath];
            
            if (typeof(rule) === "string") {
                rule = fixupExpandSpec(rule);
            }
            
            var expanded = expandRule(model, targetPath, rule);
            if (typeof(expanded) !== "undefined") {
                fluid.set(transformed, targetPath, expanded);
            }
        };
        return transformed;
    };
    
})(jQuery, fluid_1_3);
