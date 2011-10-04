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

Access the Themes area of the Administration section.

                  **************

File & Directory Sturcture
--------------------------

All themes go in /themes/.

All theme specific files are in /themes/[theme_name]/, where 
`[theme_name]` is the directory name of the theme. The `theme_name` need not be
the exact name of the theme (ie. a theme named `Blueberry Cheesecake` may exist
in a directory called `bb_cc`). The actual name of the theme is specified
in that theme's configuration file.

The theme may be renamed however, if another theme by the same name already exists.

The files below are in the theme directory:

  - theme_info.xml             - The configuration file (see "Theme Configuration File" below)
  - screenshot.jpg             - 200x125 pixel screenshot of the theme

Any additional files or images may be placed in sub-directories. 
Example: /[theme_name]/images/ may be used for theme specific images.

                  **************

Theme Configuration File - theme_info.xml
----------------------------------------

Each theme has a configuration file that must exist and must be named 'theme_info.xml' .
If the theme_info.xml file cannot be found in the theme's directory then the theme will
not be made available to use. The fields in the theme_info.xml file are documented in the 
file; they describe such things as the name of the theme, its author, and the default
course banner style.

                  **************

Creating a Theme
----------------

The best way to start your own theme is by exporting one of the themes displayed on the Theme Manager.
The theme is exported onto your desktop as a '.zip' file.
You must extract the contents of this file in order to change them.
You should then edit the theme_info.xml file to give it a name.
Once those changes are made you should zip the files and import them using the import theme function.
The name of the zip file should be the name you want to give your theme, sunstituting '_' for spaces.

The theme files described above are basically PHP files. You do not need to know a lot 
about PHP to create a theme; most of the syntax if straight forward and uses mostly
if-statements and foreach-loops. For additional information on PHP check out: php.net .

The theme files contain variables which look like $this->[something]. Those variables get
set by ATutor and may contain simple text or in some cases arrays (or vectors) of text.

The first theme files that should be created are the header.tmpl.php, footer.tmpl.php and
styles.css . Editing those three files alone will let you dramatically change the look
and feel of an ATutor installation. Once those files are complete you can move on to the 
in-course files; those are files that only get used when viewing a course. The in-course
files format the dropdowns and the overall look of the course.