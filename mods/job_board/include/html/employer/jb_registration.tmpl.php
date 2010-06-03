<script language="JavaScript" src="sha-1factory.js" type="text/javascript"></script>
<script type="text/javascript">
function encrypt_password()
{
	document.jb_registration_form.jb_registration_password_error.value = "";

	err = verify_password(document.jb_registration_form.jb_registration_password.value, document.jb_registration_form.jb_registration_password2.value);
	if (err.length > 0)
	{
		document.jb_registration_form.jb_registration_password_error.value = err;
	}
	else
	{
		document.jb_registration_form.jb_registration_password_hidden.value = hex_sha1(document.jb_registration_form.jb_registration_password.value);
		document.jb_registration_form.jb_registration_password.value = "";
		document.jb_registration_form.jb_registration_password2.value = "";
	}
}
</script>

<div class="input-form">
	<form method="post" action="" name="jb_registration_form">
		<div class="row">
			<span class="required" title="<?php echo _AT('required_field'); ?>">*</span>
			<label for="jb_registration_username"><?php echo _AT('jb_registration_username'); ?></label>
			<input type="text" id="jb_registration_username" name="jb_registration_username" value="<?php echo htmlentities_utf8($_POST['jb_registration_username']); ?>"/>
		</div>

		<div class="row">
			<span class="required" title="<?php echo _AT('required_field'); ?>">*</span>
			<label for="jb_registration_password"><?php echo _AT('password'); ?></label>
			<input type="password" id="jb_registration_password" name="jb_registration_password" />
		</div>

		<div class="row">
			<span class="required" title="<?php echo _AT('required_field'); ?>">*</span>
			<label for="jb_registration_password2"><?php echo _AT('password_again'); ?></label>
			<input type="password" id="jb_registration_password2" name="jb_registration_password2" />
		</div>

		<div class="row">
			<span class="required" title="<?php echo _AT('required_field'); ?>">*</span>
			<label for="jb_registration_employer_name"><?php echo _AT('jb_registration_employer_name'); ?></label>
			<input type="text" id="jb_registration_employer_name" name="jb_registration_employer_name" value="<?php echo htmlentities_utf8($_POST['jb_registration_employer_name']); ?>"/>
		</div>

		<div class="row">
			<span class="required" title="<?php echo _AT('required_field'); ?>">*</span>
			<label for="jb_registration_email"><?php echo _AT('jb_email'); ?></label>
			<input type="text" id="jb_registration_email" name="jb_registration_email" value="<?php echo htmlentities_utf8($_POST['jb_registration_email']); ?>"/>
		</div>
		
		<div class="row">
			<span class="required" title="<?php echo _AT('required_field'); ?>">*</span>
			<label for="jb_registration_company"><?php echo _AT('company'); ?></label>
			<input type="text" id="jb_registration_company" name="jb_registration_company" value="<?php echo htmlentities_utf8($_POST['jb_registration_company']); ?>"/>
		</div>

		<div class="row">
			<label for="jb_registration_website"><?php echo _AT('website'); ?></label>
			<input type="text" id="jb_registration_website" name="jb_registration_website" value="<?php echo htmlentities_utf8($_POST['jb_registration_website']); ?>"/>
		</div>
		
		<div class="row">
			<label for="jb_registration_description"><?php echo _AT('description'); ?></label>
			<textarea id="jb_registration_description" name="jb_registration_description" ><?php echo htmlentities_utf8($_POST['jb_registration_description']); ?></textarea>
		</div>

		<div class="row">
			<input class="hidden" name="jb_registration_password_hidden" />
			<input class="hidden" name="jb_registration_password_error" />
			<input class="button" type="submit" name="submit" value="<?php echo _AT('submit'); ?>" onclick="encrypt_password()" />
		</div>		
	</form>
</div>