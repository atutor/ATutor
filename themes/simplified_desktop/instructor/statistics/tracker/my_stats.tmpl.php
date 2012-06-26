<?php global $contentManager;?>
<div class="table-surround">
<table class="data static" summary="">
<thead>
<tr>
	<th scope="col"><?php echo _AT('page'); ?></th>
	<th scope="col"><?php echo _AT('visits'); ?></th>
	<th scope="col"><?php echo _AT('duration'); ?></th>
	<th scope="col"><?php echo _AT('last_accessed'); ?></th>
</tr>
</thead>
<tbody>
<?php
	

	if (mysql_num_rows($this->result) > 0) {
		while ($row = mysql_fetch_assoc($this->result)) {
			if ($row['total'] == '') {
				$row['total'] = _AT('na');
			}

			echo '<tr>';
			echo '<td><a href='.AT_BASE_HREF.url_rewrite('content.php?cid='.$row['content_id']). '>' . $contentManager->_menu_info[$row['content_id']]['title'] . '</a></td>';
			echo '<td>' . $row['total_hits'] . '</td>';
			echo '<td>' . $row['total_duration'] . '</td>';
			if ($row['last_accessed'] == '') {
				echo '<td>' . _AT('na') . '</td>';
			} else {
				echo '<td>' . AT_date(_AT('forum_date_format'), $row['last_accessed'], AT_DATE_MYSQL_DATETIME) . '</td>';
			}
			echo '</tr>';
		} //end while

		echo '</tbody>';

	} else {
		echo '<tr><td colspan="4">' . _AT('none_found') . '</td></tr>';
		echo '</tbody>';
	}
	?>
</tbody>
</table>
</div>

