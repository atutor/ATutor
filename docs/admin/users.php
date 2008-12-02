<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$
define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_USERS);

if ( (isset($_GET['edit']) || isset($_GET['password']) || isset($_GET['enrollment'])) && (isset($_GET['id']) && count($_GET['id']) > 1) ) {
	$msg->addError('SELECT_ONE_ITEM');
} else if (isset($_GET['edit'], $_GET['id'])) {
	header('Location: edit_user.php?id='.$_GET['id'][0]);
	exit;
} else if (isset($_GET['password'], $_GET['id'])) {
	header('Location: password_user.php?id='.$_GET['id'][0]);
	exit;
} else if (isset($_GET['enrollment'], $_GET['id'])) {
	header('Location: user_enrollment.php?id='.$_GET['id'][0]);
	exit;
} else if ( isset($_GET['apply']) && isset($_GET['id']) && $_GET['change_status'] >= -1) {
	$ids = implode(',', $_GET['id']);
	$status = intval($_GET['change_status']);
	if ($status == -1) {
		header('Location: admin_delete.php?id='.$ids);
		exit;
	} else {
		header('Location: user_status.php?ids='.$ids.'&status='.$status);
		exit;
	}
} else if ( (isset($_GET['apply']) || isset($_GET['apply_all'])) && $_GET['change_status'] < -1) {
	$msg->addError('NO_ACTION_SELECTED');
} else if (isset($_GET['apply']) || isset($_GET['edit']) || isset($_GET['delete']) || isset($_GET['password'])) {
	$msg->addError('NO_ITEM_SELECTED');
}

if ($_GET['reset_filter']) {
	unset($_GET);
}

$page_string = '';
$orders = array('asc' => 'desc', 'desc' => 'asc');
$cols   = array('login' => 1, 'public_field' => 1, 'first_name' => 1, 'second_name' => 1, 'last_name' => 1, 'email' => 1, 'status' => 1, 'last_login' => 1);

if (isset($_GET['asc'])) {
	$order = 'asc';
	$col   = isset($cols[$_GET['asc']]) ? $_GET['asc'] : 'login';
} else if (isset($_GET['desc'])) {
	$order = 'desc';
	$col   = isset($cols[$_GET['desc']]) ? $_GET['desc'] : 'login';
} else {
	// no order set
	$order = 'asc';
	$col   = 'login';
}
if (isset($_GET['status']) && ($_GET['status'] != '')) {
	$_GET['status'] = intval($_GET['status']);
	$status = '=' . intval($_GET['status']);
	$page_string .= SEP.'status'.$status;
} else {
	$status = '<>-1';
	$_GET['status'] = '';
}

if (isset($_GET['last_login_days'], $_GET['last_login_have']) && ($_GET['last_login_have'] >= 0) && $_GET['last_login_days']) {
	$have = intval($_GET['last_login_have']);
	$days = intval($_GET['last_login_days']);
	$page_string .= SEP.'last_login_have='.$have;
	$page_string .= SEP.'last_login_days='.$days;

	if ($have) {
		$ll =  " >= TO_DAYS(NOW())-$days)";
	} else {
		$ll =  " < TO_DAYS(NOW())-$days OR last_login+0=0)";
	}
	$last_login_days = '(TO_DAYS(last_login)'.$ll;
} else {
	$last_login_days = '1';
}

if (isset($_GET['include']) && $_GET['include'] == 'one') {
	$checked_include_one = ' checked="checked"';
	$page_string .= SEP.'include=one';
} else {
	$_GET['include'] = 'all';
	$checked_include_all = ' checked="checked"';
	$page_string .= SEP.'include=all';
}

