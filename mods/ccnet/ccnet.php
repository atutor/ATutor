<?php
/*
This is the ATutor CCNet admin module page. It allows an admin user
to set or edit  the URL for the CCNet installation for ATutor, and
it includes the launcher, which opens CCNet in a new window

*/
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

if($_REQUEST['saved_ccnet_url'] == 1){
	if($_REQUEST['ccnet_url'] == ''){
			$msg->addError('CCNETURL_ADD_EMPTY');
	}else{
		$ccnet_url = addslashes(stripslashes($_REQUEST['ccnet_url']));
		$sql = "INSERT INTO ".TABLE_PREFIX."config VALUES('ccnet', '".$ccnet_url."')";
		if(!$result = mysql_query($sql)){
			$msg->addError('CCNETURL_ADD_FAILED');
		}else{
			$msg->addFeedback('CCNETURL_ADD_SAVED');
		}
	}
}

if($_REQUEST['edited_ccnet_url'] == 1){
	if($_REQUEST['ccnet_url'] == ''){
			$msg->addError('CCNETURL_ADD_EMPTY');
			//	$_POST['edit_ccnet_url'] = 1;
			
	}else{
		$ccnet_url = addslashes(stripslashes($_REQUEST['ccnet_url']));
		$sql = "UPDATE ".TABLE_PREFIX."config SET  value='".$ccnet_url."' WHERE name = 'ccnet'";
		if(!$result = mysql_query($sql)){
			$msg->addError('CCNETURL_ADD_FAILED');
		}else{
			$msg->addFeedback('CCNETURL_ADD_SAVED');
		}
	}
}

//////////
//Check to see if the url to CCNet exists in the db. if not
//then request it, otherwise display the launch page
$sql = 'SELECT * from '.TABLE_PREFIX.'config WHERE name="ccnet"';
$result = mysql_query($sql, $db);

while($row = mysql_fetch_array($result)){
	$ccnet_url_db = $row[1];
}

require (AT_INCLUDE_PATH.'header.inc.php');

if($ccnet_url_db == '' || $_POST['edit_ccnet_url']){ 

?>
		<div class="input-form">
		<div class="row">
			<p><?php echo _AT('ccnet_add_url'); ?>
		</p>
			<div class="row buttons">
			<form action="<?php $_SERVER['PHP_SELF']?>" method="post">
			<?php if($_POST['edit_ccnet_url']){ ?>
				<input type="hidden" name="edited_ccnet_url" value="1">
			<?php }else{ ?>
				<input type="hidden" name="saved_ccnet_url" value="1">
			<?php } ?>
			<?php if($_POST['edit_ccnet_url']){ ?>
				<input type="text" name="ccnet_url" value="<?php echo $ccnet_url_db; ?>" size="80" length="150" />
				<?php }else{ ?>
				<input type="text" name="ccnet_url" value="<?php echo $ccnet_url; ?>" size="80" length="150" />
			
			<?php } ?>	
			<input type="submit" value="<?php echo _AT('ccnet_save'); ?>" style="botton">
			</form>
			</div>
		</div>
		</div>

<?php }else{?>

		<div class="input-form">
		<div class="row">
			<p><?php echo _AT('ccnet_text');  ?>
		</p>
			<div class="row buttons">
			<form>
			<input type="submit" value="<?php echo _AT('ccnet_open'); ?>" onclick="window.open('<?php echo $ccnet_url_db; ?>','mywindow','width=800,height=600,scrollbars=yes, resizable=yes' ); return false;" style="botton">
			</form>
			</div>
		</div>
		</div>
		<div class="input-form">
		<div class="row">
			<p><?php echo _AT('ccnet_location'); ?></p>
			<p>	<strong><?php echo $ccnet_url_db; ?> </strong>									</p>
			<div class="row buttons">
			<form action="<?php  $_SERVER['PHP_SELF']; ?>" method="post">
			<input type="hidden" name="edit_ccnet_url" value="1">
			<input type="submit" value="<?php echo _AT('ccnet_edit'); ?>" style="botton">
			</form>
			</div>
		</div>
		</div>
<?php } ?>

<?php  require (AT_INCLUDE_PATH.'footer.inc.php'); ?>