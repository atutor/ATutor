<?php 
/* start output buffering: */
ob_start(); ?>

hello world

<?php
$savant->assign('dropdown_contents', ob_get_contents());
ob_end_clean();

$savant->assign('title', _AT('hello_world')); // the box title
$savant->display('include/box.tmpl.php');
?>