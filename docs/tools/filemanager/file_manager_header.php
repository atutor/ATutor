<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en">
<head>
	<title>ATutor File Manager</title>

	<link rel="stylesheet" href="<?php echo $_base_href.'/themes/'.$_SESSION['prefs']['PREF_THEME']; ?>/styles.css" type="text/css" />
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
</head>

<body <?php
	if ($_SESSION['done']) {
		echo 'onLoad="parent.window.close();"';
	}
?> 