<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="<?php echo $this->lang_code; ?>"> 
<head>
	<title><?php echo SITE_NAME; ?> : <?php echo $this->page_title; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $this->lang_charset; ?>" />
	<meta name="Generator" content="ATutor - Copyright 2005 by http://atutor.ca" />
	<base href="<?php echo $this->content_base_href; ?>" />
	<link rel="shortcut icon" href="<?php echo $this->base_path; ?>favicon.ico" type="image/x-icon" />
	<link rel="stylesheet" href="<?php echo $this->base_path.'themes/'.$this->theme; ?>/print.css" type="text/css" media="print" />
	<link rel="stylesheet" href="<?php echo $this->base_path.'themes/'.$this->theme; ?>/styles.css" type="text/css" />
	<!--[if IE]>
	  <link rel="stylesheet" href="<?php echo $this->base_path.'themes/'.$this->theme; ?>/ie_styles.css" type="text/css" />
	<![endif]-->
    <link rel="stylesheet" href="<?php echo $this->base_path.'themes/'.$this->theme; ?>/forms.css" type="text/css" />
    <link rel="stylesheet" href="<?php echo $this->base_path; ?>jscripts/jquery-ui-1.7.2/css/smoothness/jquery-ui-1.7.2.custom.css" type="text/css" />
<?php echo $this->rtl_css; ?>
<?php if (isset($this->course_id) && $system_courses[$this->course_id]['rss']): ?>
	<link rel="alternate" type="application/rss+xml" title="<?php echo SITE_NAME; ?> - RSS 2.0" href="<?php echo $this->base_href; ?>get_rss.php?<?php echo $this->course_id; ?>-2" />
	<link rel="alternate" type="application/rss+xml" title="<?php echo SITE_NAME; ?> - RSS 1.0" href="<?php echo $this->base_href; ?>get_rss.php?<?php echo $this->course_id; ?>-1" />
<?php endif; ?>
	<script src="<?php echo $this->base_path; ?>jscripts/infusion/InfusionAll.js" type="text/javascript"></script>
    <script src="<?php echo $this->base_path; ?>jscripts/infusion/jquery.autoHeight.js" type="text/javascript"></script>
    <script src="<?php echo $this->base_path; ?>jscripts/jquery-ui-1.7.2/development-bundle/ui/ui.resizable.js" type="text/javascript"></script>
	<script language="javascript" type="text/javascript">
	//<!--
	jQuery.noConflict();
	//-->
	</script>
    <script src="<?php echo $this->base_path; ?>jscripts/ATutor.js" type="text/javascript"></script>   
<?php echo $this->custom_css; ?>
    <style id="pref_style" type="text/css"></style> 
</head>

<body>
<div class="page_wrapper">
<div id="header">
	<a href="<?php echo htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES); ?>#content" accesskey="c">
	<img src="<?php echo $this->base_path; ?>images/clr.gif" height="1" width="1" border="0" alt="<?php echo _AT('goto_content'); ?> ALT+c" /></a>	
	<a href="<?php echo htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES); ?>#menu<?php echo $_REQUEST['cid']  ?>"  accesskey="m"><img src="<?php echo $this->base_path; ?>images/clr.gif" height="1" width="1" border="0" alt="<?php echo _AT('goto_menu'); ?> ALT+m" /></a>

	<?php if (!empty($this->icon)) { // if a course icon is available, display it here.  ?>
		<a href="<?php echo $this->base_path.url_rewrite('index.php'); ?>"><img src="<?php echo $this->icon; ?>" class="headicon" alt="<?php echo  _AT('home'); ?>" /></a>	
	<?php } ?>



	<?php
	// If there is a custom course banner in the file manager called banner.html, display it here
	@readfile(AT_CONTENT_DIR . $this->course_id.'/banner.txt'); 

	/*
	and example banner.html file might look like:
	<div style="width: 760px; height: 42px; background: white;"><img src="http://[mysite]/atutor15rc3/banners/kart-camb.jpg"></div>
	*/

	?>
	<!-- section title -->
	<?php if (isset($_SESSION['valid_user']) && $_SESSION['valid_user']): 
		echo '<div class="site-name">'.stripslashes(SITE_NAME).'</div>'; 
	else:
		echo '<br />';	
	endif; ?>
	<h1 id="section-title"><?php echo $this->section_title; ?>
		<?php if ((isset($this->course_id) && $this->course_id > 0) && ($_SESSION['enroll'] == AT_ENROLL_NO)) : ?> 
			- <small><a href="<?php echo $this->base_path; ?>enroll.php?course=<?php echo $this->course_id; ?>"><?php echo _AT('enroll_me'); ?></a></small>
		<?php endif; ?>
	</h1>
</div>
<div class="contentwrapper">
