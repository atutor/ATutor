<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2010                                      */
/* Inclusive Design Institute                                   */
/* http://atutor.ca                                             */
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: 

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
	public static function splitSqlFile(&$ret, $sql)
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
        	// the comment signs can be "# ", "/*", "--". The comment start signs must be at the begining of a line.
        	else if ($char == '#' || ($char == '/' && $sql[$i+1] == '*') || ($char == ' ' && $i > 1 && $sql[$i-2] . $sql[$i-1] == '--')) {
            	// starting position of the comment depends on the comment type
        		$start_of_comment = (($char == '#' || ($char == '/' && $sql[$i+1] == '*')) ? $i : $i-2);
            	// if no "\n" exits in the remaining string, checks for "\r"
            	// (Mac eol style)
           		if ($char == '/' && $sql[$i+1] == '*')
           			$end_of_comment = strpos($sql, "*/", $i+2) + 2;
           		else
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
	public static function prefixQuery($query, $prefix)
	{
		$pattern = "/^(REPLACE INTO|INSERT INTO|CREATE TABLE|ALTER TABLE|UPDATE)(\s)+([`]?)([^`\s]+)\\3(\s)+/siU";
		$pattern2 = "/^(DROP TABLE)(\s)+([`]?)([^`\s]+)\\3(\s)?$/siU";
		if (preg_match($pattern, $query, $matches) || preg_match($pattern2, $query, $matches)) {
			$replace = "\\1 ".$prefix."\\4\\5";
			$matches[0] = preg_replace($pattern, $replace, $query);
			
			// handle the subquery of "SELECT ... FROM ..."
			$pattern_from = "/^(.*FROM)(\s)+(`)([^`\s]+)\\3(\s|;|$)+/sU";
	
			if (preg_match($pattern_from, $matches[0])) {
				//print_r($matches_from);
				$replace = "\\1 ".$prefix."\\4\\5";
				$matches[0] = preg_replace($pattern_from, $replace, $matches[0]);
			}
			
			return $matches;
		}
		return false;
	}

	public static function queryFromFile($sql_file_path, $table_prefix = null, $in_plain_msg = true)
	{
		global $db, $progress, $errors, $msg;

		$tables = array();

		if (!file_exists($sql_file_path))
			return false;

		$sql_query = trim(fread(fopen($sql_file_path, 'r'), filesize($sql_file_path)));
		SqlUtility::splitSqlFile($pieces, $sql_query);

		foreach ($pieces as $piece)
		{
			$piece = trim($piece);
			
			// [0] contains the prefixed query
			// [4] contains unprefixed table name
			if ($table_prefix || ($table_prefix == ''))
				$prefixed_query = SqlUtility::prefixQuery($piece, $table_prefix);
			else
				$prefixed_query = $piece;
	
			if ($prefixed_query != false )
			{
				$table = $table_prefix.$prefixed_query[4];
				if($prefixed_query[1] == 'CREATE TABLE')
				{
					if (mysql_query($prefixed_query[0],$db) !== false) {
						if ($in_plain_msg) {
							$progress[] = 'Table <b>'.$table . '</b> created successfully.';
						} else {
							$msg->addFeedback(array('TABLE_CREATED', $table));
						}
					}
					else
					{
						if (mysql_errno($db) == 1050) {
							if ($in_plain_msg) {
								$progress[] = 'Table <b>'.$table . '</b> already exists. Skipping.';
							} else {
								$msg->addFeedback(array('TABLE_EXIST', $table));
							}
						} else {
							if ($in_plain_msg) {
								$errors[] = 'Table <b>' . $table . '</b> creation failed.';
							} else {
								$msg->addError(array('CREATE_TABLE_FAIL', $table));
							}
						}
					}
				}
				elseif($prefixed_query[1] == 'INSERT INTO') {
					mysql_query($prefixed_query[0],$db);
				} elseif($prefixed_query[1] == 'REPLACE INTO') {
					mysql_query($prefixed_query[0],$db);
				} elseif($prefixed_query[1] == 'ALTER TABLE') {
					if (mysql_query($prefixed_query[0],$db) !== false) {
						if ($in_plain_msg) {
							$progress[] = 'Table <strong>'.$table.'</strong> altered successfully.';
						} else {
							$msg->addFeedback(array('TABLE_ALTERED', $table));
						}
					} else {
						if (mysql_errno($db) == 1060) {
							if ($in_plain_msg) {
								$progress[] = 'Table <strong>'.$table . '</strong> fields already exists. Skipping.';
							} else {
								$msg->addFeedback(array('TABLE_FIELD_EXIST', $table));
							}
						} elseif (mysql_errno($db) == 1091) {
							if ($in_plain_msg) {
								$progress[] = 'Table <strong>'.$table . '</strong> fields already dropped. Skipping.';
							} else {
								$msg->addFeedback(array('TABLE_FIELD_DROPPED', $table));
							}
						} else {
							if ($in_plain_msg) {
								$errors[] = 'Table <strong>'.$table.'</strong> alteration failed.';
							} else {
								$msg->addError(array('ALTER_TABLE_FAIL', $table));
							}
						}
					}
				} elseif($prefixed_query[1] == 'DROP TABLE') {
					mysql_query($prefixed_query[1] . ' ' .$table,$db);
				} elseif($prefixed_query[1] == 'UPDATE'){
					mysql_query($prefixed_query[0],$db);
				}
			}
		}
		
		return true;
	}

	// This function only revert queries on "CREATE TABLE" and "INSERT INTO language_text"
	public static function revertQueryFromFile($sql_file_path, $table_prefix)
	{
		global $db, $progress, $errors;

		$tables = array();

		if (!file_exists($sql_file_path))
			return false;

		$sql_query = trim(fread(fopen($sql_file_path, 'r'), filesize($sql_file_path)));
		SqlUtility::splitSqlFile($pieces, $sql_query);

		foreach ($pieces as $piece)
		{
			$piece = trim($piece);

			$pattern_create_table = "/^CREATE TABLE\s+([`]?)([^`\s]+)\\1(\s)+/siU";
			if (preg_match($pattern_create_table, $piece, $matches))
			{
				$sql = 'DROP TABLE '. $table_prefix . $matches[2];
				mysql_query($sql, $db);
			}
			
			$pattern_insert_lang = "/^INSERT INTO\s+([`]?)language_text\\1\s+.*VALUES.*'.*'.*'(.*)'.*'(.*)'/siU";
			if (preg_match($pattern_insert_lang, $piece, $matches))
			{
				$sql = "DELETE FROM ".$table_prefix."language_text WHERE variable='".$matches[2]."' AND term='".$matches[3]."'";
				mysql_query($sql, $db);
			}
		}

    return TRUE;
  }
}
?>