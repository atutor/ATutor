<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
if (!defined('AT_INCLUDE_PATH')) { exit; }

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="<?php echo $this->tmpl_lang; ?>">
<head>
	<title><?php echo $this->tmpl_title; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $this->tmpl_charset; ?>" />
	<meta name="Generator" content="ATutor - Copyright 2004 by http://atutor.ca" />
	<base href="<?php echo $this->tmpl_content_base_href; ?>" />
	<link rel="shortcut icon" href="<?php echo $this->tmpl_base_path; ?>favicon.ico" type="image/x-icon" />
	<link rel="stylesheet" href="<?php echo $this->tmpl_base_path; ?>print.css" type="text/css" media="print" />
	<link rel="stylesheet" href="<?php echo $this->tmpl_base_path.'themes/'.$this->tmpl_theme; ?>/styles.css" type="text/css" />
	<?php echo $this->tmpl_rtl_css; ?>
	<style type="text/css"><?php echo $this->tmpl_banner_style; ?></style>
</head>
<body <?php echo $this->tmpl_onload; ?>><div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
<script language="JavaScript" src="<?php echo $this->tmpl_base_path; ?>overlib.js" type="text/javascript"><!-- overLIB (c) Erik Bosrup --></script>

<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" id="maintable" summary="">
<tr>
	<td>

<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" summary="">

<tr class="header-bg">
	<td nowrap="nowrap" valign="top">
		<table border="0" cellpadding="0" cellspacing="0" summary="" width="100%">
			<tr>
				<td align="left" valign="bottom"><?php echo $this->tmpl_bypass_links; ?>
					<table>
						<tr><td valign="bottom">
							<?php if (HEADER_LOGO): ?>
								&nbsp;<img src="<?php echo $this->tmpl_base_path.HEADER_LOGO; ?>" border="0" alt="<?php echo SITE_NAME; ?>" />&nbsp;
							<?php endif; ?>
						</td>
						<td valign="middle">						
							<strong><?php echo stripslashes(SITE_NAME);
							if ($this->tmpl_section !='') { echo ' - '.$this->tmpl_section; } 
							?></strong>
						</td>
						</tr>
					</table>
				</td>
				<td valign="bottom" align="right">
					<small>
					<?php echo $this->tmpl_current_date; ?>&nbsp;<br />
					<?php echo _AT('logged_in_as'); ?>: <?php echo $this->tmpl_user_name; ?>&nbsp; / <?php echo $this->tmpl_log_link; ?></small>&nbsp;<br />
				</td>
			</tr>	
		</table>
	</td>
</tr>
<?php if ($this->tmpl_user_nav): ?>
<!-- admin navigation -->
	<tr>
		<td><a name="navigation"></a>
		<table border="0" cellspacing="0" cellpadding="0">
			<tr>			
				<td class="course-nav-between">&nbsp;</td>
				<?php foreach ($this->tmpl_user_nav as $page => $link): ?>
					<!-- regular menu item -->					
					<?php if ($this->tmpl_page == $page): ?>
						<td valign="top" nowrap="nowrap" class="admin-nav-item selected">&nbsp; 
					<?php else: ?>
						<td valign="top" nowrap="nowrap" class="admin-nav-item">&nbsp;				
					<?php endif; ?>
						<a href="<?php echo $link['url']; ?>" <?php echo $link['attribs']; ?> title="<?php echo $link['title']; ?>" ><?php echo $link['name'] ?></a>
					
					&nbsp;</td>
					<td class="course-nav-between">&nbsp;</td>
					<!-- end regular menu item -->					
				<?php endforeach; ?>
				<td align="right" class="course-nav-between" width="100%">&nbsp;</td>
			</tr>
		</table>
		</td>
	</tr>
<?php endif; ?>
<!-- end admin navigation -->

</table>
</td></tr>
<tr>
	<td colspan="2" height="100%" valign="top"><a name="content"></a>