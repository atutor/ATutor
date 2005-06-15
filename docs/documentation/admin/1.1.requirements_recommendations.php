<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg 		*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id: 10.1.link_categories.php 4824 2005-06-08 19:27:33Z joel $

require('../common/body_header.inc.php'); ?>

<h2>1.1 Requirements &amp; Recommendations</h2>
	<h3>Web Server</h3>
		<p>We strongly recommend Apache 1.3 as the required web server and discourage the use of Apache 2 in production environments. For non-production systems Apache 2 can be used, and in those cases it will function effectively. We believe that unless your server is running on Windows, Apache 2 does not provide a significant performance improvement over Apache 1 to necessitate migration. ATutor has also been successfully installed on Microsoft IIS, but it is not part of our development testing, so we cannot make any guarantees.</p>

	<h3>PHP</h3>
		<p><acronym title="Recursive acronym for PHP: Hypertext Preprocessor, the language ATutor is written in">PHP</acronym> 4.2.0 or higher with Zlib, MySQL, and session support enabled is also required. PHP version 4.3.0 or higher is highly recommended as it provides greater performance than previous versions. PHP version 5.0.4 or higher should be fine for non-production environments. Additionally, the following <kbd>php.ini</kbd> configuration setting is required:</p>

		<pre>safe_mode               = Off</pre>

		<p>The following are recommended settings:</p>
<pre>display_errors          = Off
arg_separator.input     = ";&"
register_globals        = Off
magic_quotes_gpc        = Off
magic_quotes_runtime    = Off
allow_url_fopen         = On
register_argc_argv      = Off
zlib.output_compression = On
post_max_size           = 8M ; or greater
file_uploads            = On
upload_max_filesize     = 2M ; or greater
session.use_trans_sid   = 0
</pre>

	<h3>MySQL</h3>
		<p>Currently ATutor only supports the MySQL database. MySQL 3.23.0 or higher, or 4.0.12 or higher is required. MySQL 4.0.20 and higher is recommended, especially if you are using languages that would benefit from being represented in the <acronym title="UCS Transformation Format, a multibyte character encoding format.">UTF-8</acronym> character set.</p>

		<p>A database user account with database creation privileges is required if your database does not already exist. That same user will then need table creation privileges as well. See the MySQL chapter <a href="http://dev.mysql.com/doc/mysql/en/privileges.html">How the Privilege System Works</a> for additional information.</p>

	<h3>Web Browser</h3>
		<p>ATutor makes use of many new HTML features that are only supported in recent web browsers. Though ATutor will function effectively in older browsers we strongly recommend using the latest version of your favorite browser. We recommend <a href="http://getfirefox.com">FireFox</a> for either Windows, Unix or Mac OS X.</p>

<?php require('../common/body_footer.inc.php'); ?>
