<?php
global $savant;
ob_start(); 
?>

<form action="<?php echo $_base_path; ?>mods/google_search/g_search.php" method="get" name="gsearchform">
<input type="hidden" name="search" value="1" />

<input type="text" name="search_query" class="formfield" size="20" value="<?php echo stripslashes(htmlspecialchars($_GET['search_query'])); ?>" /><br /><br />
<input type="submit" name="submit" value="<?php echo _AT('search'); ?>" class="button" />
</form>

<?php
$savant->assign('dropdown_contents', ob_get_contents());
ob_end_clean();

$savant->assign('title', _AT('google_search'));
$savant->display('include/box.tmpl.php');

?>