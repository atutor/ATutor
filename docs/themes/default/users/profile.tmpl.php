<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

require(AT_INCLUDE_PATH.'header.inc.php');
global $msg;
global $languageManager;

$msg->printAll();

?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">

<div class="input-form">

	<div class="row"><?php echo _AT('login_name'); ?><br />
	<?php echo $this->row['login'];?></div>

	<div class="row"><img src="images/asterisk.gif" alt="Required" /><label for="password"><?php   echo _AT('password'); ?></label><br /><input id="password" class="formfield" name="password" type="password"  size="15" maxlength="15" value="<?php echo stripslashes(htmlspecialchars($this->row['password'])); ?>" /><br /></div>

		<small class="spacer">&middot; <?php echo _AT('combination'); ?><br />
		&middot; <?php echo _AT('15_max_chars'); ?></small>

	<div class="row"><img src="images/asterisk.gif" alt="Required" /><label for="password2"><?php echo _AT('password_again'); ?></label><br /><input id="password2" class="formfield" name="password2" type="password" size="15" maxlength="15" value="<?php if ($_POST['submit']){ echo stripslashes(htmlspecialchars($_POST['password2'])); } else { echo stripslashes(htmlspecialchars($this->row['password'])); }?>" /></div>

	<div class="row"><img src="images/asterisk.gif" alt="Required" /><label for="email"><?php   echo _AT('email_address'); ?></label><br /><input id="email" class="formfield" name="email" type="text" size="30" maxlength="60"  value="<?php echo stripslashes(htmlspecialchars($this->row['email']));?>" /></div>


	<div class="row"><img src="images/asterisk.gif" alt="Required" /><label for="pri_langs"><?php echo _AT('language'); ?></label><br /><?php $languageManager->printDropdown($_SESSION['lang'], 'lang', 'pri_langs'); ?></div>

	<div class="row"><img src="images/asterisk.gif" alt="Required" /><label for="language"><?php echo _AT('status'); ?></label><br />
	<?php
		if ($this->row['status']) { 
			echo _AT('instructor'); 
		} else { 
			echo _AT('student'); 
			if (ALLOW_INSTRUCTOR_REQUESTS) {
				echo ' <br /><a href="users/request_instructor.php">'._AT('request_instructor_account').'</a>';
			} else {
				echo '<br /><small>'._AT('request_instructor_disabled').'</small>';
			}
		}
	?></div>

	<div class="row"><?php echo _AT('auto_login1'); ?><br />
	<?php
		if ( ($_COOKIE['ATLogin'] != '') && ($_COOKIE['ATPass'] != '') ) {
			echo _AT('auto_enabled').' - <a href="users/profile.php?auto=disable">'._AT('auto_disable').'</a>';
		} else {
			echo _AT('auto_disabled').' - <a href="users/profile.php?auto=enable">'._AT('auto_enable').'</a>';
		}
	?></div>

	<div class="row"><label for="first_name"><?php   echo _AT('first_name'); ?></label><br /><input id="first_name" class="formfield" name="first_name" type="text" value="<?php echo stripslashes(htmlspecialchars($this->row['first_name']));?>" /></div>

	<div class="row"><label for="last_name"><?php   echo _AT('last_name'); ?></label><br /><input id="last_name" class="formfield" name="last_name" type="text"  value="<?php echo stripslashes(htmlspecialchars($this->row['last_name']));?>" /></div>

	<div class="row"><?php echo _AT('date_of_birth'); ?><br />
		<?php
		$dob = explode('-',$this->row['dob']); 

		if (!isset($yr) && ($dob[0] > 0)) { $yr = $dob[0]; }
		if (!isset($mo) && ($dob[1] > 0)) { $mo = $dob[1]; }
		if (!isset($day) && ($dob[2] > 0)) { $dy = $dob[2]; }

		?>
		<!--input title="<?php echo _AT('day'); ?>" id="day" class="formfield" name="day" type="text" size="2" maxlength="2" value="<?php echo $day; ?>" />-<input title="<?php echo _AT('month'); ?>" id="month" class="formfield" name="month" type="text" size="2" maxlength="2" value="<?php echo $mo; ?>" />-<input title="<?php echo _AT('year'); ?>" id="year" class="formfield" name="year" type="text" size="4" maxlength="4" value="<?php echo $yr; ?>" /><small> <?php echo _AT('dd_mm_yyyy'); ?></small-->
		
		<select name="month">
			<option value="" ></option>
			<?php 
				$count = 1;
				foreach ($this->months as $month) {
					echo '<option value="'.$count.'"'; 
					if ($count == $mo) {
						echo 'selected="selected"';
					}
					echo '>'.$month.'</option>';
					$count++;
				}
			?>
		</select>

		 <select name="day">
			<option value=""></option>
			<?php 
				for ($day=1; $day<=31; $day++) {
					echo '<option value="'.$day.'"';
					if ($day == $dy) {
						echo 'selected="selected"';
					}
					echo '>'.$day.'</option>';
				}
			?>
		  </select>
		  
		<select name="year">
			<option value=""></option>
			<?php 
				for ($year=1960; $year<=1990; $year++) {
					echo '<option value="'.$year.'"';
					if ($year == $yr) {
						echo 'selected="selected"';
					}			
					echo '>'.$year.'</option>';
				}
			?>
		</select>
	</div>

	<div class="row"><?php   echo _AT('gender'); ?>
	<br /><?php
	if ($this->row['gender'] == 'm'){
		$m = ' checked="checked"';
	}
	if ($this->row['gender'] == 'f'){
		$f = ' checked="checked"';
	}

	?><input type="radio" name="gender" id="m" <?php echo $m;?> value="m" /><label for="m"><?php   echo _AT('male'); ?></label> <input type="radio" value="f" name="gender" id="f" <?php echo $f;?>  size="2" maxlength="2" /><label for="f"><?php   echo _AT('female'); ?></label> <input type="radio" value="ns" name="gender" id="ns" <?php if (($this->row['gender'] == 'ns') || ($this->row['gender'] == '')) { echo 'checked="checked"'; } ?> /><label for="ns"><?php echo _AT('not_specified'); ?></label>
	</div>

	<div class="row"><label for="address"><?php   echo _AT('street_address'); ?></label><br /><input id="address" class="formfield" name="address" size="40" type="text"   value="<?php echo stripslashes(htmlspecialchars($this->row['address']));?>" /></div>

	<div class="row"><label for="postal"><?php   echo _AT('postal_code'); ?></label><br /><input id="postal" class="formfield" name="postal" size="7" type="text"   value="<?php echo stripslashes(htmlspecialchars($this->row['postal']));?>" /></div>

	<div class="row"><label for="city"><?php   echo _AT('city'); ?></label><br /><input id="city" class="formfield" name="city" type="text" value="<?php echo stripslashes(htmlspecialchars($this->row['city'])); ?>" /></div>

	<div class="row"><label for="province"><?php   echo _AT('province'); ?></label><br /><input id="province" class="formfield" name="province" type="text"   value="<?php echo stripslashes(htmlspecialchars($this->row['province']));?>" /></div>

	<div class="row"><label for="country"><?php   echo _AT('country'); ?></label><br /><input id="country" class="formfield" name="country" type="text"   value="<?php echo stripslashes(htmlspecialchars($this->row['country']));?>" /></div>

	<div class="row"><label for="phone"><?php   echo _AT('phone'); ?></label><br /><input class="formfield" size="11" name="phone" id="phone" type="text" value="<?php echo stripslashes(htmlspecialchars($this->row['phone']));?>" /> <small>(Eg. 123-456-7890)</small></div>

	<div class="row"><label for="website"><?php   echo _AT('web_site'); ?></label><br /><input id="website" class="formfield" name="website" size="40" type="text" value="<?php echo stripslashes(htmlspecialchars($this->row['website']));?>" /></div>

	<div class="row buttons"><input type="submit" value=" <?php   echo _AT('save'); ?>" name="submit" accesskey="s" /> <input type="submit" name="cancel" value=" <?php echo  _AT('cancel'); ?>" /></div>

</div>

</form>
<?php
	require(AT_INCLUDE_PATH.'footer.inc.php');
?>