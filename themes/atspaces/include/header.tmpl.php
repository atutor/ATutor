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
// $Id:$
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
 * $this->cats		 array or course categories
 * $this->theme				the directory name of the current theme
 * $this->theme_path        the directory name of where "themes" directory resides
 * $this->base_href			the full url to this atutor installation
 * $this->onload			javascript onload() calls
 * $this->img				the absolute path to this theme's images/ directory
 * $this->sequence_links	associative array of 'previous', 'next', and/or 'resume' links
 * $this->path				associative array of path to this page: aka bread crumbs
 * $this->rel_url			the relative url from the installation root to this page
 * $this->nav_courses		associative array of this user's enrolled courses
 * $this->section_title		the title of this section (course, public, admin, my start page)
 * $this->top_level_pages	associative array of the top level navigation
 * $this->current_top_level_page	the full path to the current top level page with file name
 * $this->sub_level_pages			associate array of sub level navigation
 * $this->sub_level_pages_i			associate array of sub level navigation tools for instructors
 * $this->back_to_page				if set, the path and file name to the part of this page (if parent is not a top level nav)
 * $this->current_sub_level_page	the full path to the current sub level page with file name
 * $this->current_sub_level_page_i	the full path to the current sub level page with file name for sub navigation intructor tools
 * $this->guide				the full path and file name to the guide page
 * $this->shortcuts         the array of tools' shortcuts to display at top right corner. Used by content.php and edit_content_folder.php
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
global $system_courses, $_custom_css, $db;
?>
<!DOCTYPE html>
<html lang="<?php echo $this->lang_code; ?>"> 
<head>
	<?php if(isset($this->section_title)){ ?>
		<title><?php echo $this->section_title; ?>:	 <?php echo $this->page_title; ?></title>
	<?php }else { ?>
		<title><?php echo SITE_NAME; ?> : <?php echo $this->page_title; ?></title>
	<?php } ?>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $this->lang_charset; ?>" />
    <?php if(isset($this->content_keywords) && $this->content_keywords != ''){ ?>
    <meta name="keywords" content="<?php echo $this->content_keywords; ?>" />
    <?php } ?>
    <?php if(isset($this->content_description) && $this->content_description != ''){ ?>
    <meta name="description" content="<?php echo $this->content_description; ?>" />
    <?php } ?>
	<meta name="Generator" content="ATutor - Copyright 2013 by http://atutor.ca" />
	<base href="<?php echo $this->content_base_href; ?>" />
	<link rel="shortcut icon" href="<?php echo $this->theme_path; ?>favicon.ico" type="image/x-icon" />
	<link rel="stylesheet" href="<?php echo $this->theme_path.'themes/'.$this->theme; ?>/print.css" type="text/css" media="print" />
  	<link rel="stylesheet" href="<?php echo $this->theme_path.'jscripts/infusion/framework/fss/css/fss-layout.css'; ?>" type="text/css" />
	<link rel="stylesheet" href="<?php echo $this->theme_path.'themes/'.$this->theme; ?>/styles.css" type="text/css" />
	    <link rel="stylesheet" href="<?php echo $this->theme_path.'themes/'.$this->theme; ?>/forms.css" type="text/css" />
	<!--[if IE]>
	  <link rel="stylesheet" href="<?php echo $this->theme_path.'themes/'.$this->theme; ?>/ie_styles.css" type="text/css" />
	<![endif]-->
	<!--[if IE 8]>
	  <link rel="stylesheet" href="<?php echo $this->theme_path.'themes/'.$this->theme; ?>/ie8_styles.css" type="text/css" />
	<![endif]-->

	<?php  if ( strpos($_SERVER['HTTP_USER_AGENT'], 'Safari') ){ ?>	
		<link rel="stylesheet" href="<?php echo $this->theme_path.'themes/'.$this->theme; ?>/safari.css" type="text/css"/>
	<?php } ?>
	
<?php if (isset($this->course_id) && isset($system_courses[$this->course_id]['rss'])): ?>
	<link rel="alternate" type="application/rss+xml" title="<?php echo SITE_NAME; ?> - RSS 2.0" href="<?php echo $this->base_href; ?>get_rss.php?<?php echo $this->course_id; ?>-2" />
	<link rel="alternate" type="application/rss+xml" title="<?php echo SITE_NAME; ?> - RSS 1.0" href="<?php echo $this->base_href; ?>get_rss.php?<?php echo $this->course_id; ?>-1" />
