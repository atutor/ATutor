<?php require('../common/body_header.inc.php'); $lm = '$LastChangedDate: 2006-06-30 19:56:09 -0400 (Fri, 30 Jun 2006) $'; ?>

<h2>Managing Existing Themes</h2>
	<p>All available themes on an ATutor system are listed in the Administrator's Themes section.</p>

	<dl>
		<dt><code>Preview</code></dt>
		<dd>Use the Preview button to test the theme to make sure it doesn't break. If a previewed theme breaks, simply log-out and login again to restore the default theme. The Preview button can also be used to preview disabled themes. This feature is available in ATutor 1.5.1+.</dd>

		<dt><code>Enable/Disable</code></dt>
		<dd>Enabled themes are available to users in their Preferences. Themes can be disabled, helpful if you are modifying a theme. If a student's preferred theme is disabled, the system default theme will be used in its place.</dd>

		<dt><code>Set as Default</code></dt>
		<dd>If a theme is set as the Default Theme, it will display for students who have not selected a prefered theme, and it will be displayed on public pages, such as the Login screen or Registration screen.</dd>

		<dt><code>Export</code></dt>
		<dd>Any theme can be exported from an ATutor installation to share with others.  It can also be imported back into an ATutor installation as a copy, available to be modified for creating a new theme.</dd>

		<dt><code>Delete</code></dt>
		<dd>A theme is removed from the system if the Delete button is used. The Default theme can not be deleted.</dd>
	</dl>

	<h3>Category Themes</h3>
	<p>If there are <a href="categories.php">Course Categories</a> defined and the <a href="system_preferences.php">System Preferences</a> <em>Theme Specific Categories</em>  has been enabled, themes can be assigned to categories of courses so they are all displayed with the same look and feel. When defining course categories while Category Themes is enabled, a list of available themes will appear to select from, and assign to each category.</p>

	<p>Note that when Category Themes has been enabled, users will no longer be able to select themes from their personal preference settings.</p>

<?php require('../common/body_footer.inc.php'); ?>