<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en">
<head>
	<title><?php echo _AT('upload_progress'); ?></title>
	<?php if ($_GET['frame']) { ?>
		<META HTTP-EQUIV="refresh" content="3;URL=prog.php?frame=1"> 
	<?php } ?>
	<link rel="stylesheet" href="<?php echo $_base_href.'/themes/'.$_SESSION['prefs']['PREF_THEME']; ?>/styles.css" type="text/css" />
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
</head>
<?php
	if ($myPrefs['refresh'] != 'manual') {
?>
	<script language="javascript" type="text/javascript">
	<!--
		setTimeout("reDisplay()", <?php echo $myPrefs['refresh'] * 1000; ?>);
		function reDisplay() {
			window.location.href = "<?php echo $_SERVER[PHP_SELF]; ?>";
		}
	//-->
	</script>
<?php
	} /* end if */
?>

<body <?php
	if ($_SESSION['done']) {
		echo 'onLoad="parent.window.close();"';
	}
?>>