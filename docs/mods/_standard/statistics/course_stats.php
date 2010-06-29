<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2010                                      */
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: course_stats.php 7208 2008-01-09 16:07:24Z greg $

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_ADMIN, AT_PRIV_RETURN);

$year  = intval($_GET['year']);
$month = intval($_GET['month']);

	if ($month == 0) {
		$month = date('m');
		$year  = date('Y');
	}

	$days	= array();
	$sql	= "SELECT * FROM ".TABLE_PREFIX."course_stats WHERE course_id=$_SESSION[course_id] AND MONTH(login_date)=$month AND YEAR(login_date)=$year ORDER BY login_date ASC";
	$result = mysql_query($sql, $db);
	//$today  = 1; /* we start on the 1st of the month */
	$max_total_logins = 0;
	$min_total_logins = (int) 99999999;
	$total_logins = 0;

	$empty = true;
	while ($row = mysql_fetch_array($result)) {
		$empty = false;
		$row_day = substr($row['login_date'], 8, 2);

		if (substr($row_day, 0,1) == '0') {
			$row_day = substr($row_day, 1, 1);
		}
		
		while ($today < $row_day-1) {
			$today++;
			$days[$today] = array(0, 0);
			$min_total_logins = 0;
		}

		$today = $row_day; /* skip this day in the fill-in-the-blanks-loop */
				
		$days[$row_day] = array($row['guests'], $row['members']);

		if ($max_total_logins < $row['guests']+$row['members']) {
			$max_total_logins = $row['guests']+$row['members'];
		}

		if ($min_total_logins > $row['guests']+$row['members']) {
			$min_total_logins = $row['guests']+$row['members'];
		}

		$total_logins += $row['guests']+$row['members'];
	}

	/* add zeros to the end of the month, only if it isn't the current month */
	$now_month = date('m');
	$now_year  = date('Y');
	if ( (($month < $now_month) && ($now_year == $year)) || ($now_year < $year) ) {
		$today++;
		while (checkdate($month, $today,$year)) {
			$days[$today] = array(0, 0);
			$today++;
		}
	}
	$num_days = count($days);

	if ($total_logins > 0) {
		$avg_total_logins = $total_logins/$num_days;
	} else {
		$avg_total_logins = 0;
	}

	$block_height		= 10;
	$multiplyer_height  = 5; /* should be multiples of 5 */

	if ($month == 12) {
		$next_month = 1;
		$next_year  = $year + 1;
	} else {
		$next_month = $month + 1;
		$next_year  = $year;
	}

	if ($month == 1) {
		$last_month = 12;
		$last_year  = $year - 1;
	} else {
		$last_month = $month - 1;
		$last_year  = $year;
	}

require(AT_INCLUDE_PATH.'header.inc.php');

?>
	<table cellspacing="1" cellpadding="1" border="0" class="bodyline" summary="" align="center">
	<tr>
		<th colspan="2" class="cyan"><small class="bigspacer"><?php
			echo '<a href="'.$_SERVER['PHP_SELF'].'?month='.($last_month).SEP.'year='.$last_year.'">';
			echo ' '.AT_date('%F', $last_month, AT_DATE_INDEX_VALUE ); ?></a> |</small>
			<?php echo AT_date('%F', $month, AT_DATE_INDEX_VALUE ); ?> <small class="bigspacer">| <?php
			echo '<a href="'.$_SERVER['PHP_SELF'].'?month='.$next_month.SEP.'year='.$next_year.'">';
			echo AT_date('%F', $next_month, AT_DATE_INDEX_VALUE); ?> </a></small></th>
	</tr>
<?php
		if (($num_days == 0) || ($empty)) {
			echo '<tr>';
			echo '<td class="row1" colspan="2">'._AT('no_month_data').'</td>';
			echo '</tr>';
			echo '</table>';
			require(AT_INCLUDE_PATH.'footer.inc.php');
			exit;
		}
?>
	<tr>
		<td class="row1" valign="top" align="right"><strong><?php echo _AT('total'); ?>:</strong></td>
		<td class="row1"><?php echo $total_logins; ?></td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1" valign="top" align="right"><strong><?php echo _AT('maximum'); ?>:</strong></td>
		<td class="row1"><?php echo $max_total_logins; ?></td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>

	<tr>
		<td class="row1" valign="top" align="right"><strong><?php echo _AT('minimum'); ?>:</strong></td>
		<td class="row1"><?php
		if ($min_total_logins < 99999999) {
			echo $min_total_logins; 
		} else {
			echo '0';
		} ?></td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1" valign="top" align="right"><strong><?php   echo _AT('average'); ?>:</strong></td>
		<td class="row1"><?php echo number_format($avg_total_logins, 1); ?> <?php   echo _AT('per_day'); ?></td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>

	<tr>
		<td class="row1" valign="top" align="right"><strong><?php   echo _AT('graph'); ?>:</strong></td>
		<td class="row1">
			<table border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td valign="top" class="graph1"><small><?php echo $max_total_logins; ?></small></td>

<?php
			foreach ($days as $day => $logins) {
			$dd++;
				echo '<td valign="bottom" class="graph"><img src="images/clr.gif" height="'.(($max_total_logins*$multiplyer_height) % $block_height + $block_height).'" width="10" alt="" /><br /><img src="images/blue.gif" height="'.($logins[0]*$multiplyer_height).'" width="9" alt="'.$logins[0].' '._AT('guests').' ('.($logins[0]+$logins[1]).' '._AT('total').')" /><br /><img src="images/red.gif" height="'.($logins[1]*$multiplyer_height).'" width="9" alt="'.$logins[1].' '._AT('members').' ('.($logins[1]+$logins[0]).' '._AT('total').')" /><br /><small>'.$dd.'&nbsp;</small></td>';

			} while ($row = mysql_fetch_array($result));
?>

			</tr>
			<tr>
				<td valign="top"><small>0</small></td>
			</tr>
			</table>

			<small><?php  echo _AT('legend'); ?>: <img src="images/red.gif" height="10" width="10" alt="<?php echo _AT('red_members'); ?>" /> <?php   echo _AT('members'); ?>,
				<img src="images/blue.gif" height="10" width="10" alt="<?php echo _AT('blue_guests'); ?>" /> <?php echo _AT('guests'); ?>.</small>
		</td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1" valign="top" align="right"><strong><?php echo _AT('raw_data'); ?>:</strong></td>
		<td class="row1" align="center">
	
		<table class="data static" summary="" rules="cols">
		<thead>
		<tr>
			<th scope="col"><?php echo _AT('date');    ?></th>
			<th scope="col"><?php echo _AT('guests');  ?></th>
			<th scope="col"><?php echo _AT('members'); ?></th>
		</tr>
		</thead>
		<tbody>
		<?php $short_name = $month_name_con['en'][$month-1]; ?>
		<?php foreach ($days as $day => $logins):?>
			<tr>
				<td><?php echo $short_name.' '.$day; ?></td>
				<td><?php echo $logins[0]; ?></td>
				<td><?php echo $logins[1]; ?></td>
			</tr>
		<?php endforeach; ?>
		<tbody>
		</table>

		</td>
	</tr>
	</table>
<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>