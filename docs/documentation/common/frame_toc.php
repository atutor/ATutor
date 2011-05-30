<?php
require(dirname(__FILE__) . '/vitals.inc.php');

/**
 * handbook toc printer
 * prints an unordered html list representation of the multidimensional array.
 * $pages    the array of items to print.
 * $section  the directory name of the files.
 */
function hb_print_toc($pages, $section) {
	global $_pages, $req_lang;
	echo '<ul>';
	foreach ($pages as $page_key => $page_value) {
		echo '<li>';
		if (is_array($page_value)) {
			echo '<a href="../'.$section.'/'.$page_key.'?'.$req_lang.'" id="id'.$page_key.'" class="tree">'.$_pages[$page_key].'</a>';
			hb_print_toc($page_value, $section);
		} else {
			echo '<a href="../'.$section.'/'.$page_value.'?'.$req_lang.'" id="id'.$page_value.'" class="leaf">'.$_pages[$page_value].'</a>';
		}
		echo '</li>';
	}
	echo '</ul>';
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="<?php if ($missing_lang) { echo 'en'; } else { echo $req_lang; } ?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php get_text('handbook_toc'); ?></title>
	<base target="body" />
<style type="text/css">
body { font-family: Verdana,Arial,sans-serif; font-size: x-small; margin: 0px; padding: 0px; background: #f4f4f4; margin-left: -5px; }
ul { list-style: none; padding-left: 0px; margin-left: -15px; }
li { margin-left: 19pt; padding-top: 2px; }
a { background-repeat: no-repeat; background-position: 0px 1px; padding-left: 12px; text-decoration: none; }
a.tree { background-image: url('folder.gif'); }
a.leaf { background-image: url('paper.gif'); }
a:link, a:visited { color: #006699; }
a:hover { color: #66AECC; }
</style>
<script type="text/javascript">
// <!--
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
// -->
</script>
</head>
<body onload="highlight(false);">
<?php
require(dirname(__FILE__).'/../'.$section.'/pages.inc.php');
if (($req_lang != 'en') && (file_exists(dirname(__FILE__).'/../'.$section.'/'.$req_lang.'/pages.inc.php'))) {
	require(dirname(__FILE__).'/../'.$section.'/'.$req_lang.'/pages.inc.php');
}
if ($section == 'admin'){
	$pages = array(
				'introduction.php',
				'installation.php' => array(
											'requirements_recommendations.php',
											'new_installation.php',
											'upgrading.php'
											),
				'configuration.php' => array('my_account.php'),
				'system_preferences.php' => array(
												'default_preferences.php',
												'languages.php',
												'auto_enroll.php',
												'themes.php' => array(
																		'importing_themes.php',
																		'managing_existing_themes.php',
																		'creating_themes.php'
																		),
												'error_logging.php',
												'feeds.php',
												'google_key.php',
												'cron_setup.php'
												),
				'enrollment.php' => array(
									'enrollment_privileges.php',
									),
				'users.php' => array(
										'instructor_requests.php',
										'master_student_list.php',
										'email_users.php',
										'administrators.php'
										),
				'courses.php' => array(
										'forums.php',
										'creating_courses.php',
										'default_student_tools.php',
										'default_side_menu.php',
										'backups.php',
										'categories.php'
										),
				'modules.php',
				'basiclti_external_tools.php',
				'patcher.php' => array(
										'create_patches.php'
										),
				'troubleshooting.php',
			);

	hb_print_toc($pages, 'admin');

} else if ($section == 'instructor'){
	$pages = array(
			'introduction.php' => array('creating_courses.php','student_tools.php', 'fha_student_tools.php','side_menu.php'),
			'announcements.php',
			'assignments.php',
			'backups.php' => array(
									'creating_restoring.php',
									'downloading_uploading.php',
									'editing_deleting.php'
									),
			'chat.php',
			'content.php' => array(
									'creating_editing_content.php' => array(
				'content_edit.php',
				'content_properties.php',								'glossary_terms.php',
				'content_preview.php',
				'arrange_content.php',
				'content_alternatives.php',								'accessibility.php',
					'content_tests.php'	
													),
									'content_packages.php',
									'content_usage.php',
									'tile_repository.php',
									'scorm_packages.php'
									),
			'course_email.php',
			'enrollment.php' => array(
									'enrollment_privileges.php',
									'enrollment_alumni.php',
									'enrollment_course_list.php'
										),
			'file_manager.php' => array(
									'managing_files_folders.php',
									'extracting_zip_archives.php'
									),
			'forums.php' => array(				'managing_threads.php' => 									array('managing_posts.php'),
									'forum_export.php'
									),
			'faq.php',
			'glossary.php',
			'groups.php',
			'links.php',
			'polls.php',
			'properties.php' => array('authenticated_access.php', 'delete_course.php'),
			'reading_list.php',
			'statistics.php',
			'tests_surveys.php' => array(
											'creating_tests_surveys.php',
											'question_database.php' => array('creating_questions.php'),
											'question_categories.php',
											'edit_delete_tests.php',
											'preview.php',
											'add_questions.php',
											'student_submissions.php',
											'test_statistics.php'
											),
			'feeds.php',
			'gradebook.php' => array(
							'gradebook_add.php',	
							'gradebook_update.php',	
							'gradebook_external_marks.php',	
							'gradebook_edit_marks.php',	
							'gradebook_scales.php'	
							),
			'web_search.php',
			);
	hb_print_toc($pages, 'instructor');
} else { 

	$pages = array(
				'introduction.php',
				'login.php',
				'register.php',
				'browse_courses.php',
				'password_reminder.php',
				'my_start_page.php' => array(
												'my_courses.php' => array('create_course.php'),
												'profile.php',
												'preferences.php',
												'inbox.php'
												),
				'inside_course.php' => array(
												'export_content.php',
												'packages.php',
												'tile.php',
												'file_storage.php'
												),
				'my_network.php' => array(					
											'my_contacts.php',			
											'my_groups.php',			
											'my_profile.php',			
											'my_gadgets.php',			
											'my_settings.php'	
											),
				'pa_index.php' => array(
											'pa_albums.php',
											'pa_photo.php',
											'pa_comments.php'
											)
			);
	hb_print_toc($pages, 'general');
} ?>

</body>
</html>
