<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $myLang->getCode(); ?>" lang="<?php echo $myLang->getCharacterSet(); ?>">
<head>
	<title>ATutor - <?php echo $_SESSION['course_title'];
	if ($cid != 0) {
		$myPath = $path;
		$num_path = count($myPath);
		for ($i =0; $i<$num_path; $i++) {
			echo ' - ';
			echo $myPath[$i]['title'];
		}
	} else if (is_array($_section) ) {
		$num_sections = count($_section);
		for($i = 0; $i < $num_sections; $i++) {
			echo ' - ';
			echo $_section[$i][0];
		}
	}
	?></title>
	<base href="<?php echo AT_BASE_HREF; ?>" />
	<link rel="stylesheet" href="<?php echo $_base_path; ?>themes/<?php echo $_SESSION['prefs']['PREF_THEME']; ?>/styles.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $_base_path.'themes/'.$_SESSION['prefs']['PREF_THEME']; ?>/forms.css" type="text/css" />
	<?php
		
		if ($myLang->isRTL()) {
			echo '<link rel="stylesheet" href="'.$_base_path.'rtl.css" type="text/css" />'."\n";
		}
	?>
	<meta http-equiv="Content-Type" content="text/html; <?php echo $myLang->getCharacterSet(); ?>" />
</head>
<body bgcolor="#FFFFFF">
