<?php
ob_start(); 
?>

hello world

<?php
$savant->assign('dropdown_contents', ob_get_contents());
ob_end_clean();

$savant->assign('title', _AT('hello_world'));
$savant->display('include/box.tmpl.php');

?>