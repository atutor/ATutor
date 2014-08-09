<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca                                                     */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id$
if (!defined('AT_INCLUDE_PATH')) { exit; }

//header('Cache-Control: private, pre-check=0, post-check=0, max-age=0');

//Harris Timer
  $mtime = microtime(); 
  $mtime = explode(' ', $mtime); 
  $mtime = $mtime[1] + $mtime[0]; 
  $starttime = $mtime; 
//Harris Timer Ends

global $myLang;
global $savant;
global $onload;
global $content_base_href, $course_base_href;
global $_base_path;
global $cid;
global $contentManager;
global $db;
global $_pages;
global $_stacks;
global $framed, $popup;
global $_custom_css;
global $_custom_head;
global $_custom_script;
global $substr, $strlen, $_course_id;
global $_tool_shortcuts;
global $content_description;
global $content_keywords;

require(AT_INCLUDE_PATH . 'lib/menu_pages.php');
//require(AT_INCLUDE_PATH."../jscripts/opensocial/all_opensocial.php");

$_custom_css = AT_print($_custom_css, 'url.css');
$savant->assign('lang_code', $_SESSION['lang']);
$savant->assign('lang_charset', $myLang->getCharacterSet());
$savant->assign('base_path', AT_print($_base_path, 'url.self'));
$savant->assign('base_tmpl_path', $_SERVER['HTTP_HOST']);
$savant->assign('theme', $_SESSION['prefs']['PREF_THEME']);
$savant->assign('theme_path', AT_print(AT_CUSTOMIZED_DATA_DIR, 'url.self'));
$savant->assign('current_date', AT_date(_AT('announcement_date_format')));
$savant->assign('just_social', $_config['just_social']);
$theme_img  = AT_print($_base_path, 'url.base') . 'themes/'. $_SESSION['prefs']['PREF_THEME'] . '/images/';
$savant->assign('img', $theme_img);

$_tmp_base_href = AT_BASE_HREF;
if (isset($course_base_href) || isset($content_base_href)) {
	$_tmp_base_href .= $course_base_href;
	if ($content_base_href) {
		$_tmp_base_href .= $content_base_href;
	}
}

$savant->assign('content_base_href', AT_print($_tmp_base_href, 'url.self'));
$savant->assign('base_href', AT_print(AT_BASE_HREF, 'url.self'));

//Handle pretty url pages
if ((($_config['course_dir_name'] + $_config['pretty_url']) > 0) && ($temp = strpos($_SERVER['PHP_SELF'], AT_PRETTY_URL_HANDLER)) > 0){
	$current_page = $pretty_current_page; //this is set in AT_PRETTY_URL_HANDLER
}

if ($myLang->isRTL()) {
	$savant->assign('rtl_css', '<link rel="stylesheet" href="'.AT_print($_base_path, 'url.base').'themes/'.$_SESSION['prefs']['PREF_THEME'].'/rtl.css" type="text/css" />');
} else {
	$savant->assign('rtl_css', '');
}

$custom_head = '';
if (isset($_custom_css)) {
	$custom_head = '<link rel="stylesheet" href="'.$_custom_css.'" type="text/css" />';
}

if (isset($_custom_head)) {
	$custom_head .= '
' . $_custom_head;
}
if (isset($_custom_script)) {
	$custom_head .= '
' . $_custom_script;
}
$custom_head .= '<meta name="google-site-verification" content="dNU6z27-f4GLPfPishC4RK8HhFdtjvr6-Hca2GFn5to" />';
$custom_head .= '
    <script type="text/javascript" src="'.AT_print($_base_path, 'url.base').'jscripts/lib/jquery-scrolltofixed-min.js"></script>
    <script type="text/javascript" src="'.AT_print($_base_path, 'url.base').'jscripts/lib/jquery.cookie.js"></script>';
