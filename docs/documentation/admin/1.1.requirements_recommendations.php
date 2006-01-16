<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay, Joel Kronenberg 		*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

require('../common/body_header.inc.php'); ?>

<h2>1.1 Requirements &amp; Recommendations</h2>
	<h3>Web Server</h3>
		<p>The ATutor development and testing processes are done almost exclusively on Apache 1.3, and as such we strongly recommend it for production environments. ATutor has been successfully installed on other web servers, including, Zeus, lighttpd, Apache 2 (using pre-forking), Abyss, Zazou Mini Web Server, Microsoft IIS, and Jana-Server.</p>

		<p>The web server can be configured with <acronym title="Secure Sockets Layer">SSL</acronym> for added security or to use a non-standard port and ATutor will function without modification.</p>

	<h3>PHP</h3>
		<p><acronym title="Recursive acronym for PHP: Hypertext Preprocessor, the language ATutor is written in">PHP</acronym> 4.3.0 or higher with Zlib, MySQL, and session support enabled is required. PHP version 5.0.2 or higher is also supported, but ATutor does not make use of its added functionality. Additionally, the following <kbd>php.ini</kbd> configuration setting is required:</p>

<pre>safe_mode               = Off
display_errors          = Off
arg_separator.input     = ";&amp;"
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
include_path            = ".:/usr/local/lib/php" ; must include . (dot)
</pre>

	<h3>MySQL</h3>
		<p>Currently ATutor only supports the MySQL database. MySQL 3.23.0 or higher, 4.0.12 or higher, or 4.1.10 or higher is required. MySQL 4.0.20 and higher or 4.1.10 and higher is recommended, especially if you are using languages that would benefit from being represented in the <acronym title="UCS Transformation Format, a multibyte character encoding format.">UTF-8</acronym> character set. As ATutor moves towards utilizing UTF-8 throughout, support for older version of MySQL will be removed.</p>

		<p>A database user account with database creation privileges is required if your database does not already exist. That same user will then need table creation privileges for the chosen database.. See the MySQL chapter <a href="http://dev.mysql.com/doc/mysql/en/privileges.html" target="_new">How the Privilege System Works</a> for additional information.</p>

	<h3>Web Browser</h3>
		<p>ATutor makes use of many new HTML features that are only supported in recent web browsers. Though ATutor is designed to function effectively in older browsers we strongly recommend using the latest version of your favorite browser. We recommend <a href="http://getfirefox.com" target="_new">FireFox</a> for either Windows, *nix or Mac OS X.</p>

<?php require('../common/body_footer.inc.php'); ?>