<?php endif; ?>

    <!-- Fluid and jQuery Dependencies -->
    <script type="text/javascript" src="<?php echo $this->base_href; ?>jscripts/infusion/InfusionAll.js"></script>
    <script type="text/javascript" src="<?php echo $this->base_href; ?>jscripts/infusion/framework/enhancement/js/ProgressiveEnhancement.js"></script>
    <script src="<?php echo $this->base_path; ?>jscripts/mobile.js" type="text/javascript"></script>   
	<script type="text/javascript">
	//<!--
	jQuery.noConflict();
	//-->
	</script>
    
    <script src="<?php echo $this->base_path; ?>jscripts/ATutor.js" type="text/javascript"></script>   
    <?php echo $this->custom_css; ?>
    <?php echo $this->rtl_css; ?>

    <style id="pref_style" type="text/css"></style> 
</head>
<body onload="<?php if(isset($this->onload)){echo $this->onload;} ?>">
<div class="headfootbar" style="height:1em;">
&nbsp;
</div>
<div id="top-bar">
	
<div class="bypass">
	<a href="<?php echo htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES); ?>#content" accesskey="c" title="<?php echo _AT('goto_content'); ?> Alt-c">
	<?php echo _AT('goto_content'); ?></a>
	<a href="<?php echo htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES); ?>#main-nav" accesskey="n" title="<?php echo _AT('goto_main_nav'); ?> Alt-n">
	<?php echo _AT('goto_main_nav'); ?></a>
	<a href="<?php echo htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES); ?>#menu<?php if(isset($_REQUEST['cid'])){echo htmlentities_utf8($_REQUEST['cid']);}  ?>"  accesskey="m" title="<?php echo _AT('goto_menu'); ?> Alt-m"><?php echo _AT('goto_menu'); ?></a>
</div>	

	<?php if (isset($_SESSION['valid_user']) && $_SESSION['valid_user']): 
		echo '<div class="site-name">'.stripslashes(SITE_NAME).'</div>'; 
	else:
		echo '<div class="site-name">&nbsp;</div>';	
	endif; ?>

	<div id="top-links"  role="navigation"> 
	<!-- top help/search/login links -->
		<?php if (isset($_SESSION['member_id']) && $_SESSION['member_id']): ?>
			<?php if(!$this->just_social): ?>
			 <div id="top-links-jump">
			<!-- start the jump menu -->
			<?php if (empty($_GET)): ?>
				<form method="post" action="<?php echo $this->base_path; ?>bounce.php?p=<?php echo urlencode($this->rel_url); ?>" target="_top">
			<?php else: ?>
				<form method="post" action="<?php echo $this->base_path; ?>bounce.php" target="_top">
			<?php endif; ?>
			<label for="jumpmenu" accesskey="j"></label>
				<select name="course" id="jumpmenu" title="<?php echo _AT('jump'); ?>:  Alt-j">							
					<option value="0"><?php echo _AT('my_start_page'); ?></option>
					<optgroup label="<?php echo _AT('courses_below'); ?>">
						<?php foreach ($this->nav_courses as $this_course_id => $this_course_title): ?>
							<option value="<?php echo $this_course_id; ?>"><?php echo $this_course_title; ?></option>
						<?php endforeach; ?>
					</optgroup>
				</select> <input type="submit" name="jump" value="<?php echo _AT('jump'); ?>" class="button" /> </form>
			<!-- /end the jump menu -->
			</div>
			<?php endif; ?>
			<?php endif; ?>
			
			<div id="top-links-text">
			<?php if (isset($_SESSION['is_super_admin'])): ?>
				<a href="<?php echo $this->base_path; ?>bounce.php?admin"><?php echo _AT('return_to_admin_area'); ?></a> | 
			<?php endif; ?>

			<?php if ($this->course_id > -1): ?>
				<?php if (get_num_new_messages()): ?>
					<a href="<?php echo $this->base_path; ?>inbox/index.php"><?php echo _AT('inbox'); ?> (<?php echo get_num_new_messages(); ?>)</a> 
				<?php else: ?>
					<a href="<?php echo $this->base_path; ?>inbox/index.php"><?php echo _AT('inbox'); ?></a>
				<?php endif; ?>
			<?php endif; ?>

		<?php if(!$this->just_social): ?>
			<?php
				global $_config;
				if(isset($_SESSION['course_id']) && $_SESSION['course_id'] > 0 && isset($_SESSION['is_guest']) && $_SESSION['is_guest'] > 0 && $_config['allow_browse'] == '1'){ ?>
			<a href="<?php echo $this->base_path; ?>browse.php"><?php echo _AT('browse_courses'); ?></a> 
			<?php } ?>
			<a href="<?php echo $this->base_path; ?>search.php"><?php echo _AT('search'); ?></a> 
		<?php endif; ?>
		<a href="<?php echo $this->base_path; ?>help/index.php"><?php echo _AT('help'); ?></a>	
		</div>
	</div>
