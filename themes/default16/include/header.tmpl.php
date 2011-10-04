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
 * $this->top_level_pages	associative array of the top level navigation
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
 */

// will have to be moved to the header.inc.php
global $system_courses, $_custom_css,$db;

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="<?php echo $this->lang_code; ?>"> 
<head>
	<title><?php echo SITE_NAME; ?> : <?php echo $this->page_title; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $this->lang_charset; ?>" />
	<meta name="Generator" content="ATutor - Copyright 2010 by http://atutor.ca" />
	<base href="<?php echo $this->content_base_href; ?>" />
	<link rel="shortcut icon" href="<?php echo $this->base_path; ?>favicon.ico" type="image/x-icon" />
	<link rel="stylesheet" href="<?php echo $this->base_path.'themes/'.$this->theme; ?>/print.css" type="text/css" media="print" />
	<link rel="stylesheet" href="<?php echo $this->base_path.'themes/'.$this->theme; ?>/styles.css" type="text/css" />
	<!--[if IE]>
	  <link rel="stylesheet" href="<?php echo $this->base_path.'themes/'.$this->theme; ?>/ie_styles.css" type="text/css" />
	<![endif]-->
    <link rel="stylesheet" href="<?php echo $this->base_path.'themes/'.$this->theme; ?>/forms.css" type="text/css" />
<link rel="stylesheet" type="text/css" href="<?php echo $this->base_path; ?>jscripts/infusion/framework/fss/css/fss-layout.css" />

<?php if (isset($this->course_id) && $system_courses[$this->course_id]['rss']): ?>
	<link rel="alternate" type="application/rss+xml" title="<?php echo SITE_NAME; ?> - RSS 2.0" href="<?php echo $this->base_href; ?>get_rss.php?<?php echo $this->course_id; ?>-2" />
	<link rel="alternate" type="application/rss+xml" title="<?php echo SITE_NAME; ?> - RSS 1.0" href="<?php echo $this->base_href; ?>get_rss.php?<?php echo $this->course_id; ?>-1" />
<?php endif; ?>
	<script src="<?php echo $this->base_path; ?>jscripts/infusion/InfusionAll.js" type="text/javascript"></script>
	<script language="javascript" type="text/javascript">
	//<!--
	jQuery.noConflict();
	//-->
	</script>

    <script src="<?php echo $this->base_path; ?>jscripts/ATutor.js" type="text/javascript"></script>   
    <?php echo $this->custom_css; ?>
    <?php echo $this->rtl_css; ?>
    <style id="pref_style" type="text/css"></style> 

</head>
<body onload="<?php echo $this->onload; ?>"><div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>

<div id="header">
	<div id="top-links"> <!-- top help/search/login links -->
		<?php if (isset($_SESSION['member_id']) && $_SESSION['member_id']): ?>
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
			<?php if ($_SESSION['is_super_admin']): ?>
				<a href="<?php echo $this->base_path; ?>bounce.php?admin"><?php echo _AT('return_to_admin_area'); ?></a> | 
			<?php endif; ?>

			<?php if ($_SESSION['course_id'] > -1): ?>
				<?php if (get_num_new_messages()): ?>
					<a href="<?php echo $this->base_path; ?>inbox/index.php"><?php echo _AT('inbox'); ?> (<?php echo get_num_new_messages(); ?>)</a> 
				<?php else: ?>
					<a href="<?php echo $this->base_path; ?>inbox/index.php"><?php echo _AT('inbox'); ?></a>
				<?php endif; ?>
			<?php endif; ?>
		<?php endif; ?>
		<a href="<?php echo $this->base_path; ?>search.php"><?php echo _AT('search'); ?></a> <a href="<?php echo $this->base_path; ?>help/index.php"><?php echo _AT('help'); ?></a>
	</div>
	<?php if (!empty($this->icon)) { // if a course icon is available, display it here.  ?>
		<a href="<?php echo $this->base_path; ?>index.php"><img src="<?php echo $this->icon; ?>" class="headicon" alt="<?php echo  _AT('home'); ?>" /></a>	
	<?php } ?>

	<a href="<?php echo htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES); ?>#content" accesskey="c">
	<img src="<?php echo $this->base_path; ?>images/clr.gif" height="1" width="1" border="0" alt="<?php echo _AT('goto_content'); ?> ALT+c" /></a>		

	<a href="<?php echo htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES); ?>#menu"  accesskey="m"><img src="<?php echo $this->base_path; ?>images/clr.gif" height="1" width="1" border="0" alt="<?php echo _AT('goto_menu'); ?> ALT+m" /></a>

	<?php
	// If there is a custom course banner in the file manager called banner.html, display it here
	@readfile(AT_CONTENT_DIR . $_SESSION['course_id'].'/banner.txt'); 

	/*
	and example banner.html file might look like:
	<div style="width: 760px; height: 42px; background: white;"><img src="http://[mysite]/atutor15rc3/banners/kart-camb.jpg"></div>
	*/

	?>
	<!-- section title -->
	<?php if ($_SESSION['valid_user']): 
		echo '<div style="font-size:small;font-weight:bold;padding-left:1em;color:white;">'.stripslashes(SITE_NAME).'</div>'; 
	else:
		echo '<br />';	
	endif; ?>
	<h1 id="section-title"><?php echo $this->section_title; ?>
		<?php if (($_SESSION['course_id'] > 0) && ($_SESSION['enroll'] == AT_ENROLL_NO)) : ?> 
			- <small><a href="<?php echo $this->base_path; ?>enroll.php?course=<?php echo $_SESSION['course_id']; ?>"><?php echo _AT('enroll_me'); ?></a></small>
		<?php endif; ?>
	</h1>


