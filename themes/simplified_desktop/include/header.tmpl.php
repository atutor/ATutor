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

?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="<?php echo $this->lang_code; ?>"> 

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
	<meta name="Generator" content="ATutor - Copyright 2005 by http://atutor.ca" />
	<base href="<?php echo $this->content_base_href; ?>" />
	<link rel="shortcut icon" href="<?php echo $this->base_path; ?>favicon.ico" type="image/x-icon" /> 
	<link rel="stylesheet" href="<?php echo $this->base_path.'themes/'.$this->theme; ?>/print.css" type="text/css" media="print" />
	<!-- mobile fss -->	
	<link rel="stylesheet" href="<?php echo $this->base_path; ?>jscripts/infusion/framework/fss/css/fss-mobile-layout.css" type="text/css"/>
	<link rel="stylesheet" href="<?php echo $this->base_path; ?>jscripts/infusion/framework/fss/css/fss-mobile-theme-iphone.css" type="text/css"/>	
	

	<link rel="stylesheet" href="<?php echo $this->base_path.'themes/'.$this->theme; ?>/tablet.css" type="text/css"/>
	<meta name="viewport"  content="user-scalable=yes, width=device-width"/>
	<!--[if IE]>
	  <link rel="stylesheet" href="<?php echo $this->base_path.'themes/'.$this->theme; ?>/ie_styles.css" type="text/css" />
	<![endif]-->
	<!--[if IE8]>
	  <link rel="stylesheet" href="<?php echo $this->base_path.'themes/'.$this->theme; ?>/ie8_styles.css" type="text/css" />
	<![endif]-->

	<?php  if ( strpos($_SERVER['HTTP_USER_AGENT'], 'Safari') ){ ?>	
		<link rel="stylesheet" href="<?php echo $this->base_path.'themes/'.$this->theme; ?>/safari.css" type="text/css"/>
	<?php } ?>
	<link rel="stylesheet" href="<?php echo $this->base_path.'themes/'.$this->theme; ?>/forms.css" type="text/css" />

	<!-- Fluid Infusion mobile fss extension... Remove when it is committed to Mobile FSS.  
	<link rel="stylesheet" href="<?php echo $this->base_path.'themes/'.$this->theme; ?>/moz.css" type="text/css" />-->	
	<!-- Fluid Infusion -->
	<script src="<?php echo $this->base_path; ?>jscripts/infusion/InfusionAll.js" type="text/javascript"></script>
	<script language="javascript" type="text/javascript">

	//<!--
	jQuery.noConflict();
	//-->
	</script>
	<script src="<?php echo $this->base_path; ?>jscripts/ATutor.js" type="text/javascript"></script>   
	<script src="<?php echo $this->base_path; ?>jscripts/mobile.js" type="text/javascript"></script>   

	
<?php echo $this->rtl_css; ?>
<?php if (isset($this->course_id) && $system_courses[$this->course_id]['rss']): ?>
	<link rel="alternate" type="application/rss+xml" title="<?php echo SITE_NAME; ?> - RSS 2.0" href="<?php echo $this->base_href; ?>get_rss.php?<?php echo $this->course_id; ?>-2" />
	<link rel="alternate" type="application/rss+xml" title="<?php echo SITE_NAME; ?> - RSS 1.0" href="<?php echo $this->base_href; ?>get_rss.php?<?php echo $this->course_id; ?>-1" />
<?php endif; ?>


<?php echo $this->custom_css; ?>
</head>


<?php  if ($this->mobile_device_type != IPAD_DEVICE): ?><!--  smartphone theme only -->

