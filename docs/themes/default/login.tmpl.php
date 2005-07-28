<?php require(AT_INCLUDE_PATH.'header.inc.php'); ?>

<style type="text/css">
div#container {
	text-align: left;
	margin: 0px auto;
	padding: 0px;
	border:0;
	width: 90%;
}

div.column {
	border: 1px solid #e0e0e0;
	float: left;
	width: 30%;
	margin: 5px;
	padding: 0px;
	min-width: 160px;
}

div.column h3 {
	margin-left: 0px;
}

div.insidecol {
   min-height: 160px;
   height: 160px;
}
div[class] .insidecol {
   height: auto;
   padding: 5px;
}
</style>

<div id="container">
	<div class="column">
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
		<input type="hidden" name="form_login_action" value="true" />
		<input type="hidden" name="form_course_id" value="<?php echo $this->course_id; ?>" />

		<h3 style="border-bottom: 1px solid #e0e0e0; background-color:#fafafa; text-align:center; padding:5px;"><?php echo _AT('login'); ?></h3>
		<div class="insidecol"><p><?php echo _AT('login_text') ;?></p>
			<div class="input-form" style="text-align: center; border:0px; margin-bottom: 10px; width: 90%; line-height:180%">

				<?php if ($_GET['course']): ?>
					<div class="row">
						<h3><?php echo _AT('login'). ' ' . $this->title; ?></h3>
					</div>
				<?php endif;?>

				<label for="login"><?php echo _AT('login_name'); ?></label><br />
				<input type="text" name="form_login" id="login" /><br />

				<label for="pass"><?php echo _AT('password'); ?></label><br />
				<input type="password" class="formfield" name="form_password" id="pass" />
			</div>
		</div>
		<div style="background-color:#fafafa; text-align:center; padding:5px; border-top: 1px solid #e0e0e0; ">
			<input type="submit" name="submit" value="<?php echo _AT('login'); ?>" class="button" />
		</div>
		</form>
	</div>
		
	<div class="column">
		<form action="registration.php" method="get">
		<h3 style="border-bottom: 1px solid #e0e0e0; background-color:#fafafa; text-align:center; padding:5px;"><?php echo _AT('new_user');?></h3>
		<div class="insidecol"><p><?php echo _AT('registration_text'); ?></p>

		<?php if (defined('AT_EMAIL_CONFIRMATION') && AT_EMAIL_CONFIRMATION): ?>
			<p><?php echo _AT('confirm_account_text'); ?></p>
		<?php endif; ?>
		</div>

		<div style="background-color:#fafafa; text-align:center; padding:5px; border-top: 1px solid #e0e0e0;">
			<input type="submit" name="register" value="<?php echo _AT('register'); ?>" class="button" />
		</div>
		</form>
	</div>
		
	<div class="column">
		<form action="password_reminder.php" method="get">
		<h3 style="border-bottom: 1px solid #e0e0e0; background-color:#fafafa; text-align:center; padding:5px;"><?php echo _AT('password_reminder'); ?></h3>
		<div class="insidecol"><p><?php echo _AT('password_reminder_text'); ?></p></div>

		<div style="position: relative; margin-bottom: 0px; background-color:#fafafa; text-align:center; padding:5px; border-top: 1px solid #e0e0e0;">
			<input type="submit" name="forgot" value="<?php echo _AT('email_reminder'); ?>" class="button" />
		</div>
		</form>
	</div>
</div>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>