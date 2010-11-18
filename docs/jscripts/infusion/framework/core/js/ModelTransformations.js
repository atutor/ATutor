/*global fluid, mccord, jQuery*/

var fluid_1_2 = fluid_1_2 || {};
var fluid = fluid || fluid_1_2;

(function ($) {

    fluid.model = fluid.model || {};
    fluid.model.transform = fluid.model.transform || {};
    
    /******************************
     * General Model Transformers *
     ******************************/
    fluid.model.transform.value = function (model, options) {
        if (!options.path && options.literalValue) {
            return options.literalValue;
        }
         
        try {
            return fluid.model.getBeanValue(model, options.path) || options.defaultValue;
        } catch (e) {
            if (options.failOnInvalidPath) {
                throw new Error ("Model transformation error: Can't find a LHS value at path \"" + options.path + "\".");
            }
        }
    };
    
    fluid.model.transform.arrayValue = function (model, options) {
        return $.makeArray(fluid.transformers.value(model, options));
    };
     
    fluid.model.transform.count = function (model, options) {
        try {
            var value = fluid.model.getBeanValue(model, options.path);
            return value ? $.makeArray(value).length : 0;
        } catch (e) {
            return 0;
        }
    };
     
    fluid.model.transform.firstAvailableValue = function (model, options) {
        var value;
        for (var i = 0; i < options.paths.length; i++) {
            var path = options.paths[i];
            value = fluid.model.getBeanValue(model, path);
            if (value) {
                break;
            }
        }
        return value;
    };
    
    
    /******************************
     * Basic model transformation *
     ******************************/
     
    var fixupTransformSpec = function (transformSpec) {
        return {
            transformer: "fluid.model.transform.value",
            options: {
                path: transformSpec
            }
        };
    }
    
    fluid.model.transformModel = function (entityNames, transformDoc, model) {
        var transformedModel = {};
        entityNames = $.makeArray(entityNames);
        
        $.each(entityNames, function (idx, entityName) {
            var entityTransform = transformDoc[entityName];
            var transformed = fluid.transform(entityTransform, function (transformSpec, key) {
                // If we get a right hand side value of a string, assume this is a "value" transform.
                if (typeof(transformSpec) === "string") {
                    transformSpec = fixupTransformSpec(transformSpec);
                }
                return fluid.invokeGlobalFunction(transformSpec.transformer, [model, transformSpec.options]);
            });
            $.extend(transformedModel, transformed);
        });
        
        return transformedModel;
    };
    
})(jQuery, fluid_1_2);
