<div class="social-wrapper">
<?php
include('lib/profile_menu.inc.php');
?>
<br />
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
<?php global $languageManager, $_config; ?>
<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('required_information'); ?></legend>
	<div class="row">
		<h3><?php echo _AT('required_information'); ?></h3>
	</div>

	<div class="row">
		<label for="login"><?php echo _AT('login_name'); ?></label><br />
				<span id="login"><?php echo stripslashes(htmlspecialchars($_POST['login'])); ?></span>
				<input name="member_id" type="hidden" value="<?php echo intval($_POST['member_id']); ?>" />
				<input name="login" type="hidden" value="<?php echo stripslashes(htmlspecialchars($_POST['login'])); ?>" />
	</div>
	<div class="row">
		<?php echo _AT('email_address'); ?><br />
		<?php echo stripslashes(htmlspecialchars($_POST['email'])); ?>
		<input type="checkbox" id="priv" name="private_email" value="1" <?php if ($_POST['private_email']) { echo 'checked="checked"'; } ?> /><label for="priv"><?php echo _AT('keep_email_private');?></label>
	</div>
	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="first_name"><?php echo _AT('first_name'); ?></label><br />
		<input id="first_name" name="first_name" type="text" value="<?php echo stripslashes(htmlspecialchars($_POST['first_name'])); ?>" />
	</div>

	<div class="row">
		<label for="second_name"><?php echo _AT('second_name'); ?></label><br />
		<input id="second_name" name="second_name" type="text" value="<?php echo stripslashes(htmlspecialchars($_POST['second_name'])); ?>" />
	</div>

	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="last_name"><?php echo _AT('last_name'); ?></label><br />
		<input id="last_name" name="last_name" type="text" value="<?php echo stripslashes(htmlspecialchars($_POST['last_name'])); ?>" />
	</div>
	
	<?php if (admin_authenticate(AT_ADMIN_PRIV_USERS, TRUE)): 
			if ($_POST['status'] == AT_STATUS_INSTRUCTOR) {
				$inst = ' checked="checked"';
			} else if ($_POST['status'] == AT_STATUS_STUDENT) {
				$stud = ' checked="checked"';
			}  else if ($_POST['status'] == AT_STATUS_DISABLED) {
				$disa = ' checked="checked"';
			} else {
				$uncon = ' checked="checked"';
			}?>
			<input type="hidden" name="id" value="<?php echo $_POST['member_id']; ?>" >
			<div class="row">
				<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><?php echo _AT('account_status'); ?><br />

				<input type="radio" name="status" value="0" id="disa" <?php echo $disa; ?> /><label for="disa"><?php echo _AT('disabled'); ?></label>
				<?php if (defined('AT_EMAIL_CONFIRMATION') && AT_EMAIL_CONFIRMATION): ?>
					<input type="radio" name="status" value="1" id="uncon" <?php echo $uncon; ?> /><label for="uncon"><?php echo _AT('unconfirmed'); ?></label>
				<?php endif; ?>

				<input type="radio" name="status" value="2" id="stud" <?php echo $stud; ?> /><label for="stud"><?php echo _AT('student'); ?></label>

				<input type="radio" name="status" value="3" id="inst" <?php echo $inst; ?> /><label for="inst"><?php echo _AT('instructor'); ?></label>

				<input type="hidden" name="old_status" value="<?php echo $_POST['old_status']; ?>" />
			</div>
	<?php endif; ?>
	</fieldset>
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('personal_information'); ?></legend>
	<div class="row">
		<h3><?php echo _AT('personal_information').' ('._AT('optional').')'; ?></h3>
	</div>

	<?php if (admin_authenticate(AT_ADMIN_PRIV_USERS, TRUE) && defined('AT_MASTER_LIST') && AT_MASTER_LIST): ?>
		<div class="row">
			<label for="student_id"><?php echo _AT('student_id'); ?></label><br />
				<input type="text" name="student_id" value="<?php echo $_POST['student_id']; ?>" size="20" /><br />
		</div>
		<div class="row">
			<label for="student_pin"><?php echo _AT('student_pin'); ?></label><br />
			<input id="student_pin" name="student_pin" type="password" size="15" maxlength="15" value="<?php echo stripslashes(htmlspecialchars($_POST['student_pin'])); ?>" /><br />
		</div>
	<?php endif; ?>

	<div class="row">
		<?php echo _AT('date_of_birth'); ?><br />
		<label for="year"><?php echo _AT('year'); ?>: </label><input id="year" class="formfield" name="year" type="text" size="4" maxlength="4" value="<?php echo $_POST['year']; ?>" />  <label for="month"><?php echo _AT('month'); ?>: </label><input id="month" class="formfield" name="month" type="text" size="2" maxlength="2" value="<?php echo $_POST['month']; ?>" /> <label for="day"><?php echo _AT('day'); ?>: </label><input id="day" class="formfield" name="day" type="text" size="2" maxlength="2" value="<?php echo $_POST['day']; ?>" />
	</div>

	<div class="row">
		<?php echo _AT('gender'); ?><br />
		<input type="radio" name="gender" id="m" value="m" <?php if ($_POST['gender'] == 'm') { echo 'checked="checked"'; } ?> /><label for="m"><?php echo _AT('male'); ?></label> <input type="radio" value="f" name="gender" id="f" <?php if ($_POST['gender'] == 'f') { echo 'checked="checked"'; } ?> /><label for="f"><?php echo _AT('female'); ?></label>  <input type="radio" value="n" name="gender" id="ns" <?php if (($_POST['gender'] == 'n') || ($_POST['gender'] == '')) { echo 'checked="checked"'; } ?> /><label for="ns"><?php echo _AT('not_specified'); ?></label>
	</div>

	<div class="row">
		<label for="address"><?php echo _AT('street_address'); ?></label><br />
		<input id="address" name="address" size="40" type="text" value="<?php echo stripslashes(htmlspecialchars($_POST['address'])); ?>" />
	</div>

	<div class="row">
		<label for="postal"><?php echo _AT('postal_code'); ?></label><br />
		<input id="postal" name="postal" size="7" type="text" value="<?php echo stripslashes(htmlspecialchars($_POST['postal'])); ?>" />
	</div>

	<div class="row">
		<label for="city"><?php echo _AT('city'); ?></label><br />
		<input id="city" name="city" type="text" value="<?php echo stripslashes(htmlspecialchars($_POST['city'])); ?>" />
	</div>

	<div class="row">
		<label for="province"><?php echo _AT('province'); ?></label><br />
		<input id="province" name="province" type="text" value="<?php echo stripslashes(htmlspecialchars($_POST['province'])); ?>" />
	</div>

	<div class="row">
		<label for="country"><?php echo _AT('country'); ?></label><br />
		<input id="country" name="country" type="text" value="<?php echo stripslashes(htmlspecialchars($_POST['country'])); ?>" />
	</div>

	<div class="row">
		<label for="phone"><?php echo _AT('phone'); ?></label><br />
		<input size="11" name="phone" type="text" value="<?php echo stripslashes(htmlspecialchars($_POST['phone'])); ?>" id="phone" />
	</div>

	<div class="row">
		<label for="website"><?php echo _AT('web_site'); ?></label><br />
		<input id="website" name="website" size="40" type="text" value="<?php if ($_POST['website'] == '') { echo 'http://'; } else { echo stripslashes(htmlspecialchars($_POST['website'])); } ?>" />
	</div>
	</fieldset>
	<div class="row buttons">
		<input type="submit" name="submit" value=" <?php echo _AT('save'); ?> " accesskey="s" />
		<input type="submit" name="cancel" value=" <?php echo _AT('cancel'); ?> " />
	</div>
</div>
</form>
<div style="clear:both;"></div>
</div>