<?php 
/* start output buffering: */
global $savant, $_config;
ob_start(); ?>

	<script language="javascript" type="text/javascript"  src="http://www.userplane.com/chatlite/medallion/chatlite.cfm?DomainID=<?php  echo $_config['userplane']; ?>&initialRoom=<?php echo $_SESSION['course_tile']; ?>"></script><noscript>You must have JavaScript enabled to use <a href="http://www.userplane.com" title="Userplane" target="_blank">Userplane Chat</a></noscript>

<?php
$savant->assign('dropdown_contents', ob_get_contents());
ob_end_clean();

$savant->assign('title', _AT('userplane')); // the box title
$savant->display('include/box.tmpl.php');
?>