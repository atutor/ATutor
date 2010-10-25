
ATutor LDAP authentication module, version 0.2

This module provide basic functions of user authentication via LDAP Server, copy user information from LDAP Server and insert it into ATutor DB.
Also module provide GUI for settings LDAP-auth and listing all user's which authenticated via LDAP. 
Transfering user passwords from browsers to ATutor server protected by strongly public key encryption (RSA with 512 bit key) 

	REQUIREMENTS
1. PHP must be with ldap and openssl extensions (required for LDAP functions in ldap_lib.php and decryption in rsa.inc.php)
2. In your server operating system must be installed OpenSSL package (OpenSSL package using when generated private/public keys)

	INSTALLING
Module consist of several php and java scripts and sql file for DB updating
Next schema describe module structure and file in module

	----------------------------------
	admin/ ---------|
			|- ldap_lib.php      -  Library of basic LDAP authentication functions. Provide connect to LDAP 
			|			server, authentication users with 
			|			password in LDAP server, coping user's info from LDAP to ATutor DB.
			|- config_ldap.php   -  Script which generated page for configure LDAP authentication.
			|- ldap_auth_log.php -  Script for genaratin page with list of user's which created via 
			|			LDAP authoring
	
	include/lib/ ---|
			| - rsa.inc.php       - Library provide basic RSA decryption via private key and managament 
			|			of authoring cookie which useed 
			|			to check valid of encrypted string.
			| - menu_pages.php    - Modified standart menu_pages.php file. In this file added strings 
			|			which describe new pages for ATutor administrator.
			| - pk.pem 	      - Example private key 

	rsa/  ----------|
			| - base64.js 
			| - jsbn.js
			| - prng4.js
			| - rng.js
			| - rsa.js    	       - ALL of this files using for encryption user password in login page.
			|			 Using jscript's from http://www-cs-students.stanford.edu/~tjw/jsbn/
	 
	themes/default/-|
			| - login.tmpl.php     - Modified standart login.tmpl.php. Added new hidden input and 
			|			password encryption via RSA public key.
	
        jscript/jqgrid -|   -                  javascript files required for AJAX table in admin LDAP log page
                        |
	login.php   -----------------------------Modified standart login.php. To this file added required functions 
						 for RSA encryption/decryption and LDAP communication.

Also module has:
1. atutor.ldap.struct.sql file - Use this file to update your ATutor DB and create requried tables.



Install module:

1. Copy all files from atutor.ldap.mod  to your ATutor and put them in appropriate directories (see module schema).

2. Use atutor.ldap.struct.sql to modify your ATutor DB. In this step you may use next command:
	mysql -u "user_name" -p "your_atutor_DB" < atutor.ldap.struct.sql, then press ENTER and put "user_password"
		where "user_name"      - user that can modify your ATutor DB (see your ATutor's 
					 include/config.inc.php)
		      "user_password"  - password for access "user_name" to your ATutor DB (see your ATutor's 
					 include/config.inc.php)
		      "your_atutor_DB" - name of DB which used by your ATutor 

		EXAMPLE:  mysql -u atutor -p atutor154 < atutor.ldap.struct.sql   

3. Now you must generate a private key using openssl.(This module has example private key, but strongly recomended     generate new private key)  
  
  To generate RSA private key use next commands:
	
	$ openssl genrsa -out priv_key.pem.	
	Generating RSA private key, 512 bit long modulus
.	.++++++++++++
	..............++++++++++++
	e is 65537 (0x10001)
	$

	Private key will be saved in priv_key.pem

4. Copy your private key in a place which can't be readed by everyone (don't copy your private key to directory,       which can be readed by Apache  web-server), but rsa.inc.php must has access to private key.
   In my ATutor installations I do following steps (let private key stored in priv_key.pem):
		1. Copy priv_key.pem to my_atutor_installation_path/include/lib/ directory. 
		2. Use chmod 644 priv_key.pem (now rsa.inc.php has read access to priv_key.pem !!!)
		3. In httpd.conf of my Apache web server I put next dirictives 
				<Directory "my_atutor_installation_path/include/lib/">
					Order deny,allow
					Deny from all
				</Directory>
			Now, include/lib/ directory protected and nobody can read priv_key.pem 
   
   Path to your private key must be defined in rsa.inc.php 

5. Getting modulus from private key and configure rsa.inc.php

	$ openssl rsa -in priv_key.pem -noout -modulus
	Modulus=DA3BB4C40E3C7E76F7DBDD8BF3DF0714CA39D3A0F7F9D7C2E4FEDF8C7B28C2875F7EB98950B22AE82D539C1ABC1AB550BA
	$
	
	Copy modulus to rsa.inc.php 
	
6. Confgiure LDAP authentication
 	1. Login in your ATutor system.
	2. Go to System Prefernces and then to LDAP Authentication page
	3. Set LDAP Server name. It may in two variants, first - FQDN, second - LDAP URL ("ldap://your_ldap_server"
	   or "ldap://xxx.xxx.xxx.xxx", where xxx.xxx.xxx.xxx - IP address of LDAP server
	4. Set LDAP port. By default using standart LDAP port
	5. Set LDAP Server tree. You must define LDAP tree (or subtree) where stored user's entries.
		EXAMPLE. If LDAP server has name  example.com and it has subtree with name "accounts". Subtree 
		"accounts" has children subtree "users" where stored user's entries. So, your LDAP Server tree
		will be "ou=users,ou=accounts,dc=example,dc=com".
		
		Contact with your LDAP server administrator to get full information about LDAP structure.
	6. Set attributes of user entries.
		In "LDAP Server field" you must set name of LDAP entries attribute. 
		EXAMPLE. If user entries in LDAP has 6 attributes, 1 is uid attribute, where stored user's login, 
		2 is password attribute, where stored user's password, 3, 4, 5 is l_name, f_name, s_name attributes where stored user's last, first and second names, and 6 attribute is mail, where stored user's email. 
		In this case, you must set in "Login" field - "uid", "E-mail" field - "mail", in "Last name", "First name", "Second name" fields - "l_name", "f_name", "s_name".
		
		Contact with your LDAP server administrator to get full information about entries attributes.


This module tested (and it's work) in next platforms

 	1. OpenSUSE 10.2 + Apache 2.2.3 + MySQL 5.0.26 + PHP 5.2.0 + OpenLDAP 2.2 
	2. Slackware 11.0 + Apache 1.3.37 + MySQL 5.0.33 + PHP 4.4.6 + OpenLDAP 2.3.32
        3. Fedora 10 + Apache 2 + MySQL 5.0.23 + PHP 5.2 + OpenLDAP

Currently ATutor + ldap module running on Fedora 10 + Apache 2.2.3 + MySQL 5.0.22 + PHP 5.1.6  and LDAP server running on Fedora Core 4 + OpenLDAP 2.2.4 In  this configuration system running aproximetly 25 month. 

This module with a few changes can be used for user authentication via Microsoft Active Directory.

This module distributed "as is" and can be modified for your needs.

If you use or modified this module, please, email me.

	 smal (Serhiy Voyt)
	 smalgroup@gmail.com
	
	 Distributed under GPL (c)Sehiy Voyt 2005-2009

