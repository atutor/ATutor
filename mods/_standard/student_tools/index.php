<?php
/************************************************************************/
/* ATutor                                                               */
/************************************************************************/
/* Copyright (c) 2002 - 2009                                            */
/* Inclusive Design Institute                                           */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

$fha_student_tools = array();

$sql = "SELECT links FROM ".TABLE_PREFIX."fha_student_tools WHERE course_id=$_SESSION[course_id]";
$result = mysql_query($sql, $db);
if ($row = mysql_fetch_assoc($result)) {
	$fha_student_tools = explode('|', $row['links']);
}

if($fha_student_tools[0] == "" ){
	$msg->addInfo('NO_TOOLS_FOUND');
}

$sql = "SELECT home_view FROM ".TABLE_PREFIX."fha_student_tools WHERE course_id = $_SESSION[course_id]";
$result = mysql_query($sql,$db);
$row= mysql_fetch_assoc($result);
$home_view = $row['home_view'];

// Enable drag and drop to reorder displayed modules when the module view mode is 
// set to "detail view" and user role is instructor
if ($home_view == 1 && authenticate(AT_PRIV_ADMIN,AT_PRIV_RETURN))
{
	$_custom_head .= '
<link rel="stylesheet" type="text/css" href="'.AT_BASE_HREF.'jscripts/infusion/framework/fss/css/fss-layout.css" />
<link rel="stylesheet" type="text/css" href="'.AT_BASE_HREF.'jscripts/infusion/framework/fss/css/fss-text.css" />
<link rel="stylesheet" type="text/css" href="'.AT_BASE_HREF.'jscripts/infusion/framework/fss/css/fss-theme-mist.css" />
<link rel="stylesheet" type="text/css" href="'.AT_BASE_HREF.'jscripts/infusion/framework/fss/css/fss-theme-hc.css" />
<link rel="stylesheet" type="text/css" href="'.AT_BASE_HREF.'jscripts/infusion/components/reorderer/css/Reorderer.css" />

<script type="text/javascript">
jQuery(document).ready(function () {
	var reorder_example_grid = fluid.reorderGrid("#details_view",  {
		selectors : {
			movables : ".home_box"
		},
	    listeners: {
			afterMove: function (item, requestedPosition, movables) {
				//save the state to the db
				var myDivs = jQuery ("div[class^=home_box]", "#details_view");
				var moved_modules = "";
				
				if (myDivs.constructor.toString().indexOf("Array"))   // myDivs is an array
				{
					for (i=0; i<myDivs.length; i++)
						moved_modules += myDivs[i].id+"|";
				}
				moved_modules = moved_modules.substring(0, moved_modules.length-1); // remove the last "|"
				
				if (moved_modules != "")
					jQuery.post("'.AT_BASE_HREF.'move_module.php", { "moved_modules":moved_modules, "from":"student_tools" }, function(data) {});     
	        }
	    },
		styles: {
		    selected: "draggable_selected",
		    hover: "draggable_selected"
		}
});

});

function remove_module(module)
{
	jQuery.post("'.AT_BASE_HREF.'move_module.php", { "remove":module, "from":"student_tools" }, function(data) {});
	jQuery("div[id="+module.replace(/\//g,"-")+"]").remove();
}

</script>
	
';
}

require (AT_INCLUDE_PATH.'header.inc.php');

$home_links = array();

if($fha_student_tools[0] != "" ){
	//query reading the type of home viewable. 0: icon view   1: detail view
	
	$savant->assign('view_mode', $home_view);
	$savant->assign('home_links', get_home_navigation($fha_student_tools));
}

$savant->assign('num_pages', 0);
$savant->assign('current_page', 0);
$savant->display('index.tmpl.php');

?>


<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>