if ($_GET['search']) {
	$page_string .= SEP.'search='.urlencode($stripslashes($_GET['search']));
	$search = $addslashes($_GET['search']);
	$search = explode(' ', $search);

	if ($_GET['include'] == 'all') {
		$predicate = 'AND ';
	} else {
		$predicate = 'OR ';
	}

	$sql = '';
	foreach ($search as $term) {
		$term = trim($term);
		$term = str_replace(array('%','_'), array('\%', '\_'), $term);
		if ($term) {
			$term = '%'.$term.'%';
			$sql .= "((M.first_name LIKE '$term') OR (M.second_name LIKE '$term') OR (M.last_name LIKE '$term') OR (M.email LIKE '$term') OR (M.login LIKE '$term')) $predicate";
		}
	}
	$sql = '('.substr($sql, 0, -strlen($predicate)).')';
	$search = $sql;
} else {
	$search = '1';
}

if ($_GET['searchid']) {
	$_GET['searchid'] = trim($_GET['searchid']);
	$page_string .= SEP.'searchid='.urlencode($_GET['searchid']);
	$searchid = $addslashes($_GET['searchid']);

	$searchid = explode(',', $searchid);

	$sql = '';
	foreach ($searchid as $term) {
		$term = trim($term);
		$term = str_replace(array('%','_'), array('\%', '\_'), $term);
		if ($term) {
			if (strpos($term, '-') === FALSE) {
				$term = '%'.$term.'%';
				$sql .= "(L.public_field LIKE '$term') OR ";
			} else {
				// range search
				$range = explode('-', $term, 2);
				$range[0] = trim($range[0]);
				$range[1] = trim($range[1]);
				if (is_numeric($range[0]) && is_numeric($range[1])) {
					$sql .= "(L.public_field >= $range[0] AND L.public_field <= $range[1]) OR ";
				} else {
					$sql .= "(L.public_field >= '$range[0]' AND L.public_field <= '$range[1]') OR ";
				}
			}
		}
	}
	$sql = '('.substr($sql, 0, -3).')';
	$searchid = $sql;
} else {
	$searchid = '1';
}

if (defined('AT_MASTER_LIST') && AT_MASTER_LIST) {
	$sql	= "SELECT COUNT(M.member_id) AS cnt FROM ".TABLE_PREFIX."members M LEFT JOIN (SELECT * FROM ".TABLE_PREFIX."master_list WHERE member_id <> 0) L USING (member_id) WHERE M.status $status AND $search AND $searchid AND $last_login_days";
} else {
	$sql	= "SELECT COUNT(member_id) AS cnt FROM ".TABLE_PREFIX."members M WHERE status $status AND $search AND $last_login_days";
}

$result = mysql_query($sql, $db);
$row = mysql_fetch_assoc($result);
$num_results = $row['cnt'];

$results_per_page = 50;
$num_pages = max(ceil($num_results / $results_per_page), 1);
$page = intval($_GET['p']);
if (!$page) {
	$page = 1;
}	
$count  = (($page-1) * $results_per_page) + 1;
$offset = ($page-1)*$results_per_page;

if ( isset($_GET['apply_all']) && $_GET['change_status'] >= -1) {
	$offset = 0;
	$results_per_page = 999999;
}

if (defined('AT_MASTER_LIST') && AT_MASTER_LIST) {
	$sql	= "SELECT M.member_id, M.login, M.first_name, M.second_name, M.last_name, M.email, M.status, M.last_login+0 AS last_login, L.public_field FROM ".TABLE_PREFIX."members M LEFT JOIN (SELECT * FROM ".TABLE_PREFIX."master_list WHERE member_id <> 0) L USING (member_id) WHERE M.status $status AND $search AND $searchid AND $last_login_days ORDER BY $col $order LIMIT $offset, $results_per_page";
} else {
	$sql	= "SELECT M.member_id, M.login, M.first_name, M.second_name, M.last_name, M.email, M.status, M.last_login+0 AS last_login FROM ".TABLE_PREFIX."members M WHERE M.status $status AND $search AND $last_login_days ORDER BY $col $order LIMIT $offset, $results_per_page";
}

$result = mysql_query($sql, $db);

