<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
$_custom_css = $_base_path . 'mods/mediawiki/module.css'; // use a custom stylesheet
admin_authenticate(AT_ADMIN_PRIV_MEDIAWIKI);

$_POST['mw-url'] = $addslashes($_POST['mw-url']);

if($_POST['submit']){
	foreach($_POST as $key=>$mw_config)
	if($key != "submit"){
	$sql="REPLACE INTO ".TABLE_PREFIX."config SET name='$key', value='".$mw_config."'";
	if($result= mysql_query($sql, $db)){
		$msg->addFeedback("MW_CONFIG_SAVED");
		$_config['mw-url'] = $_POST['mw-url'];

	}else{
		$msg->addError("WP_CONFIG_FAIL");
	}

	}
}

require (AT_INCLUDE_PATH.'header.inc.php');

?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<label for="mw-url"><?php echo _AT('mediawiki_login_url'); ?></label>
<input type="text" name="mw-url" id="mw-url" value="<?php if($_config['mw-url']){echo $_config['mw-url'];}else{ echo 'http://';} ?>" size="60" /><br />

<input type="submit" name="submit" value="<?php echo _AT('mediawiki_save'); ?>">
</form>


<?php
if($_config['mw-url']){?>
	<br /><br /><a href="<?php echo $_config['mw-url']; ?>index.php?title=Special:UserLogin&returnto=Special:UserLogin" target="toolframe"><?php echo _AT('mediawiki_login'); ?></a>
	<iframe name="toolframe" src="<?php echo $_config['mw-url']; ?>" width="95%" height="450" id="frame_set">
	<p><?php echo _AT('mediawiki_no_iframes',$_config['mw-url']); ?> ?></p>
	</iframe>
<?php }else{ ?>
	<p><?php echo _AT('mediawiki_do_setup'); ?> ?></p>

<?php } ?>


<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>