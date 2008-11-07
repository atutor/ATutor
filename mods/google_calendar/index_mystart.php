<?php
$_user_location	= 'users';

define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
require (AT_INCLUDE_PATH.'header.inc.php');
require('get_google_user.php');

?>
<div>
<p><a href="<?php echo $_SERVER['PHP_SELF'] ?>#setup"><?php echo _AT('google_setup'); ?></a></p>
</div>

<div id="google_calendar" style="margin-left:2em;">

<?php

if($calendar_html != ''){
?>

	<iframe src="<?php echo $calendar_html; ?>" style="border: 0" width="95%" height="480" frameborder="0" scrolling="no"></iframe> 

<?php }else{ ?>

	<iframe src="http://www.google.com/calendar/embed?src=<?php echo $my_email_address; ?>&ctz=<?php echo $timezone; ?>" style="border: 0" width="95%" height="480" frameborder="0" scrolling="no"></iframe>

<?php }

?>

</div>
<a name="setup"></a>
<h3><?php echo _AT('google_setup'); ?></h3>
<div class="box">
<?php echo _AT('google_howto'); ?>
</div>

<h4><?php echo _AT('google_private_calendar_prefs'); ?></h4>
<?php 

require('google_prefs.php'); 

require (AT_INCLUDE_PATH.'footer.inc.php'); ?>