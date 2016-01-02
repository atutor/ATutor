<?php
global $savant, $_config, $stripslashes;
ob_start(); 
?>
<form action="<?php echo $_base_path; ?>mods/_standard/google_search/index.php" method="get" name="gsearchform">
<input type="hidden" name="l" value="<?php echo $_SESSION['lang']; ?>" />
<input type="hidden" name="search" value="1" />
<input type="text" name="q" class="formfield" size="20" value="" title="<?php echo _AT('enter_search_terms'); ?>"/><br /><br />
<input type="hidden" name="submit" value="<?php echo _AT('search'); ?>" />
<input type="submit" class="button" />
</form>

<?php
$savant->assign('dropdown_contents', ob_get_contents());
ob_end_clean();

$savant->assign('title', _AT('google_search'));
$savant->display('include/box.tmpl.php');

?>