// Set session timeout warning if user is logged in
if(isset($_SESSION['valid_user'])){
    // Setup the timeout warning when a user logs in
    if($_config['session_timeout']){
        $_at_timeout = ($_config['session_timeout']*60);
    }else{
        $_at_timeout = '1200';
    }

    $session_timeout = intVal($_at_timeout) * 1000;
    $session_warning = 300 * 1000;                      // 5 minutes
    
    $custom_head .= '
        <link rel="stylesheet"  type="text/css" href="'.AT_print($_base_path, 'url.base').'jscripts/lib/jquery-ui.css" />
        <script type="text/javascript" src="'.AT_print($_base_path, 'url.base').'jscripts/infusion/lib/jquery/core/js/jquery.js"></script>
        <script type="text/javascript" src="'.AT_print($_base_path, 'url.base').'jscripts/lib/jquery-ui.min.js"></script>
        <script type="text/javascript" src="'.AT_print($_base_path, 'url.base').'jscripts/ATutorAutoLogout.js"></script>
        <script type="text/javascript" src="'.AT_print($_base_path, 'url.base').'jscripts/lib/jquery.cookie.js"></script>
        <script type="text/javascript" src="'.AT_print($_base_path, 'url.base').'jscripts/lib/jquery-scrolltofixed-min.js"></script>
    	<script type="text/javascript" src="'.AT_print($_base_path, 'url.base').'jscripts/lib/jquery.switch.min.js"></script>
        <link rel="stylesheet" type="text/css" href="'.AT_print($_base_path, 'url.base').'jscripts/lib/jquery.switch.css">';
    if(isset($_SESSION['member_id'])){
        $custom_head .= "\n".'    <script type="text/javascript">
        $(document).ready(function() {
            ATutor.autoLogout({
                timeLogout              : '.$session_timeout.',
                timeWarningBeforeLogout : '.$session_warning.',
                logoutUrl               : "'.AT_print($_base_path, 'url.base').'logout.php",
                title                   : "'._AT('session_timeout_title').'",
                textButtonLogout        : "'._AT('session_timeout_logout_now').'",
                textButtonStayConnected : "'._AT('session_timeout_stay_connected').'",
                message                 : "'._AT('session_will_expire').'"
            });
        });
    
        </script>';
    }
}
    //////////
    // Analytics for ATutorSpaces course site usage tracking
    
        $custom_head .= "\n"." <!--  // We collect anonymous usage data to help us better understand \n         how ATutor is used, and to improve it to best suit those using it.\n --> <script type=\"text/javascript\">
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-42182978-1', 'auto');
  ga('send', 'pageview');
        </script>"; 
    /////////////    
// End session timeout warning

$savant->assign('custom_css', $custom_head);

if ($onload && ($_SESSION['prefs']['PREF_FORM_FOCUS'] || ($substr($onload, -8) != 'focus();'))) {
	$savant->assign('onload', $onload);
}

if (isset($_SESSION['valid_user']) && $_SESSION['valid_user'] === true) {
	if (!empty($_SESSION['member_id'])) {
		$savant->assign('user_name', get_display_name($_SESSION['member_id']));
	} else {
		$savant->assign('user_name', $_SESSION['login']);
	}
} else {
	$savant->assign('user_name', _AT('guest'));
}

if (!isset($_pages[$current_page])) {
	global $msg;
	$msg->addError('PAGE_NOT_FOUND'); // probably the wrong error
	header('location: '.AT_BASE_HREF.'index.php');
	exit;
}

$_top_level_pages        = get_main_navigation($current_page);

$_current_top_level_page = get_current_main_page($current_page);

if (empty($_top_level_pages)) {
	if (!$_SESSION['member_id'] && !$_SESSION['course_id']) {
		$_top_level_pages = get_main_navigation($_pages[AT_NAV_PUBLIC][0]);
	} else if ($_SESSION['course_id'] < 0) {
		$_top_level_pages = get_main_navigation($_pages[AT_NAV_ADMIN][0]);
	} else if (!$_SESSION['course_id']) {
		$_top_level_pages = get_main_navigation($_pages[AT_NAV_START][0]);
	} else {
		$_top_level_pages = get_main_navigation($_pages[AT_NAV_COURSE][0]);
	}
}


