<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }

global $system_courses, $_custom_css, $_base_path;

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php echo $this->lang_code; ?>">
<head>
	<title><?php echo SITE_NAME; ?> : <?php echo $this->page_title; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $this->lang_charset; ?>" />
	<meta name="Generator" content="ATutor - Copyright 2005 by http://atutor.ca" />
	<base href="<?php echo $this->content_base_href; ?>" />
	<link rel="shortcut icon" href="<?php echo $this->base_path; ?>favicon.ico" type="image/x-icon" />
	<link rel="stylesheet" href="<?php echo $this->base_path; ?>themes/default/print.css" type="text/css" media="print" />
	<link rel="stylesheet" href="<?php echo $this->base_path; ?>themes/default/forms.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $this->base_path.'themes/'.$this->theme; ?>/styles.css" type="text/css" />
	<!--[if IE]>
	  <link rel="stylesheet" href="<?php echo $this->base_path.'themes/'.$this->theme; ?>/ie_styles.css" type="text/css" />
	<![endif]-->


	<style type="text/css"><?php echo $this->banner_style; ?></style>
	<?php if ($system_courses[$this->course_id]['rss']): ?>
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
<body onload="<?php echo $this->onload; ?>">
<div class="bypass">
	<!-- hidden direct link to content -->
	<a href="<?php echo htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES); ?>#content" style="border: 0px;" accesskey="c"><img src="<?php echo $this->base_path; ?>images/clr.gif" height="1" width="1" border="0" alt="<?php echo _AT('goto_content'); ?> ALT+c" /></a>
</div>
<div id="member-links">
	<?php if ($_SESSION['is_super_admin']): ?>
		<a href="<?php echo $this->base_path; ?>bounce.php?admin"><?php echo _AT('return_to_admin_area'); ?></a> | 
	<?php endif; ?>
	<div class="logout">
	<?php if ($_SESSION['valid_user']): ?>

	<?php if (isset($this->course_id) && ($this->course_id >= 0)): ?>
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

<?php     if (!admin_authenticate(AT_ADMIN_PRIV_ADMIN, AT_PRIV_RETURN) && $last_path_part != 'preferences.php') {?>
		    <a class="pref_wiz_launcher"><img border="0" alt="<?php echo _AT('preferences').' - '._AT('new_window'); ?>" src="<?php echo $this->base_href; ?>images/wand.png" /></a> |
		    <?php } ?> 
		<span style="font-weight:bold;"><?php echo get_display_name($_SESSION['member_id']); ?></span>  | 

		<?php if ($this->course_id > -1): ?>
			<?php if (get_num_new_messages()): ?>
				<a href="<?php echo $this->base_path; ?>inbox/index.php"><?php echo _AT('inbox'); ?> - <?php echo get_num_new_messages(); ?></a> | 
			<?php else: ?>
				<a href="<?php echo $this->base_path; ?>inbox/index.php"><?php echo _AT('inbox'); ?></a> | 
			<?php endif; ?>
		<?php endif; ?>
		<a href="<?php echo $this->base_path; ?>search.php"><?php echo _AT('search'); ?></a> | 
		<a href="<?php echo $this->base_path; ?>help/index.php"><?php echo _AT('help'); ?></a> |
		<a href="<?php echo $this->base_path; ?>logout.php"><?php echo _AT('logout'); ?></a>
	<?php else: ?>
		<a href="<?php echo $this->base_path; ?>login.php?course=<?php echo $this->course_id; ?>"><?php echo _AT('login'); ?></a> | 
		<a href="<?php echo $this->base_path; ?>search.php"><?php echo _AT('search'); ?></a> | 
		<a href="<?php echo $this->base_path; ?>help/index.php"><?php echo _AT('help'); ?></a>
	<?php endif; ?>
	</div><!-- end logout div -->

<?php if ($_SESSION["prefs"]["PREF_SHOW_BREAD_CRUMBS"]) { ?>
<!-- the bread crumbs -->
<div id="breadcrumbs">
	<?php foreach ($this->path as $page): ?>
		<a href="<?php echo $page['url']; ?>" title="<?php echo _AT('back_to').' '.$page['title']; ?>"><?php echo htmlspecialchars($page['title'], ENT_COMPAT, "UTF-8"); ?></a> &raquo; 
	<?php endforeach; ?> <?php echo $this->page_title; ?>

	<?php } ?>
  
   </div><!-- end member-links div -->