</div>
<div class="page_wrapper">

<div id="header">


	<?php
	// If there is a custom course banner in the file manager called banner.html, display it here
	@readfile(AT_CONTENT_DIR . $this->course_id.'/banner.html'); 

	/*
	and example banner.html file might look like:
	<div style="width: 760px; height: 42px; background: white;"><img src="http://[mysite]/atutor15rc3/banners/kart-camb.jpg"></div>
	*/

	?>
	<!-- section title -->

	<!-- Course Title -->
	<div id="course_title_container" <?php if(empty($this->icon)){echo ' style="left:1em;"';}   ?> role="banner">
	<?php if(isset($_SESSION['valid_user'])):?>
	
	<h1 id="section-title"><?php echo $this->section_title; ?>
		<?php if ((isset($this->course_id) && $this->course_id > 0) && ($_SESSION['enroll'] == AT_ENROLL_NO)) : ?> 
			- <small><a href="<?php echo $this->base_path; ?>enroll.php?course=<?php echo $this->course_id; ?>"><?php echo _AT('enroll_me'); ?></a></small>
		<?php endif; ?>
    </h1>
	<?php else: ?>
	<h1 id="site-name-lrg"><?php echo $this->section_title; ?>
	</h1>
	<?php endif; ?>
	</div>
</div>
<!-- the main navigation. in our case, tabs -->
<div id="lrg_topnav">
    <div id="topnavlistcontainer" role="navigation">
        <a name="main-nav"></a>
        <ul id="topnavlist">
            <?php $accesscounter = 0; //initialize ?>
            <?php foreach ($this->top_level_pages as $page): ?>
                <?php ++$accesscounter; $accesscounter = ($accesscounter == 10 ? 0 : $accesscounter); ?>
                <?php $accesskey_text = ($accesscounter < 10 ? 'accesskey="'.$accesscounter.'"' : ''); ?>
                <?php $accesskey_title = ($accesscounter < 10 ? ' Alt+'.$accesscounter : ''); ?>
                <?php if ($page['url'] == $this->current_top_level_page): ?>
                    <li><a href="<?php echo $page['url']; ?>" <?php echo $accesskey_text; ?> title="<?php echo $page['title'] . $accesskey_title; ?>" class="active"><?php echo $page['title']; ?></a></li>
                <?php else: ?>
                    <li><a href="<?php echo $page['url']; ?>" <?php echo $accesskey_text; ?> title="<?php echo $page['title'] . $accesskey_title; ?>"><?php echo $page['title']; ?></a></li>
                <?php endif; ?>
                <?php $accesscounter = ($accesscounter == 0 ? 11 : $accesscounter); ?>
            <?php endforeach; ?>
        </ul>
    </div> 
</div>