/****
Toggle to switch between mobile and responsive themes
****/
if($_GET['mobile'] == '2'){
	global $msg;
	unset($_SESSION['prefs']['PREF_RESPONSIVE'] );
	if(isset($_GET['cid'])){
		$cid="?cid=".$_GET['cid'];
	}
	//unset($_COOKIE['responsive']);
	setcookie("responsive", NULL, -1);
	save_prefs();
	$msg->addFeedback('MOBILE_ON');
	header('Location:'.$_SERVER['PHP_SELF'].$cid);
	exit;
} else if($_GET['mobile'] == '1') {
	global $msg;
	$_SESSION['prefs']['PREF_RESPONSIVE'] = 1;
	if(isset($_GET['cid'])){
		$cid="?cid=".$_GET['cid'];
	}
	save_prefs();
	setcookie("responsive", $_SESSION['prefs']['PREF_RESPONSIVE'], (time()+60*60*24*30)); // 30 day expire
	$msg->addFeedback('MOBILE_OFF');
	header('Location:'.$_SERVER['PHP_SELF'].$cid);
	exit;
}
$_sub_level_pages        = get_sub_navigation($current_page);
$_sub_level_pages_i        = get_sub_navigation_i($current_page);
$_current_sub_level_page = get_current_sub_navigation_page($current_page);
$_current_sub_level_page_i = get_current_sub_navigation_page_i($current_page);

$_path = get_path($current_page);
unset($_path[0]);
if (isset($_pages[$current_page]['title'])) {
	$_page_title = $_pages[$current_page]['title'];
} else {
	$_page_title = _AT($_pages[$current_page]['title_var']);
}

/*****
* When setting the back_to_page, determine the URL the tool is being accessed from
* and the title of that page, and hold in the SESSION for as long as that tool
* is being used. Allows return via a student page or the Manage page.
****/
global $_base_path, $_pages;
$mod_path = str_replace($_base_path, '', $_SERVER['PHP_SELF']);

if(!isset($_SESSION['tool_origin'])){
    $_SESSION['origin_title'] = $_page_title;
}
if(isset($_SESSION['tool_origin'])){
    if($_SESSION['tool_origin']['url'] == $_base_href.$current_page){
        unset($_SESSION['tool_origin']);
        unset($back_to_page);      
    }else if($_pages[$mod_path]['parent'] != 'tools/index.php'){
        $back_to_page['title'] = _AT($_pages[$_pages[$mod_path]['parent']]['title_var']);
        $back_to_page['url'] = $_base_href.$_pages[$mod_path]['parent'];
    } else{
        $back_to_page = $_SESSION['tool_origin'];
        
    }

} else if (isset($_path[2]['url'], $_sub_level_pages[0]['url']) && $_path[2]['url'] == $_sub_level_pages[0]['url']) {
	$back_to_page = $_path[3];
} else if (isset($_path[1]['url'], $_sub_level_pages[0]['url']) && $_path[1]['url'] == $_sub_level_pages[0]['url']) {
	$back_to_page = isset($_path[2]) ? $_path[2] : null;
} else if (isset($_path[2]['url'], $_sub_level_pages_i[0]['url']) && $_path[2]['url'] == $_sub_level_pages_i[0]['url']) {
	$back_to_page = $_path[3];
} else if (isset($_path[1]['url'], $_sub_level_pages_i[0]['url']) && $_path[1]['url'] == $_sub_level_pages_i[0]['url']) {
	$back_to_page = isset($_path[2]) ? $_path[2] : null;
} else if (isset($_path[1])) {
	$back_to_page = $_path[1];
} 

if (isset($_SESSION['course_id']) && $_SESSION['course_id'] > 0) {
	$_path[] = array('url' => AT_print($_base_path . url_rewrite('index.php'),'url.base'), 'title' => $_SESSION['course_title']);
} else if (isset($_SESSION['course_id']) && $_SESSION['course_id'] < 0) {
	$_path[] = array('url' => AT_print($_base_path . 'admin/index.php', 'url.base'), 'title' => _AT('administration'));
}

if (isset($_SESSION['member_id']) && $_SESSION['member_id']) {
	$_path[] = array('url' =>  AT_print($_base_path . 'bounce.php?course=0', 'url.base'), 'title' => _AT('my_start_page'));
} else if (!isset($_SESSION['course_id']) || !$_SESSION['course_id']) {
	$_path[] = array('url' =>  AT_print($_base_path . 'login.php', 'url.base'), 'title' => SITE_NAME);
}

$_path = array_reverse($_path);
/*
if (isset($_pages[$current_page]['title'])) {
	$_page_title = $_pages[$current_page]['title'];
} else {
	$_page_title = _AT($_pages[$current_page]['title_var']);
}
*/


