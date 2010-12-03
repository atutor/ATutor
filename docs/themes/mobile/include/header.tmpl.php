<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id: header.tmpl.php 3825 2005-03-11 15:35:51 joel $
if (!defined('AT_INCLUDE_PATH')) { exit; }
/* available header.tmpl.php variables:
 * $this->lang_code			the ISO language code
 * SITE_NAME				the site name from the config file
 * $this->page_title		the name of this page to use in the <title>
 * $this->lang_charset		the ISO language character set
 * $this->content_base_href	the <base href> to use for this page
 * $this->base_path			the absolute path to this atutor installation
 * $this->rtl_css			if set, the path to the RTL style sheet
 * $this->icon			the path to a course icon
 * $this->banner_style		-deprecated-
 * $this->theme				the directory name of the current theme
 * $this->base_href			the full url to this atutor installation
 * $this->onload			javascript onload() calls
 * $this->img				the absolute path to this theme's images/ directory
 * $this->sequence_links	associative array of 'previous', 'next', and/or 'resume' links
 * $this->path				associative array of path to this page: aka bread crumbs
 * $this->rel_url			the relative url from the installation root to this page
 * $this->nav_courses		associative array of this user's enrolled courses
 * $this->section_title		the title of this section (course, public, admin, my start page)
 * $this->current_top_level_page	the full path to the current top level page with file name
 * $this->sub_level_pages			associate array of sub level navigation
 * $this->back_to_page				if set, the path and file name to the part of this page (if parent is not a top level nav)
 * $this->current_sub_level_page	the full path to the current sub level page with file name
 * $this->guide				the full path and file name to the guide page
 * ======================================
 * top_level_pages           array(array('url', 'title'))     the top level pages. ATutor default creates tabs.
 * section_title             string                           the name of the current section. either name of the course, administration, my start page, etc.
 * page_title                string                           the title of the current page.
 * path                      array(array('url', 'title'))     the path to the current page.
 * back_to_page              array('url', 'title')            the link back to the part of the current page, if needed.
 * current_top_level_page    string                           full url to the current top level page in "top_leve_pages"
 * current_sub_level_page    string                           full url to the current sub level page in the "sub_level_pages"
 * sub_level_pages           array(array('url', 'title'))     the sub level pages.
 * is_mobile_device          true or false                    the request is from a mobile device or a desktop device
 * mobile_device_type        One of the constants: IPOD_DEVICE, BLACKBERRY_DEVICE, ANDROID_DEVICE, UNKNOWN_DEVICE (@see include/lib/constants.inc.php)
 */

// will have to be moved to the header.inc.php
global $system_courses, $_custom_css, $db, $_base_path, $contentManager;

// 1. any click on the page closes the content menu but the link "content_link" itself
// 2. the click on link "content_link" opens the content menu


require ('TeraWurflRemoteClient.php');
$wurflObj = new TeraWurflRemoteClient('http://wurfl.thesedays.com/webservice.php');
$capabilities = array("product_info");
$data_format = TeraWurflRemoteClient::$FORMAT_JSON;
$wurflObj->getCapabilitiesFromAgent(null, $capabilities, $data_format);