<div class="logoutbar">
	<div id="userlinks">
		<?php if (isset($_SESSION['valid_user']) && $_SESSION['valid_user']): ?>
		<?php $path_parts = explode("/", $this->current_top_level_page); 
		      $last_path_part = $path_parts[sizeof($path_parts) - 1];
               if (!admin_authenticate(AT_ADMIN_PRIV_ADMIN, AT_PRIV_RETURN) && $last_path_part != 'preferences.php') {?>
		    <a href="" class="pref_wiz_launcher"><img alt="<?php echo _AT('preferences').' - '._AT('new_window'); ?>" title="<?php echo _AT('preferences').' - '._AT('new_window'); ?>"  src="<?php echo $this->img; ?>wand.png" class="img1616" style="margin-bottom:-.5em;"/></a> |
		    <?php } ?> 
			<strong><?php echo get_display_name($_SESSION['member_id']); ?></strong> | 
			<a href="<?php echo $this->base_path; ?>logout.php"><?php echo _AT('logout'); ?></a>
		<?php else: ?>
			 <a href="<?php echo $this->base_path; ?>login.php?course=<?php echo $this->course_id; ?>"><?php echo _AT('login'); ?></a> | <a href="<?php echo $this->base_path; ?>registration.php"><?php echo _AT('register'); ?></a>
		<?php endif; ?>
	</div>
</div>

<div id="sm_topnav">
    <?php if ($this->current_sub_level_page): ?>
    <div id="topnavlistcontainer_sm" role="navigation" aria-live="assertive" class="topnavlistcontainer fl-container" style="height:auto;">
    <a class="navigation-bar-button topnavlist-link active" id="topnavlist-link" href="javascript:void(0);" title="Toggle to open and close main navigation."><?php echo _AT('navigation'); ?><span title="Toggle to open and close main navigation">&nbsp;</span></a>
    <br />
        <div id="navigation-column">
        <?php if ($this->current_sub_level_page): ?>
        <ul id="topnavlist_sm"  class="fl-list-menu" role="menu">
            <?php $accesscounter = 0; //initialize ?>
            <?php foreach ($this->top_level_pages as $page): ?>
                <?php ++$accesscounter; $accesscounter = ($accesscounter == 10 ? 0 : $accesscounter); ?>
                <?php $accesskey_text = ($accesscounter < 10 ? 'accesskey="'.$accesscounter.'"' : ''); ?>
                <?php $accesskey_title = ($accesscounter < 10 ? ' Alt+'.$accesscounter : ''); ?>
                <?php if ($page['url'] == $this->current_top_level_page): ?>
                    <!-- note bug http://issues.fluidproject.org/browse/FLUID-4313 makes class "flc-screenNavigator-backButton fl-link-hilight" not work -->
                    <li><a  href="<?php echo $page['url']; ?>" <?php echo $accesskey_text; ?> class="flc-screenNavigator-backButton fl-link-hilight"><?php echo $page['title']; ?></a>  </li>
                <?php else: ?>
                    <li><a  href="<?php echo $page['url']; ?>" <?php echo $accesskey_text; ?>><?php echo $page['title']; ?></a></li>
                <?php endif; ?>
                <?php $accesscounter = ($accesscounter == 0 ? 11 : $accesscounter); ?>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
    </div>

    <?php endif; ?>	
    
    </div>

</div>	
  <?php if (isset($_SESSION["prefs"]["PREF_SHOW_BREAD_CRUMBS"]) && $_SESSION["prefs"]["PREF_SHOW_BREAD_CRUMBS"] || $_SESSION['course_id'] = -1) { ?>
		  <!-- the bread crumbs -->
		<div class="crumbcontainer" role="navigation">
		  <div id="breadcrumbs" tabindex="0"  aria-label="<?php echo _AT('breadcrumb_links'); ?>">
			  <?php foreach ($this->path as $page): ?>
				  <a href="<?php echo $page['url']; ?>"><?php echo htmlspecialchars($page['title'], ENT_COMPAT, "UTF-8"); ?></a> > 
			  <?php endforeach; ?> <?php echo $this->page_title; ?>
		  </div>
	  <?php } else { ?>
	   <div class="crumbcontainer" style="padding-bottom:1.2em;">
	  <?php } ?>
		  <?php if (isset($this->guide) && isset($_SESSION["course_id"]) && $this->guide && ($_SESSION["prefs"]["PREF_SHOW_GUIDE"] || $_SESSION["course_id"] == "-1")) : ?>
      <div id="guide_box">
			  <a href="<?php echo $this->guide; ?>" id="guide" onclick="ATutor.poptastic('<?php echo $this->guide; ?>'); return false;" target="new"><?php echo $this->page_title; ?></a>
      </div>
		  <?php endif; ?>
      <?php if (isset($this->shortcuts)): ?>
      <div id="shortcuts">
	      <ul>
		      <?php foreach ($this->shortcuts as $link): ?>
			      <li><a href="<?php echo $link['url']; ?>"><img src="<?php echo $link['icon']; ?>" alt="<?php echo $link['title']; ?>"  title="<?php echo $link['title']; ?>" class="shortcut_icon"/><!-- <?php echo $link['title']; ?> --></a></li>
		      <?php endforeach; ?>
	      </ul>
      </div>
      <?php endif; ?>

      </div>

