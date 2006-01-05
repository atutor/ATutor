<?php
/*
This is the ATutor phpMyAdmin module page. It allows an admin user
to set or edit  the URL for the phpMyAdmin installation for ATutor, and
it includes the launcher, which opens phpMyAdmin in a new window

*/
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

if($_REQUEST['saved_phpmyadmin_url'] == 1){
	if($_REQUEST['phpmyadmin_url'] == ''){
			$msg->addError('PHPMYADMINURL_ADD_EMPTY');
	}else{
		$phpmyadmin_url = addslashes(stripslashes($_REQUEST['phpmyadmin_url']));
		$sql = "INSERT INTO ".TABLE_PREFIX."config VALUES ('phpmyadmin', '".$phpmyadmin_url."')";
		if(!$result = mysql_query($sql, $db)){
			$msg->addError('PHPMYADMINURL_ADD_FAILED');
		}else{
			$msg->addFeedback('PHPMYADMINURL_ADD_SAVED');
		}
	}
}

if($_REQUEST['edited_phpmyadmin_url'] == 1){
	if($_REQUEST['phpmyadmin_url'] == ''){
			$msg->addError('PHPMYADMINURL_ADD_EMPTY');
			//	$_POST['edit_phpmyadmin_url'] = 1;
			
	}else{
		$phpmyadmin_url = addslashes(stripslashes($_REQUEST['phpmyadmin_url']));
		$sql = "UPDATE ".TABLE_PREFIX."config SET  value='".$phpmyadmin_url."' WHERE name = 'phpmyadmin'";
		if(!$result = mysql_query($sql, $db)){
			$msg->addError('PHPMYADMINURL_ADD_FAILED');
		}else{
			$msg->addFeedback('PHPMYADMINURL_ADD_SAVED');
		}
	}
}

//////////
//Check to see if the url to phpMyAdmin exists in the db 
$sql = 'SELECT * from '.TABLE_PREFIX.'config WHERE name="phpmyadmin"';
$result = mysql_query($sql, $db);

while($row = mysql_fetch_array($result)){
	$phpmyadmin_url_db = $row[1];
}

require (AT_INCLUDE_PATH.'header.inc.php');
?>

<?php if($phpmyadmin_url_db == '' || $_POST['edit_phpmyadmin_url']): ?>

	<div class="input-form">
		<div class="row">
			<p><?php echo _AT('phpmyadmin_add_url'); ?>
		</p>
			<div class="row buttons">
			<form action="<?php $_SERVER['PHP_SELF']?>" method="post">
			<?php if($_POST['edit_phpmyadmin_url']){ ?>
				<input type="hidden" name="edited_phpmyadmin_url" value="1">
			<?php }else{ ?>
				<input type="hidden" name="saved_phpmyadmin_url" value="1">
			<?php } ?>
			<?php if($_POST['edit_phpmyadmin_url']){ ?>
				<input type="text" name="phpmyadmin_url" value="<?php echo $phpmyadmin_url_db; ?>" size="80" length="150" />
				<?php }else{ ?>
				<input type="text" name="phpmyadmin_url" value="<?php echo $phpmyadmin_url; ?>" size="80" length="150" />
			
			<?php } ?>	
			<input type="submit" value="<?php echo _AT('phpmyadmin_save'); ?>" style="botton">
			</form>
			</div>
		</div>
	</div>

<?php else: ?>
	<div class="input-form">
		<div class="row">
			<p><?php echo _AT('phpmyadmin_text');  ?>
		</p>
			<div class="row buttons">
			<form>
			<input type="submit" value="<?php echo _AT('phpmyadmin_open'); ?>" onclick="window.open('<?php echo $phpmyadmin_url_db; ?>','mywindow','width=800,height=600,scrollbars=yes, resizable=yes', 'false')" style="botton">
			</form>
			</div>
		</div>
		</div>
		<div class="input-form">
		<div class="row">
			<p><?php echo _AT('phpmyadmin_location'); ?></p>
			<p>	<strong><?php echo $phpmyadmin_url_db; ?> </strong>									</p>
			<div class="row buttons">
			<form action="<?php  $_SERVER['PHP_SELF']; ?>" method="post">
			<input type="hidden" name="edit_phpmyadmin_url" value="1">
			<input type="submit" value="<?php echo _AT('phpmyadmin_edit'); ?>" style="botton">
			</form>
			</div>
		</div>
		</div>
<?php endif; ?>

<?php  require (AT_INCLUDE_PATH.'footer.inc.php'); ?>