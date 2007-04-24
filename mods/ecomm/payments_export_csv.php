<?php
exit;
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

$sql1 = stripslashes(urldecode($_GET['thisquery']));
$result1 = mysql_query($sql1,$db);
$today = date("Y-m-d_h-m-s");
$path = AT_CONTENT_DIR . 'payments_'.$today.'.csv';

function quote_csv($line) {
	$line = str_replace('"','""', $line);
	$line = str_replace(",", "", $line);
	//$line = str_replace("\n", '\n', $line);
	//$line = str_replace("\r", '\r', $line);
	$line = str_replace("\x00", '\0', $line);
	$line = trim($line);

	return '"'.$line.'"';
}
	$fp = fopen($path, 'w');
	$this_purchase .= ''; 
	while($row = mysql_fetch_array($result1)){
		$this_purchase .= quote_csv($row['shopid']);
		$this_purchase .= quote_csv($row['member_id']);
		$this_purchase .= quote_csv($row['firstname']);
		$this_purchase .= quote_csv($row['lastname']);
		$this_purchase .= quote_csv($row['email']);
		$this_purchase .= quote_csv($row['organization']);
		$this_purchase .= quote_csv($row['address']);
		$this_purchase .= quote_csv($row['postal']);
		$this_purchase .= quote_csv($row['telephone']);
		$this_purchase .= quote_csv($row['country']);
		$this_purchase .= quote_csv($row['miraid']);
		$this_purchase .= quote_csv($row['date']);
		$this_purchase .= quote_csv($row['course_name']);
		$this_purchase .= quote_csv($row['amount']);
		$this_purchase .= quote_csv($row['comments']);
		$this_purchase .= quote_csv($row['course_id'])."\r\n";
	}
	fputs($fp, $this_purchase);
	fclose($fp);

	header('Content-Type: application/x-excel');
	header('Content-Disposition: inline; filename="payments_'.$today.'.csv"');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
	echo $this_purchase;
	exit;

?>