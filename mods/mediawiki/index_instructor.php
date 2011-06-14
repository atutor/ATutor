<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_MEDIAWIKI);
require (AT_INCLUDE_PATH.'header.inc.php');


if(!$_COOKIE['mysql_active_user']){
	echo '<a href="'.$_config['mw-url'].'index.php?title=Special:UserLogin&returnto=Special:UserLogin" target="toolframe">'._AT('mediawiki_login').'</a>';
}
?>

<iframe src="<?php echo $_config['mw-url']; ?>" width="95%" height="450" style="border:none;" name="blog_frame">
<p><?php echo _AT('mediawiki_no_iframe',$_config['mw-url']); ?>"><?php echo _AT('mediawiki_login'); ?></a></p>
</iframe>


<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>