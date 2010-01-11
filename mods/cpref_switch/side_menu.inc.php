<?php 
global $savant;
/* start output buffering: */
ob_start(); ?>

Content preferences switcher

<?php
$savant->assign('dropdown_contents', ob_get_contents());
ob_end_clean();

$savant->assign('title', _AT('cpref_switch')); // the box title
$savant->display('include/box.tmpl.php');
?>