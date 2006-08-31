<?php
global $savant, $_config, $stripslashes;
ob_start(); 
?>

<?php if ($_config['gsearch']): ?>
	<form action="<?php echo $_base_path; ?>mods/_standard/google_search/index.php" method="get" name="gsearchform">
<?php else: ?>
	<form action="http://www.google.com/search" method="get" target="_new">
	<input type="hidden" name="l" value="<?php echo $_SESSION['lang']; ?>" />
<?php endif; ?>

<?php if (!$_config['gsearch']): ?>
	<?php echo _AT('google_new_window'); ?>
<?php endif; ?>

<input type="hidden" name="search" value="1" />

<input type="text" name="q" class="formfield" size="20" value="<?php echo $stripslashes(htmlspecialchars($_GET['search_query'])); ?>" /><br /><br />
<input type="submit" name="submit" value="<?php echo _AT('search'); ?>" class="button" />
</form>

<?php
$savant->assign('dropdown_contents', ob_get_contents());
ob_end_clean();

$savant->assign('title', _AT('google_search'));
$savant->display('include/box.tmpl.php');

?>