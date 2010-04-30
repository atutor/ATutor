<?php
/************************************************************************/
/* ATutor                                                               */
/************************************************************************/
/* Copyright (c) 2002 - 2009                                            */
/* Adaptive Technology Resource Centre / University of Toronto          */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

define('AT_INCLUDE_PATH', 'include/');
require(AT_INCLUDE_PATH . 'vitals.inc.php');

if (isset($_GET['cid'])) {
	header('Location: '.$_base_href.'content.php?cid='.intval($_GET['cid']));
	exit;
}

if (isset($_SESSION['course_id'])) 
	$course_id = $_SESSION['course_id'];
else if (isset($_GET['p_course'])) // is set when pretty url is turned on and a public course is accessed
	$course_id = $_GET['p_course'];
else // is set when guests access protected course
	$course_id = $_GET['course'];
	
require(AT_INCLUDE_PATH . '../mods/_standard/tests/lib/test_result_functions.inc.php');
	
if (defined('AT_FORCE_GET_FILE') && AT_FORCE_GET_FILE) {
	$course_base_href = 'get.php/';
} else {
	$course_base_href = 'content/' . $course_id . '/';
}

//query reading the type of home viewable. 0: icon view   1: detail view
$sql = "SELECT home_view FROM ".TABLE_PREFIX."courses WHERE course_id = $course_id";
$result = mysql_query($sql,$db);
$row= mysql_fetch_assoc($result);
$home_view = $row['home_view'];

// Enable drag and drop to reorder displayed modules when the module view mode is 
// set to "detail view" and user role is instructor
if ($home_view == 1 && authenticate(AT_PRIV_ADMIN,AT_PRIV_RETURN))
{
	$_custom_head .= '
<link rel="stylesheet" type="text/css" href="'.AT_BASE_HREF.'jscripts/infusion/framework/fss/css/fss-text.css" />
<link rel="stylesheet" type="text/css" href="'.AT_BASE_HREF.'jscripts/infusion/framework/fss/css/fss-theme-mist.css" />
<link rel="stylesheet" type="text/css" href="'.AT_BASE_HREF.'jscripts/infusion/framework/fss/css/fss-theme-hc.css" />
<link rel="stylesheet" type="text/css" href="'.AT_BASE_HREF.'jscripts/infusion/components/reorderer/css/Reorderer.css" />

<script type="text/javascript">
//<!--
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
					jQuery.post("'.AT_BASE_HREF.'move_module.php", { "moved_modules":moved_modules, "from":"course_index" }, function(data) {});     
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
	jQuery.post("'.AT_BASE_HREF.'move_module.php", { "remove":module, "from":"course_index" }, function(data) {});
	jQuery("div[id="+module.replace(/\//g,"-")+"]").remove();
}
//-->
</script>
	
';
}

require(AT_INCLUDE_PATH . 'header.inc.php');

/* the "home" links: */
$home_links = get_home_navigation();
$savant->assign('home_links', $home_links);


/* the news announcements: */
$news = array(); 
$num_pages = 1;
$page = isset($_GET['p']) ? intval($_GET['p']) : 1;
if (!$page) {
	$page = 1;
}	

$module =& $moduleFactory->getModule(AT_MODULE_DIR_STANDARD.'/announcements');
if (!$module->isEnabled()) {
	$result = FALSE;
	$news = array();
} else {
	$sql	= "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."news WHERE course_id=$course_id";
	$result = mysql_query($sql, $db);
}

if ($result && ($row = mysql_fetch_assoc($result))) {
	$num_results = $row['cnt'];
	$results_per_page = NUM_ANNOUNCEMENTS;
	$num_pages = ceil($num_results / $results_per_page);

	$count = (($page-1) * $results_per_page) + 1;

	$offset = ($page-1)*$results_per_page;

	$sql = "SELECT N.*, DATE_FORMAT(N.date, '%Y-%m-%d %H:%i:%s') AS date, first_name, last_name 
	          FROM ".TABLE_PREFIX."news N, ".TABLE_PREFIX."members M 
	         WHERE N.course_id=$course_id 
	           AND N.member_id = M.member_id
	         ORDER BY date DESC LIMIT $offset, $results_per_page";
	
	$result = mysql_query($sql, $db);
	while ($row = mysql_fetch_assoc($result)) {
		/* this can't be cached because it called _AT */

		$news[$row['news_id']] = array(
						'date'		=> AT_date(	_AT('announcement_date_format'), 
						$row['date'],
						AT_DATE_MYSQL_DATETIME),
					  	'author'  => $row['first_name'] . ' ' . $row['last_name'],
						'title'		=> AT_print($row['title'], 'news.title'),
						'body'		=> format_content($row['body'], $row['formatting'], $glossary));

	}
}

$sql = "SELECT banner FROM ".TABLE_PREFIX."courses WHERE course_id=$course_id";
$result = mysql_query($sql, $db);
if ($row = mysql_fetch_assoc($result)) {
	$savant->assign('banner', AT_print($row['banner'], 'courses.banner'));
} else {
	$savant->assign('banner', '');
}

$savant->assign('view_mode', $home_view);
$savant->assign('announcements', $news);
$savant->assign('num_pages', $num_pages);
$savant->assign('current_page', $page);
$savant->display('index.tmpl.php');

require(AT_INCLUDE_PATH.'footer.inc.php');

?>