</div>

<div id="topnavlistcontainer">
<!-- the main navigation. in our case, tabs -->
	<ul id="topnavlist">
		<?php foreach ($this->top_level_pages as $page): ?>
			<?php ++$accesscounter; $accesscounter = ($accesscounter == 10 ? 0 : $accesscounter); ?>
			<?php $accesskey_text = ($accesscounter < 10 ? 'accesskey="'.$accesscounter.'"' : ''); ?>
			<?php $accesskey_title = ($accesscounter < 10 ? ' Alt5+'.$accesscounter : ''); ?>
			<?php if ($page['url'] == $this->current_top_level_page): ?>
				<li><a href="<?php echo $page['url']; ?>" <?php echo $accesskey_text; ?> title="<?php echo $page['title'] . $accesskey_title; ?>" class="active"><?php echo $page['title']; ?></a></li>
			<?php else: ?>
				<li><a href="<?php echo $page['url']; ?>" <?php echo $accesskey_text; ?> title="<?php echo $page['title'] . $accesskey_title; ?>"><?php echo $page['title']; ?></a></li>
			<?php endif; ?>
			<?php $accesscounter = ($accesscounter == 0 ? 11 : $accesscounter); ?>
		<?php endforeach; ?>
	</ul>
</div>
	<!-- the sub navigation -->
<div class="logoutbar">
		<?php if (isset($_SESSION['valid_user']) && $_SESSION['valid_user']): ?>
		<?php $path_parts = explode("/", $this->current_top_level_page); 
		      $last_path_part = $path_parts[sizeof($path_parts) - 1];
               if (!admin_authenticate(AT_ADMIN_PRIV_ADMIN, AT_PRIV_RETURN) && $last_path_part != 'preferences.php') {?>
		    <a class="pref_wiz_launcher"><img border="0" alt="<?php echo _AT('preferences').' - '._AT('new_window'); ?>" src="<?php echo $this->base_href; ?>images/wand.png" /></a> |
		    <?php } ?> 
			<strong><?php echo get_display_name($_SESSION['member_id']); ?></strong> | 
			<a href="<?php echo $this->base_path; ?>logout.php"><?php echo _AT('logout'); ?></a>
		<?php else: ?>
			 <a href="<?php echo $this->base_path; ?>login.php?course=<?php echo $this->course_id; ?>"><?php echo _AT('login'); ?></a> | <a href="<?php echo $this->base_path; ?>registration.php"><?php echo _AT('register'); ?></a>
		<?php endif; ?>
</div>

<div style="padding:3px;">			
	<!-- the bread crumbs -->
	<div id="breadcrumbs">
		<?php foreach ($this->path as $page): ?>
			<a href="<?php echo $page['url']; ?>"><?php echo $page['title']; ?></a> > 
		<?php endforeach; ?> <?php echo $this->page_title; ?>
	</div>
	<?php if ($this->guide): ?>
		<a href="<?php echo $this->guide; ?>" id="guide" onclick="ATutor.poptastic('<?php echo $this->guide; ?>'); return false;" target="_new"><strong><?php echo $this->page_title; ?></strong></a>
	<?php endif; ?>
