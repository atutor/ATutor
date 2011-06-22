=== Plugin Name ===
Contributors: charlener
Donate link: none
Tags: authentication, login, database, MSSQL, MySQL, PostgreSQL
Requires at least: 2.7
Tested up to: 2.9
Stable tag: 3.15

A plugin that allows the use of an external database (MySQL, PostgreSQL, or MSSQL) for authentication into wordpress.  Supports various password hashing methods and user role checks from the external database.

== Description ==
A plugin that allows the use of an external MySQL, PostgreSQL, or MSSQL database for authentication into wordpress.  It requires you know the encryption method for the passwords stored in the external database and allows you to use MD5, SHA1, plaintext, or enter the hash/salt method yourself.  It disables password reset/retrieval and account creation within the wordpress system on the user-end, and it doesn't allow updating from the wordpress end back into the external authentication source. 

In addition to authentication the plugin allows you to:
1. Choose additional fields, such as first name/last name and website, to be imported into the wordpress user system.
1. Enter a custom message for users concerning logins. 
1. Do user role checks from the external database: you can set the plugin to check from a specific role field and compare to a value to allow login to wordpress.  

PostgreSQL or MSSQL database authentication requires MDB2 PEAR database abstraction package and relevant database drivers. MySQL continues to use the built-in PHP functions.

== Installation ==

1. Prepare your WP admin account on your external database: make a login with username 'admin' and password hashed the way accounts are handled on that database.
1. If using PostgreSQL or MSSQL, install the MDB2 PEAR database abstraction package and relevant database drivers and confirm its include path.
1. Change "New User Default Role" in Settings->General, if desired, to whatever level of control you wish externally authenticated users to have.
1. Upload `ext_db_auth.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Enter your external database settings in Settings->External DB settings


== Frequently Asked Questions ==

= My admin account for WP doesn't work anymore! =

We're authenticating externally, right?  Take that admin account you initially created in your WP setup and make sure it's in your external database.  Once it's in there you'll be able to log in as admin with no problems.  If you can't do this, delete the plugin and it'll restore access using your WP admin account.

= Can I still create accounts within WordPress? =

You could, but they don't work properly as it's not checking against the WP database, just the external then making sure the WP user info is the same as what's currently on the external database.

= Can I update user information within WordPress? =

Nope.

= My external database's passwords are hashed with a salt/datestamp/phases of the moon/etc =

Choose "Other" as your encoding method, then enter the method you use in the "Other" textbox as PHP code. If it involves more than the username and password, though, you may need to modify the plugin source code.

= I'm locked out! =

Delete or rename the plugin; if it's a DB connection-related error most likely you have the wrong connection, etc. information for the external database.

== Screenshots ==

1. Plugin config screen
2. Example login warning message upon access to wp-login.php
3. Example "Lost my password" retrieval attempt

== Changelog ==

= 3.15 =
* Disables password reset on user profile screen and has warning concerning updating profile within Wordpress
* Changes to config screen form for improved usability.
* Made lost your password and register error messages less fugly.
* Updated readme.txt

= 3.12 =
* Now correctly imports unicode text into user profile fields
* Checked with 2.8x WP, 2.8x WPMU, and 2.9 WP
* Added in line as noted in forum post http://wordpress.org/support/topic/277235?replies=4