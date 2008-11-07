<?php  chdir(dirname(__FILE__));

/*
This is a sample config file for the ewiki addon for ATutor. You might choose to modify it
by adding or removing ewiki plugin. See the README & README.plugin and README_ATUTOR_MODULE
 file for details.
*/

if (!$ok) {
	// Setup the flat file database in the ATutor content directory
	// 
	if(@!opendir(AT_CONTENT_DIR.$_SESSION['course_id']."/wiki")){
		mkdir(AT_CONTENT_DIR.$_SESSION['course_id']."/wiki", 0700);
	}
	define("EWIKI_DBFILES_DIRECTORY", AT_CONTENT_DIR.$_SESSION['course_id']."/wiki");
   	include_once("plugins/db/flat_files.php");
}
@define("EWIKI_SCRIPT", "mods/wiki/page.php?page=");

#-- only loaded if it exists
@include_once("local/config.php");

#-- predefine some of the configuration constants
define("EWIKI_PAGE_INDEX", "FrontPage");
define("EWIKI_LIST_LIMIT", 25);
define("EWIKI_HTML_CHARS", 1);
@define("EWIKI_PRINT_TITLE", 1);

/*
 Error reporting is turned off so the downloads.php plugin will not complain
 because it uses allow_call_time_pass_reference = on. Disable the downloads plugin if you
 don't need it. We're hoping this will be fixed in a future version of ewiki.
 allow_call_time_pass_reference should normally be turned off
*/

error_reporting(0);

if($_SESSION['is_admin']){
	$actions_allowed = array($_SESSION['login'] => array("edit", "delete", "control", "admin"));
}

define("EWIKI_PROTECTED_MODE", 1);

	if($_SESSION['is_admin'] ==1){
		define("EWIKI_AUTH_DEFAULT_RING", 0);
	}else if ($_SESSION['enroll'] ==0){
		define("EWIKI_AUTH_DEFAULT_RING", 3);
	}else{
		define("EWIKI_AUTH_DEFAULT_RING", 2);
	}
 // setlocale(LC_TIME, "nl");

#-- helper scripts for broken/outdated PHP configurations
include_once("plugins/lib/fix.php");
include_once("plugins/lib/upgrade.php");


#-- load plugins, before core script ewiki.php
/* Load admin plugins id ATutor user is_admin  or privileged*/
if($_SESSION['is_admin'] ==1 || authenticate(AT_PRIV_WIKI, AT_PRIV_RETURN)){
 	include_once("plugins/admin/control.php");
}

include_once("plugins/init.php");
//include_once("plugins/page/README.php");     
include_once("plugins/pluginloader.php");

include_once("plugins/action/rss.php");

include_once("plugins/appearance/title_calendar.php");
include_once("plugins/appearance/listpages_ul.php");

include_once("plugins/auth/auth_perm_ring.php");
include_once("plugins/auth/userdb_userregistry.php");
include_once("plugins/auth/auth_method_http.php");

include_once("plugins/aview/linktree.php");
include_once("plugins/aview/toc.php");

include_once("plugins/edit/templates.php");

include_once("plugins/interwiki/intermap.php");

include_once("plugins/lib/feed.php");
include_once("plugins/lib/mime_magic.php");

include_once("plugins/markup/css.php");
#enable for page footnotes (footnote anchors are broken however)
#include_once("plugins/markup/footnotes.php");
include_once("plugins/markup/rescuehtml.php");
#enable for full html support (to allow students to create homepages, for instance)
#include_once("plugins/markup/rendering_null.php");
include_once("plugins/markup/naturallists.php");
//include_once("plugins/markup/fix_source_mangling.php");
include_once("plugins/markup/braceabbr.php");
include_once("plugins/markup/table_rowspan.php");

include_once("plugins/meta/meta.php");

include_once("plugins/module/downloads.php");
include_once("plugins/module/calendar.php");
include_once("plugins/module/tour.php");

include_once("plugins/mpi/mpi.php");

include_once("plugins/notify.php");

include_once("plugins/page/powersearch.php");
include_once("plugins/page/ewikilog.php");
include_once("plugins/page/wordindex.php");
include_once("plugins/page/imagegallery.php");
include_once("plugins/page/orphanedpages.php");
include_once("plugins/page/textupload.php");
include_once("plugins/page/wantedpages.php");
include_once("plugins/page/wikidump.php");
include_once("plugins/page/wikinews.php");
include_once("plugins/page/hitcounter.php");
include_once("plugins/page/scandisk.php");
include_once("plugins/page/recentchanges.php");
include_once("plugins/linking/link_icons.php");

include_once("ewiki.php");
?>
