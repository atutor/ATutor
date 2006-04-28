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
				'troubleshooting.php',
			);

	hb_print_toc($pages, 'admin');

} else if ($section == 'instructor'){
	$pages = array(
			'0.0.introduction.php' => array('0.1.creating_courses.php'),
			'1.0.announcements.php',
			'2.0.backups.php' => array(
									'2.1.creating_restoring.php',
									'2.2.downloading_uploading.php',
									'2.3.editing_deleting.php'
									),
			'3.0.chat.php',
			'4.0.content.php' => array(
									'4.1.creating_editing_content.php' => array(
																				'4.1.1.content.php',
																				'4.1.2.content_properties.php',
																				'4.1.3.glossary_terms.php',
																				'4.1.4.preview.php',
																				'4.1.5.accessibility.php'
																				),
									'4.2.content_packages.php',
									'4.3.content_usage.php',
									'4.4.tile_repository.php',
									'4.5.scorm_packages.php'
									),
			'5.0.course_email.php',
			'6.0.enrollment.php' => array(
										'6.1.privileges.php',
										'6.2.alumni.php',
										'6.3.groups.php',
										'6.4.course_list.php'
										),
			'7.0.file_manager.php' => array(
											'7.1.creating_folders.php',
											'7.2.uploading_files.php',
											'7.3.creating_new_files.php',
											'7.4.editing_files.php',
											'7.5.previewing_files.php',
											'7.6.managing_files_folders.php',
											'7.7.extracting_zip_archives.php'
											),
			'8.0.forums.php' => array(
									'8.1.creating_forums.php',
									'8.2.editing_forums.php',
									'8.3.shared_forums.php',
									'8.4.managing_threads.php' => array('8.4.1.managing_posts.php')
									),
			'9.0.glossary.php',
			'10.0.links.php' => array('10.1.link_categories.php'),
			'11.0.polls.php',
			'12.0.properties.php' => array('12.1.delete_course.php'),
			'13.0.statistics.php',
			'14.0.student_tools.php' => array('14.1.side_menu.php'),
			'15.0.tests_surveys.php' => array(
											'15.1.creating_tests_surveys.php',
											'15.2.question_database.php' => array('15.2.1.creating_questions.php'),
											'15.3.question_categories.php',
											'15.4.edit_delete_tests.php',
											'15.5.preview.php',
											'15.6.add_questions.php',
											'15.7.student_submissions.php',
											'15.8.statistics.php'
											),
			'16.0.faq.php',
			'17.0.feeds.php',
			'18.0.web_search.php',
			);
	hb_print_toc($pages, 'instructor');
} else { 

	$pages = array(
				'0.0.introduction.php',
				'1.0.login.php',
				'2.0.register.php',
				'3.0.browse_courses.php',
				'4.0.password_reminder.php',
				'5.0.my_start_page.php' => array(
												'5.1.my_courses.php' => array('5.1.1.create_course.php'),
												'5.2.profile.php',
												'5.3.preferences.php',
												'5.4.inbox.php'
												),
				'6.0.inside_course.php' => array(
												'6.1.export_content.php',
												'6.2.packages.php',
												'6.3.tile.php'
												)
			);
	hb_print_toc($pages, 'general');
} ?>

</body>
</html>