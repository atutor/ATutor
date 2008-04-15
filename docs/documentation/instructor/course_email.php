<?php require('../common/body_header.inc.php'); $lm = '$LastChangedDate$'; ?>

<h2>Course Email</h2>
	<p>Using the <em>Course Email</em> option, you can send an email to all assistants (students with privileges), enrolled, un-enrolled, and /or alumni students in your current course. A copy of the email is also sent to your registered email address.</p>
	<p>The following tags can be added to course emails to customize the message to the recipients. They are replaced with their personal information.</p>
<ul>
<li><strong>{AT_FNAME}</strong> Replaced with recipient's first name in the body or subject line.</li>
<li><strong>{AT_LNAME}</strong> Replaced with recipient's last name in the body or subject line.</li>
<li><strong>{AT_EMAIL}</strong> Replaced with recipient's email in the body.</li>
<li><strong>{AT_USER}</strong>  Replaced with recipient's login name in the body.</li>
</ul>

<?php require('../common/body_footer.inc.php'); ?>