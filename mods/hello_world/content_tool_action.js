/* The javascript is used in module.php @ $this->_content_tools["js"] */

/*global jQuery*/
/*global ATutor */
/*global tinyMCE */
/*global window */

ATutor = ATutor || {};
ATutor.mods = ATutor.mods || {};
ATutor.mods.hello_world = ATutor.mods.hello_world || {};

(function () {
    var helloWorldOnClick = function () {
    	alert("Clicked on hello world tool icon!");
    }
    
	//set up click handlers and show/hide appropriate tools
    var initialize = function () {
        jQuery("#helloworld_tool").click(helloWorldOnClick);
    };
    
    jQuery(document).ready(initialize);
})();