// open/close content menu
$this->onload .= "
jQuery('#content_link').click(function(e) {
  e.stopPropagation();
  jQuery('#content').slideToggle();";
$this->onload .= "});
";

// open/close navigational menu 
$this->onload .= "
jQuery(document).click(function () {
jQuery('#topnavlist').hide();}); 
jQuery('#topnavlist-link').click(function(e) {
  e.stopPropagation();
  jQuery('#topnavlist').slideToggle();
});
";

// Hide the addressbar
$this->onload .= "
setTimeout(function() { window.scrollTo(0, 1) }, 100);
";

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="<?php echo $this->lang_code; ?>"> 

<head>
	<title><?php echo SITE_NAME; ?> : <?php echo $this->page_title; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $this->lang_charset; ?>" />
	<meta name="Generator" content="ATutor - Copyright 2005 by http://atutor.ca" />
	<meta name="viewport" content="user-scalable=no, width=device-width" />
	<base href="<?php echo $this->content_base_href; ?>" />
	<link rel="shortcut icon" href="<?php echo $this->base_path; ?>favicon.ico" type="image/x-icon" /> 
	<link rel="stylesheet" href="<?php echo $this->base_path.'themes/'.$this->theme; ?>/print.css" type="text/css" media="print" />
	<!-- mobile FSS -->
	<link rel="stylesheet" href="<?php echo $this->base_path; ?>jscripts/infusion/framework/fss/css/fss-mobile-layout.css" type="text/css"/>
	<link rel="stylesheet" href="<?php echo $this->base_path; ?>jscripts/infusion/framework/fss/css/fss-mobile-theme-iphone.css" type="text/css"/>	
	


<?php if ($this->is_mobile_device == true): ?>
	<?php if ($this->mobile_device_type == ANDROID_DEVICE): ?>
	<link rel="stylesheet" href="<?php echo $this->base_path.'themes/'.$this->theme; ?>/android.css" type="text/css"/>
	<?php endif; ?>
	<?php if ($this->mobile_device_type == IPOD_DEVICE): ?>
	<link rel="stylesheet" href="<?php echo $this->base_path.'themes/'.$this->theme; ?>/iphone.css" type="text/css"/>
	<?php endif; ?>
	<!-- Armin 25.08.2010: Detect BLACKBERRY_DEVICE and use blackberry.css-->
	<?php if ($this->mobile_device_type == BLACKBERRY_DEVICE): ?>
	<link rel="stylesheet" href="<?php echo $this->base_path.'themes/'.$this->theme; ?>/blackberry.css" type="text/css"/>
	<?php endif; ?>
<?php endif; ?>

	<!--[if IE]>
	  <link rel="stylesheet" href="<?php echo $this->base_path.'themes/'.$this->theme; ?>/ie_styles.css" type="text/css" />
	<![endif]-->
	<link rel="stylesheet" href="<?php echo $this->base_path.'themes/'.$this->theme; ?>/forms.css" type="text/css" />
		

	<script src="<?php echo $this->base_path; ?>jscripts/infusion/InfusionAll.js" type="text/javascript"></script>
	<script language="javascript" type="text/javascript">
	//<!--
	jQuery.noConflict();
	//-->
	</script>
	<script src="<?php echo $this->base_path; ?>jscripts/ATutor.js" type="text/javascript"></script>   
<?php if (($wurflObj->getDeviceCapability("mobile_browser")=="Safari")): ?>	

<?php endif; ?>
	
<?php echo $this->rtl_css; ?>
<?php if (isset($this->course_id) && $system_courses[$this->course_id]['rss']): ?>
	<link rel="alternate" type="application/rss+xml" title="<?php echo SITE_NAME; ?> - RSS 2.0" href="<?php echo $this->base_href; ?>get_rss.php?<?php echo $this->course_id; ?>-2" />
	<link rel="alternate" type="application/rss+xml" title="<?php echo SITE_NAME; ?> - RSS 1.0" href="<?php echo $this->base_href; ?>get_rss.php?<?php echo $this->course_id; ?>-1" />
<?php endif; ?>


<?php echo $this->custom_css; ?>
</head>
<body onload="<?php echo $this->onload; ?>" class="fl-theme-iphone">

<div id="wrapper">
<div id="main">
<div id="header">

	<a href="<?php echo htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES); ?>#content">
	<img src="<?php echo $this->base_path; ?>images/clr.gif" height="1" width="1" border="0" alt="<?php echo _AT('goto_content'); ?> ALT+c" /></a>		

	<div id="header-section-title">
		<!-- <?php if (isset($_SESSION['valid_user']) && $_SESSION['valid_user']): 
				echo '<div id="site-name">'.stripslashes(SITE_NAME).'</div>'; 
			endif; ?> --> 
			<h1 id="section-title"><?php echo $this->section_title; ?>
			<?php if ((isset($this->course_id) && $this->course_id > 0) && ($_SESSION['enroll'] == AT_ENROLL_NO)) : ?> 
				<!-- <small><a href="<?php echo $this->base_path; ?>enroll.php?course=<?php echo $this->course_id; ?>"><?php echo _AT('enroll_me'); ?></a></small>-->
			<?php endif; ?>
			</h1>
	</div>
</div> <!--  END HEADER -->


<div id="contentwrapper">

<!--  Note: ARIA roles cause XHTML validation errors because the XHTML DTD does not yet support ARIA. Use ARIA anyway -->
<div id="navigation-contentwrapper">
	<div id="navigation-bar">
	<!--  this should be a button on its own  -->
		<?php if ($this->current_sub_level_page): ?>
		<div id="topnavlistcontainer" role="navigation" aria-live="assertive" >
		<a class="navigation-bar-button" id="topnavlist-link" href="javascript:void(0);" tabindex="1"><?php echo _AT('navigation'); ?></a>
			<ul id="topnavlist"  class="fl-list-menu">
				<?php $accesscounter = 0; //initialize ?>
				<?php foreach ($this->top_level_pages as $page): ?>
					<?php ++$accesscounter; $accesscounter = ($accesscounter == 10 ? 0 : $accesscounter); ?>
					<?php $accesskey_text = ($accesscounter < 10 ? 'accesskey="'.$accesscounter.'"' : ''); ?>
					<?php $accesskey_title = ($accesscounter < 10 ? ' Alt+'.$accesscounter : ''); ?>
					<?php if ($page['url'] == $this->current_top_level_page): ?>
						<li role="menuitem"><a  href="<?php echo $page['url']; ?>" <?php echo $accesskey_text; ?> title="<?php echo $page['title'];?>" class="flc-screenNavigator-backButton"><?php echo $page['title']; ?></a>  </li>
					<?php else: ?>
						<li role="menuitem"><a  href="<?php echo $page['url']; ?>" <?php echo $accesskey_text; ?> title="<?php echo $page['title']; ?>" class="flc-screenNavigator-backButton"><?php echo $page['title']; ?></a></li>
					<?php endif; ?>
				
					<?php $accesscounter = ($accesscounter == 0 ? 11 : $accesscounter); ?>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php endif; ?>
	</div>


	<ul class="fl-tabs" id="home-guide">

		<li><a href="<?php echo $this->base_path; ?>users/index.php"><?php echo _AT("home"); ?></a></li>
		<?php if (isset($this->guide) && isset($_SESSION["course_id"]) && $this->guide && ($_SESSION["prefs"]["PREF_SHOW_GUIDE"] || $_SESSION["course_id"] == "-1")) : ?>
		<li>
	    	<div id="guide_box">
				<!--    <a href="<?php echo $this->guide; ?>" id="guide" onclick="ATutor.poptastic('<?php echo $this->guide; ?>'); return false;" target="_new"><img src="<?php echo $this->img; ?>guide-icon.png" width="30" height="30" title="guide: <?php echo $this->page_title; ?>"alt="guide: <?php echo $this->page_title; ?>"></img></a> -->
      		
			  <a href="<?php echo $this->guide; ?>" id="guide" onclick="ATutor.poptastic('<?php echo $this->guide; ?>'); return false;" target="_new"><?php echo _AT("guide"); ?></a> 
      		</div>
		</li>
		<?php endif; ?>
	</ul>
</div><!--  END navigation-contentwrapper -->

<div id="inner-contentwrapper" class="fl-container">
	<!-- ENSURE "content_link" DOESN'T APPEAR IF NOT LOGGED IN -->
	
	
	<?php if(isset($_SESSION['course_id']) && $_SESSION['course_id'] > 0): ?> 
		<div id="content-link-container" role="navigation" aria-live="assertive">
		<a id="content_link" href="javascript:void(0);"><?php echo _AT("content_navigation"); ?></a>
		<div id="content" style=" display: none; position: relative; z-index: 1;">
		<?php $contentManager->printMainMenu(); ?>
				<script language="javascript" type="text/javascript">
			
				</script>
		</div>
		</div>
	<?php endif; ?>
	


	<div id="contentcolumn">	
		<?php if ((isset($this->course_id) && $this->course_id <= 0)): ?>
			<!-- style="margin-left:0.5em;width:99%;" -->
		<?php endif; ?>
		<?php if (isset($this->course_id) && $this->course_id > 0): ?>
		<div class="sequence-links">
		<?php if ($_SESSION["prefs"]["PREF_SHOW_NEXT_PREVIOUS_BUTTONS"]) { ?>
			<?php if ($this->sequence_links['resume']): ?>
					<a href="<?php echo $this->sequence_links['resume']['url']; ?>" class="previous-next" title="<?php echo _AT('resume').': '.$this->sequence_links['resume']['title']; ?>"><?php echo $this->sequence_links['resume']['title']; ?></a>
			<?php else:
				if ($this->sequence_links['previous']): ?>
					<a href="<?php echo $this->sequence_links['previous']['url']; ?>" class="previous-next" title="<?php echo _AT('previous_topic').': '. $this->sequence_links['previous']['title']; ?>"> <?php echo _AT('previous_topic').': '. $this->sequence_links['previous']['title']; ?> </a>
				<?php endif;
				if ($this->sequence_links['next']): ?>
					<a href="<?php echo $this->sequence_links['next']['url']; ?>" class="previous-next"  title="<?php echo _AT('next_topic').': '.$this->sequence_links['next']['title']; ?>"> <?php echo _AT('next_topic').': '.$this->sequence_links['next']['title']; ?></a>
				<?php endif; ?>
			<?php endif; ?>
		<?php } ?>
			&nbsp;
		</div> <!-- end sequence-links -->
		<?php endif; ?>

	<!-- the page title -->
	<a name="content" title="<?php echo _AT('content'); ?>"></a>
	<h2 class="page-title"><?php echo $this->page_title; ?></h2>
	
	<?php global $msg; $msg->printAll(); $_base_href;?>
	
	<!-- the sub navigation -->
	<?php if (count($this->sub_level_pages) > 0): ?>
			<div id="subnavlistcontainer">
				<div id="subnavbacktopage">
				<?php if (isset($this->back_to_page)): ?>
					<a href="<?php echo $this->back_to_page['url']; ?>">
					<img border="0" width="10" height="11" alt="<?php echo _AT('back_to').' '.$this->back_to_page['title']; ?>" src="<?php echo $this->base_href; ?>images/arrowicon.gif" style="float:left;"/></a>&nbsp;
				<?php endif; ?>
				</div>

				<ul id="subnavlist">
				<?php $num_pages = count($this->sub_level_pages); ?>
				<?php for ($i=0; $i<$num_pages; $i++): ?>				
					<?php if ($this->sub_level_pages[$i]['url'] == $this->current_sub_level_page): ?>
						<li id="test" ><?php echo $this->sub_level_pages[$i]['title']; ?></li>
					<?php else: ?>
						<li><a href="<?php echo $this->sub_level_pages[$i]['url']; ?>"><?php echo $this->sub_level_pages[$i]['title']; ?></a></li>
					<?php endif; ?>
				<?php if ($i < $num_pages-1): 
					echo " ";?>
				<?php endif; ?>
				<?php endfor; ?>
				</ul>
			</div>
	<?php endif; ?>



