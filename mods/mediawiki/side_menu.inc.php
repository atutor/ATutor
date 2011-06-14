<?php 
/* start output buffering: */
// disabled by default in this version of the module

ob_start(); ?>

hello world

<?php
$savant->assign('dropdown_contents', ob_get_contents());
ob_end_clean();

$savant->assign('title', _AT('mediawiki')); // the box title
$savant->display('include/box.tmpl.php');
?>