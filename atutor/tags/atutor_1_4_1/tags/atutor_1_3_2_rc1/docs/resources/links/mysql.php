<?php
// phpHoo2 - a yahoo-like link directory written in PHP3
// Copyright (C) 1999/2000 Rolf V. Ostergaard http://www.cable-modems.org/phpHoo/
Class MySQL
{
	// ////////////////////////////////////////////////////////////////////////////
	// Need to set these constant variables:
	var $AUTOAPPROVE = false;	// True to automatically approve submissions
	var $REQUIRE_SUBMIT_EMAIL = false; 
					// True to require the email address for 
					// any submissions (note you can surf the web 
					// without an email address)

	var $CAT_TBL = 'resource_categories';	// MySQL table name for the categories table
	var $LNK_TBL = 'resource_links';	// MySQL table name for the links table
	// That's all!
	// ////////////////////////////////////////////////////////////////////////////

	var $CONN = '';
	var $TRAIL = array();

	function error($text)
	{
		$no = mysql_errno();
		$msg = mysql_error();
		exit;
	}

	function init ()
	{
		$user = DB_USER;
		$pass = DB_PASSWORD;
		$server = DB_HOST;
		$dbase = DB_NAME;

		$conn = @mysql_connect($server,$user,$pass);
		if(!$conn) {
			$this->error("Connection attempt failed");
		}
		if(!mysql_select_db($dbase,$conn)) {
			$this->error("Dbase Select failed");
		}
		$this->CONN = $conn;

		$this->CAT_TBL = TABLE_PREFIX.$this->CAT_TBL;
		$this->LNK_TBL = TABLE_PREFIX.$this->LNK_TBL;
		return true;
	}

/****************************************************************/
/*						MySQL Specific methods					*/
/****************************************************************/
	function select ($sql='', $column='')
	{

		if(empty($sql)) { return false; }
		if(!eregi("^select",$sql))
		{
			echo "<H2>Wrong function silly!</H2>\n";
			return false;
		}
		if(empty($this->CONN)) { return false; }
		$conn = $this->CONN;

		$results = mysql_query($sql,$conn);

		if (!$results) {
			debug($sql);
			return false;
		}
		$count = 0;
		$data = array();
		while ( $row = mysql_fetch_array($results))
		{
			$data[$count] = $row;
			$count++;
		}
		mysql_free_result($results);
		return $data;
	}

	function increment_count($link_id)
	{
		if(empty($this->CONN)) { return false; }
		$conn = $this->CONN;
		$results = mysql_query("SELECT * FROM $this->LNK_TBL where LinkID=$link_id",$conn);
		if( (!$results) or (empty($results)) ) {
			mysql_free_result($results);
			return false;
		}
		$row = mysql_fetch_array($results);
		$row[9]++;
		mysql_free_result($results);
		$results = mysql_query("UPDATE $this->LNK_TBL set hits=$row[9] WHERE LinkID=$link_id",$conn);
		Header("Location: $row[2]");
		exit;
	}

	function insert ($sql='')
	{
		if(empty($sql)) { return false; }
		if(!eregi("^insert",$sql))
		{
			echo "<H2>Wrong function silly!</H2>\n";
			return false;
		}
		if(empty($this->CONN))
		{
			echo "<H2>No connection!</H2>\n";
			return false;
		}
		$conn = $this->CONN;
		$results = mysql_query($sql,$conn);
		if(!$results) 
		{
			return false;
		}
		$results = mysql_insert_id();
		return $results;
	}

	function sql_query ($sql='')
	{
		if(empty($sql)) { return false; }
		if(empty($this->CONN)) { return false; }
		$conn = $this->CONN;
		$results = mysql_query($sql,$conn);
		if(!$results) 
		{
			echo "<H2>Query went bad!</H2>\n";
			echo mysql_errno().":  ".mysql_error()."<P>";
			return false;
		}
		return $results;
	}

	function sql_cnt_query ($sql='')
	{
		if(empty($sql)) { return false; }
		if(empty($this->CONN)) { return false; }
		$conn = $this->CONN;
		$results = mysql_query($sql,$conn);
		if( (!$results) or (empty($results)) ) {
			return false;
		}
		$count = 0;
		$data = array();
		while ( $row = mysql_fetch_array($results))
		{
			$data[$count] = $row;
			$count++;
		}
		return $data[0][0];
	}


/****************************************************************/
/*						phpHoo Specific Methods					*/
/****************************************************************/

	function get_Cats ($CatParent= '')
	{
		if(empty($CatParent) || ($CatParent == '0'))
		{
			$CatParent = 'IS NULL';
		} else {
			$CatParent = "= $CatParent";
		}
		$course = "AND course_id=$_SESSION[course_id]";
		$sql = "SELECT CatID,CatName FROM $this->CAT_TBL WHERE CatParent $CatParent $course ORDER BY CatName";

		$results = $this->select($sql);
		
		return $results;
	}

	function get_ChildrenInt($CatID='')
	{
		if(empty($CatID) || ($CatID == '0')) { return false; }
		unset($this->TRAIL);
		$this->TRAIL = array();
		$this->get_Children($CatID);
	}

	function get_Children($CatID='')
	{
		if( (empty($CatID)) or ("$CatID" == "NULL")) { return false; }
		$sql = "SELECT CatID,CatName FROM $this->CAT_TBL WHERE CatParent = $CatID AND course_id=$_SESSION[course_id]";

		$conn = $this->CONN;
		$results = mysql_query($sql,$conn);
		if( (!$results) or (empty($results)) ) {
			mysql_free_result($results);
			return false;
		}

		while ( $row = mysql_fetch_array($results))
		{
			$trail = $this->TRAIL;
			$count = count($trail);
			$trail[$count] = $row;
			$this->TRAIL = $trail;
			//$id = $row["CatID"];
		}
		return true;
	}

//	The primer for a recursive query
	function get_ParentsInt($CatID='')
	{

		if(empty($CatID) || ($CatID == '0')) { return false; }
		unset($this->TRAIL);
		$this->TRAIL = array();
		$this->get_Parents($CatID);
	}

//	Use get_ParentsInt(), NOT this one!
//	The power of recursive queries
	function get_Parents ($CatID='')
	{

		if( (empty($CatID)) or ("$CatID" == "NULL")) { return false; }
		$sql = "SELECT CatID,CatParent,CatName FROM $this->CAT_TBL WHERE CatID = $CatID";

		$conn = $this->CONN;
		$results = mysql_query($sql,$conn);
		if( (!$results) or (empty($results)) ) {
			mysql_free_result($results);
			return false;
		}

		while ( $row = mysql_fetch_array($results))
		{
			$trail = $this->TRAIL;
			$count = count($trail);
			$trail[$count] = $row;
			$this->TRAIL = $trail;
			$id = $row["CatParent"];
			$this->get_Parents($id);
		}
		return true;
	}

	function get_CatIDFromName($CatName="")
	{

		if(empty($CatName)) { return false; }
		$sql = "SELECT CatID FROM $this->CAT_TBL WHERE CatName='$CatName' AND course_id=$_SESSION[course_id]";
		$results = $this->select($sql);
		if(!empty($results))
		{
			$results = $results[0]["CatID"];
		}
		return $results;
	}

	function get_CatNames($CatID='')
	{
		if($CatID == 0) { return "Top"; }
		$single = false;
		
		if(!empty($CatID))
		{
			$single = true;
			$CatID = "WHERE CatID=$CatID AND course_id=$_SESSION[course_id]";
			/*
			we may be viewing a category not for this course if $v=1
			*/
		} else {
			$CatID = "WHERE course_id=$_SESSION[course_id]";
		} 
		$sql = "SELECT CatName FROM $this->CAT_TBL $CatID ";

		$results = $this->select($sql);
		if($single)
		{
			if(!empty($results))
			{
				$results = $results[0]["CatName"];
			}
		}
		return $results;
	}

	function get_AllCats()
	{
		$sql = "SELECT CatID,CatName FROM $this->CAT_TBL WHERE course_id=$_SESSION[course_id]";
		$results = $this->select($sql);
		return $results;
	}

	function get_Links($CatID = '')
	{
		if(empty($CatID))
		{
			// $CatID = "AND CatID = 0";
			$sql = "SELECT * FROM $this->LNK_TBL WHERE (Approved<>0) ORDER BY hits DESC LIMIT 0,5";
		} else if ($CatID == -1) {
			/* show only new for this course */
			$sql = "SELECT L.* FROM $this->LNK_TBL L, $this->CAT_TBL C WHERE (L.Approved<>0) AND (C.course_id=$_SESSION[course_id]) AND (L.CatID=C.CatID) ORDER BY SubmitDate DESC LIMIT 0,10";
		} else {
			$sql = "SELECT * FROM $this->LNK_TBL WHERE  (Approved<>0) AND CatID = $CatID ORDER BY LinkName";
		}
	
		$results = $this->select($sql);
		return $results;
	}

	function get_OneLink($LinkID = "")
	{
		if(empty($LinkID)) { 
			$err_msg = "No LinkID given.";
			return false; 
		}

		$sql = "SELECT * FROM $this->LNK_TBL WHERE LinkID=$LinkID";
		$results = $this->select($sql);
		return $results;
	}

	function get_Submissions()
	{
		$sql = "SELECT L.* FROM $this->LNK_TBL L, $this->CAT_TBL C WHERE  (L.Approved = 0) AND L.CatID=C.CatID AND C.course_id=$_SESSION[course_id] ORDER BY L.Url";
		$results = $this->select($sql);
		return $results;
	}

	function get_CatFromLink($LinkID="")
	{
		if(empty($LinkID)) { return false; }
		$sql = "SELECT CatID FROM $this->LNK_TBL WHERE LinkID = $LinkID";
		$results = $this->select($sql);
		if(!empty($results))
		{
			$results = $results[0]["CatID"];
		}
		return $results;
	}

	// Check if a CatID is indeed in the table of valid categories
	function isValidCatID($CatID="") 
	{
		if (empty($CatID)) { return false; }
		if ($CatID=="0") { return true; }
		/* this query may actually have to get the "course_id" part removed */
		/* b/c we may want to check when not logged in?						*/
		$sql = "SELECT * FROM $this->CAT_TBL WHERE CatID = $CatID AND course_id=$_SESSION[course_id]";
		$results = $this->select($sql);
		if (empty($results)) { return false; }
		return true;
	}	

	function search ($keywords = '')
	{
		if(empty($keywords)) { return false; }

		$DEBUG = ""; // set DEBUG == "\n" to see this query

		$keywords = trim(urldecode($keywords));
		$keywords = ereg_replace("([    ]+)"," ",$keywords);

		if(!ereg(" ",$keywords))
		{
			// Only 1 keyword
			$KeyWords[0] = "$keywords";
		} else {
			$KeyWords = explode(" ",$keywords);
		}

		/* search only within this course */
		$sql = "SELECT DISTINCT L.LinkID,L.CatID,L.Url,L.LinkName,L.Description FROM $this->LNK_TBL L, $this->CAT_TBL C WHERE (L.Approved<>0) AND (C.course_id=$_SESSION[course_id]) AND (C.CatID=L.CatID) AND ( $DEBUG ";
		$count = count($KeyWords);

		if ($count == 1)
		{
			$single = $KeyWords[0];
			$sql .= " (L.Description LIKE '%$single%') OR (L.LinkName LIKE '%$single%') OR (L.Url LIKE '%$single%') ) ORDER BY L.LinkName $DEBUG ";
		} else {
			$ticker = 0;
			while ( list ($key,$word) = each ($KeyWords) )
			{
				$ticker++;
				if(!empty($word))
				{
					if($ticker != $count)
					{
						$sql .= " ( (L.Description LIKE '%$word%') OR (L.LinkName LIKE '%$word%') OR (L.Url LIKE '%$word%') ) OR $DEBUG ";
					} else {
						// Last condition, omit the trailing OR
						$sql .= " ( (L.Description LIKE '%$word%') OR (L.LinkName LIKE '%$word%') OR (L.Url LIKE '%$word%') ) $DEBUG ";
					}
				}
			}
			$sql .= " ) ORDER BY L.LinkName $DEBUG";
		}

		if(!empty($DEBUG)) { echo "<PRE>$sql\nTicker [$ticker]\nCount [$count]</PRE>\n"; }

		// echo $sql;
		$results = $this->select($sql);
		return $results;
	}

	function suggest ($postData="",&$err_msg)
	{
		/* course ID not needed */
		$err_msg="";

		if( (empty($postData)) or (!is_array($postData)) ) { 
			$err_msg = "No data submitted or not an array of data";
			return false; 
		}

		$CatID = $postData["CatID"];
		$Url = addslashes($postData["Url"]);
		$Description = addslashes($postData["Description"]);
		$LinkName = addslashes($postData["LinkName"]);
		$SubmitName = addslashes($postData["SubmitName"]);
		$SubmitEmail = addslashes($postData["SubmitEmail"]);
		// $SubmitDate = time();
		$SubmitDate = date("Y-m-d");

		if(!$this->isValidCatID($CatID)) {
			$err_msg = "Invalid category.";
			return false; 
		}
		if(empty($Url) || ($Url == 'http://'))  { 
			$err_msg = "No URL specified.";
			return false; 
		}
		/*
		if(empty($Description)) { 
			$err_msg = "No description given.";
			return false; 
		}
		*/
		if(empty($LinkName)) { 
			$err_msg = "No link name given.";
			return false; 
		}
		/*
		if(empty($SubmitName)) { 
			$err_msg = "No name given.";
			return false; 
		}
		if(empty($SubmitEmail)) { 
			if ($REQUIRE_SUBMIT_EMAIL) {
				$err_msg = "No email address given.";
				return false; 
			} else {
				$SubmitEmail = 'anonymous';
			}
		}
		*/

		$Approved = 0;
		if($this->AUTOAPPROVE) { $Approved = 1; }

		$sql = "INSERT INTO $this->LNK_TBL ";
		$sql .= "(CatID,Url,LinkName,Description,SubmitName,SubmitEmail,SubmitDate,Approved) ";
		$sql .= "values ";
		$sql .= "($CatID,'$Url','$LinkName','$Description','$SubmitName','$SubmitEmail','$SubmitDate',$Approved) ";
		$results = $this->insert($sql);

		return $results;
	}

	function update ($postData="",&$err_msg)
	{
		$err_msg="";

		if( (empty($postData)) or (!is_array($postData)) ) { 
			$err_msg = "No data submitted or not an array of data";
			return false; 
		}

		$LinkID = $postData['LinkID'];
		$CatID = $postData['CatID'];
		$Url = addslashes($postData['Url']);
		$Description = addslashes($postData['Description']);
		$LinkName = addslashes($postData['LinkName']);
		$SubmitName = addslashes($postData['SubmitName']);
		$SubmitEmail = addslashes($postData['SubmitEmail']);
		$SubmitDate = date('Y-m-d');

		if(!$this->isValidCatID($CatID)) {
			$err_msg = "Invalid category.";
			return false; 
		}
		if(empty($Url)) { 
			$err_msg = "No URL specified.";
			return false; 
		}
		/*
		if(empty($Description)) { 
			$err_msg = "No description given.";
			return false; 
		}
		if(empty($LinkName)) { 
			$err_msg = "No link name given.";
			return false; 
		}
		if(empty($SubmitName)) { 
			$err_msg = "No name given.";
			return false; 
		}
		if(empty($SubmitEmail)) { 
			if ($REQUIRE_SUBMIT_EMAIL) {
				$err_msg = "No email address given.";
				return false; 
			} else {
				$SubmitEmail = "anonymous";
			}
		}
		*/

		$Approved = 0;
		if($this->AUTOAPPROVE) { $Approved = 1; }

		$sql = "UPDATE $this->LNK_TBL SET ";
		$sql .= "CatID=$CatID,";
		$sql .= "Url='$Url',";
		$sql .= "LinkName='$LinkName',";
		$sql .= "Description='$Description',";
		$sql .= "SubmitName='$SubmitName',";
		$sql .= "SubmitEmail='$SubmitEmail',";
		$sql .= "SubmitDate='$SubmitDate',";
		$sql .= "Approved=$Approved";
		$sql .= " WHERE LinkID='$LinkID'";
		$results = $this->sql_query($sql);
		return $results;
	}

	function approve ($LinkID="",&$err_msg)
	{
		$err_msg="";

		if(empty($LinkID)) { 
			$err_msg = "No LinkID given.";
			return false; 
		}

		$sql = "UPDATE $this->LNK_TBL SET Approved=1 WHERE LinkID='$LinkID'";
		$results = $this->sql_query($sql);
		return $results;
	}

	function disapprove ($LinkID="",&$err_msg)
	{
		$err_msg="";

		if(empty($LinkID)) { 
			$err_msg = "No LinkID given.";
			return false; 
		}

		$sql = "UPDATE $this->LNK_TBL SET Approved=0 WHERE LinkID='$LinkID'";
		$results = $this->sql_query($sql);
		return $results;
	}

	function delete_link ($LinkID="",&$err_msg)
	{
		$err_msg="";

		if(empty($LinkID)) {
			// print_error();
			$err_msg = "No LinkID given.";
			return false;
		}

		$sql = "DELETE FROM $this->LNK_TBL WHERE LinkID='$LinkID'";
		$results = $this->sql_query($sql);
		return $results;
	}

	function add_cat ($postData="",&$err_msg)
	{

		$err_msg="";

		if( (empty($postData)) or (!is_array($postData)) ) { 
			$err_msg = "No data submitted or not an array of data";
			return false; 
		}

		$CatParent = $postData["CatID"];
		if (empty($CatParent) || ($CatParent == "0") || ($CatParent == "top")) {
			$CatParent = "NULL";
		}
		$CatName = addslashes($postData["NewCatName"]);

		if(empty($CatName)) { 
			$err_msg = "No new category name given.";
			return false; 
		}

		$sql = "INSERT INTO $this->CAT_TBL ";
		$sql .= "(CatID, course_id, CatName,CatParent) ";
		$sql .= "values ";
		$sql .= "(0, $_SESSION[course_id], '$CatName',$CatParent) ";
		$results = $this->insert($sql);
		return $results;
	}

	function get_approved_cnt ()
	{
		$sql = "select count(*) from $this->LNK_TBL L, $this->CAT_TBL C where L.approved=1 AND L.CatID=C.CatID AND C.course_id=$_SESSION[course_id]";
		$results = $this->sql_cnt_query($sql);
		return $results;
	}

	function get_not_approved_cnt ()
	{
		$sql = "select count(*) from $this->LNK_TBL L, $this->CAT_TBL C where L.approved=0 AND L.CatID=C.CatID AND C.course_id=$_SESSION[course_id]";
		$results = $this->sql_cnt_query($sql);
		return $results;
	}

	// Return number of approved links in a specific category
	function get_LinksInCat_cnt($CatID="")
	{
		if(empty($CatID)) { return 0; }
		$sql = "select count(*) from $this->LNK_TBL where CatID=$CatID and approved=1";
		$results = $this->sql_cnt_query($sql);
		return $results;
	}

	// Return number of subcategories in a specific category
	function get_CatsInCat_cnt($CatID="")
	{
		if(empty($CatID)) { return 0; }
		$sql = "SELECT COUNT(*) FROM $this->CAT_TBL WHERE CatParent=$CatID AND course_id=$_SESSION[course_id]";
		$results = $this->sql_cnt_query($sql);
		return $results;
	}

	// Watch out: another recursive query!
	// Returns the total number of links in the category and all subcategories thereof.
	function get_TotalLinksInCat_cnt($CatID='')
	{
		if(empty($CatID) || ($CatID == "0")) { return "0"; }
		$sum = 0;
		
		// Sum all subcategories from here

		$sql = "SELECT * from $this->CAT_TBL where CatParent = $CatID AND course_id=$_SESSION[course_id]";
		$conn = $this->CONN;
		$results = mysql_query($sql,$conn);
		if( (!$results) or (empty($results)) ) {
			mysql_free_result($results);
			return ($sum);
		}

		while ($row = mysql_fetch_array($results))
		{
			$id = $row["CatID"];
			$sum = $sum + $this->get_TotalLinksInCat_cnt($id);
		}

		// Then add this category

		$sum = $sum + $this->get_LinksInCat_cnt($CatID);

		return ($sum);
	}

}	//	End Class
?>