</div><!-- end bredcrumbs div -->
<div class="header">
    <?php if ($_SESSION['valid_user']) : 
	    echo '<span style="font-size:small;font-weight:bold;padding-left:5px;">'.stripslashes(SITE_NAME).'</span>'; 
    endif; ?>
    <h1 class="section-title">

	    <!-- section title -->
	    <?php echo $this->section_title; ?>
	    <?php if (($this->course_id > 0) && ($_SESSION['enroll'] == AT_ENROLL_NO)) : ?>
		    - <a href="<?php echo $this->base_path; ?>enroll.php?course=<?php echo $this->course_id; ?>"><?php echo _AT('enroll_me'); ?></a></small>
	    <?php endif; ?></h1>

</div>

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


<!-- the sub navigation -->
<div style="text-align: right; padding-top: 5px; padding-right: 5px; float:right"><small><?php echo $this->current_date; ?></small></div>
<?php if ($this->sub_level_pages): ?>
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

<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<?php if ($this->course_id > 0): ?>
			<td valign="top" width="100%">
		<?php else: ?>
			<td valign="top" width="100%" colspan="2">
		<?php endif; ?>

<!-- the page title -->

	<div  id="sequence-links">
	<?php if ($_SESSION["prefs"]["PREF_SHOW_NEXT_PREVIOUS_BUTTONS"]) { ?>
		<?php if ($this->sequence_links['resume']): ?>
				<a style="color:white;" href="<?php echo $this->sequence_links['resume']['url']; ?>" accesskey="."><img src="<?php echo $this->base_href; ?>themes/default_classic/images/resume.png" border="0" title="<?php echo _AT('resume').': '.$this->sequence_links['resume']['title']; ?> Alt+." alt="<?php echo $this->sequence_links['resume']['title']; ?> Alt+." /></a>
		<?php else:
			if ($this->sequence_links['previous']): ?>
				<a href="<?php echo $this->sequence_links['previous']['url']; ?>" title="<?php echo _AT('previous_topic').': '. $this->sequence_links['previous']['title']; ?> Alt+," accesskey=","><img src="<?php echo $this->base_href; ?>themes/default_classic/images/previous.png" border="0" alt="<?php echo _AT('previous_topic').': '. $this->sequence_links['previous']['title']; ?> Alt+,"/></a>
			<?php endif;
			if ($this->sequence_links['next']): ?>
				<a href="<?php echo $this->sequence_links['next']['url']; ?>" title="<?php echo _AT('next_topic').': '.$this->sequence_links['next']['title']; ?> Alt+." accesskey="."><img src="<?php echo $this->base_href; ?>themes/default_classic/images/next.png" border="0" alt="<?php echo _AT('next_topic').': '.$this->sequence_links['next']['title']; ?> Alt+."  /></a>
			<?php endif; ?>
		<?php endif; ?>
	<?php } ?>
		&nbsp;
	</div>

		<?php if ($this->guide && ($_SESSION["prefs"]["PREF_SHOW_GUIDE"] || $_SESSION["course_id"] == "-1")): ?>
	<div id="guide_box">
			<a href="<?php echo $this->guide; ?>" id="guide" onclick="ATutor.poptastic('<?php echo $this->guide; ?>'); return false;" target="_new"><strong><?php echo $this->page_title; ?></strong></a>
		</div>
	  <?php endif; ?>




      <?php if ($this->shortcuts): ?>
      <div id="shortcuts">
	      <ul>
		      <?php foreach ($this->shortcuts as $link): ?>
			      <li><a href="<?php echo $link['url']; ?>"><img src="<?php echo $link['icon']; ?>" alt="<?php echo $link['title']; ?>"  title="<?php echo $link['title']; ?>" class="shortcut_icon"/><!-- <?php echo $link['title']; ?> --></a></li>
		      <?php endforeach; ?>
	      </ul>
      </div>
      <?php endif; ?>
	<h2 class="page-title"><?php echo htmlspecialchars($this->page_title, ENT_COMPAT, "UTF-8"); ?></h2>
		<?php if ($this->course_id > 0): ?>
	    <div id="menutoggle">
                <a accesskey="n"><img src="" title="" alt="" /></a>
            </div>
		<?php endif; ?>

<a name="content"></a>
<?php global $msg; $msg->printAll(); ?>