<div id="contentwrapper" 
		<?php if (isset($_SESSION["prefs"]["PREF_SHOW_BREAD_CRUMBS"]) && $_SESSION["prefs"]["PREF_SHOW_BREAD_CRUMBS"] == 0):
			$style.='margin-top:-2em;';
			echo 'style="'.$style.'"';
		endif; ?>>
		      <div style="font-size:.8em;margin-left:-.5em;">
      <?php mobile_switch(); ?>
      </div>
	<?php if (isset($this->course_id) && $this->course_id > 0 && $system_courses[$this->course_id]['side_menu']): ?>
	
	
    <div id="sm_content">
		<div id="content_link" role="navigation" aria-live="assertive" class="flc-screenNavigator-navbar ">
				<a class="content_link_tablet content_link"  href="javascript:void(0);"><?php echo  _AT("content"); ?></a>	
		</div>	
    </div>		
    <div id="lrg_content">
		<div id="leftcolumn" role="complementary">
		  <a id="menu"></a>
		     <div id="side-menu">
		        <?php require(AT_INCLUDE_PATH.'side_menu.inc.php'); ?>
		    </div>
		</div>
    </div>		
		

	<?php endif; ?>

	
	<div id="contentcolumn"  role="main">
	<?php  admin_switch(); ?>
		<?php if (isset($this->course_id) && $this->course_id > 0 && $system_courses[$this->course_id]['side_menu']): ?>
		<div id="menutoggle">
		   <a href="javascript:void(0)" accesskey="n"><img src="" title="" alt="" class="img1616"/></a>
		</div>

		<br />
		<div class="sequence-links">
		<?php if ($_SESSION["prefs"]["PREF_SHOW_NEXT_PREVIOUS_BUTTONS"]) { ?>
			<?php if ($this->sequence_links['resume']): ?>
					<a style="color:white;" href="<?php echo $this->sequence_links['resume']['url']; ?>" accesskey="."><img src="<?php echo $this->img; ?>resume.png" title="<?php echo _AT('resume').': '.$this->sequence_links['resume']['title']; ?> Alt+." alt="<?php echo $this->sequence_links['resume']['title']; ?> Alt+." class="img1616" /></a>
			<?php else:
				if ($this->sequence_links['previous']): ?>
					<a href="<?php echo $this->sequence_links['previous']['url']; ?>" title="<?php echo _AT('previous_topic').': '. $this->sequence_links['previous']['title']; ?> Alt+," accesskey=","><img src="<?php echo $this->img; ?>previous.png" alt="<?php echo _AT('previous_topic').': '. $this->sequence_links['previous']['title']; ?> Alt+," class="img1616" /></a>
				<?php endif;
				if ($this->sequence_links['next']): ?>
					<a href="<?php echo $this->sequence_links['next']['url']; ?>" title="<?php echo _AT('next_topic').': '.$this->sequence_links['next']['title']; ?> Alt+." accesskey="."><img src="<?php echo $this->img; ?>next.png" alt="<?php echo _AT('next_topic').': '.$this->sequence_links['next']['title']; ?> Alt+." class="img1616" /></a>
				<?php endif; ?>
			<?php endif; ?>
		<?php } ?>
			&nbsp;
		</div>
		<?php endif; ?>

	<!-- the page title -->
	<a id="content" name="content" title="<?php echo _AT('content'); ?>"></a>
	<h2 class="page-title"><?php echo $this->page_title; ?></h2>
	
	<div id="message">
	<?php global $msg; $msg->printAll(); $_base_href;?>
    </div>
    

	<?php  if (count($this->sub_level_pages) > 1 || $this->sub_level_pages_i > 0): ?>
	<div id="lrg_subnav">
		<div id="subnavlistcontainer" role="navigation">
		<a name="admin_tools" id="admin_tools" title="<?php echo _AT("course_admin_tools"); ?>"></a>
			<div id="subnavbacktopage">
			<?php if (isset($this->back_to_page)): ?>
				<a href="<?php echo $this->back_to_page['url']; ?>">
				<img  alt="<?php echo _AT('back_to').' '.htmlentities_utf8($this->back_to_page['title']); ?>" title="<?php echo _AT('back_to').' '.htmlentities_utf8($this->back_to_page['title']); ?>" src="<?php echo $this->base_href; ?>images/goback.png" style="float:left;" class="imggoback"/></a>&nbsp;
			<?php endif; ?>
			</div>
			<span class="subnav_toggle" id="hidesubnav"  title="<?php echo _AT("hide_sub_navigation"); ?>">&nbsp;&nbsp;&nbsp;</span>
			<span class="subnav_toggle" id="showsubnav" title="<?php echo _AT("open_sub_navigation"); ?>">&nbsp;&nbsp;&nbsp;</span>
			<ul id="subnavlist" role="navigation"  aria-label="<?php echo _AT('tools'); ?>">
			<?php 

			$num_pages = count($this->sub_level_pages); 

