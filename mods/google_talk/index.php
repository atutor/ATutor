<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
require (AT_INCLUDE_PATH.'header.inc.php');

?>

<div id="google_talk">
<script src="http://gmodules.com/ig/ifr?url=http://www.google.com/ig/modules/googletalk.xml&amp;synd=open&amp;w=640&amp;h=451&amp;title=<?php echo SITE_NAME; ?>&amp;lang=en&amp;country=US&amp;border=%23ffffff%7C3px%2C1px+solid+%23999999&amp;output=js"></script>	
</div>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>