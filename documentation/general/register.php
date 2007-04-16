<?php require('../common/body_header.inc.php'); $lm = '$LastChangedDate$'; ?>

<h2>Register</h2>

<p>In order for a user to login to the ATutor system, a unique system account needs to be created.  Use the <em>Register</em> link in the main navigation to access the registration form. Enter all the required fields, and optional data if desired (including the option to keep your email private or not), then use the <code>Submit</code> button. If email-confirmation has been enabled by the system administrator, a message will be sent to the email address registered, containing a link that must be followed to confirm the new account. Once this has been done, the login name or email address, and the password entered during registration can now be used on the <a href="login.php">Login</a> screen.</p>

<p>Note that if a system administrator has specified users to be checked against a Master List of allowed Student IDs and PINs (for example), this information must also be entered during registration.</p>

<?php require('../common/body_footer.inc.php'); ?>