<body onload="<?php echo $this->onload; ?>" class="fl-theme-iphone ui-mobile-viewport">
<div id="wrapper">
<div id="main">

	<div id="header">
	<div class="bypass">
		<a href="<?php echo htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES); ?>#content" accesskey="c">
		<img src="<?php echo $this->base_path; ?>images/clr.gif" height="1" width="1" border="0" alt="<?php echo _AT('goto_content'); ?> ALT+c" /></a>
	</div>
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
	<div id="navigation-contentwrapper" role="menubar" >
	
			<?php if ($this->current_sub_level_page): ?>
			<div id="topnavlistcontainer" role="navigation" aria-live="assertive" class="topnavlistcontainer fl-container" >
			<a class="navigation-bar-button topnavlist-link" id="topnavlist-link" href="javascript:void(0);" tabindex="1"><?php echo _AT('navigation'); ?></a>
			
				<div id="navigation-column">
				<?php if ($this->current_sub_level_page): ?>
				<ul id="topnavlist-tablet"  class="fl-list-menu" role="menu">
					<?php $accesscounter = 0; //initialize ?>
					<?php foreach ($this->top_level_pages as $page): ?>
						<?php ++$accesscounter; $accesscounter = ($accesscounter == 10 ? 0 : $accesscounter); ?>
						<?php $accesskey_text = ($accesscounter < 10 ? 'accesskey="'.$accesscounter.'"' : ''); ?>
						<?php $accesskey_title = ($accesscounter < 10 ? ' Alt+'.$accesscounter : ''); ?>
						<?php if ($page['url'] == $this->current_top_level_page): ?>
							<!-- note bug http://issues.fluidproject.org/browse/FLUID-4313 makes class "flc-screenNavigator-backButton fl-link-hilight" not work -->
							<li role="menuitem"><a  href="<?php echo $page['url']; ?>" <?php echo $accesskey_text; ?> class="flc-screenNavigator-backButton fl-link-hilight" title="<?php echo $page['title'];?>"><?php echo $page['title']; ?></a>  </li>
						<?php else: ?>
							<li role="menuitem"><a  href="<?php echo $page['url']; ?>" <?php echo $accesskey_text; ?> title="<?php echo $page['title']; ?>"><?php echo $page['title']; ?></a></li>
						<?php endif; ?>
						<?php $accesscounter = ($accesscounter == 0 ? 11 : $accesscounter); ?>
					<?php endforeach; ?>
				</ul>
				<?php endif; ?>
			</div>
			</div>
			<?php endif; ?>	
		
				
	<?php  ?>	
	<ul class="home-guide fl-tabs" id="home-guide" role="menu">
	<!--  CHECK TO SEE IF USER IS A STUDENT -->
	<?php if($_SESSION['is_admin'] === false&& $_SESSION['privileges'] == 0 ):?>
		<li role="menuitem"><a  href="<?php echo $this->base_path; ?>users/index.php"><?php echo _AT("home"); ?></a></li> 
	<?php endif;?>		
	<!--  CHECK TO SEE IF USER IS AN ADMINISTRATOR -->
	<?php //if($_SESSION['is_admin'] == 0 && $_SESSION['privileges'] == 1):
		if($_SESSION['is_admin'] === false && $_SESSION['privileges'] == AT_ADMIN_PRIV_ADMIN):?>
		<li role="menuitem"><a href="<?php echo $this->base_path; ?>admin/index.php"><?php echo _AT("home"); ?></a></li> 
	<?php endif;?>
	<!--  CHECK TO SEE IF USER IS AN INSTRUCTOR -->
	<?php if($_SESSION['is_admin'] === true): ?>
		<li role="menuitem"><a href="<?php echo $this->base_path; ?>users/index.php"><?php echo _AT("home"); ?></a></li> 
	<?php endif;?>
	
	<?php if (isset($this->guide) && isset($_SESSION["course_id"]) && $this->guide && ($_SESSION["prefs"]["PREF_SHOW_GUIDE"] || $_SESSION["course_id"] == "-1")) : ?>
			<li role="menuitem">
				  <a href="<?php echo $this->guide; ?>" id="guide" onclick="ATutor.poptastic('<?php echo $this->guide; ?>'); return false;" target="_new"><?php echo _AT("guide"); ?></a> 
			</li>
			<?php endif; ?>
		</ul>
	<?php ?>	
		

		<?php if(isset($_SESSION['course_id']) && $_SESSION['course_id'] > 0): ?> 	
		<div id="course-level-navigation" role="navigation" aria-live="assertive">
			<div id="content_link" role="navigation" aria-live="assertive" class="flc-screenNavigator-navbar ">
				<a id="" class="content_link_tablet content_link"  href="javascript:void(0);"><?php echo  _AT("content"); ?></a>	
			</div>	
			<div id="content">
				<?php $contentManager->printMainMenu(); ?>
				<script language="javascript" type="text/javascript"></script>
			</div>
		</div><!-- course-level navigation -->				
	</div> <!-- end sequence-links-course-navigation -->
	<?php endif; ?>
		
		
	
	</div><!--  END navigation-contentwrapper -->
	

		<div id="inner-contentwrapper" class="fl-container" >

	
			
		<!-- the sub navigation -->
		<div id="subnavbacktopage">
		<?php if (count($this->sub_level_pages) > 0): ?>
			
			<div id="subnavlistcontainer">		
				<!-- id="subnavlist" -->
			<div class="subnavcontain-contain" role="menu" aria-live="assertive">	
				<div class="subnavcontain">
					<div class="rectangle">
						<?php 
						echo $num_pages;
						$num_pages = count($this->sub_level_pages); ?>	
								<?php for ($i=0; $i<$num_pages; $i++): ?>	
									<?php if($i==0): ?>
				
									<!-- <a id="subnavlist-link" class="content-expand" href="javascript:void(0);"> Topics in <?php echo $this->sub_level_pages[$i]['title']; ?></a> -->
									<?php endif; ?>
								<?php endfor;?>
					</div>
				</div>
					<ul id="subnavlist">
					<?php $num_pages = count($this->sub_level_pages); ?>	
					<?php for ($i=0; $i<$num_pages; $i++): ?>				
						<?php if ($this->sub_level_pages[$i]['url'] == $this->current_sub_level_page): ?>
							<li  class="selected"><a href="<?php echo $this->sub_level_pages[$i]['url']; ?>"><?php echo $this->sub_level_pages[$i]['title']; ?></a></li> 
						<?php else: ?>
							<li><a href="<?php echo $this->sub_level_pages[$i]['url']; ?>"><?php echo $this->sub_level_pages[$i]['title']; ?></a></li>
						<?php endif; ?>
					<?php if ($i < $num_pages-1): 
						echo " ";?>
					<?php endif; ?>
					<?php endfor; ?>
					</ul>
				</div>
			</div>	
		<?php endif; ?>
	</div> <!--end subnavbacktopage-->	

	<div id="contentcolumn">	
		
   <?php if ($this->shortcuts): ?>
      <div id="shortcuts">
	      <ul>
		      <?php foreach ($this->shortcuts as $link): ?>
			      <li><a href="<?php echo $link['url']; ?>"><img src="<?php echo $link['icon']; ?>" alt="<?php echo $link['title']; ?>"  title="<?php echo $link['title']; ?>" class="shortcut_icon"/><!-- <?php echo $link['title']; ?> --></a></li>
		      <?php endforeach; ?>
	      </ul>
      </div>
      <?php endif; ?>
      
      
		<!--the page title-->
		
		<div id="page-title-back-to-page">
		<a name="content" title="<?php echo _AT('content'); ?>"></a>
		<h2 class="page-title"><?php echo $this->page_title; ?></h2>
			<div id="back-to-page">
				<?php if (isset($this->back_to_page)): ?>
					<a href="<?php echo $this->back_to_page['url']; ?>">
					<img border="0" alt="<?php echo _AT('back_to').' '.$this->back_to_page['title']; ?>" title="<?php echo _AT('back_to').' '.$this->back_to_page['title']; ?>" src="<?php echo $this->base_href; ?>themes/simplified_desktop/images/backarrow.jpg" style="float:left;"/></a>&nbsp;
				<?php endif; ?>
		</div>		
		</div>
		
		<!--  end page-title-back-to-page -->
	
		<?php global $msg; $msg->printAll(); $_base_href;?>
			<div id="content-sequence-links">
	
	<?php if(isset($_SESSION['course_id']) && $_SESSION['course_id'] > 0): ?> 
		
