/*
Copyright 2008 University of Toronto

Licensed under the Educational Community License (ECL), Version 2.0 or the New
BSD license. You may not use this file except in compliance with one these
Licenses.

You may obtain a copy of the ECL 2.0 License and BSD License at
https://source.fluidproject.org/svn/LICENSE.txt
*/

/*global jQuery*/
/*global fluid_0_5*/

fluid_0_5 = fluid_0_5 || {};

(function ($, fluid) {
    
    /*
     * Start Pager Link Display 
     */
    
    /**   Private stateless functions   **/
    var updateStyles = function (pageLinks, currentPageStyle, pageNum, oldPageNum) {
        var pageLink, oldLink;
        
        pageLink = $(pageLinks[pageNum - 1]);
        pageLink.addClass(currentPageStyle); 

        if (oldPageNum) {
            oldLink = $(pageLinks[oldPageNum - 1]);
            oldLink.removeClass(currentPageStyle);
        }
        
    };

    var updatePreviousNext = function (previous, next, pageNum, numPageLinks, disabledStyle) {
        if (pageNum < 2) {
            previous.addClass(disabledStyle);
        } else {
            previous.removeClass(disabledStyle);
        }
        
        if (pageNum >= numPageLinks) {
            next.addClass(disabledStyle);
        } else {
            next.removeClass(disabledStyle);
        }
    };
   
    /**   Pager Link Display creator   **/
   
    fluid.pagerLinkDisplay = function (pageLinks, previous, next, currentPageStyle, disabledStyle, pageWillChange) {

        return {
            pageLinks: pageLinks,
            previous: previous,
            next: next,
            selectPage: function (pageNum, oldPageNum) {
                // Do we really want to pass the DOM element or do we just want the page number?
                if (pageWillChange) {
                    var pageLink = $(pageLinks[pageNum - 1]);
                    pageWillChange(pageLink[0]);
                }
                updateStyles(pageLinks, currentPageStyle, pageNum, oldPageNum);
                updatePreviousNext(previous, next, pageNum, pageLinks.length, disabledStyle);        
            },
            pageIsSelected: function (pageNum, oldPageNum) {
                updateStyles(pageLinks, currentPageStyle, pageNum, oldPageNum);        
                updatePreviousNext(previous, next, pageNum, pageLinks.length, disabledStyle);        
            }

        };
    };
   
    /*
     * Start of Pager Bar
     */

    /**   Pager Bar creator   **/

    fluid.pagerBar = function (bar, selectors, currentPageStyle, disabledStyle, pageWillChange) {  
        var pageLinks, previous, next, linkDisplay, isPageLink, isNext, isPrevious;
              
        pageLinks = $(selectors.pageLinks, bar);
        previous = $(selectors.previous, bar);
        next = $(selectors.next, bar);
        
        linkDisplay = fluid.pagerLinkDisplay(pageLinks, previous, next, currentPageStyle, disabledStyle, pageWillChange);
        
        isPageLink = function (element) {
            return pageLinks.index(element) > -1;
        };
        isNext = function (element) {
            return (element === next[0]);
        };
        isPrevious = function (element) {
            return (element === previous[0]);
        };
    
        return {
            bar: bar,
            linkDisplay: linkDisplay,
            selectPage: function (pageNum, oldPageNum) {
                linkDisplay.selectPage(pageNum, oldPageNum);
            },
            pageIsSelected: function (pageNum, oldPageNum) {
                linkDisplay.pageIsSelected(pageNum, oldPageNum);
            },
            pageNumOfLink: function (link) {
                link = fluid.findAncestor(link, isPageLink);
                return pageLinks.index(link) + 1;
            },
            isNext: function (link) {
                return !!fluid.findAncestor(link, isNext);
            },
            isPrevious: function (link) {
                return !!fluid.findAncestor(link, isPrevious);
            }
        };
    };

    /* 
     * Start of the Pager
     */
    
    /**   Private stateless functions   **/
    var bindSelectHandler = function (pager) {
        var selectHandler = function (evt) {
            // We need a better way of checking top and bottom bar. This is so repetitive.
            if (pager.topBar.isNext(evt.target) || pager.bottomBar.isNext(evt.target)) {
                pager.next();
                return false;
            }
            if (pager.topBar.isPrevious(evt.target) || pager.bottomBar.isPrevious(evt.target)) {
                pager.previous();
                return false;
            }
            var newPageNum = pager.topBar.pageNumOfLink(evt.target) || pager.bottomBar.pageNumOfLink(evt.target);
            if (newPageNum < 1) {
                return true;
            }

            pager.selectPage(newPageNum);
            return false;
        };

        pager.container.click(selectHandler);
    };

    /**   Constructor  **/ 
               
    fluid.Pager = function (componentContainerId, options) {
        var selectors, top, bottom;
        
        // Mix in the user's configuration options.
        options = options || {};
        selectors = $.extend({}, this.defaults.selectors, options.selectors);
        this.styles = $.extend({}, this.defaults.styles, options.styles);
        this.pageWillChange = options.pageWillChange || this.defaults.pageWillChange; 

        // Bind to the DOM.
        this.container = fluid.jById(componentContainerId);
        
        // Create pager bars
        top = $(selectors.pagerTop, this.container);
        this.topBar = fluid.pagerBar(top, selectors, this.styles.currentPage, this.styles.disabled, this.pageWillChange);
        bottom = $(selectors.pagerBottom, this.container);
        this.bottomBar = fluid.pagerBar(bottom, selectors, this.styles.currentPage, this.styles.disabled, this.pageWillChange);

        this.pageNum = 1;
        this.topBar.pageIsSelected(this.pageNum);
        this.bottomBar.pageIsSelected(this.pageNum);
        
        bindSelectHandler(this);
    };
 
     /**   Public stuff   **/   
     
    fluid.Pager.prototype.defaults = {
        selectors: {
            pagerTop: ".pager-top",
            pagerBottom: ".pager-bottom",
            pageLinks: ".page-link",
            previous: ".previous",
            next: ".next"
        },

        styles: {
            currentPage: "current-page",
            disabled: "disabled"
        },
        
        pageWillChange: function (link) {
            // AJAX call here
        }
    };
    
    fluid.Pager.prototype.selectPage = function (pageNum) {
        if (pageNum === this.pageNum) {
            return;
        }
        this.topBar.selectPage(pageNum, this.pageNum);
        this.bottomBar.pageIsSelected(pageNum, this.pageNum);
        this.pageNum = pageNum;
    };
    
    fluid.Pager.prototype.next = function () {
        // this test needs to be refactored - we know too much about the implementation I think
        if (this.pageNum < this.topBar.linkDisplay.pageLinks.length) {
            this.selectPage(this.pageNum + 1);
        }
    };
   
    fluid.Pager.prototype.previous = function () {
        if (this.pageNum > 1) {
            this.selectPage(this.pageNum - 1);
        }
    };
    
})(jQuery, fluid_0_5);