</div>

      <?php if ($this->shortcuts): ?>
      <div id="shortcuts">
	      <ul>
		      <?php foreach ($this->shortcuts as $link): ?>
			      <li><a href="<?php echo $link['url']; ?>"><img src="<?php echo $link['icon']; ?>" alt="<?php echo $link['title']; ?>"  title="<?php echo $link['title']; ?>" class="shortcut_icon"/><!-- <?php echo $link['title']; ?> --></a></li>
		      <?php endforeach; ?>
	      </ul>
      </div>
      <?php endif; ?>

<div id="contentwrapper">

	<?php if (isset($this->course_id) && $this->course_id > 0 && $system_courses[$this->course_id]['side_menu']): ?>
		<div id="leftcolumn">
		  <a name="menu"></a>
		     <div id="side-menu">
		        <?php require(AT_INCLUDE_PATH.'side_menu.inc.php'); ?>
		    </div>
		</div>
	<?php endif; ?>

	<div id="contentcolumn"
		<?php if (($_SESSION['course_id'] <= 0) && !$this->side_menu): ?>
			style="margin-left:0.5em;width:95%;"
		<?php endif; ?>
		>

		<?php if (isset($this->course_id) && $this->course_id > 0 && $system_courses[$this->course_id]['side_menu']): ?>
		<div id="menutoggle">
		    <a accesskey="n"><img src="" title="" alt="" /></a>
		</div>
		<div id="sequence-links">
			<?php if ($this->sequence_links['resume']): ?>
					<a style="color:white;" href="<?php echo $this->sequence_links['resume']['url']; ?>" accesskey="."><img src="<?php echo $this->img; ?>resume.gif" border="0" title="<?php echo _AT('resume').': '.$this->sequence_links['resume']['title']; ?> Alt+." alt="<?php echo $this->sequence_links['resume']['title']; ?> Alt+." class="img-size-ascdesc" /></a>
			<?php else:
				if ($this->sequence_links['previous']): ?>
					<a href="<?php echo $this->sequence_links['previous']['url']; ?>" title="<?php echo _AT('previous_topic').': '. $this->sequence_links['previous']['title']; ?> Alt+," accesskey=","><img src="<?php echo $this->img; ?>previous.gif" border="0" alt="<?php echo _AT('previous_topic').': '. $this->sequence_links['previous']['title']; ?> Alt+," class="img-size-ascdesc" /></a>
				<?php endif;
				if ($this->sequence_links['next']): ?>
					<a href="<?php echo $this->sequence_links['next']['url']; ?>" title="<?php echo _AT('next_topic').': '.$this->sequence_links['next']['title']; ?> Alt+." accesskey="."><img src="<?php echo $this->img; ?>next.gif" border="0" alt="<?php echo _AT('next_topic').': '.$this->sequence_links['next']['title']; ?> Alt+." class="img-size-ascdesc" /></a>
				<?php endif; ?>
			<?php endif; ?>
			&nbsp;
		</div>
		<?php endif; ?>

	<!-- the page title -->
	<a name="content" title="<?php echo _AT('content'); ?>"></a>
	<h2 class="page-title"><?php echo $this->page_title; ?></h2>
	<?php global $msg; $msg->printAll(); $_base_href;?>

	<?php if (count($this->sub_level_pages) > 0): ?>

<!-- <div id="topnavlistcontainer">
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
</div> -->

		<div id="subnavlistcontainer">
			<div id="subnavbacktopage">
			<?php if (isset($this->back_to_page)): ?>
				<a href="<?php echo $this->back_to_page['url']; ?>">
				<img border="0" width="10" height="11" alt="<?php echo _AT('back_to').' '.htmlentities_utf8($this->back_to_page['title']); ?>" src="<?php echo $this->base_href; ?>images/arrowicon.gif" style="float:left;" class="img1111"/></a>&nbsp;
			<?php endif; ?>
			</div>

			<ul id="subnavlist">
			<?php $num_pages = count($this->sub_level_pages); 



?>
			<?php for ($i=0; $i<$num_pages; $i++): ?>
				
				<?php if ($this->sub_level_pages[$i]['url'] == $this->current_sub_level_page): ?>
				      <li class="active"><?php echo htmlentities_utf8($this->sub_level_pages[$i]['title']); ?></li>
				<?php else: ?>
					<li><a href="<?php echo $this->sub_level_pages[$i]['url']; ?>"><?php echo htmlentities_utf8($this->sub_level_pages[$i]['title']); ?></a></li>
				<?php endif; ?>
				<?php if ($i < $num_pages-1): 
					echo " ";?>
				<?php endif; ?>
			<?php endfor; ?>
			</ul>
		</div>
	<?php endif; ?>

<!-- the main navigation. in our case, tabs -->