<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
$_custom_css = $_base_path . 'mods/userplane/module.css'; // use a custom stylesheet
require (AT_INCLUDE_PATH.'header.inc.php');
?>
	<div id="userplane"><p><?php echo  _AT('userplane_howto');?></p></div>
	<div id="userplane">
	<script language="javascript" type="text/javascript" src="http://www.userplane.com/chatlite/medallion/chatlite.cfm?DomainID=<?php  echo $_config['userplane'];?>&initialRoom=<?php echo $_SESSION['course_title']; ?>"></script><noscript>You must have JavaScript enabled to use <a href="http://www.userplane.com" title="Userplane" target="_blank">Userplane Chat</a></noscript>
	</div>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>