<!--		<div class="subnavcontain-contain" role="menu" aria-live="assertive">	
			<div class="subnavcontain">
				<div class="rectangle">
				<a id="content_link_phone"  class="content-expand" href="javascript:void(0);" >View Course Content</a> 
				 <a href="#">content</a>
				</div>
			</div> -->
					
	<!--	<div id="content">
			<?php $contentManager->printMainMenu(); ?>
				<script language="javascript" type="text/javascript"></script>
		</div> -->

	</div>


		
	<?php if (isset($this->course_id) && $this->course_id > 0): ?>
			
			<div class="subnavcontain2">
			
			
			<ul class="sequence-links">
				<?php if ($_SESSION["prefs"]["PREF_SHOW_NEXT_PREVIOUS_BUTTONS"]) { ?>
					<?php if ($this->sequence_links['resume']): ?>
						
						<li class="rectangle2 arrow forward">
							<a href="<?php echo $this->sequence_links['resume']['url']; ?>" class="previous-next resume" title="<?php echo _AT('resume').': '.$this->sequence_links['resume']['title']; ?>"><?php echo _AT('resume'); ?></a>
						</li>
					<?php else:
						if ($this->sequence_links['previous']): ?>
						<li class="rectangle2 arrow back">
							<a  href="<?php echo $this->sequence_links['previous']['url']; ?>" class="arrow back" title="<?php echo _AT('previous_topic').': '. $this->sequence_links['previous']['title']; ?>"> <?php echo _AT('previous'); ?> </a>
						</li>
					<?php endif;
						if ($this->sequence_links['next']): ?>		
						<li class=" rectangle2 arrow forward">
							<a  href="<?php echo $this->sequence_links['next']['url']; ?>" class=""  title="<?php echo _AT('next_topic').': '.$this->sequence_links['next']['title']; ?>"> <?php echo _AT('next'); ?></a>
						</li>
					<?php endif; ?>
				<?php endif; ?>
			<?php } ?>
				&nbsp;
				</div>
			</ul>
		<?php endif; ?>
	</div>
	<?php endif; ?><!-- the sub navigation -->
<?php  endif; ?>
	<!--[if lt IE 8]>
	  <div id="feedback">
		<ul>
			<li>For an optimal experience, this theme should be used with Internet Explorer 8 or higher.</li>
		</ul>
	 </div>
	<![endif]-->