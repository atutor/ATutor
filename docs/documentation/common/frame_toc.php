<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Documentation TOC</title>
	<base target="body" />
<style>
body {
    font-family: Verdana,Arial,sans-serif;
	font-size: x-small;
	margin: 0px;
	padding: 0px;
	background: #f4f4f4;
	margin-left: -5px;
}

ul {
	list-style: none;
	padding-left: 0px;
	margin-left: -15px;
}
li {
	margin-left: 19pt;
	padding-top: 2px;
}
a {
	/* white-space: pre; */
	background-repeat: no-repeat;
	background-position: 0px 1px;
	padding-left: 12px;
	text-decoration: none;
}
a.tree {
	background-image: url('folder.gif');
}

a.leaf {
	background-image: url('paper.gif');
}
a:link, a:visited {
	color: #006699;
}
a:hover {
	color: #66AECC;
}
</style>
<script>
//	var currentPage = '';

function highlight(page) {
	if (page == false) {
		if (parent.header.currentPage) {
			var toc = parent.toc.document.getElementById(parent.header.currentPage);
			toc.style.color = 'blue';
			toc.style.fontWeight = 'bold';
		}
	} else {
		if (parent.header.currentPage) {
			var toc = parent.toc.document.getElementById(parent.header.currentPage);
			toc.style.color = '';
			toc.style.fontWeight = '';
		}
	
		var toc = parent.toc.document.getElementById(page);
		toc.style.color = 'blue';
		toc.style.fontWeight = 'bold';
		parent.header.currentPage = page;
	}
}
</script>
</head>
<body onload="highlight(false);">
<?php if (isset($_GET['admin'])): ?>
<ul>
	<li><a href="../admin/0.0.introduction.php" class="leaf" id="0.0.introduction.php">Introduction</a></li>

	<li><a href="../admin/1.0.installation.php" class="tree" id="1.0.installation.php">Installation</a>
		<ul>
			<li><a href="../admin/1.1.requirements_recommendations.php" class="leaf" id="1.1.requirements_recommendations.php">Requirements &amp; Recommendations</a></li>
			<li><a href="../admin/1.2.new_installation.php" class="leaf" id="1.2.new_installation.php">New Installation</a></li>
			<li><a href="../admin/1.3.upgrading.php" class="leaf" id="1.3.upgrading.php">Upgrading an Installation</a></li>
		</ul>
	</li>
		<li><a href="../admin/2.0.configuration.php" class="tree" id="2.0.configuration.php">Configuration</a>
		<ul>
			<li><a href="../admin/2.1.my_account.php" class="leaf" id="2.1.my_account.php">My Account</a></li>
			<li><a href="../admin/2.2.system_preferences.php" class="leaf" id="2.2.system_preferences.php">System Preferences</a></li>
			<li><a href="../admin/2.3.languages.php" class="tree" id="2.3.languages.php">Languages</a>
				<ul>
					<li><a href="../admin/2.3.1.importing_languages.php" class="leaf" id="2.3.1.importing_languages.php">Importing Languages</a></li>
					<li><a href="../admin/2.3.2.managing_existing_languages.php" class="leaf" id="2.3.2.managing_existing_languages.php">Managing Existing Languages</a></li>
					<li><a href="../admin/2.3.3.translating_atutor.php" class="leaf" id="2.3.3.translating_atutor.php">Translating ATutor</a></li>
				</ul>
				</li>
			<li><a href="../admin/2.4.themes.php" class="tree" id="2.4.themes.php">Themes</a>
				<ul>
					<li><a href="../admin/2.4.1.importing_themes.php" class="leaf" id="2.4.1.importing_themes.php">Importing Themes</a></li>
					<li><a href="../admin/2.4.2.managing_existing_themes.php" class="leaf" id="2.4.2.managing_existing_themes.php">Managing Existing Themes</a></li>
					<li><a href="../admin/2.4.3.creating_themes.php" class="leaf" id="2.4.3.creating_themes.php">Creating Themes</a></li>
				</ul>
			</li>
			<li><a href="../admin/2.5.error_logging.php" class="leaf" id="2.5.error_logging.php">Error Logging</a></li>
		</ul>
	</li>

	<li><a href="../admin/3.0.users.php" class="tree" id="3.0.users.php">Users</a>
		<ul>
			<li><a href="../admin/3.1.instructor_requests.php" class="leaf" id="3.1.instructor_requests.php">Instructor Requests</a></li>
			<li><a href="../admin/3.2.master_student_list.php" class="tree" id="3.2.master_student_list.php">Master Student List</a>
				<ul>
					<li><a href="../admin/3.2.1.importing_student_ids.php" class="leaf" id="3.2.1.importing_student_ids.php">Importing Student IDs</a></li>
				</ul>
				</li>
			<li><a href="../admin/3.3.email_users.php" class="leaf" id="3.3.email_users.php">Email Users</a></li>
			<li><a href="../admin/3.4.administrators.php" class="tree" id="3.4.administrators.php">Administrators</a>
				<ul>
					<li><a href="../admin/3.4.1.administrator_activity_log.php" class="leaf" id="3.4.1.administrator_activity_log.php">Administrator Activity Log</a></li>
				</ul>
			</li>
		</ul>
	</li>

	<li><a href="../admin/4.0.courses.php" class="tree" id="4.0.courses.php">Courses</a>
		<ul>
			<li><a href="../admin/4.1.backups.php" class="tree" id="4.1.backups.php">Backups</a>
				<ul>
					<li><a href="../admin/4.1.1.creating_backups.php" class="leaf" id="4.1.1.creating_backups.php">Creating Backups</a></li>
					<li><a href="../admin/4.1.2.restoring_backups.php" class="leaf" id="4.1.2.restoring_backups.php">Restoring Backups</a></li>
				</ul>
			</li>
			<li><a href="../admin/4.2.forums.php" class="leaf" id="4.2.forums.php">Forums</a></li>
			<li><a href="../admin/4.3.categories.php" class="leaf" id="4.3.categories.php">Categories</a></li>
		</ul>
	</li>

	<li><a href="../admin/5.troubleshooting.php" class="leaf" id="5.troubleshooting.php">Troubleshooting</a></li>
