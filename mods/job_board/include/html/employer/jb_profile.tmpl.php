<div class="input-form">
	<form action="" method="post">
		<div class="row">
			<label for="jb_employer_name"><?php echo _AT('name'); ?></label>
			<input type="text" name="jb_employer_name" id="jb_employer_name" value="<?php echo htmlentities_utf8($this->name); ?>"/>
		</div>

		<div class="row">
			<label for="jb_employer_password"><?php echo _AT('password'); ?></label>
			<input type="text" name="jb_employer_password" id="jb_employer_password"/>
		</div>

		<div class="row">
			<label for="jb_employer_password2"><?php echo _AT('password_again'); ?></label>
			<input type="text" name="jb_employer_password2" id="jb_employer_password2"/>
		</div>

		<div class="row">
			<label for="jb_employer_company"><?php echo _AT('company'); ?></label>
			<input type="text" name="jb_employer_company" id="jb_employer_company" value="<?php echo htmlentities_utf8($this->company); ?>"/>
		</div>

		<div class="row">
			<label for="jb_employer_email"><?php echo _AT('email'); ?></label>
			<input type="text" name="jb_employer_email" id="jb_employer_email" value="<?php echo htmlentities_utf8($this->email); ?>"/>
		</div>

		<div class="row">
			<label for="jb_employer_email2"><?php echo _AT('email_again'); ?></label>
			<input type="text" name="jb_employer_email2" id="jb_employer_email2"/>
		</div>

		<div class="row">
			<label for="jb_employer_website"><?php echo _AT('jb_company_url'); ?></label>
			<input type="text" name="jb_employer_website" id="jb_employer_website" value="<?php echo htmlentities_utf8($this->website); ?>"/>
		</div>

		<div class="row">
			<input class="button" type="submit" name="submit" value="<?php echo _AT('submit'); ?>" />
		</div>
	</form>
</div>