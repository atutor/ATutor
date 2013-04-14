<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }

/* available header.tmpl.php variables:
 * $this->lang_code			the ISO language code
 * SITE_NAME				the site name from the config file
 * $this->page_title		the name of this page to use in the <title>
 * $this->lang_charset		the ISO language character set
 * $this->content_base_href	the <base href> to use for this page
 * $this->base_path			the absolute path to this atutor installation
 * $this->rtl_css			if set, the path to the RTL style sheet
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
global $system_courses;

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
	<meta name="Generator" content="ATutor - Copyright 2007 by http://atutor.ca" />
	<base href="<?php echo $this->content_base_href; ?>" />
	<link rel="shortcut icon" href="<?php echo $this->base_path; ?>favicon.ico" type="image/x-icon" />
	<link rel="stylesheet" href="<?php echo $this->base_path.'themes/'.$this->theme; ?>/print.css" type="text/css" media="print" />
	<link rel="stylesheet" href="<?php echo $this->base_path.'themes/'.$this->theme; ?>/styles.css" type="text/css" />
	<!--[if IE]>
	  <link rel="stylesheet" href="<?php echo $this->base_path.'themes/'.$this->theme; ?>/ie_styles.css" type="text/css" />
	<![endif]-->
	<link rel="stylesheet" href="<?php echo $this->base_path.'themes/'.$this->theme; ?>/forms.css" type="text/css" />
	<?php if ($system_courses[$_SESSION['course_id']]['rss']): ?>
	<link rel="alternate" type="application/rss+xml" title="<?php echo SITE_NAME; ?> - RSS 2.0" href="<?php echo $this->base_path; ?>get_rss.php?<?php echo $_SESSION['course_id']; ?>-2" />
	<link rel="alternate" type="application/rss+xml" title="<?php echo SITE_NAME; ?> - RSS 1.0" href="<?php echo $this->base_path; ?>get_rss.php?<?php echo $_SESSION['course_id']; ?>-1" />
	<?php endif; ?>
	<script src="<?php echo $this->base_path; ?>jscripts/infusion/InfusionAll.js" type="text/javascript"></script>
    <script src="<?php echo $this->base_path; ?>jscripts/ATutor.js" type="text/javascript"></script>   
    <script language="javascript" type="text/javascript">
    //<!--
    jQuery.noConflict();
    //-->
    </script>
    <?php echo $this->custom_css; ?>
    <?php echo $this->rtl_css; ?>
    <style id="pref_style" type="text/css"></style> 
</head>
<body onload="<?php echo $this->onload; ?>">
<div id="member-links" style="float: right;">
	<!-- hidden direct link to content -->
	<a href="<?php echo htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES); ?>#content" style="border: 0px;" accesskey="c"><img src="<?php echo $this->base_path; ?>images/clr.gif" height="1" width="1" border="0" alt="<?php echo _AT('goto_content'); ?> ALT+c" /></a>
	<a href="<?php echo htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES); ?>#menu" style="border: 0px;" accesskey="m"><img src="<?php echo $this->base_path; ?>images/clr.gif" height="1" width="1" border="0" alt="<?php echo _AT('goto_menu'); ?> ALT+m" /></a>

	<?php if ($_SESSION['is_super_admin']): ?>
		<a href="<?php echo $this->base_path; ?>bounce.php?admin"><?php echo _AT('return_to_admin_area'); ?></a> | 
	<?php endif; ?>

	<?php if ($_SESSION['valid_user']): ?>
		<?php     if (!admin_authenticate(AT_ADMIN_PRIV_ADMIN, AT_PRIV_RETURN) && $last_path_part != 'preferences.php') {?>
		    <a class="pref_wiz_launcher"><img border="0" alt="<?php echo _AT('preferences').' - '._AT('new_window'); ?>" src="<?php echo $this->base_href; ?>images/wand.png" /></a> |
		    <?php } ?> 
		<strong><?php echo get_display_name($_SESSION['member_id']); ?></strong>  |
		<?php if ($_SESSION['course_id'] > -1): ?>
			<?php if (get_num_new_messages()): ?>
				<strong><a href="<?php echo $this->base_path; ?>inbox/index.php"><?php echo _AT('inbox'); ?> - <?php echo get_num_new_messages(); ?></a></strong> | 
			<?php else: ?>
				<a href="<?php echo $this->base_path; ?>inbox/index.php"><?php echo _AT('inbox'); ?></a> | 
			<?php endif; ?>
		<?php endif; ?>
		<a href="<?php echo $this->base_path; ?>search.php"><?php echo _AT('search'); ?></a> |
		<a href="<?php echo $this->base_path; ?>help/index.php"><?php echo _AT('help'); ?></a> |
		<a href="<?php echo $this->base_path; ?>logout.php"><?php echo _AT('logout'); ?></a>
	<?php elseif ($_SESSION['course_id'] == -1): ?>
		<img src="<?php echo $this->img;?>user-star.gif" style="vertical-align: middle;" class="img-size-star" alt="" /><strong><?php echo get_display_name($_SESSION['member_id']); ?></strong>  |
		<a href="<?php echo $this->base_path; ?>search.php"><?php echo _AT('search'); ?></a> |
		<a href="<?php echo $this->base_path; ?>help/index.php"><?php echo _AT('help'); ?></a> |
		<a href="<?php echo $this->base_path; ?>logout.php"><?php echo _AT('logout'); ?></a>
	<?php else: ?>
		<a href="<?php echo $this->base_path; ?>browse.php"><?php echo _AT('browse_courses'); ?></a> | 
		<a href="<?php echo $this->base_path; ?>login.php?course=<?php echo $_SESSION['course_id']; ?>"><?php echo _AT('login'); ?></a> | 
		<a href="<?php echo $this->base_path; ?>search.php"><?php echo _AT('search'); ?></a> | 
		<a href="<?php echo $this->base_path; ?>help/index.php"><?php echo _AT('help'); ?></a>
	<?php endif; ?>
</div>

<div style="float: right;">
	<?php if (isset($_SESSION['member_id']) && $_SESSION['member_id']): ?>
		<!-- start the jump menu -->
		<?php if (empty($_GET)): ?>
			<form method="post" action="<?php echo $this->base_path; ?>bounce.php?p=<?php echo urlencode($this->rel_url); ?>" target="_top">
		<?php else: ?>
			<form method="post" action="<?php echo $this->base_path; ?>bounce.php" target="_top">
		<?php endif; ?>
		<label for="jumpmenu" accesskey="j"></label>
		<select name="course" id="jumpmenu" title="<?php echo _AT('jump'); ?>:  Alt-j">							
			<option value="0" id="start-page"><?php echo _AT('my_start_page'); ?></option>
			<optgroup label="<?php echo _AT('courses_below'); ?>">
				<?php foreach ($this->nav_courses as $this_course_id => $this_course_title): ?>
					<option value="<?php echo $this_course_id; ?>"><?php echo $this_course_title; ?></option>
				<?php endforeach; ?>
			</optgroup>
		</select> <input type="submit" name="jump" value="<?php echo _AT('jump'); ?>" id="jump-button" /></form>
		<!-- /end the jump menu -->
	<?php endif; ?>
</div>

<?php if ($_SESSION["prefs"]["PREF_SHOW_BREAD_CRUMBS"]) { ?>
<div id="breadcrumbs">
	<span style="white-space:nowrap;font-size:smaller;padding-top:150px;">
	<?php foreach ($this->path as $page): ?>
		<a href="<?php echo $page['url']; ?>" title="<?php echo _AT('back_to').' '.$page['title']; ?>"><?php echo htmlspecialchars($page['title'], ENT_COMPAT, "UTF-8"); ?></a> &raquo; 
	<?php endforeach; ?> <?php echo $this->page_title; ?></span>
</div>
<?php } ?>

<div class="header">
	<!-- section title -->	
	<?php if ($_SESSION['valid_user']): 
		echo '<span style="font-size:small;font-weight:bold;padding-left:5px;">'.stripslashes(SITE_NAME).'</span>'; 
	else:
		echo '<br />';	
	endif; ?>
	<h1><?php echo $this->section_title; ?>
	<?php if (($_SESSION['course_id'] > 0) && ($_SESSION['enroll'] == AT_ENROLL_NO)) : ?>
		- <small><a href="<?php echo $this->base_path; ?>enroll.php?course=<?php echo $_SESSION['course_id']; ?>"><?php echo _AT('enroll_me'); ?></a></small>
	<?php endif; ?></h1>

	<div id="topnavlistcontainer">
		<!-- the main navigation. in our case, tabs -->
		<ul id="topnavlist">
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

<!-- the sub navigation -->
<div style="text-align: right; padding-top: 5px; padding-right: 5px;"><small><?php echo $this->current_date; ?></small></div>
<?php if ($this->sub_level_pages && !(count($this->sub_level_pages) == 1 && $this->sub_level_pages[0]['url'] == $this->current_sub_level_page)): ?>
	<div id="sub-navigation">
		<?php if (isset($this->back_to_page)): ?>
			<a href="<?php echo $this->back_to_page['url']; ?>" id="back-to"><?php echo _AT('back_to'); ?> <?php echo $this->back_to_page['title']; ?></a> | 
		<?php endif; ?>

		<?php $num_pages = count($this->sub_level_pages); ?>
		<?php for($i=0; $i<$num_pages; $i++): ?>
			<?php if ($this->sub_level_pages[$i]['url'] == $this->current_sub_level_page): ?>
				<strong><?php echo $this->sub_level_pages[$i]['title']; ?></strong>
			<?php else: ?>
				<a href="<?php echo $this->sub_level_pages[$i]['url']; ?>"><?php echo $this->sub_level_pages[$i]['title']; ?></a>
			<?php endif; ?>
			<?php if ($i < $num_pages-1): ?>
				| 
			<?php endif; ?>
		<?php endfor; ?>
	</div>
<?php else: ?>
	<div id="sub-navigation">
		&nbsp;
	</div>
<?php endif; ?>

<table align="center" border="0" cellpadding="0" cellspacing="0" width="99%">
	<tr>
		<?php if ($_SESSION['course_id'] > 0): ?>
			<td valign="top" width="100%">
		<?php else: ?>
			<td valign="top" width="100%" colspan="2">
		<?php endif; ?>

<!-- the page title -->
	<div style="text-align: right; padding-bottom: 10px; padding-right: 10px; float: right; margin-top: 10px; padding-right: 5px;">
		<?php if ($this->guide && ($_SESSION["prefs"]["PREF_SHOW_GUIDE"] || $_SESSION["course_id"] == "-1")): ?>
			<a href="<?php echo $this->guide; ?>" id="guide" onclick="ATutor.poptastic('<?php echo $this->guide; ?>'); return false;" target="_new"><strong><?php echo $this->page_title; ?></strong></a>
		<?php endif; ?>


	</div>
	<?php if ($_SESSION['course_id'] > 0 && $system_courses[$_SESSION['course_id']]['side_menu']): ?>
	    <div id="menutoggle">
		<a accesskey="n"><img src="" title="" alt="" /></a>
	    </div>


	<div id="sequence-links">
	<?php if ($_SESSION["prefs"]["PREF_SHOW_NEXT_PREVIOUS_BUTTONS"]) { ?>
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
	<?php } ?>
		&nbsp;
	</div>
<br style="clear:both;">
	<?php endif; ?>
	<h2 class="page-title"><?php echo htmlspecialchars($this->page_title, ENT_COMPAT, "UTF-8"); ?></h2>

<a name="content"></a>
<?php global $msg; $msg->printAll(); ?>
