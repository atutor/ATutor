<?php require('../common/body_header.inc.php'); $lm = '$LastChangedDate: 2006-06-29 11:21:18 -0400 (Thu, 29 Jun 2006) $'; ?>

<h2>Auto-Enroll at Registration</h2>
	<p>Administrators can use this tool to generate a unique URL for the ATutor registration screen, so that when new users follow the link and register, they are automatically enrolled in a specified number of courses. Once the URL is generated, make it available to perspective students so they can register, enroll, and login in one easy step. When students access the registration form using the link, the selected courses will be listed on the form, and when the registration is complete, those courses will be added to the student's My Start Page, and he or she will be logged in.</p>
	<p> Click on <strong>Create/Edit Auto Enrollment</strong> to create an auto-enroll URL</p>

	<dl>
		<dt>Title</dt>
		<dd>This should be a general reference that encompasses the grouping of courses. For example, if students will be enrolled in introductory Boilogy 101, Botany 101, and Zoology 101, the title might be "First Year Natural Science" .</dd>

		<dt>Courses to Enroll</dt>
		<dd>Select from the available courses on the system to have them added to the auto-enroll grouping of courses for the title described above. Once all the required courses have been choosen, press the <kdb>Save</kdb> button to generate the link </dd>
	</dl>

<?php require('../common/body_footer.inc.php'); ?>
