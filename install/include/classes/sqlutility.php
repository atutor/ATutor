<?php


class SqlUtility
{
	/**
	* Function from phpMyAdmin (http://phpwizard.net/projects/phpMyAdmin/)
	*
 	* Removes comment and splits large sql files into individual queries
 	*
	* Last revision: September 23, 2001 - gandon
 	*
 	* @param   array    the splitted sql commands
 	* @param   string   the sql commands
 	* @return  boolean  always true
 	* @access  public
 	*/
	function splitSqlFile(&$ret, $sql)
	{
		$sql               = trim($sql);
		$sql_len           = strlen($sql);
		$char              = '';
    	$string_start      = '';
    	$in_string         = false;

    	for ($i = 0; $i < $sql_len; ++$i) {
        	$char = $sql[$i];

           // We are in a string, check for not escaped end of
		   // strings except for backquotes that can't be escaped
           if ($in_string) {
           		for (;;) {
               		$i         = strpos($sql, $string_start, $i);
					// No end of string found -> add the current
					// substring to the returned array
                	if (!$i) {
						$ret[] = $sql;
                    	return true;
                	}
					// Backquotes or no backslashes before 
					// quotes: it's indeed the end of the 
					// string -> exit the loop
                	else if ($string_start == '`' || $sql[$i-1] != '\\') {
						$string_start      = '';
                   		$in_string         = false;
                    	break;
                	}
                	// one or more Backslashes before the presumed 
					// end of string...
                	else {
						// first checks for escaped backslashes
                    	$j                     = 2;
                    	$escaped_backslash     = false;
						while ($i-$j > 0 && $sql[$i-$j] == '\\') {
							$escaped_backslash = !$escaped_backslash;
                        	$j++;
                    	}
                    	// ... if escaped backslashes: it's really the 
						// end of the string -> exit the loop
                    	if ($escaped_backslash) {
							$string_start  = '';
                        	$in_string     = false;
							break;
                    	}
                    	// ... else loop
                    	else {
							$i++;
                    	}
                	} // end if...elseif...else
            	} // end for
        	} // end if (in string)
        	// We are not in a string, first check for delimiter...
        	else if ($char == ';') {
				// if delimiter found, add the parsed part to the returned array
            	$ret[]    = substr($sql, 0, $i);
            	$sql      = ltrim(substr($sql, min($i + 1, $sql_len)));
           		$sql_len  = strlen($sql);
            	if ($sql_len) {
					$i      = -1;
            	} else {
                	// The submited statement(s) end(s) here
                	return true;
				}
        	} // end else if (is delimiter)
        	// ... then check for start of a string,...
        	else if (($char == '"') || ($char == '\'') || ($char == '`')) {
				$in_string    = true;
				$string_start = $char;
        	} // end else if (is start of string)

        	// for start of a comment (and remove this comment if found)...
        	else if ($char == '#' || ($char == ' ' && $i > 1 && $sql[$i-2] . $sql[$i-1] == '--')) {
            	// starting position of the comment depends on the comment type
           		$start_of_comment = (($sql[$i] == '#') ? $i : $i-2);
            	// if no "\n" exits in the remaining string, checks for "\r"
            	// (Mac eol style)
           		$end_of_comment   = (strpos(' ' . $sql, "\012", $i+2))
                              ? strpos(' ' . $sql, "\012", $i+2)
                              : strpos(' ' . $sql, "\015", $i+2);
           		if (!$end_of_comment) {
                // no eol found after '#', add the parsed part to the returned
                // array and exit
               		$ret[]   = trim(substr($sql, 0, $i-1));
               		return true;
				} else {
                	$sql     = substr($sql, 0, $start_of_comment) . ltrim(substr($sql, $end_of_comment));
                	$sql_len = strlen($sql);
                	$i--;
            	} // end if...else
        	} // end else if (is comment)
    	} // end for

    	// add any rest to the returned array
    	if (!empty($sql) && trim($sql) != '') {
			$ret[] = $sql;
    	}
    	return true;
	}

	/**
	 * add a prefix.'_' to all tablenames in a query
     * 
     * @param   string  $query  valid MySQL query string
     * @param   string  $prefix prefix to add to all table names
	 * @return  mixed   FALSE on failure
	 */
	function prefixQuery($query, $prefix)
	{
		$pattern = "/^(INSERT INTO|REPLACE INTO|CREATE TABLE|ALTER TABLE|UPDATE)(\s)+([`]?)([^`\s]+)\\3(\s)+/siU";
		$pattern2 = "/^(DROP TABLE)(\s)+([`]?)([^`\s]+)\\3(\s)?$/siU";
		if (preg_match($pattern, $query, $matches) || preg_match($pattern2, $query, $matches)) {
			$replace = "\\1 ".$prefix."\\4\\5";
			$matches[0] = preg_replace($pattern, $replace, $query);

			if ($matches[1] == 'INSERT INTO') {
				$parts = explode(' ', $matches[0]);
				$size_of_parts = count($parts);
				if ($parts[$size_of_parts-2] == 'FROM') {
					$parts[$size_of_parts-1] = $prefix . str_replace('`', '', $parts[$size_of_parts-1]);
					$matches[0] = implode(' ', $parts);
				}
			} else if ($matches[1] == 'ALTER TABLE') {
				$parts = explode(' ', $matches[0]);
				if ($parts[3] == 'RENAME') {
					$parts[4] = $prefix . str_replace('`', '', $parts[4]);
					$matches[0] = implode(' ', $parts);
				}
			}

			return $matches;
		}
		return false;
	}
}
?>