if ( isset($_GET['apply_all']) && $_GET['change_status'] >= -1) {
	$ids = '';
	while ($row = mysql_fetch_assoc($result)) {
		$ids .= $row['member_id'].','; 
	}
	$ids = substr($ids,0,-1);
	$status = intval($_GET['change_status']);

	if ($status==-1) {
		header('Location: admin_delete.php?id='.$ids);
		exit;
	} else {
		header('Location: user_status.php?ids='.$ids.'&status='.$status);
		exit;
	}
}
require(AT_INCLUDE_PATH.'header.inc.php');

?>
<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<div class="input-form">
		<div class="row">
			<h3><?php echo _AT('results_found', $num_results); ?></h3>
		</div>

		<div class="row">
			<?php echo _AT('account_status'); ?><br />
			<input type="radio" name="status" value="0" id="s0" <?php if ($_GET['status'] == 0) { echo 'checked="checked"'; } ?> /><label for="s0"><?php echo _AT('disabled'); ?></label> 

			<input type="radio" name="status" value="1" id="s1" <?php if ($_GET['status'] == 1) { echo 'checked="checked"'; } ?> /><label for="s1"><?php echo _AT('unconfirmed'); ?></label> 

			<input type="radio" name="status" value="2" id="s2" <?php if ($_GET['status'] == 2) { echo 'checked="checked"'; } ?> /><label for="s2"><?php echo _AT('student'); ?></label>

			<input type="radio" name="status" value="3" id="s3" <?php if ($_GET['status'] == 3) { echo 'checked="checked"'; } ?> /><label for="s3"><?php echo _AT('instructor'); ?></label>

			<input type="radio" name="status" value="" id="s" <?php if ($_GET['status'] === '') { echo 'checked="checked"'; } ?> /><label for="s"><?php echo _AT('all'); ?></label>
		</div>

		<div class="row">
			<label for="search"><?php echo _AT('search'); ?> (<?php echo _AT('login_name').', '._AT('first_name').', '._AT('second_name').', '._AT('last_name') .', '._AT('email'); ?>)</label><br />

			<input type="text" name="search" id="search" size="40" value="<?php echo htmlspecialchars($_GET['search']); ?>" />
			<br/>
			<?php echo _AT('search_match'); ?>:
			<input type="radio" name="include" value="all" id="match_all" <?php echo $checked_include_all; ?> /><label for="match_all"><?php echo _AT('search_all_words'); ?></label> 
			<input type="radio" name="include" value="one" id="match_one" <?php echo $checked_include_one; ?> /><label for="match_one"><?php echo _AT('search_any_word'); ?></label>
		</div>

		<?php if (defined('AT_MASTER_LIST') && AT_MASTER_LIST): ?>
			<div class="row">
				<label for="searchid"><?php echo _AT('search'); ?> (<?php echo _AT('student_id'); ?>)</label><br />
				<input type="text" name="searchid" id="searchid" size="20" value="<?php echo htmlspecialchars($_GET['searchid']); ?>" />
			</div>
		<?php endif; ?>

		<div class="row">
			<label for="last_login_have"><?php echo _AT('last_login'); ?></label><br />					
			<select name="last_login_have" id="last_login_have">
				<option value="-1">- <?php echo _AT('select'); ?> -</option>
				<option value="1" <?php if($_GET['last_login_have']=='1') { echo 'selected="selected"';}?>><?php echo _AT('have'); ?></option>
				<option value="0" <?php if(isset($_GET['last_login_have']) && $_GET['last_login_have']=='0') { echo 'selected="selected"';}?>><?php echo _AT('have_not'); ?></option>
			</select> <?php echo _AT('logged_in_within'); ?>: <input type="text" name="last_login_days" size="3" value="<?php echo htmlspecialchars($_GET['last_login_days']); ?>" /> <?php echo _AT('days'); ?> <br />
			
		</div>

		<div class="row buttons">
			<input type="submit" name="filter" value="<?php echo _AT('filter'); ?>" />
			<input type="submit" name="reset_filter" value="<?php echo _AT('reset_filter'); ?>" />
		</div>
	</div>
</form>

<?php print_paginator($page, $num_results, $page_string . SEP . $order .'='. $col, $results_per_page); ?>

