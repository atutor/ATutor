<?php
	$parts = pathinfo($_SERVER['PHP_SELF']);
	$this_page = $parts['basename'];

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>ATutor 1.5 Administrator Documentation</title>
	<link rel="stylesheet" href="../common/styles.css" type="text/css" />
</head>

<body onload="doparent();">
<script type="text/javascript">
// <!--
function doparent() {
	if (parent.toc && parent.toc.highlight) {
		parent.toc.highlight('id<?php echo $this_page; ?>');
	}
}
// -->
</script>
<?php
if(strstr($parts['dirname'], "admin")){
	$_pages['0.0.introduction.php']                  = 'Introduction';
	$_pages['1.0.installation.php']                  = 'Installation';
	$_pages['1.1.requirements_recommendations.php']  = 'Requirements &amp; Recommendations';
	$_pages['1.2.new_installation.php']              = 'New Installation';
	$_pages['1.3.upgrading.php']                     = 'Upgrading an Installation';
	$_pages['2.0.configuration.php']                 = 'Configuration';
	$_pages['2.1.my_account.php']                    = 'My Account';
	$_pages['2.2.system_preferences.php']            = 'System Preferences';
	$_pages['2.3.languages.php']                     = 'Languages';
	$_pages['2.3.1.importing_languages.php']         = 'Importing Languages';
	$_pages['2.3.2.managing_existing_languages.php'] = 'Managing Existing Languages';
	$_pages['2.3.3.translating_atutor.php']          = 'Translating ATutor';
	$_pages['2.4.themes.php']                        = 'Themes';
	$_pages['2.4.1.importing_themes.php']            = 'Importing Themes';
	$_pages['2.4.2.managing_existing_themes.php']    = 'Managing Existing Themes';
	$_pages['2.4.3.creating_themes.php']             = 'Creating Themes';
	$_pages['2.5.error_logging.php']                 = 'Error Logging';
	$_pages['3.0.users.php']                         = 'Users';
	$_pages['3.1.instructor_requests.php']           = 'Instructor Requests';
	$_pages['3.2.master_student_list.php']           = 'Master Student List';
	$_pages['3.2.1.importing_student_ids.php']       = 'Importing Student IDs';
	$_pages['3.3.email_users.php']                   = 'Email Users';
	$_pages['3.4.administrators.php']                = 'Administrators';
	$_pages['3.4.1.administrator_activity_log.php']  = 'Administrator Activity Log';
	$_pages['4.0.courses.php']                       = 'Courses';
	$_pages['4.1.backups.php']                       = 'Backups';
	$_pages['4.1.1.creating_backups.php']            = 'Creating Backups';
	$_pages['4.1.2.restoring_backups.php']           = 'Restoring Backups';
	$_pages['4.2.forums.php']                        = 'Forums';
	$_pages['4.3.categories.php']                    = 'Categories';
	$_pages['5.troubleshooting.php']                 = 'Troubleshooting';

}elseif(strstr($parts['dirname'], "instructor")){

	$_pages['0.0.introduction.php']                  = 'Introduction';
	$_pages['1.0.announcements.php']                 = 'Announcements';
	$_pages['2.0.backups.php']                       = 'Backups';
	$_pages['2.1.creating_restoring.php']            = 'Creating &amp; Restoring Backups';
    $_pages['2.2.downloading_uploading.php']         = 'Downloading &amp; Uploading Backups';
    $_pages['2.3.editing_deleting.php']              = 'Editing &amp; Deleting Backups';
    $_pages['3.0.chat.php']                          = 'Chat';
    $_pages['4.0.content.php']                       = 'Content';
    $_pages['4.1.creating_editing_content.php']      = 'Creating &amp; Editing Content';
    $_pages['4.1.1.content.php']                     = 'Edit Content';
    $_pages['4.1.2.content_properties.php']          = 'Content Properties';
    $_pages['4.1.3.glossary_terms.php']              = 'Glossary Terms';
    $_pages['4.1.4.preview.php']                     = 'Preview';
    $_pages['4.1.5.accessibility.php']               = 'Accessibility';
    $_pages['4.2.content_packages.php']              = 'Import/Export Content';
    $_pages['4.3.content_usage.php']                 = 'Content Usage';
    $_pages['4.4.tile_repository.php']               = 'TILE Repository';
    $_pages['4.5.scorm_packages.php']                = 'SCORM Packages';
    $_pages['5.0.course_email.php']                  = 'Course Email';
    $_pages['6.0.enrollment.php']                    = 'Enrollment';
    $_pages['6.1.roles_privileges.php']              = 'Roles &amp; Privileges';
    $_pages['6.2.alumni.php']                        = 'Alumni';
    $_pages['6.3.groups.php']                        = 'Groups';
    $_pages['6.4.course_list.php']                   = 'Course Lists';
    $_pages['7.0.file_manager.php']                  = 'File Manager';
    $_pages['7.1.creating_folders.php']              = 'Creating Folders';
    $_pages['7.2.uploading_files.php']               = 'Uploading Files';
    $_pages['7.3.creating_new_files.php']            = 'Creating New Files';
    $_pages['7.4.editing_files.php']                 = 'Editing Files';
    $_pages['7.5.previewing_files.php']              = 'Previewing Files';
    $_pages['7.6.deleting_files_folders.php']        = 'Deleting Files &amp; Folder';
    $_pages['7.7.extracting_zip_archives.php']       = 'Extracting Zip Archives';
    $_pages['8.0.forums.php']                        = 'Forums';
    $_pages['8.1.creating_forums.php']               = 'Creating Forums';
    $_pages['8.2.editing_forums.php']                = 'Editing Forums';
    $_pages['8.3.shared_forums.php']                 = 'Shared Forums';
    $_pages['8.4.managing_forums.php']               = 'Managing Forums';
    $_pages['9.0.glossary.php']                      = 'Glossary';
    $_pages['10.0.links.php']                        = 'Links';
    $_pages['10.1.link_categories.php']              = 'Link Categories';
    $_pages['11.0.polls.php']                        = 'Polls';
    $_pages['12.0.properties.php']                   = 'Course Propertiess';
    $_pages['12.1.delete_course.php']                = 'Delete Course';
    $_pages['13.0.statistics.php']                   = 'Statistics';
    $_pages['14.0.student_tools.php']                = 'Student Tools';
    $_pages['14.1.side_menu.php']                    = 'Side Menu';
    $_pages['15.0.tests_surveys.php']                = 'Tests &amp; Surveys';
    $_pages['15.1.creating_tests_surveys.php']       = 'Creating Tests &amp; Surveys';
    $_pages['15.2.question_database.php']            = 'Question Database';
    $_pages['15.2.1.creating_questions.php']         = 'Creating Questions';
    $_pages['15.3.question_categories.php']          = 'Question Categories';
    $_pages['15.4.managing_test_questions.php']      = 'Managing Test Questions';
    $_pages['15.5.student_submissions.php']          = 'Student Submissions';
}

while (current($_pages) !== FALSE) {
	if (key($_pages) == $this_page) {
		next($_pages);
		$next_page = key($_pages);
		break;
	}
	$previous_page = key($_pages);
	next($_pages);
}
?>
<div class="seq">
	<?php if (isset($previous_page)): ?>
		Previous Chapter: <a href="<?php echo $previous_page; ?>" accesskey="," title="<?php echo $_pages[$previous_page]; ?> Alt+,"><?php echo $_pages[$previous_page]; ?></a><br /> 
	<?php endif; ?>

	<?php if (isset($next_page)): ?>
		Next Chapter: <a href="<?php echo $next_page; ?>" accesskey="." title="<?php echo $_pages[$next_page]; ?> Alt+."><?php echo $_pages[$next_page]; ?></a>
	<?php endif; ?>
</div>
