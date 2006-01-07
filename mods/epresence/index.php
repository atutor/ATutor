<?php
/*
This is the ATutor student ePresence module page. 

*/
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

require (AT_INCLUDE_PATH.'header.inc.php');

?>

<a href="<?php echo $_SERVER['PHP_SELF']; ?>" onclick="window.open('<?php echo $_config['epresence']; ?>','epresencewin','width=800,height=720,scrollbars=yes, resizable=yes'); return false"><?php echo  _AT('epresence_own_window'); ?></a> </li>

<iframe name="epresence" id="epresence" title="ePresence" scrolling="yes" src="<?php echo $_config['epresence']; ?>" height="800" width="90%" align="center" style="border:thin white solid; align:center;"></iframe>

<?php  require (AT_INCLUDE_PATH.'footer.inc.php'); ?>