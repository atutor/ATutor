<?php require('../common/body_header.inc.php'); $lm = '$LastChangedDate$'; ?>

<h2>Preferences Wizard</h2>
<p>At any time (except while viewing the Preferences screen) the Preferences Wizard can be opened by clicking the wand icon next to your login name to the upper right (location may vary across themes). Any of the settings you can set through the Preferences panels described below, can also be set using the wizard.</p>

<h2>Preferences</h2>
<p>The following preferences allow a user to control how some features function, and how information is displayed.</p>
<h3>ATutor Settings</h3>
<dl>
    <dt>Theme</dt>
    <dd>Themes are used for changing the look and feel.</dd>

    <dt>Time Zone Offset</dt>
    <dd>Add or subtract hours from the times and dates displayed in ATutor, so they match your local time. Valid values range from -12 to 12. The positive sign is <strong>not</strong> required when adding hours. The minus sign is required when subtracting hours.</dd>

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
 
   <dt>Show Context Sensitive Handbook Pages</dt>
    <dd>Once you are familiar with ATutor you may wish to hide the links included with various tools to their associated handbook page. You can always access the handbook using the link in the  ATutor footer area.</dd>

	<dt>Content Editor</dt>
	<dd>This preference controls how content is entered. Choose between <em>Plain Text</em> for entering content text that will escape any HTML markup and will be formatted as entered; <em>HTML</em> for entering HTML content manually; and <em>HTML - Visual Editor</em> for entering HTML content using the visual (also known as a <acronym title="What You See Is What You Get">WYSIWYG</acronym>) editor which represents the content as it will be displayed. It is also possible to change the editor manually for each item.</dd>
</dl>
<h3>Text Settings</h3>
<p>These settings are used to control the overall colours and fonts displayed.</p>

<dl>
    <dt>Text</dt>
    <dd>Select from the various text formatting options to control how text and colours are displayed in ATutor.</dd>
</dl>
<h3>Content Settings</h3>
<p>These settings are used to control which versions of content are displayed, if for example the primary version is not accessible to you, or you prefer an alternate format. These settings will be ignored if the alternative versions you prefer are not available with the content you are viewing. Instructors and content authors should review <a href="../instructors/content_alternative.php">Alternate Content</a> for information on including alternate formats with ATutor content.</p>
<dl>
    <dt>Alternatives to Text</dt>
    <dd>If you are a person with a print related disability, or you prefer content in mutli-modal forms, select from these options to have alternate forms either replace text versions of the content, or have the alternate forms appended to the content.</dd>

    <dt>Alternatives to Audio</dt>
    <dd>If you are a person with an auditory disability, or if you prefer to read along with audio, or view visual alternatives to audio, select from these options to have alternatives replace or append where ever there is audio content.</dd>

    <dt>Alternatives to Visual</dt>
    <dd>If you are a person with a visual disability, of you prefer content without the usually larger, slow to load, visual information in content, select from these options to have alternatives to visual information either replace, or append to, visual information in the primary version of the content.</dd>
</dl>
<h3>Learner Supports</h3>
<p>These settings are used to control which  learning tools are available to you in a side menu block.</p>

<dl>
    <dt>Learner Supports</dt>
    <dd>Select from the various tools, the ones you would like available to you when in your ATutor courses.</dd>
</dl>
<h3>Navigations</h3>
<p>These settings are used to enable or disabled various ATutor navigation tools.</p>

<dl>
    <dt>Navigation</dt>
    <dd>Choose to show a <strong>Table of Contents</strong> at the top of each content page that can be used to navigate to sub sections within the page. Note that a Table of Contents is generated based on the headings (i.e. HTML H1 to H6), so it is important for content authors to structure their content properly with appropriate headings and sub-headings.</dd>
    <dd>Choose to show <strong>Next/Previous Navigation</strong>  links to aid navigation through content in the order pages are intended to be viewed, or to provide quick access back to the content page you left off on, when you return to viewing content in a current or future session.</dd>

    <dd>Choose to display <strong>Breadcrumb Navigation</strong> at the top of every page to provide up and down navigation through hierarchies of topics and sub-topics, or to keep a display of your current location within ATutor in view at all times.</dd>

</dl>
<?php require('../common/body_footer.inc.php'); ?>