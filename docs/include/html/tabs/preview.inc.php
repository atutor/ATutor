	<tr>
		<td colspan="2" valign="top" align="left" class="row1"><?php 
		
		if ($_POST['text']) {
			echo format_content($_POST['text'], $_POST['formatting']);
		} else if ($row['text']) {
			echo format_content($row['text'], $row['formatting']);
		} else { 
			$infos[] = AT_INFOS_NO_PAGE_CONTENT;
			print_infos($infos);
	
		} ?>
		</td>
	</tr>