</ul>

<?php else: ?>

<ul>
	<li><a href="../instructor/0.0.introduction.php" class="leaf" id="0.0.introduction.php">Introduction</a></li>

	<li><a href="../instructor/1.0.announcements.php" class="leaf" id="1.0.announcements.php">Announcements</a></li>

	<li><a href="../instructor/2.0.backups.php" class="tree" id="2.0.backups.php">Backups</a>
		<ul>
			<li><a href="../instructor/2.1.creating_restoring.php" class="leaf" id="2.1.creating_restoring.php">Creating &amp; Restoring Backups</a></li>
			<li><a href="../instructor/2.2.downloading_uploading.php" class="leaf" id="2.2.downloading_uploading.php">Downloading &amp; Uploading Backups</a></li>
			<li><a href="../instructor/2.3.editing_deleting.php" class="leaf" id="2.3.editing_deleting.php">Editing &amp; Deleting Backups</a></li>
		</ul>
	</li>

	<li><a href="../instructor/3.0.chat.php" class="leaf" id="3.0.chat.php">Chat</a></li>

	<li><a href="../instructor/4.0.content.php" class="tree" id="4.0.content.php">Content</a>
		<ul>
			<li><a href="../instructor/4.1.creating_editing_content.php" class="leaf" id="4.1.adding_content.php">Creating &amp; Editing Content</a></li>
			<li><a href="../instructor/4.2.content_packages.php" class="leaf" id="4.2.content_packages.php">Content Packages</a></li>
			<li><a href="../instructor/4.3.content_usage.php" class="leaf" id="4.3.content_usage.php">Content Usage</a></li>
			<li><a href="../instructor/4.4.tile_repository.php" class="leaf" id="4.4.tile_repository.php">TILE Repository</a></li>
		</ul>
	</li>

	<li><a href="../instructor/5.0.course_email.php" class="leaf" id="5.0.course_email.php">Course email</a></li>

	<li><a href="../instructor/6.0.enrollment.php" class="tree" id="6.0.enrollment.php">Enrollment</a>
		<ul>
			<li><a href="../instructor/6.1.rols_privileges.php" class="leaf" id="6.1.rols_privileges.php">Roles &amp; Privileges</a></li>
			<li><a href="../instructor/6.2.alumni.php" class="leaf" id="6.2.alumni.php">Alumni</a></li>
			<li><a href="../instructor/6.3.groups.php" class="leaf" id="6.3.groups.php">Groups</a></li>
			<li><a href="../instructor/6.4.course_list.php" class="leaf" id="6.4.course_list.php">Course Lists</a></li>
		</ul>
	</li>

	<li><a href="../instructor/7.0.file_manager.php" class="tree" id="7.0.file_manager.php">File Manager</a>
		<ul>
			<li><a href="../instructor/7.1.creating_folders.php" class="leaf" id="7.1.creating_folders.php">Creating Folders</a></li>
			<li><a href="../instructor/7.2.uploading_files.php" class="leaf" id="7.2.uploading_files.php">Uploading Files</a></li>
			<li><a href="../instructor/7.3.creating_new_files.php" class="leaf" id="7.3.creating_new_files.php">Creating New Files</a></li>
			<li><a href="../instructor/7.4.editing_files.php" class="leaf" id="7.4.editing_files.php">Editing Files</a></li>
			<li><a href="../instructor/7.5.previewing_files.php" class="leaf" id="7.5.previewing_files.php">Previewing Files</a></li>
			<li><a href="../instructor/7.6.deleting_files_folders.php" class="leaf" id="7.6.deleting_files_folders.php">Deleting Files &amp; Folders</a></li>
			<li><a href="../instructor/7.7.extracting_zip_archives.php" class="leaf" id="7.7.extracting_zip_archives.php">Extracting Zip Archives</a></li>
		</ul>
	</li>

	<li><a href="../instructor/8.0.forums.php" class="tree" id="8.0.forums.php">Forums</a>
		<ul>
			<li><a href="../instructor/8.1.creating_forums.php" class="leaf" id="8.1.creating_forums.php">Creating Forums</a></li>
			<li><a href="../instructor/8.2.editing_forums.php" class="leaf" id="8.2.editing_forums.php">Editing Forums</a></li>
			<li><a href="../instructor/8.3.shared_forums.php" class="leaf" id="8.3.shared_forums.php">Shared Forums</a></li>
		</ul>
	</li>

	<li><a href="../instructor/9.0.glossary.php" class="leaf" id="9.0.glossary.php">Glossary</a></li>

	<li><a href="../instructor/10.0.links.php" class="tree" id="10.0.links.php">Links</a>
		<ul>
			<li><a href="../instructor/10.1.link_categories.php" class="leaf" id="10.1.link_categories.php">Link Categories</a></li>
		</ul>
	</li>

	<li><a href="../instructor/11.0.polls.php" class="leaf" id="11.0.polls.php">Polls</a></li>

	<li><a href="../instructor/12.0.properties.php" class="tree" id="12.0.properties.php">Properties</a>
		<ul>
			<li><a href="../instructor/12.1.delete_course.php" class="leaf" id="12.1.delete_course.php">Delete Course</a></li>
		</ul>
	</li>

	<li><a href="../instructor/13.0.statistics.php" class="leaf" id="13.0.statistics.php">Statistics</a></li>

	<li><a href="../instructor/14.0.student_tools.php" class="tree" id="14.0.student_tools.php">Student Tools</a>
		<ul>
			<li><a href="../instructor/14.1.side_menu.php" class="leaf" id="14.1.side_menu.php">Side Menu</a></li>
		</ul>
	</li>

	<li><a href="../instructor/15.0.tests_surveys.php" class="tree" id="15.0.tests_survesy.php">Tests &amp; Surveys</a>
		<ul>
			<li><a href="../instructor/15.1.creating_tests_surveys.php" class="leaf" id="15.1.creating_tests_surveys.php">Creating Tests &amp; Surveys</a></li>
			<li><a href="../instructor/15.2.question_database.php" class="tree" id="15.2.question_database.php">Question Database</a>
				<ul>
					<li><a href="../instructor/15.2.1.creating_questions.php" class="leaf" id="15.2.1.creating_questions.php">Creating Questions</a></li>
				</ul>
			</li>
			<li><a href="../instructor/15.3.question_categories.php" class="leaf" id="15.3.question_categories.php">Question Categories</a></li>
			<li><a href="../instructor/15.4.managing_test_questions.php" class="leaf" id="15.4.managing_test_questions.php">Managing Test Questions</a></li>
			<li><a href="../instructor/15.5.student_submissions.php" class="leaf" id="15.5.student_submissions.php">Student Submissions</a></li>
		</ul>
	</li>
</ul>
<?php endif; ?>

</body>
</html>