<?php
/*
This is the ATutor admin plog module page. It allows an admin user
to set or edit  the URL for the plog installation for ATutor, and edit
the plog location URL.
*/
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

// switch the comments in the two lines to switch between LifeType 1.0.2 and 1.1
//require ('sync_plog11.php');
require ('sync_plog.php');


// Insert the initial pLog location in the ATutor config table
if($_REQUEST['saved_plog_url'] == 1){
	if($_REQUEST['plog_url'] == ''){
			$msg->addError('PLOG_URL_ADD_EMPTY');
	}else{
		$plog_url = addslashes(stripslashes($_REQUEST['plog_url']));
		$sql = "INSERT INTO ".TABLE_PREFIX."config VALUES('plog', '".$plog_url."')";
		if(!$result = mysql_query($sql)){
			$msg->addError('PLOG_URL_ADD_FAILED');
		}else{
			$msg->addFeedback('PLOG_URL_ADD_SAVED');
		}
	}
}

// Update the pLog location if it is being edited
if($_REQUEST['edited_plog_url'] == 1){
	if($_REQUEST['plog_url'] == ''){

			$msg->addError('PLOG_URL_ADD_EMPTY');
			//	$_POST['edit_plog_url'] = 1;
			
	}else{

		$plog_url = addslashes(stripslashes($_REQUEST['plog_url']));
		$sql = "UPDATE ".TABLE_PREFIX."config SET  value='".$plog_url."' WHERE name = 'plog'";
		if(!$result = mysql_query($sql)){

			$msg->addError('PLOG_URL_ADD_FAILED');

		}else{

			$msg->addFeedback('PLOG_URL_ADD_SAVED');
		}
	}
}



//Check to see if the url to plog exists in the db 
$sql = 'SELECT * from '.TABLE_PREFIX.'config WHERE name="plog"';
$result = mysql_query($sql, $db);

while($row = mysql_fetch_array($result)){
	$plog_url_db = $row[1];
}

require (AT_INCLUDE_PATH.'header.inc.php');

// Display the form the enter the initial pLog URL, or display the form to edit the location
if($plog_url_db == '' || $_POST['edit_plog_url']){ 

?>
		<div class="input-form">
		<div class="row">
			<p><?php echo _AT('plog_add_url'); ?>
		</p>
			<div class="row buttons">
			<form action="<?php $_SERVER['PHP_SELF']?>" method="post">

			<?php if($_POST['edit_plog_url']){ ?>
				<input type="hidden" name="edited_plog_url" value="1">
			<?php }else{ ?>
				<input type="hidden" name="saved_plog_url" value="1">
			<?php } ?>
			<?php if($_POST['edit_plog_url']){ ?>
				<input type="text" name="plog_url" value="<?php echo $plog_url_db; ?>" size="80" length="150" />
				<?php }else{ ?>
				<input type="text" name="plog_url" value="<?php echo $plog_url; ?>" size="80" length="150" />
			
			<?php } ?>	
			<input type="submit" value="<?php echo _AT('plog_save'); ?>" style="botton">
			</form>
			</div>
		</div>
		</div>

<?php }else{?>

		<div class="input-form">
		<div class="row">
			<p><?php echo _AT('plog_sync'); ?></p>
			<p>	<strong><?php echo $plog_sync; ?> </strong>									</p>
			<div class="row buttons">
			<form action="<?php  $_SERVER['PHP_SELF']; ?>" method="post">
			<input type="hidden" name="plog_sync" value="1">
			<input type="submit" value="<?php echo _AT('plog_sync_button'); ?>" style="botton">
			</form>
			</div>
		</div>
		</div>
		<div class="input-form">
		<div class="row">
			<p><?php echo _AT('plog_location'); ?></p>
			<p>	<strong><?php echo $plog_url_db; ?> </strong>									</p>
			<div class="row buttons">
			<form action="<?php  $_SERVER['PHP_SELF']; ?>" method="post">
			<input type="hidden" name="edit_plog_url" value="1">
			<input type="submit" value="<?php echo _AT('plog_edit'); ?>" style="botton">
			</form>
			</div>
		</div>
		</div>


		<div class="input-form">
		<div class="row">

<?php } 
// Display the pLog admin screen, or login screen
?>
	<iframe name="plog" id="plog" title="pLog" scrolling="yes" src="<?php echo $plog_url_db; ?>admin.php" height="800" width="100%" align="center" style="border:thin white solid; align:center;"></iframe>
<?php

 require (AT_INCLUDE_PATH.'footer.inc.php'); ?>