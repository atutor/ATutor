/**
 * @author Alexey Novak
 * @copyright Copyright © 2013, ATutor, All rights reserved.
 */

/*global jQuery*/
/*global ATutor */

ATutor = ATutor || {};
ATutor.mods = ATutor.mods || {};
ATutor.mods.tests = ATutor.mods.tests || {};

(function () {
    "use strict";
    
    /**
    * Function mainly for edit_test and create_test for disabling/enabling elements depending on user interactions
    * @author    Alexey Novak
    */
    ATutor.mods.tests.disable_elements = function(name, disableFlag) {
        var passpercent = $("#passpercent"),
            passscore = $("#passscore"),
            num_questions = $("#num_questions"),
            show_guest_form = $("#show_guest_form");
        
        // Disable/Enable elements based on what user clicked
        if (name === "both") {
            passpercent.attr("disabled", "disabled");
            passscore.attr("disabled", "disabled");
            passpercent.val(0);
            passscore.val(0);
        } else if (name === "percentage") {
            passpercent.attr("disabled", "disabled");
            passscore.removeAttr("disabled");
            passpercent.val(0);
        } else if (name === "points") {
            passpercent.removeAttr("disabled");
            passscore.attr("disabled", "disabled");
            passscore.val(0);
        } else if (name === "num_questions") {
            num_questions[(disableFlag) ? "attr" : "removeAttr"]("disabled", "");
        } else if (name === "show_guest_form") {
            show_guest_form[(disableFlag) ? "attr" : "removeAttr"]("disabled", "");
            if (disableFlag) {
                show_guest_form.attr("checked", false);
            }
        }
    };
    
    /**
    * Function to scroll to the selector
    * @author    Alexey Novak
    */
    ATutor.mods.tests.jumpTo = function(selector) {
        var selector = $(selector);
        
        if (selector.length === 0) {
            return;
        }
        
        $("html, body").animate({
            scrollTop: $(selector).offset().top
        }, 1);
    };
    
    /**
    * Function to scroll to the top of the window and focus on the Test Begin button.
    * @author    Alexey Novak
    */
    ATutor.mods.tests.focusBegin = function() {
        ATutor.mods.tests.jumpTo("a[name=content]");
        $(".row.buttons input[name=submit]").focus();
    };
    /**
    * Function to activate Slide All link for the Remedial Content divs
    * @author    Alexey Novak
    */
    var activateHideAllLink = function(collapsibleElements, hideAllLink) {
        // If there is no remedial content then just exit
        if (collapsibleElements.length === 0) {
            return;
        }
        
        // Show the link
        hideAllLink.show();
        
        // Assign a click to the link
        hideAllLink.click(function (event) {
            var link = (event.currentTarget) ? $(event.currentTarget) : $(event.srcElement),
                linkHideText = link.find(".hideAllLabel"),
                linkShowText = link.find(".showAllLabel"),
                isAllHidden = linkShowText.is(":visible"),
                linkClass;
            
            if (isAllHidden) {
                linkHideText.show();
                linkShowText.hide();
                linkClass = ".show";
            } else {
                linkHideText.hide();
                linkShowText.show();
                linkClass = ".hide";
            }
            
            collapsibleElements.filter(linkClass).click();
            
            link.focus();
            return false;
        });    
    };
    
    /**
    * Function which would assign a slide function to all areas with links which have a specified class
    * @options
    *    collapsibleElements                - jQuery links which would have a click event bind to them
    *    collapsedClass                    - class which will be added/removed to the div area which will have a slide effect
    *    fieldsetNotCollapsedMinHeight    - min-height for the div area when it is not collapsed
    *    fieldsetCollapsedMinHeight        - min-height for the div area when it is collapsed
    * @author    Alexey Novak
    */
    var makeCollapsibles = function(options) {
        options = options || {};
        
        var collapsibleElements = options.collapsibleElements;
        
        if (!collapsibleElements || collapsibleElements.length === 0) {
            return;
        }
        
        // Bind the click event
        collapsibleElements.click(function (event) {
            var link = (event.currentTarget) ? $(event.currentTarget) : $(event.srcElement),
                linkHideText = link.find(".hideLabel"),
                linkShowText = link.find(".showLabel"),
                fieldset = link.parent().parent(),
                row = fieldset.find(".row"),
                collapsedClass = options.collapsedClass,
                isCollapsed = fieldset.hasClass(collapsedClass),
                addRemoveClass, rowShowHide;
            
            // If area is currently sliding then just do not do anything
            if (row.is(":animated")) {
                return;
            }
            
            if (isCollapsed) {
                linkHideText.show();
                linkShowText.hide();
                
                link.addClass("hide").removeClass("show");
    
                addRemoveClass = "removeClass";
                rowShowHide = "slideDown";
                
                fieldset[addRemoveClass](collapsedClass);
                fieldset.animate({"min-height": options.fieldsetNotCollapsedMinHeight}, "slow");
                row[rowShowHide]("slow");
            } else {
                linkHideText.hide();
                linkShowText.show();
                
                link.removeClass("hide").addClass("show");
                
                addRemoveClass = "addClass";
                rowShowHide = "slideUp";
                
                fieldset.animate({"min-height": options.fieldsetCollapsedMinHeight}, "slow");
                
                row[rowShowHide]("slow", function() {
                    fieldset[addRemoveClass](collapsedClass);
                });
            }
            
            link.focus();
            return false;
        });
    };
    
    /**
    * Function to be called upon page load
    * @author    Alexey Novak
    */
    var initialize = function() {
        // Get all the links for remedial content and Hide All Remedial content link
        var collapsibleElements = $(".collapsible"),
            hideAllLink = $(".hideAllRemedialLink");
        
        // Set up Remedial Content Hide/Show separate links
        makeCollapsibles({
            collapsibleElements: collapsibleElements,
            collapsedClass: "collapsed",
            fieldsetNotCollapsedMinHeight: "170px",
            fieldsetCollapsedMinHeight: "5px"
        });
        
        // Set up Hide All Remedial Content link
        activateHideAllLink(collapsibleElements, hideAllLink);
    };

    jQuery(document).ready(initialize);
})();