<form name="form" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="status" value="<?php echo $_GET['status']; ?>" />
<input type="hidden" name="search" value="<?php echo htmlspecialchars($_GET['search']); ?>" />
<input type="hidden" name="include" value="<?php echo htmlspecialchars($_GET['include']); ?>" />

<?php if (defined('AT_MASTER_LIST') && AT_MASTER_LIST) {  $col_counts = 1; } else { $col_counts = 0; } ?>
<table summary="" class="data" rules="rows">
<colgroup>
	<?php if ($col == 'login'): ?>
		<col />
		<col class="sort" />
		<col span="<?php echo 5 + $col_counts; ?>" />
	<?php elseif($col == 'public_field'): ?>
		<col span="<?php echo 1 + $col_counts; ?>" />
		<col class="sort" />
		<col span="6" />
	<?php elseif($col == 'first_name'): ?>
		<col span="<?php echo 2 + $col_counts; ?>" />
		<col class="sort" />
		<col span="5" />
	<?php elseif($col == 'second_name'): ?>
		<col span="<?php echo 3 + $col_counts; ?>" />
		<col class="sort" />
		<col span="4" />
	<?php elseif($col == 'last_name'): ?>
		<col span="<?php echo 4 + $col_counts; ?>" />
		<col class="sort" />
		<col span="3" />
	<?php elseif($col == 'email'): ?>
		<col span="<?php echo 5 + $col_counts; ?>" />
		<col class="sort" />
		<col span="2" />
	<?php elseif($col == 'status'): ?>
		<col span="<?php echo 6 + $col_counts; ?>" />
		<col class="sort" />
		<col />
	<?php elseif($col == 'last_login'): ?>
		<col span="<?php echo 7 + $col_counts; ?>" />
		<col class="sort" />
	<?php endif; ?>
</colgroup>
<thead>
<tr>
	<th scope="col" align="left"><input type="checkbox" value="<?php echo _AT('select_all'); ?>" id="all" title="<?php echo _AT('select_all'); ?>" name="selectall" onclick="CheckAll();" /></th>

	<th scope="col"><a href="admin/users.php?<?php echo $orders[$order]; ?>=login<?php echo $page_string; ?>"><?php echo _AT('login_name');      ?></a></th>
	<?php if (defined('AT_MASTER_LIST') && AT_MASTER_LIST): ?>
		<th scope="col"><a href="admin/users.php?<?php echo $orders[$order]; ?>=public_field<?php echo $page_string; ?>"><?php echo _AT('student_id'); ?></a></th>
	<?php endif; ?>
	<th scope="col"><a href="admin/users.php?<?php echo $orders[$order]; ?>=first_name<?php echo $page_string; ?>"><?php echo _AT('first_name'); ?></a></th>
	<th scope="col"><a href="admin/users.php?<?php echo $orders[$order]; ?>=second_name<?php echo $page_string; ?>"><?php echo _AT('second_name'); ?></a></th>
	<th scope="col"><a href="admin/users.php?<?php echo $orders[$order]; ?>=last_name<?php echo $page_string; ?>"><?php echo _AT('last_name');   ?></a></th>
	<th scope="col"><a href="admin/users.php?<?php echo $orders[$order]; ?>=email<?php echo $page_string; ?>"><?php echo _AT('email');           ?></a></th>
	<th scope="col"><a href="admin/users.php?<?php echo $orders[$order]; ?>=status<?php echo $page_string; ?>"><?php echo _AT('account_status'); ?></a></th>
	<th scope="col"><a href="admin/users.php?<?php echo $orders[$order]; ?>=last_login<?php echo $page_string; ?>"><?php echo _AT('last_login'); ?></a></th>
</tr>

