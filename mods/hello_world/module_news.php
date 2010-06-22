<?php
/*
* Rename the function to match the name of the module. Names of all news functions must be unique
* across all modules installed on a system. Return a variable called $news
*/

function helloworld_news() {
	$sql = "SELECT something FROM a table WHERE date < NOW() LIMIT 3";
	if($result = mysql_query($sql, $db){
	    while($row = mysql_fetch_assoc($result)){
		$news[] = $row['something'];
	     }
	}
	return $news;
}

?>
