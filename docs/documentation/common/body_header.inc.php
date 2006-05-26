<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

require(dirname(__FILE__) . '/vitals.inc.php');

$missing_lang = FALSE;
if ($req_lang != 'en') {
	$file_name = basename($_SERVER['PHP_SELF']);

	$lang_file = $req_lang . '/' . $file_name;

	if (file_exists($lang_file)) {
		header('Location: '.$lang_file.'?r');
		exit;
	} else if (!isset($_GET['r'])) {
		$missing_lang = TRUE;
		$lang = 'en';
	}
}

if (strpos(@ini_get('arg_separator.input'), ';') !== false) {
	define('SEP', ';');
} else {
	define('SEP', '&');
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="<?php if ($missing_lang) { echo 'en'; } else { echo $req_lang; } ?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php get_text('atutor_documentation'); ?></title>
	<link rel="stylesheet" href="<?php echo $rel_path; ?>common/styles.css" type="text/css" />
</head>

<body onload="doparent();">
<script type="text/javascript">
// <!--
function doparent() {
	if (parent.toc && parent.toc.highlight) parent.toc.highlight('id<?php echo $this_page; ?>');
}
// -->
</script>
<?php
require(dirname(__FILE__).'/../'.$section.'/pages.inc.php');
if (($req_lang != 'en') && (file_exists(dirname(__FILE__).'/../'.$section.'/'.$req_lang.'/pages.inc.php'))) {
	require(dirname(__FILE__).'/../'.$section.'/'.$req_lang.'/pages.inc.php');
}

while (current($_pages) !== FALSE) {
	if (key($_pages) == $this_page) {
		next($_pages);
		$next_page = key($_pages);
		break;
	}
	$previous_page = key($_pages);
	next($_pages);
}
?>
<div class="seq">
	<?php if (isset($previous_page)): ?>
		<?php get_text('previous_chapter'); ?>: <a href="<?php echo $rel_path; ?><?php echo $section; ?>/<?php echo $previous_page; ?>?<?php echo $req_lang; ?>" accesskey="," title="<?php echo $_pages[$previous_page]; ?> Alt+,"><?php echo $_pages[$previous_page]; ?></a><br />
	<?php endif; ?>

	<?php if (isset($next_page)): ?>
		<?php get_text('next_chapter'); ?>: <a href="<?php echo $rel_path; ?><?php echo $section; ?>/<?php echo $next_page; ?>?<?php echo $req_lang; ?>" accesskey="." title="<?php echo $_pages[$next_page]; ?> Alt+."><?php echo $_pages[$next_page]; ?></a>
	<?php endif; ?>
</div>

<?php if ($missing_lang): ?>
	<div style="margin: 20px auto; border: 1px solid #aaf; padding: 4px; text-align: center; background-color: #eef;">
		<?php get_text('page_not_translated'); ?>
	</div>
<?php endif; ?>

<?php debug($_SESSION); ?>