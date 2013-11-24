<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2013              								*/
/* Inclusive Design Institute                                           */
/* http://atutor.ca                                                     */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id$
if (AT_INCLUDE_PATH == 'NULL') {
    echo "Something went wrong.";
    exit;
}

//require_once(AT_INCLUDE_PATH . 'classes/ErrorHandler/ErrorHandler.class.php');

function at_db_connect($db_host, $db_port, $db_user, $db_password){
    global $msg;
	$db = @mysql_connect($db_host . ':' . $db_port, $db_user, $db_password);	
	if(!db){
	    $db = force_db_connect();
	}
	if (!$db) {
		/* AT_ERROR_NO_DB_CONNECT */
		require_once(AT_INCLUDE_PATH . 'classes/ErrorHandler/ErrorHandler.class.php');
		$err = new ErrorHandler();
		trigger_error('VITAL#Unable to connect to db.', E_USER_ERROR);
		exit;
	} else {
	    return $db;
	}
}
function force_db_connect(){
    // Used during instllation only
    if(!$db && isset($_POST['db_host'])){
        $db = mysql_connect($_POST['db_host'] . ':' . $_POST['db_port'], $_POST['db_login'], $_POST['db_password']);
        mysql_select_db($_POST['db_name'], $db);
    }
    //install
    else if(!$db && isset($_POST['step2']['db_host'])){
            $db = mysql_connect($_POST['step2']['db_host']. ':' .$_POST['step2']['db_port'], $_POST['step2']['db_login'], $_POST['step2']['db_password']);
	        mysql_select_db($_POST['step2']['db_name'], $db);
    }
    //upgrade
    else if(!$db && isset($_POST['step1']['db_host'])){
            $db = mysql_connect($_POST['step1']['db_host']. ':' .$_POST['step1']['db_port'], $_POST['step1']['db_login'], $_POST['step1']['db_password']);
	        mysql_select_db($_POST['step1']['db_name'], $db);
    }
    return $db;
}
function at_is_db($db_name, $db){
    // see if a databas exists
	$sql = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$db_name'";
	$result = mysql_query($sql, $db);
	$exists = mysql_num_rows($result);
	return $exists;
}
function at_create_db($db_name, $db){
    $sql = "CREATE DATABASE `$db_name` CHARACTER SET utf8 COLLATE utf8_general_ci";
	$result = mysql_query($sql, $db);
	return $result;
}
function at_create_table($sql){
    if(!isset($db)){
        $db = force_db_connect();	
    }
	$result = mysql_query($sql, $db);
	return $result;
}
function at_db_version($db){
    $sql = "SELECT VERSION() AS version";
	$result = mysql_query($sql, $db);
	$row = mysql_fetch_assoc($result);
    return $row;
}
function at_db_select($db_name, $db){
    global $msg;
    if(at_is_db($db_name, $db) > 0){
      if(!@mysql_select_db($db_name, $db)) {
            $err = new ErrorHandler();
            trigger_error('VITAL#DB connection established, but database "'.$db_name.'" cannot be selected.',
                            E_USER_ERROR);
            exit;
        }
        return $db;
    }
}
	
	//get set_utf8 config
function at_set_utf8($db){
    global $msg;
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
 * @param   $query = Query string in the vsprintf format. Basically the first parameter of vsprintf function
 * @param   $params = Array of parameters which will be converted and inserted into the query
 * @param   $oneRow = Function returns the first element of the return array if set to TRUE. Basically returns the first row if it exists
 * @param   $sanitize = if True then addslashes will be applied to every parameter passed into the query to prevent SQL injections
 * @param   $callback_func = call back another db function, default mysql_affected_rows
 * @param   $array_type = Type of array, MYSQL_ASSOC (default), MYSQL_NUM, MYSQL_BOTH, etc.
 * @return  ALWAYS returns result of the query execution as an array of rows. If no results were found than array would be empty
 * @author  Alexey Novak, Cindy Li, Greg Gay
 */
function queryDB($query, $params=array(), $oneRow = false, $sanitize = true, $callback_func = "mysql_affected_rows", $array_type = MYSQL_ASSOC) {
    $sql = create_sql($query, $params, $sanitize);
    return execute_sql($sql, $oneRow, $callback_func, $array_type);

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
function execute_sql($sql, $oneRow, $callback_func, $array_type){
    global  $msg;
 
    if(!isset($db) && (isset($_POST['db_host']) || isset($_POST['step2']['db_host']) || isset($_POST['step1']['db_host']))){
        $db = force_db_connect();
    } else {
        $db = at_db_connect(DB_HOST, DB_PORT, DB_USER, DB_PASSWORD);
        $db = at_db_select(DB_NAME, $db);
    }

    $oneRowErrorMessage = 'Query "%s" which should returned only 1 row has returned more rows.';
    $displayErrorMessage = array('DB_QUERY', date('m/d/Y h:i:s a', time()));
   
    try {
        sqlout($sql);
        $oneRowErrorMessage = sprintf($oneRowErrorMessage, $sql);
        
        // The line below must be commented out on production instance of ATutor
       error_log(print_r($sql, true), 0);    // NOTE ! Uncomment this line to start logging every single called query. Use for debugging purposes ONLY
        
        // Query DB and if something goes wrong then log the problem
        if(defined('MSQLI_ENABLED')){
               $result = mysqli_query($sql, $db) or (error_log(print_r(mysqli_error(), true), 0) and $msg->addError($displayErrorMessage)); 
            
        }else{
               $result = mysql_query($sql, $db) or (error_log(print_r(mysql_error(), true), 0) and $msg->addError($displayErrorMessage));
         }
        
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
            $row = mysql_fetch_array($result, $array_type);
            // Check that there are no more than 1 row expected.
            if (mysql_fetch_array($result, $array_type)) {
                error_log(print_r($oneRowErrorMessage, true), 0);
                $msg->addError($displayErrorMessage);
                return at_affected_rows($db);
            }
            unset($result);
            return ($row) ? $row : array();
        }
        
        $resultArray = array();
        while ($row = mysql_fetch_array($result, $array_type)) {
            $resultArray[] = $row;
        }
        unset($result);
        return $resultArray;
    } catch (Exception $e) {
        error_log(print_r($e, true), 0);
        $msg->addError($displayErrorMessage);
    }
}
function queryDBresult($sql, $params = array(), $sanitize = true){

        $db = at_db_connect(DB_HOST, DB_PORT, DB_USER, DB_PASSWORD);
        $db = at_db_select(DB_NAME, $db);

        $sql = create_sql($sql, $params, $sanitize);

        if(defined('MSQLI_ENABLED')){
               $result = mysqli_query($sql, $db) or (error_log(print_r(mysqli_error(), true), 0) and $msg->addError($displayErrorMessage)); 
               
        }else{
               $result = mysql_query($sql, $db) or (error_log(print_r(mysql_error(), true), 0) and $msg->addError($displayErrorMessage));
        }
       
    return $result;
}
function at_affected_rows($db){
    return mysql_affected_rows($db);
}
 
function at_insert_id(){
    global $db;
    return mysql_insert_id($db);
}
function at_db_errno(){
    global $db;
    return mysql_errno($db);
}
function at_db_error(){
    global $db;
    return mysql_error($db);
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
function at_field_flags($result, $i){
        return mysql_field_flags($result, $i);
//return mysqli_fetch_field_direct() [flags]
}
function at_field_name($result, $i){
    return mysql_field_name($result, $i);
    //return mysqli_fetch_field_direct() [name] or [orgname]
}

////
?>