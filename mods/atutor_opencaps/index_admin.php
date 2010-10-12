<?php
/****************************************************************/
/* Atutor-OpenCaps Module						
/****************************************************************/
/* Copyright (c) 2010                           
/* Written by Antonio Gamba						
/* Adaptive Technology Resource Centre / University of Toronto
/*
/* This program is free software. You can redistribute it and/or
/* modify it under the terms of the GNU General Public License
/* as published by the Free Software Foundation.
/****************************************************************/

define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_OPEN_CAPS);
require (AT_INCLUDE_PATH.'header.inc.php');
?>

<p>Please login as an instructor to access this module</p>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>

