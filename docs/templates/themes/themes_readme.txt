
ATutor Themes Documentation
$Id: themes_readme.txt,v 1.8 2004/04/26 19:38:22 joel Exp $
===========================================

Introduction
------------
An ATutor theme is a set of template files with images and stylesheet that 
change the overall look and feel of an ATutor installation. An installation 
may have one or more themes installed at one time; a user is given the ability 
of selecting a single theme to be used while they are logged-in.

This document briefly explains the structure of an individual theme directory.

                  **************

File & Directory Sturcture
--------------------------

All themes go in /templates/themes/.

All theme specific files are in /templates/themes/[theme_name]/, where 
`[theme_name]` is the directory name of the theme. The `theme_name` need not be
the exact name of the theme (ie. a theme named `Blueberry Cheesecake` may exist
in a directory called `bb_cc`). The actual name of the theme is specified
in that theme's configuration file.

The files below are in the theme directory:
IMPORTANT: Do not change the name of any of the files!

  - basic_styles.css           - The main stylesheet for the theme
  - course_footer.tmpl.php     - The in-course footer (only used when in a course)
  - course_header.tmpl.php     - The in-course header (only used when in a course)
  - dropdown_closed.tmpl.php   - The side menu dropdowns (when close)
  - dropdown_open.tmpl.php     - The side menu dropdowns (when open)
  - footer.tmpl.php            - The main footer
  - header.tmpl.php            - The main header
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

