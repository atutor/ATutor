<?php
/*
 * OpenCaps
 * http://opencaps.atrc.utoronto.ca
 * 
 * Copyright 2009 Heidi Hazelton
 * Adaptive Technology Resource Centre, University of Toronto
 * 
 * Licensed under the Educational Community License (ECL), Version 2.0. 
 * You may not use this file except in compliance with this License.
 * http://www.opensource.org/licenses/ecl2.php
 * 
 */

define('INCLUDE_PATH', 'include/');
require(INCLUDE_PATH.'vitals.inc.php');

unset($_SESSION['login']);
unset($_SESSION['valid_user']);
unset($_SESSION['mid']);
unset($_SESSION['username']);

unset($_SESSION['is_admin']);
unset($_SESSION['errors']);
unset($_SESSION['notices']);

$_SESSION['feedback'][] = 'Successfully logged out.';

header('Location: index.php');
exit;

?>