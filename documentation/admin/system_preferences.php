<?php require('../common/body_header.inc.php'); $lm = '$LastChangedDate$'; ?>

	<h2>System Preferences</h2>
		<dl>
			<dt>Site Name</dt>
			<dd>The name of the course server's website. This name will appear at the top of all the public pages, in the web browser's title bar, and as the <em>From</em> name when sending non-personal emails.</dd>

			<dt>Home <acronym title="Uniform Resource Locator, a site's address">URL</acronym></dt>
			<dd>This will be the web address for the 'Home' link in the public area. Leave empty to remove this link.</dd>

			<dt>Default Language</dt>
			<dd>The default language to use if the client's browser settings cannot be detected. Must be one of the languages already installed. See the <a href="languages.php">Languages</a> section on installing and managing existing languages.</dd>

			<dt>Contact Email</dt>
			<dd>The reply address used for emails sent for instructor requests and other system emails.</dd>

			<dt>Time Zone Offset</dt>
    			<dd>Add or subtract hours from the times and dates displayed in ATutor, so they match the local time for the ATutor installation. Valid values range from -12 to 12. The positive sign is <strong>not</strong> required when adding hours. The minus sign is required when subtracting hours. Individual users may also modify their own Time Zone Offset setting, if their local time differs from that of the ATutor installation.</dd>
    			
			<dt>Session Timeout</dt>
    			<dd>To help prevent a person's ATutor session from being abused if they walk away without logging out, a timeout period can be set to automatically end a person's session after a certain period of inactivity. A reasonably short period of time is advised if users might be expected to use ATutor at a public workstation. Users are warned of the timeout 5 minutes in advance, then are automatically logged out if they do not acknowledge the warning.  Default: 20 minutes</dd>
			<dt>Maximum File Size</dt>
			<dd>Maximum allowable file size in Bytes that can be uploaded to the course's File Manager. This does not override the value set for <kbd>upload_max_filesize</kbd> in <kbd>php.ini</kbd>.</dd>

			<dt>Maximum Course Size</dt>
			<dd>Total maximum allowable course size in Bytes. This is the total amount of space a course's File Manager can use.</dd>

			<dt>Maximum Course Float</dt>
			<dd>How much a course can be over its <em>Maximum Course Size</em> limit while still allowing a file to upload or import. Makes the course limit actually be <em>Max Course Size</em> + <em>Max Course Float</em>. When <em>Max Course Float</em> is reached, no more uploads will be allowed for that course until files are deleted and the course's space usage falls under the Maximum Course Size.</dd>

			<dt>Maximum login attempts</dt>
			<dd>The amount of times the user can attempt logging in before the system freeze the account for an hour.  Enter 0 for infinite times.</dd>

			<dt>Display Name Format</dt>
			<dd>The Display Name Format option controls how non-administrator users' names appear. This option is available in ATutor 1.5.4+.</dd>

			<dt>Authenticate Against A Master Student List</dt>
			<dd>Whether or not to enable Master Student List authentication. If enabled, only new accounts that validate against the master list will be created. See the <a href="master_student_list.php">Master Student List</a> section for additional details on using this feature.</dd>

			<dt>Allow Self-Registration</dt>
			<dd>If enabled, users can self-register. Disable to remove registration functions</dd>
			<dt>Course Browser</dt>
			<dd>If enabled, students are able to browse through the available course in ATutor.</dd>
			<dt>Show Things Current on My Start Page</dt>
			<dd>If enabled, students and instructors are presented with a list of current changes in their course on My Start Page, such as current announcements, new forum posts, upcoming tests, new files for download, and much more.</dd>

			<dt>Allow Instructors to enroll users from the system registration list.</dt>
			<dd>If enabled, instructors are allowed to enroll users from the system registration list.</dd>
			
			<dt>Allow the use of CAPTCHA</dt>
			<dd>This requires the GD library installed (FreeType library is recommanded to have for better effect).  If enabled, users will be asked to enter an additional field for the alphanumeric sequence of the CAPTCHA image.  The CAPTCHA image can be mended in various ways depending on your need, please visit <a href="http://www.phpcaptcha.org/captcha-gallery/" target="_new">phpCaptcha</a> for more details.</dd>

			<dt>Allow Students to Unenroll</dt>
			<dd>If enabled, students can unenroll themselves from courses. If disabled, the Unenroll functions are removed.</dd>

			<dt>Require Email Confirmation Upon Registration</dt>
			<dd>If  email confirmation is enabled, before they can login, registrants must confirm their registration by replying to a message sent to the email address they registered with.  </dd>

			<dt>Allow Instructor Requests</dt>
			<dd>If enabled, students will be allowed to request that their account be upgraded to an instructor account. Instructor account requests must be approved by administrators using the <a href="instructor_requests.php">Instructor Requests</a> section. If disabled then the <em>Create Course</em> link used for requesting an instructor account will be removed and only the administrators will be able to create instructor accounts.</dd>
			
			<dt>Allow Instructors to Create Courses</dt>
			<dd>If the ATutor administrator should be responsible for creating new courses, disable this setting. Otherwise the default setting allows instructors to create their own courses. This feature may not be avaible on some systems. </dd>
			
			<dt>Instructor Request Email Notification</dt>
			<dd>If enabled, and if <em>Allow Instructor Requests</em> is enabled, then an email notification message will be sent to the <em>Contact Email</em> each time a new instructor account request is made. This does not affect whether or not instructor requests can be made, only whether or not a notification message is sent out each time.</dd>

			<dt>Auto Approve Instructor Requests</dt>
			<dd>If <em>Allow Instructor Requests</em> is enabled, then existing students requesting instructor accounts will be upgraded automatically, bypassing the approval process. Additionally, any newly created accounts will be created as instructors rather than as students. Useful for setting up a demo version of ATutor. </dd>

			<dt>Theme Specific Categories</dt>
			<dd>Theme specific categories allows for the association between themes and categories. Courses belonging to a specific category will always be presented using that category's associated theme. This option disables the personalised theme preference. Use the <a href="categories.php">Categories</a> section to create and manage course categories, and the <a href="themes.php">Themes</a> section to install and manage themes.</dd>

			<dt>User Contributed Handbook Notes</dt>
			<dd>If enabled will allow anyone viewing the Handbook to contribute notes. User contributed notes must then be approved by an administrator by logging in on the main Handbook page. This option is available in ATutor 1.5.1+.</dd>

			<dt>Illegal File Extensions</dt>
			<dd>A list of all the file types, by extension, that are not allowed to be stored on the server. Any file that is being imported or uploaded with an extension in the specified list will be ignored and not saved. The list must contain only the file extensions seperated by commas without the leading dot.</dd>

			<dt>Cache Directory</dt>
			<dd>Where cached data is stored. On a Windows machine the path should look like <kbd>C:\Windows\temp\</kbd>, while on Unix it should look like <kbd>/tmp/cache/</kbd>. On some Linux/Unix based systems, a shared memory device can also be used <kbd>/dev/shm/</kbd> if it is available.  Leave empty to disable caching.</dd>

			<dt>LaTex Server</dt>
			<dd>The URL of the mimeTex server.  A public ATutor mimeTex web service is currently available at 'http://www.atutor.ca/cgi/mimetex.cgi?'.  For production use, please do not use the public mimeTeX web service. Install mimeTeX on your own server instead.  Please read <a href="http://www.forkosh.com/mimetex.html" target="_new">http://www.forkosh.com/mimetex.html</a> for installation details.  </dd>

			<dt>Course Backups</dt>
			<dd>The maximum number of backups that can be stored per course. The stored backups do not count towards the course's <em>Max Course Size</em>.</dd>

			<dt>Number of Days to Keep Copied Sent Messages for</dt>
			<dd>All sent messages are copied to the sender's <em>Sent Messages</em> area. This option specifies the number of days old a copied message has to be before it is automatically deleted. The recipient's message is not affected.</dd>

			<dt>Check for ATutor Updates Automatically</dt>
			<dd>If enabled, ATutor will check the atutor.ca web site for updates whenever the administrator logs in. This option is available since ATutor 1.5.2.</dd>

			<dt>Maintain File Storage Version Control</dt>
			<dd>If enabled, every file revision in the File Storage area will be saved. If space is a concern, the administrator may wish to disable this feature.</dd>

			<dt>Enable Mail Queue</dt>
			<dd>The administrator may wish to set up a <a href="cron_setup.php">cron job</a> (automated event scheduler) for email. If enabled, and if the cron has been set up, system email will be sent out at a certain time instead of immediately. This can help speed up email capable features where a slower mail server is being used.</dd>

			<dt>Automatically Install New Language Packs</dt>
			<dd>If enabled, and if the <a href="cron_setup.php">cron job</a> (automated event scheduler) has been set up, new language packs published on atutor.ca will be imported automatically This option is available in ATutor 1.5.3.2+.</dd>

			<dt>Pretty URL</dt>
			<dd>If enabled, all the public accessible pages will automatically converts their URLs to "pretty url".  Pretty URL will remove the traditional URL query stirngs, and replace them with slashes (/).  This option is available in ATutor 1.6.1+.</dd>

			<dt>Course Directory Name</dt>
			<dd>If enabled, and only if the Pretty URL is enabled.  The course id in the pretty URL will be replaced by a custom course directory name.  This name can be setup individually and uniquely in the course property.  This option is available in ATutor 1.6.1+.</dd>

			<dt>Apache mod_rewrite</dt>
			<dd>Allows ATutor to use the Apache mod_rewrite function.  The mod_rewrite module must be loaded in the conf/httpd.conf file in order for this to work.  Please contact your server administrator for more details.  If enabled, the accessible pages will be shortened to the predefined rules.  Generally, go.php will be taken out.</dd>
		</dl>

<?php require('../common/body_footer.inc.php'); ?>