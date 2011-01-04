<?php require('../common/body_header.inc.php'); $lm = '$LastChangedDate: 2010-06-02 11:11:36 -0400 (Wed, 02 Jun 2010) $'; ?>

<h2>Requirements &amp; Recommendations</h2>
	<p>The first step when installing or upgrading ATutor is to check if the minimum requirements are met. The following describes those checks.</p>

	<h3>File Integrity</h3>
		<p>The Case Sensitivity check verifies that file names were not converted to lower-case during the extraction process. This is not an issue on case-insensitive operating systems like MS Windows, but is an issue on case-sensative ones like Linux.</p>

	<h3>Web Server</h3>
		<p>The ATutor development and testing processes are done primarily on Apache 1.3 and Apache 2 (using pre-forking), and as such we strongly recommend them for production environments. ATutor has been successfully installed on other web servers, including, Zeus, lighttpd, Abyss, Zazou Mini Web Server, Microsoft IIS, and Jana-Server.</p>

		<p>The web server can be configured with <acronym title="Secure Sockets Layer">SSL</acronym> for added security or to use a non-standard port and ATutor will function without modification.</p>


	<h3>PHP</h3>
		<p>ATutor is written in the <acronym title="Recursive acronym for PHP: Hypertext Preprocessor">PHP</acronym> language. The PHP configuration file contains many configuration settings that can be changed. The following are the minimum requirements needed to install and use ATutor.</p>

		<dl>
			<dt>PHP 5.0.2+</dt>
			<dd>Version 5.2.0 or higher is recommended.</dd>

			<dt><kbd>zlib</kbd></dt>
			<dd>Zlib support must be enabled in PHP; It is used for compressing and uncompressing ZIP files.</dd>

			<dt><kbd>mysql</kbd></dt>
			<dd>MySQL support must be enabled in PHP. </dd>

			<dt><kbd>mbstring</kbd></dt>
			<dd>MBstring support must be compiled into PHP to support UTF-8 lamguage characters.</dd>

			<dt><kbd>curl</kbd> (optional)</dt>
			<dd>Curl support must be compiled into PHP for ATutor Social (Networking) .</dd>

			<dt><kbd>safe_mode = Off</kbd></dt>
			<dd><kbd>safe_mode</kbd> must be disabled in PHP. ATutor cannot function with the restrictions enforced when <kbd>safe_mode</kbd> is enabled.</dd>

			<dt><kbd>file_uploads = On</kbd></dt>
			<dd>File uploads support must be enabled in PHP.</dd>

			<dt><kbd>upload_max_filesize</kbd> >= 2 MB</dt>
			<dd>This option specifies the maximum size of files that can be uploaded to ATutor.</dd>

			<dt><kbd>post_max_size</kbd> >= 8 MB</dt>
			<dd>This value must be larger than the <kbd>upload_max_filesize</kbd>.</dd>

			<dt><kbd>sessions</kbd></dt>
			<dd>Sessions support must be enabled in PHP.</dd>

			<dt><kbd>session.auto_start = 0</kbd></dt>
			<dd><kbd>session.auto_start</kbd> must be disabled in PHP.</dd>

			<dt><kbd>session.save_path</kbd></dt>
			<dd><kbd>session.save_path</kbd> must be set to a real path that can store session data.</dd>

			<dt><kbd>.</kbd> in <kbd>include_path</kbd></dt>
			<dd><kbd>.</kbd> must be in the list of paths in the <kbd>include_path</kbd> option.</dd>
		</dl>


		<p>Additionally, the following <kbd>php.ini</kbd> configuration settings are recommended:</p>
		<pre>display_errors          = Off
arg_separator.input     = ";&amp;"
register_globals        = Off
magic_quotes_gpc        = Off
magic_quotes_runtime    = Off
allow_url_fopen         = On
allow_url_include       = Off
register_argc_argv      = Off
zlib.output_compression = On
session.use_trans_sid   = 0
</pre>

	<h3>MySQL</h3>
		<p>Currently ATutor only supports the MySQL database. MySQL 4.1.10 or higher is required.</p>

		<p>A database user account with database creation privileges is required if your database does not already exist. That same user will then need table creation privileges for the chosen database. See the MySQL chapter <a href="http://dev.mysql.com/doc/mysql/en/privileges.html" target="_new">How the Privilege System Works</a> for additional information.</p>

	<h3>Web Browser</h3>
		<p>ATutor makes use of many new HTML features that are only supported in recent web browsers. Though ATutor is designed to function effectively in older browsers we strongly recommend using the latest version of your favorite browser. We recommend <a href="http://getfirefox.com" target="_new">FireFox</a> for either Windows, *nix or Mac OS X.</p>

<?php require('../common/body_footer.inc.php'); ?>