<script language="JavaScript" src="sha-1factory.js" type="text/javascript"></script>
<script type="text/javascript">
/* 
 * Encrypt login password with sha1
 */
function encrypt_password() {
	document.jb_profile_form.jb_employer_password_error.value = "";
	//if password is empty, then don't change the password.
	if (document.jb_profile_form.jb_employer_password.value==''){
		return;
	}

	err = verify_password(document.jb_profile_form.jb_employer_password.value, document.jb_profile_form.jb_employer_password2.value);
	if (err.length > 0)	{
		document.jb_profile_form.jb_employer_password_error.value = err;
	} else {
		document.jb_profile_form.jb_employer_password_hidden.value = hex_sha1(document.jb_profile_form.jb_employer_password.value);
		document.jb_profile_form.jb_employer_password.value = "";
		document.jb_profile_form.jb_employer_password2.value = "";
	}
}
</script>
<div class="input-form">
	<form action="" method="post" name="jb_profile_form">
		<div class="row">
			<label for="jb_employer_name"><?php echo _AT('jb_employer_name'); ?></label><br/>
			<input type="text" name="jb_employer_name" id="jb_employer_name" value="<?php echo htmlentities_utf8($this->name); ?>"/>
		</div>

		<div class="row">
			<label for="jb_employer_password"><?php echo _AT('password'); ?></label><br/>
			<input type="password" name="jb_employer_password" id="jb_employer_password"/><br/>
			<small>&middot; <?php echo _AT('combination'); ?><br />
				   &middot; <?php echo _AT('15_max_chars'); ?></small>
		</div>

		<div class="row">
			<label for="jb_employer_password2"><?php echo _AT('password_again'); ?></label><br/>
			<input type="password" name="jb_employer_password2" id="jb_employer_password2"/>
		</div>

		<div class="row">
			<label for="jb_employer_company"><?php echo _AT('company'); ?></label><br/>
			<input type="text" name="jb_employer_company" id="jb_employer_company" value="<?php echo htmlentities_utf8($this->company); ?>"/>
		</div>

		<div class="row">
			<label for="jb_employer_email"><?php echo _AT('email'); ?></label><br/>
			<input type="text" name="jb_employer_email" id="jb_employer_email" value="<?php echo htmlentities_utf8($this->email); ?>"/>
		</div>

		<div class="row">
			<label for="jb_employer_email2"><?php echo _AT('email_again'); ?></label><br/>
			<input type="text" name="jb_employer_email2" id="jb_employer_email2"/>
		</div>

		<div class="row">
			<label for="jb_employer_website"><?php echo _AT('jb_website'); ?></label><br/>
			<input type="text" name="jb_employer_website" id="jb_employer_website" value="<?php echo htmlentities_utf8($this->website); ?>"/>
		</div>

        <div class="row">
			<label for="jb_employer_description"><?php echo _AT('jb_company_description'); ?></label><br/>
			<textarea id="jb_employer_description" name="jb_employer_description" ><?php echo htmlentities_utf8($this->description); ?></textarea>
		</div>

		<div class="row">
    		<input type="hidden" name="jb_employer_password_hidden" value="" />
    		<input class="hidden" name="jb_employer_password_error" />
			<input class="button" type="submit" name="submit" value="<?php echo _AT('submit'); ?>" onclick="return encrypt_password();"/>
		</div>
	</form>
</div>
