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

//This code is not being used. Place holder for some utilities to fix the [code] blocks
//in the visual html editor.

define("PATTERN", "/<\/p>[\W]*<p>/");
define("REPLACE", "\n");
define("CODESTART", "[code]");
define("CODEEND", "[/code]");

class ContentOutputUtils {
    /**
     * Recursive function which strips </p><p> tags from between [code] tags and
     * replaces them with line feeds
     *
     * @param string $text
     * @return string
     */
    public function stripPtags($text) {
        $codestart = strpos($text, CODESTART);
        if ($codestart == FALSE) {
            return $text;
        }
        else {
            $codestart += strlen(CODESTART);
            $firstpart = substr($text, 0, $codestart);
             
            $codeend = strpos($text, CODEEND) + strlen(CODEEND);
            $lastpart = substr($text, $codeend);

            $codetext = substr($text, $codestart, $codeend - $codestart);
            $newcodetext = preg_replace(PATTERN, REPLACE, $codetext);
            $result = $firstpart.$newcodetext.$this->stripPtags($lastpart);
        }
        return $result;
    }
}
?>