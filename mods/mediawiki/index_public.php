<?php
// This file is disabled by default. To enable it, modify the "public pages" 
// section in the module.php file for this module


$_user_location	= 'public';

define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
$_custom_css = $_base_path . 'mods/mediawiki/module.css'; // use a custom stylesheet
require (AT_INCLUDE_PATH.'header.inc.php');


if(!$_COOKIE['mysql_active_user']){

echo '<a href="'.$_config['mw-url'].'index.php?title=Special:UserLogin&returnto=Special:UserLogin" target="toolframe">'._AT('mediawiki_login').'</a>';

}
?>
<iframe src="<?php echo $_config['mw-url']; ?><?php if($p){ echo "index.php/".$p;} ?>" width="95%" height="500" style="border:none;" name="toolframe">
<p><?php echo _AT('mediawiki_no_iframe',$_config['mw-url']); ?>"><?php echo _AT('mediawiki_login'); ?></a></p>
</iframe>



<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>