</thead>
<?php if ($num_results > 0): ?>
	<tfoot>
	<tr>
		<td colspan="<?php echo 8 + $col_counts; ?>">
			<input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" /> 
			<input type="submit" name="password" value="<?php echo _AT('password'); ?>" />
			<?php if (admin_authenticate(AT_ADMIN_PRIV_ENROLLMENT, true)): ?>
				<input type="submit" name="enrollment" value="<?php echo _AT('enrollment'); ?>" />
			<?php endif; ?>
			<span style="padding:0px 10px">|</span> 
			
			<select name="change_status">
				<option value="-2"><?php echo _AT('more_options'); ?></option>
				<optgroup label="<?php echo _AT('status'); ?>">
					<option value="<?php echo AT_STATUS_STUDENT; ?>"><?php echo _AT('student'); ?></option>
					<option value="<?php echo AT_STATUS_INSTRUCTOR; ?>"><?php echo _AT('instructor'); ?></option>	
					<?php if ($_config['email_confirmation']): ?>
						<option value="<?php echo AT_STATUS_UNCONFIRMED; ?>"><?php echo _AT('unconfirmed'); ?></option>
					<?php endif; ?>
					<option value="<?php echo AT_STATUS_DISABLED; ?>"><?php echo _AT('disable'); ?></option>				
				</optgroup>
				<option value="-2" disabled="disabled">- - - - - - - - -</option>	
				<option value="-1"><?php echo _AT('delete'); ?></option>				
			</select>
			<input type="submit" name="apply" value="<?php echo _AT('apply'); ?>" />
			<input type="submit" name="apply_all" value="<?php echo _AT('apply_to_all_results'); ?>" />
		</td>
	</tr>
	</tfoot>
	<tbody>
		<?php while($row = mysql_fetch_assoc($result)): ?>
			<tr onmousedown="document.form['m<?php echo $row['member_id']; ?>'].checked = !document.form['m<?php echo $row['member_id']; ?>'].checked; togglerowhighlight(this, 'm<?php echo $row['member_id']; ?>');" id="rm<?php echo $row['member_id']; ?>">
				<td><input type="checkbox" name="id[]" value="<?php echo $row['member_id']; ?>" id="m<?php echo $row['member_id']; ?>" onmouseup="this.checked=!this.checked" /></td>
				<td><?php echo $row['login']; ?></td>
				<?php if (defined('AT_MASTER_LIST') && AT_MASTER_LIST): ?>
					<td><?php echo $row['public_field']; ?></td>
				<?php endif; ?>

				<td><?php echo AT_print($row['first_name'], 'members.first_name'); ?></td>
				<td><?php echo AT_print($row['second_name'], 'members.second_name'); ?></td>
				<td><?php echo AT_print($row['last_name'], 'members.last_name'); ?></td>
				<td><?php echo AT_print($row['email'], 'members.email'); ?></td>
				<td><?php echo get_status_name($row['status']); ?></td>
				<td nowrap="nowrap">
					<?php if ($row['last_login'] == 0): ?>
						<?php echo _AT('never'); ?>
					<?php else: ?>
						<?php 
						
						$startend_date_longs_format=_AT('startend_date_longs_format');
						//echo AT_date('%d/%m/%y - %H:%i', $row['last_login'], AT_DATE_MYSQL_TIMESTAMP_14);
						echo AT_date($startend_date_longs_format, $row['last_login'], AT_DATE_MYSQL_TIMESTAMP_14); 
					?>
					<?php endif; ?>
				</td>
			</tr>
		<?php endwhile; ?>
	</tbody>
<?php else: ?>
	<tr>
		<td colspan="<?php echo 8 + $col_counts; ?>"><?php echo _AT('none_found'); ?></td>
	</tr>
<?php endif; ?>
</table>
</form>
<script language="JavaScript" type="text/javascript">
//<!--
function CheckAll() {
	for (var i=0;i<document.form.elements.length;i++)	{
		var e = document.form.elements[i];
		if ((e.name == 'id[]') && (e.type=='checkbox')) {
			e.checked = document.form.selectall.checked;
			togglerowhighlight(document.getElementById("r" + e.id), e.id);
		}
	}
}

function togglerowhighlight(obj, boxid) {
	if (document.getElementById(boxid).checked) {
		obj.className = 'selected';
	} else {
		obj.className = '';
	}
}
//-->
</script>
<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>