<?php
/************************************************************************/
/* AContent                                                             */
/************************************************************************/
/* Copyright (c) 2010                                                   */
/* Inclusive Design Institute                                           */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

/**
* Root data access object
* Each table has a DAO class, all inherits from this class
* @access	public
* @author	Cindy Qi Li
* @package	DAO
*/

class DAO {

	// private
	//static private $db;     // global database connection
	/*
	function DAO()
	{
		if (!isset($this->db))
		{
			$this->db = at_db_connect(DB_HOST, DB_PORT, DB_USER, DB_PASSWORD);
			
			if (!$this->db) {
				die('Unable to connect to db.');
			}
			at_db_select(DB_NAME, $this->db);
		}
	}
	*/
	/**
	* Execute SQL
	* @access  protected
	* @param   $sql : SQL statment to be executed
	* @return  $rows: for 'select' sql, return retrived rows, 
	*          true:  for non-select sql
	*          false: if fail
	* @author  Cindy Qi Li
	*/
	function execute($sql)
	{
		global $db;
		$sql = trim($sql);
		$rows = queryDB($sql, array());
		
		// for 'select' SQL, return retrieved rows
		if (strtolower(substr($sql, 0, 6)) == 'select') 
		{
			if (count($rows) > 0) {
				return $rows;
			} else {
				return false;
			}
		}
		else {
			return true;
		}
	}

}
?>