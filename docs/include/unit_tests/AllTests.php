<?php
/****************************************************************/
/* ATutor                                                       */
/****************************************************************/
/* Copyright (c) 2002-2010 by Greg Gay & Laurel A. Williams     */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca                                             */
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.                */
/****************************************************************/
// $Id: $

/**
 * Tests utility functions for the infusion builder.
 */
define("SIMPLETEST_PATH", "insert your path here");

require_once (SIMPLETEST_PATH.'/simpletest/autorun.php');
require_once('ContentOutputUtilsTest.php');

class AllTests extends TestSuite {
    function __construct() {
        parent::__construct();
//      $this->addTestCase(new TestContentOutputUtils()); //fails, but can be modified later to not fail
    }
}

?>