?>
			<?php for ($i=0; $i<$num_pages; $i++): ?>

				<?php if ($this->sub_level_pages[$i]['url'] == $this->current_sub_level_page && $num_pages > 1){ ?>
				      <li class="active" tabindex="0"><?php echo stripslashes(htmlentities_utf8($this->sub_level_pages[$i]['title'])); ?>
                        <span id="subnav-hide" title="<?php echo _AT('sub_nav_hidden'); ?>" aria-live="polite"></span>
					  <span id="subnav-open" title="<?php echo _AT('sub_nav_opened'); ?>" aria-live="polite"></span>
                        </li>

				
				<?php } else if($num_pages > 1) { ?>
					  <li>
					  <a href="<?php echo $this->sub_level_pages[$i]['url']; ?>"><?php echo stripslashes(htmlentities_utf8($this->sub_level_pages[$i]['title'])); ?></a></li>
				<?php } ?>
				<?php if ($i < $num_pages-1): 
					echo " ";?>
				<?php endif; ?>
			<?php endfor; ?>
			</ul>		

			<?php 
			$num_pages_i = count($this->sub_level_pages_i); 
			if(is_array($this->sub_level_pages_i)){
			    $num_pages_i = count($this->sub_level_pages_i); 
			    if(intval($_GET['fid'])){
			        $fcid = "?fid=".$_GET['fid'];
			    }
			    if(intval($_GET['cid'])){
			        $fcid = "?cid=".$_GET['cid'];
			    }			 
			 
			 
			 ?>
            <span id="manage_off" title="<?php echo _AT('manage_tools_off'); ?>" aria-live="polite"></span>
            <a name="admin_tools"></a>
			<ul id="subnavlist_i" role="navigation"  aria-label="<?php echo _AT('manage_navigation_bar'); ?>">
            <span id="manage_on" title="<?php echo _AT('manage_tools_on'); ?>" aria-live="polite"></span>
			<?php for ($i=0; $i<$num_pages_i; $i++): 
			?>

				<?php if ($this->sub_level_pages_i[$i]['url'] == $this->current_sub_level_page){ ?>
				      <li class="active"><?php echo stripslashes(htmlentities_utf8($this->sub_level_pages_i[$i]['title'])); ?></li>
				<?php }else if(preg_match("/add_content.php/", $this->sub_level_pages_i[$i]['url'])){ ?>
					    <li><a href="<?php echo $this->sub_level_pages_i[$i]['url']; ?>"><?php echo stripslashes(htmlentities_utf8($this->sub_level_pages_i[$i]['title'])); ?></a></li>
				
				<?php } else { ?>
					    <li><a href="<?php echo $this->sub_level_pages_i[$i]['url'].$fcid; ?>"><?php echo stripslashes(htmlentities_utf8($this->sub_level_pages_i[$i]['title'])); ?></a></li>
				<?php } ?>
				<?php if ($i < $num_pages-1): 
					echo " ";?>
				<?php endif; ?>
			<?php endfor; ?>
			</ul>
			<?php } ?>
		</div>
		</div>
	<?php endif; ?>

<!-- the main navigation. in our case, tabs -->