/* calculate the section_title: */
if (isset($_SESSION['course_id']) && $_SESSION['course_id'] > 0) {
	//Truncate course title if it's > 45.
	$session_course_title = htmlentities($_SESSION['course_title'], ENT_QUOTES, 'UTF-8');
	$section_title = validate_length($session_course_title, 100, VALIDATE_LENGTH_FOR_DISPLAY);
	// If there is an icon, display it on the header

	$sql = 'SELECT icon FROM %scourses WHERE course_id=%d';
	$row =  queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id']), TRUE);

	if (!empty($row['icon'])){
		//Check if this is a custom icon, if so, use get_course_icon.php to get it
		//Otherwise, simply link it from the images/
		$custom_icon_path = AT_CONTENT_DIR.$_SESSION['course_id']."/custom_icons/";
		if (file_exists($custom_icon_path.$row['icon'])) {
			if (defined('AT_FORCE_GET_FILE') && AT_FORCE_GET_FILE) {
				$course_icon = AT_print($_base_path, 'url.base').'get_course_icon.php/?id='.$_SESSION['course_id'];
			} else {
				$course_icon = AT_print($_base_path, 'url.base').'content/' . $_SESSION['course_id'] . '/';
			}
		} else {
			$course_icon = AT_print($_base_path, 'url.base').'images/courses/'.$row['icon'];
		}
		$savant->assign('icon', $course_icon);
	}
} else if (!isset($_SESSION['valid_user']) || !$_SESSION['valid_user']) {
	$section_title = SITE_NAME;
	if (defined('HOME_URL') && HOME_URL) {
		$_top_level_pages[] = array('url' => HOME_URL, 'title' => _AT('home'));
	}
} else if ($_SESSION['course_id'] < 0) {
	$section_title = _AT('administration');
} else if (!$_SESSION['course_id']) {
	$section_title = _AT('my_start_page');
}

if($_current_sub_level_page_i){
    $savant->assign('current_sub_level_page', $_current_sub_level_page_i);
} else{
    $savant->assign('current_sub_level_page', $_current_sub_level_page);
}
$savant->assign('current_top_level_page', $_current_top_level_page);
$savant->assign('sub_level_pages', $_sub_level_pages);
$savant->assign('sub_level_pages_i', $_sub_level_pages_i);
$savant->assign('path', $_path);
$savant->assign('back_to_page', isset($back_to_page) ? $back_to_page : null);
$savant->assign('page_title', stripslashes(htmlspecialchars($_page_title, ENT_COMPAT, "UTF-8")));
$savant->assign('top_level_pages', $_top_level_pages);
$savant->assign('section_title', $section_title);
$savant->assign('content_keywords', $content_keywords);
$savant->assign('content_description', $content_description);

if (isset($_pages[$current_page]['guide'])) {
	$savant->assign('guide', AT_GUIDES_PATH . $_pages[$current_page]['guide']);
}

$myLang->sendContentTypeHeader();

if (isset($_SESSION['course_id']) && $_SESSION['course_id'] > -1) {

	/* the list of our courses: */
	/* used for the courses drop down */
	global $system_courses;
	if ($_SESSION['valid_user']) {

		$sql	= "SELECT E.course_id FROM %scourse_enrollment E WHERE E.member_id=%d AND E.approved<>'n'";
		$rows_courses = queryDB($sql, array(TABLE_PREFIX, $_SESSION['member_id']));

		$nav_courses = array(); /* the list of courses we're enrolled in or own */
		foreach($rows_courses as $row){
			//Truncate course title if it's > 45.
			$system_courses[$row['course_id']]['title'] = htmlentities($system_courses[$row['course_id']]['title'], ENT_QUOTES, 'UTF-8');
			$nav_courses[$row['course_id']] = validate_length($system_courses[$row['course_id']]['title'], 45, VALIDATE_LENGTH_FOR_DISPLAY);
		}

		natcasesort($nav_courses);
		reset($nav_courses);
		$savant->assign('nav_courses',    $nav_courses);
	}

	if (($_SESSION['course_id'] > 0) && isset($_SESSION['prefs']['PREF_JUMP_REDIRECT']) && $_SESSION['prefs']['PREF_JUMP_REDIRECT']) {
		$savant->assign('rel_url', $_rel_url);
	} else {
		$savant->assign('rel_url', '');
	}

	/* course specific elements: */
	/* != 'public' special case for the about.php page, which is available from a course but hides the content menu */
	$sequence_links = array();
	if ($_SESSION['course_id'] > 0) {
		$sequence_links = $contentManager->generateSequenceCrumbs($cid);
		$savant->assign('sequence_links', $sequence_links);
	}

	//side menu array
	// NOT SURE IF THIS IS DOING ANYTHING
	/*
	if ($_SESSION['course_id'] > 0) {
		$side_menu = array();
		$side_menu = explode('|', $system_courses[$_SESSION['course_id']]['side_menu']);
		debug($side_menu);
		debug($_stacks);		
		$side_menu = array_intersect($side_menu, $_stacks);
		debug($side_menu);
		$savant->assign('side_menu', $side_menu);
	}
	*/
}

