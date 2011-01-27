/* The javascript is used in module.php @ $this->_content_tools["js"] */

/*global jQuery*/
/*global ATutor */
/*global tinyMCE */
/*global window */

ATutor = ATutor || {};
ATutor.mods = ATutor.mods || {};
ATutor.mods.basiclti = ATutor.mods.basiclti || {};

(function () {
    var basicLTIOnClick = function () {
        if ( ATutor.mods.editor.content_id == 0 ) {
            alert("Please press save for your content item before configuring the remote tool");
            return;
         }
        window.open(ATutor.base_href + 'mods/basiclti/tool/content_edit.php?cid='+ATutor.mods.editor.content_id + "&framed=1&popup=1",
                    'newWinLTI', 'menubar=0,scrollbars=1,resizable=1,width=640,height=490');
        return false;
    }
    
	//set up click handlers and show/hide appropriate tools
    var initialize = function () {
        jQuery("#basiclti_tool").click(basicLTIOnClick);
    };
    
    jQuery(document).ready(initialize);
})();
