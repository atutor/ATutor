<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$
if (!defined('AT_INCLUDE_PATH')) { exit; }

/* available header.tmpl.php variables:
 *
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
<html lang="<?php echo $this->tmpl_lang; ?>">
<head>
	<title><?php echo SITE_NAME; ?> : <?php echo $this->page_title; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $this->tmpl_charset; ?>" />
	<meta name="Generator" content="ATutor - Copyright 2005 by http://atutor.ca" />
	<base href="<?php echo $this->tmpl_content_base_href; ?>" />
	<link rel="shortcut icon" href="<?php echo $this->tmpl_base_path; ?>favicon.ico" type="image/x-icon" />
	<link rel="stylesheet" href="<?php echo $this->tmpl_base_path; ?>print.css" type="text/css" media="print" />
	<link rel="stylesheet" href="<?php echo $this->tmpl_base_path.'themes/'.$this->tmpl_theme; ?>/styles.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $this->tmpl_base_path.'themes/'.$this->tmpl_theme; ?>/forms.css" type="text/css" />
	<?php echo $this->tmpl_rtl_css; ?>
	<style type="text/css"><?php echo $this->tmpl_banner_style; ?></style>
	<?php if ($system_courses[$_SESSION['course_id']]['rss']): ?>
	<link rel="alternate" type="application/rss+xml" title="ATutor course - RSS 2.0" href="<?php echo $this->tmpl_base_href; ?>get_rss.php?<?php echo $_SESSION['course_id']; ?>-2" />
	<link rel="alternate" type="application/rss+xml" title="ATutor course - RSS 1.0" href="<?php echo $this->tmpl_base_href; ?>get_rss.php?<?php echo $_SESSION['course_id']; ?>-1" />
	<?php endif; ?>
</head>
<body <?php echo $this->tmpl_onload; ?>><div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
<script language="JavaScript" src="<?php echo $this->tmpl_base_path; ?>overlib.js" type="text/javascript"></script><a href="<?php echo $_SERVER['REQUEST_URI']; ?>#content"><img src="<?php echo $this->tmpl_base_path; ?>images/clr.gif" height="1" width="1" border="0" alt="<?php echo _AT('goto_content'); ?>" /></a>

<!-- section title -->
<h1 id="section-title"><?php echo $this->section_title; ?></h1>

<!-- top help/search/login links -->
<div align="right" id="top-links">
	<a href="<?php echo $this->tmpl_base_path; ?>search.php"><?php echo _AT('search'); ?></a> | <a href="<?php echo $this->tmpl_base_path; ?>help/index.php"><?php echo _AT('help'); ?></a>
<?php if ($_SESSION['valid_user'] && ($_SESSION['course_id'] >= 0)): ?>
	 | <a href="<?php echo $this->tmpl_base_path; ?>logout.php"><?php echo _AT('logout'); ?></a><br />
	<form method="post" action="<?php echo $this->tmpl_base_path; ?>bounce.php?p=<?php echo urlencode($this->tmpl_rel_url); ?>" target="_top">
		<label for="jumpmenu" accesskey="j"></label>
			<select name="course" id="jumpmenu" title="<?php echo _AT('jump'); ?>:  ALT-j">							
				<option value="0"><?php echo _AT('my_start_page'); ?></option>
				<optgroup label="<?php echo _AT('courses_below'); ?>">
					<?php foreach ($this->tmpl_nav_courses as $this_course_id => $this_course_title): ?>
						<?php if ($this_course_id == $_SESSION['course_id']): ?>
							<option value="<?php echo $this_course_id; ?>" selected="selected"><?php echo $this_course_title; ?></option>
						<?php else: ?>
							<option value="<?php echo $this_course_id; ?>"><?php echo $this_course_title; ?></option>
						<?php endif; ?>
					<?php endforeach; ?>
				</optgroup>
			</select> <input type="submit" name="jump" value="<?php echo _AT('jump'); ?>" id="jump-button" /><input type="hidden" name="g" value="22" /></form>
<?php elseif ($_SESSION['valid_user']): ?>
	 | <a href="<?php echo $this->tmpl_base_path; ?>logout.php"><?php echo _AT('logout'); ?></a><br />
<?php else: ?>
	 | <a href="<?php echo $this->tmpl_base_path; ?>login.php?course=<?php echo $_SESSION['course_id']; ?>"><?php echo _AT('login'); ?></a><br /><br />
<?php endif; ?>
</div>

<!-- back to the current section -->
	<?php if ($_SESSION['valid_user'] && ($_SESSION['course_id'] > 0)): ?>
		<a href="<?php echo $this->tmpl_base_path; ?>bounce.php?course=0" id="my-start-page">Back to My Start Page</a>
	<?php endif; ?>

<!-- the bread crumbs -->
	<div id="breadcrumbs">
		<?php echo $this->section_title; ?> : 
		<?php foreach ($this->path as $page): ?>
			<a href="<?php echo $page['url']; ?>"><?php echo $page['title']; ?></a> » 
		<?php endforeach; ?> <?php echo $this->page_title; ?>
	</div>

<!-- the main navigation. in our case, tabs -->
<table class="tabbed-table" align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
<tr>
	<th id="left-empty-tab">&nbsp;</th>
	<?php foreach ($this->top_level_pages as $page): ?>
		<?php if ($page['url'] == $this->current_top_level_page): ?>
			<th class="selected"><a href="<?php echo $page['url']; ?>"><div><?php echo $page['title']; ?></div></a></th>
			<th class="tab-spacer">&nbsp;</th>
		<?php else: ?>
			<th class="tab"><a href="<?php echo $page['url']; ?>"><div><?php echo $page['title']; ?></div></a></th>
			<th class="tab-spacer">&nbsp;</th>
		<?php endif; ?>
	<?php endforeach; ?>
	<th id="right-empty-tab">
		<small><?php echo $this->tmpl_current_date; ?>&nbsp;</small>
	</th>
	</tr>
	</table>
</div>
<!-- the sub navigation -->

<?php if ($this->sub_level_pages): ?>
	<div id="sub-navigation">
		<?php if (isset($this->back_to_page)): ?>
			<a href="<?php echo $this->back_to_page['url']; ?>" id="back-to">Back to <?php echo $this->back_to_page['title']; ?></a> | 
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

<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<?php if ($_SESSION['course_id'] > 0): ?>
			<td valign="top" width="100%">
		<?php else: ?>
			<td valign="top" width="100%" colspan="2">
		<?php endif; ?>


<!-- the page title -->
	<?php if ($this->sequence_links): ?>
		<div id="sequence-links">
			<?php if ($this->sequence_links['resume']): ?>
				<a href="<?php echo $this->sequence_links['resume']['url']; ?>"><?php echo $this->sequence_links['resume']['title']; ?></a>
			<?php else: ?>
				<?php if ($this->sequence_links['previous'] && $this->sequence_links['next']): ?>
					<a href="<?php echo $this->sequence_links['previous']['url']; ?>">« <?php echo $this->sequence_links['previous']['title']; ?></a>
					|
					<a href="<?php echo $this->sequence_links['next']['url']; ?>"><?php echo $this->sequence_links['next']['title']; ?> »</a>
				<?php elseif ($this->sequence_links['previous']): ?>
					<a href="<?php echo $this->sequence_links['previous']['url']; ?>">« <?php echo $this->sequence_links['previous']['title']; ?></a>
				<?php elseif ($this->sequence_links['next']): ?>
					<a href="<?php echo $this->sequence_links['next']['url']; ?>"><?php echo $this->sequence_links['next']['title']; ?> »</a>
				<?php endif; ?>


			<?php endif; ?>
		</div>
	<?php endif; ?>
	<h2 class="page-title"><?php echo $this->page_title; ?></h2>

<a name="content"></a>
<?php global $msg; $msg->printAll(); ?>