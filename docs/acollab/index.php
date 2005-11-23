<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

if(!$_SESSION['valid_user']){
	header('Location: ../logout.php');
	exit;
}
require(AT_INCLUDE_PATH.'header.inc.php');

?>
<p><a href="<?php echo $_config['ac_path']; ?>index.php"><?php echo  _AT('acollab_own_window'); ?></a></p>

<script language="javascript">
function check_location(){
<!--
	if(frames['0'].window.document.forms[0].login){
		location.replace("<?php echo $_base_href; ?>acollab/bounce.php");
	}
	if(frames['0'].window.document.forms[0].jump &&  !frames['0'].window.document.forms[0].p){
		location.replace('<?php echo $_base_href; ?>index.php');
	}
}
-->
</script>

<div align="center">
<iframe onload="check_location();" src ="<?php
if($_GET['p'] != ''){
	$page = urldecode($_GET['p']);
} else {
	$page = 'index.php';
}
	echo $_config['ac_path'] . $page; ?>" style="border:1px solid #788CB3; margin: 4px;" height="640" width="98%" id="acollab_frame" title="<?php echo _AT('acollab_frame'); ?>" name="acollab_frame">
</iframe>
</div>

<?php
require(AT_INCLUDE_PATH.'footer.inc.php');
?>