function admin_switch(){ 
	if($_SESSION['is_admin'] > 0) {?>
        <div class="admin_switch">	
            <form>
              <select id="admin_switch" name="hide_admin" title="switch">
              <option value="1"><?php echo _AT('manage_on'); ?></option>
              <option value="0"><?php echo _AT('manage_off'); ?></option>
                </select>
            </form>
            <ul></ul>

        </div>
        <div class="bypass">
            <a href="<?php echo htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES); ?>#admin_tools"><?php echo _AT('jump_to_admin_tools'); ?></a>
        </div>
    <?php } ?>
<?php } 
function mobile_switch(){ 
	if(is_mobile_device() > 0) {?>
		<ul id="mobile_switch" title="<?php echo _AT('mobile_toggle'); ?>">
			 <?php if($_SESSION['prefs']['PREF_RESPONSIVE'] > 0){ ?>
				<li class="disabled left"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?mobile=2"  title="<?php echo _AT("mobile_disabled"); ?>" aria-disabled="true"><?php echo _AT('mobile'); ?></a></li>
			<?php }else{ ?>
				<li class="active left"><?php echo _AT('mobile'); ?></li>
			<?php } ?>
			<?php if($_SESSION['prefs']['PREF_RESPONSIVE'] > 0){ ?>
				<li  class="active right"><?php echo _AT('off'); ?></li>
			<?php }else{ ?>
				<li  class="disabled right"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?mobile=1"  title="<?php echo _AT("mobile_active"); ?>" aria-disabled="false"><?php echo _AT('off'); ?></a></li>
			<?php } ?>
   
		</ul>
    <?php } ?>
<?php } 
// array of content tools for shortcuts tool bar.
if (isset($_tool_shortcuts)) $savant->assign('shortcuts', $_tool_shortcuts);

/* Register our Errorhandler on everypage */
//require_once(AT_INCLUDE_PATH . 'classes/ErrorHandler/ErrorHandler.class.php');
//$err = new ErrorHandler();

//TODO*******************BOLOGNA*******************REMOVE ME*******************/
// if filemanager is a inside a popup or a frame
// i don't like this code. i don't know were these two variables are coming from
// anyone can add ?framed=1 to a URL to alter the behaviour.

// global $_course_id is set when a guest accessing a public course. 
// This is to solve the issue that the google indexing fails as the session vars are lost.
if (isset($_SESSION['course_id'])) 
	$_course_id = $_SESSION['course_id'];
else if (isset($_GET['p_course'])) // p_course is set when pretty url is turned on and public course is accessed
	$_course_id = $_GET['p_course'];

$savant->assign('course_id', $_course_id);
$savant->assign('is_mobile_device', is_mobile_device());
$savant->assign('mobile_device_type', get_mobile_device_type());

if ((isset($_REQUEST['framed']) && $_REQUEST['framed']) || (isset($_REQUEST['popup']) && $_REQUEST['popup'])) {
    $savant->assign('framed', 1);
    $savant->assign('popup', 1);

    if(isset($tool_flag) && ($tool_flag))
        $savant->display('include/tm_header.tmpl.php');         //header for toolmanager
    else
        $savant->display('include/fm_header.tmpl.php');

} else {
    //$savant->assign('opensocial', open_social_libs($_base_href));
    $savant->display('include/header.tmpl.php');
}

//tool_origin('off');
?>
