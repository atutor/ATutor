$Id: themes_readme.txt,v 1.1 2004/05/11 15:07:59 joel Exp $

===========================================
      ATutor Themes Documentation
===========================================

In this file:
	- Introduction
	- Installing a New Theme
	- File & Directory Structure
	- Theme Configuration File
	- Creating a Theme

                  **************

Introduction
------------
An ATutor theme is a set of template files with images and a stylesheet that 
change the overall look and feel of an ATutor installation. An installation 
may have one or more themes installed at one time; a user is given the ability 
of selecting a single theme to be used while they are logged-in.

This document briefly explains the structure of an individual theme directory.

                  **************

Installing a New Theme
----------------------

To install a new theme, decompress (unzip zip_file.zip or tar -zxvf tar_file.tar.gz)
the theme archive file into the /themes/ directory such that the theme files
exist in a subdirectory within the `themes` directory: /themes/[new_theme]/.

To have the new theme listed on the `preferences` page, you must edit the config.inc.php
file located in the /include/ directory. Edit the `AVAILABLE_THEMES` entry to list the
new theme's directory seperated with a comma. 

Example of adding a new theme called `My New Theme` found in a `new_theme` directory
would look like: define('AVAILABLE_THEMES, 'default,new_theme');
The above line defines the location of two themes: `default` and `new_theme`.

There must _always_ be a theme directory named `default`! If you want to make a different
theme the default theme then rename its directory to `default`.


                  **************

File & Directory Sturcture
--------------------------

All themes go in /themes/.

All theme specific files are in /themes/[theme_name]/, where 
`[theme_name]` is the directory name of the theme. The `theme_name` need not be
the exact name of the theme (ie. a theme named `Blueberry Cheesecake` may exist
in a directory called `bb_cc`). The actual name of the theme is specified
in that theme's configuration file.

The files below are in the theme directory:
IMPORTANT: Do not change the name of any of the files!

  - admin_header.tmpl.php      - The admin header (only used when in the "default" directory)
  - admin_footer.tmpl.php      - The admin footer (only used when in the "default" directory)
  - course_footer.tmpl.php     - The in-course footer (only used when in a course)
  - course_header.tmpl.php     - The in-course header (only used when in a course)
  - dropdown_closed.tmpl.php   - The side menu dropdowns (when closed)
  - dropdown_open.tmpl.php     - The side menu dropdowns (when open)
  - footer.tmpl.php            - The main footer
  - header.tmpl.php            - The main header
  - styles.css                 - The main stylesheet for the theme
  - theme.cfg.php              - The configuration file (see more details below)

Any additional files or images may be placed in sub-directories. 
Example: /[theme_name]/images/ may be used for theme specific images.

                  **************

Theme Configuration File - theme.cfg.php
----------------------------------------

Each theme has a configuration file that must exist and must be named theme.cfg.php .
If the theme.cfg.php file cannot be found in the theme's directory then the theme will
not be made available to use. The fields in the theme.cfg.php file are documented in the 
file; they describe such things as the name of the theme, its author, and the default
course banner style.

                  **************

Creating a Theme
----------------

The best way to start your own theme is by copying the `default` theme directory and
working out of the copy. You should then edit the theme.cfg.php file to give it a name.
Once those changes are made you should double check to make sure that the new theme gets
listed on the `preferences` page.

The theme files described above are basically PHP files. You do not need to know a lot 
about PHP to create a theme; most of the syntax if straight forward and uses mostly
if-statements and foreach-loops. For additional information on PHP check out: php.net .

The theme files contain variables which look like $tmpl_[something]. Those variables get
set by ATutor and may contain simple text or in some cases arrays (or vectors) of text.

The first theme files that should be created are the header.tmpl.php, footer.tmpl.php and
styles.css . Editing those three files alone will let you dramatically change the look
and feel of an ATutor installation. Once those files are complete you can move on to the 
in-course files; those are files that only get used when viewing a course. The in-course
files format the dropdowns and the overall look of the course.

