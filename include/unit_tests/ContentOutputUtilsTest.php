<?php
/****************************************************************/
/* ATutor                                                       */
/****************************************************************/
/* Copyright (c) 2002-2010                                      */
/* Inclusive Design Institute                                   */
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
require_once ('../classes/ContentOutputUtils.class.php');

class TestContentOutputUtils extends UnitTestCase
{

    private $testString = "<p>This is some text.</p>
    <p>[code]</p>
    <p>alert('1');</p>
    <p>alert('2');</p>
    <p>[/code]</p>";
    
    private $expectedResult = "<p>This is some text.</p>
    <p>[code]alert('1');alert('2');[/code]</p>";      
     
    /**
     * Tests testStripPTags
     */
    function testStripPTags1()
    {
        $utils = new ContentOutputUtils();
        $actualResult = $utils->stripPtags($this->testString);
        $actualLength = strlen($actualResult);
        $expectedLength = strlen($this->expectedResult);
        $this->assertEqual($expectedLength, $actualLength);
        $maxLength = max($expectedLength, $actualLength);
        for ($i = 0; $i < $maxLength; $i++) {
            $this->assertEqual($this->expectedResult[$i], $actualResult[$i], "i is ". $i." chars are ".$this->expectedResult[$i]." and ".$actualResult[$i]);
        }
        $this->assertEqual($this->expectedResult, $actualResult);
    }
}
?>
