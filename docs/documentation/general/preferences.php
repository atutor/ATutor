<?php require('../common/body_header.inc.php'); $lm = '$LastChangedDate$'; ?>

<h2>Preferences</h2>
<p>The following preferences allow a user to control how some features function, and how information is displayed.</p>

<dl>
    <dt>Theme</dt>
    <dd>Themes are used for changing the look and feel.</dd>

    <dt>Inbox Notification</dt>
    <dd>If enabled, an email notification message will be sent each time an Inbox message is received.</dd>

    <dt>Topic Numbering</dt>
    <dd>If enabled, content topics will be numbered.</dd>

    <dt>Direct Jump</dt>
    <dd>If enabled, using the Jump feature will redirect to the selected course and load the same section that was being viewed in the previous course (instead of the usual course Home page).</dd>

    <dt>Auto-Login</dt>
    <dd>If enabled, users are automatically logged in when they open ATutor. You should only enable this if you are accessing ATutor from a private computer, otherwise others will be able to login with your account information.</dd>

    <dt>Form Focus On Page Load</dt>
    <dd>If enabled, the cursor will be placed at the first field of the form when a page loads.</dd>

	<dt>Content Editor</dt>
	<dd>This preference controls how content is entered. Choose between <em>Plain Text</em> for entering content text that will escape any HTML markup and will be formatted as entered; <em>HTML</em> for entering HTML content manually; and <em>HTML - Visual Editor</em> for entering HTML content using the visual (also known as a <acronym title="What You See Is What You Get">WYSIWYG</acronym>) editor which represents the content as it will be displayed. It is also possible to change the editor manually for each item.</dd>
</dl>


<?php require('../common/body_footer.inc.php'); ?>