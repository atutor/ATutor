<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2008 by Harris Wong								*/
/* Inclusive Design Institute                                           */
/* http://atutor.ca                                                     */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id$
if (AT_INCLUDE_PATH !== 'NULL') {
	$db = @mysql_connect(DB_HOST . ':' . DB_PORT, DB_USER, DB_PASSWORD);	

	if (!$db) {
		/* AT_ERROR_NO_DB_CONNECT */
		require_once(AT_INCLUDE_PATH . 'classes/ErrorHandler/ErrorHandler.class.php');
		$err = new ErrorHandler();
		trigger_error('VITAL#Unable to connect to db.', E_USER_ERROR);
		exit;
	}
	if (!@mysql_select_db(DB_NAME, $db)) {
		require_once(AT_INCLUDE_PATH . 'classes/ErrorHandler/ErrorHandler.class.php');
		$err = new ErrorHandler();
		trigger_error('VITAL#DB connection established, but database "'.DB_NAME.'" cannot be selected.',
						E_USER_ERROR);
		exit;
	}
	
	//get set_utf8 config
	$sql = 'SELECT * FROM '.TABLE_PREFIX."config WHERE name='set_utf8'";
	$result = mysql_query($sql, $db);
	if ($result){
		$row = mysql_fetch_assoc($result);
	}
	if ($row['value']==1){
		mysql_query("SET NAMES 'utf8'", $db); 
	}	
}
//functions for properly escaping input strings
function my_add_null_slashes( $string ) {
    if(defined('MYSQLI_ENABLED')){
        return mysqli_real_escape_string(stripslashes($string));
    }else{
        return mysql_real_escape_string(stripslashes($string));
    }

}

function my_null_slashes($string) {
    return $string;
}

if ( get_magic_quotes_gpc() == 1 ) {
    $addslashes   = 'my_add_null_slashes';
    $stripslashes = 'stripslashes';
} else {
    if(defined('MYSQLI_ENABLED')){
        $addslashes   = 'mysqli_real_escape_string';
    }else{
        $addslashes   = 'mysql_real_escape_string';
    }
    $stripslashes = 'my_null_slashes';
}

/**
 * This function is used to make a DB query the same along the whole codebase
 * @access  public
 * @param   Query string in the vsprintf format. Basically the first parameter of vsprintf function
 * @param   Array of parameters which will be converted and inserted into the query
 * @param   Function returns the first element of the return array if set to TRUE. Basically returns the first row if it exists
 * @param...if True then addslashes will be applied to every parameter passed into the query to prevent SQL injections
 * @return  ALWAYS returns result of the query execution as an array of rows. If no results were found than array would be empty
 * @author  Alexey Novak, Cindy Li
 */
function queryDB($query, $params=array(), $oneRow = false, $sanitize = true, $callback_func = "mysql_affected_rows") {

    $sql = create_sql($query, $params, $sanitize);
    return execute_sql($sql, $oneRow, $callback_func);

}

function sqlout($sql){
    //output the sql with variable values inserted
    global $sqlout;
    $sqlout = $sql;
}

function create_sql($query, $params=array(), $sanitize = true){
    global $addslashes;

    // Prevent sql injections through string parameters passed into the query
    if ($sanitize) {
        foreach($params as $i=>$value) {
            $params[$i] = $addslashes($value);
        }
    }
    $sql = vsprintf($query, $params);
    return $sql;
}
function execute_sql($sql, $oneRow, $callback_func){
    global $db, $msg;
    
    $oneRowErrorMessage = 'Query "%s" which should returned only 1 row has returned more rows.';
    $displayErrorMessage = array('DB_QUERY', date('m/d/Y h:i:s a', time()));
    
    try {
        sqlout($sql);
        $oneRowErrorMessage = sprintf($oneRowErrorMessage, $sql);
        
        // The line below must be commented out on production instance of ATutor
       //error_log(print_r($sql, true), 0);    // NOTE ! Uncomment this line to start logging every single called query. Use for debugging purposes ONLY
        
        // Query DB and if something goes wrong then log the problem
        if(defined('MSQLI_ENABLED')){
               $result = mysqli_query($sql, $db) or (error_log(print_r(mysqli_error(), true), 0) and $msg->addError($displayErrorMessage)); 
               
        }else{
               $result = mysql_query($sql, $db) or (error_log(print_r(mysql_error(), true), 0) and $msg->addError($displayErrorMessage));
        }

        //$result = mysql_query($sql, $db) or (error_log(print_r(mysql_error(), true), 0) and $msg->addError($displayErrorMessage));
        
        // If the query was of the type which does not suppose to return rows e.g. UPDATE/SELECT/INSERT
        // is_bool is for mysql compatibility
        // === null is for mysqli compatibility
        if (is_bool($result) || $result === null) {
            if ($oneRow) {
                error_log(print_r($oneRowErrorMessage, true), 0);
                $msg->addError($displayErrorMessage);
            }
            if(isset($callback_func)){
                return $callback_func($db);                
            }else{
                return array();
            }
        }
        
        // If we need only one row then just grab it otherwise get all the results
        if ($oneRow) {
            $row = mysql_fetch_assoc($result);
            // Check that there are no more than 1 row expected.
            if (mysql_fetch_assoc($result)) {
                error_log(print_r($oneRowErrorMessage, true), 0);
                $msg->addError($displayErrorMessage);
                return array();
                return at_affected_rows($db);
            }
            unset($result);
            return ($row) ? $row : array();
        }
        
        $resultArray = array();
        while ($row = mysql_fetch_assoc($result)) {
            $resultArray[] = $row;
        }
        unset($result);
        return $resultArray;
    } catch (Exception $e) {
        error_log(print_r($e, true), 0);
        $msg->addError($displayErrorMessage);
    }
}

function at_affected_rows($db){
    return mysql_affected_rows($db);
}
 
function at_insert_id(){
    global $db;
    return mysql_insert_id($db);
}

function at_db_error(){
    return mysql_error();
}

/////////
/// USED in classes/CSVExport.class.php & CSVImport.class.php
function at_field_type($result, $i){
    return mysql_field_type($result, $i);
//mysqli_fetch_field_direct() [type]
}
function at_num_fields($result){
    return mysql_num_fields($result);
    //return mysqli_field_count()
}
function at_free_result($result){
    return mysql_free_result($result);
    //return mysqli_free_result($result);
}
////
?>