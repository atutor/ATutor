<table cellspacing="1" cellpadding="1" border="0" class="bodyline" summary="" align="center">
	<tr>
		<th colspan="2" class="cyan"><small class="bigspacer"><?php
			echo '<a href="'.$_SERVER['PHP_SELF'].'?month='.($this->last_month).SEP.'year='.$this->last_year.'">';
			echo ' '.AT_date('%F', $this->last_month, AT_DATE_INDEX_VALUE ); ?></a> |</small>
			<?php echo AT_date('%F', $this->month, AT_DATE_INDEX_VALUE ); ?> <small class="bigspacer">| <?php
			echo '<a href="'.$_SERVER['PHP_SELF'].'?month='.$this->next_month.SEP.'year='.$this->next_year.'">';
			echo AT_date('%F', $this->next_month, AT_DATE_INDEX_VALUE); ?> </a></small></th>
	</tr>
<?php
		if (($this->num_days == 0) || ($this->empty)) {
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
		<td class="row1"><?php echo $this->total_logins; ?></td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1" valign="top" align="right"><strong><?php echo _AT('maximum'); ?>:</strong></td>
		<td class="row1"><?php echo $this->max_total_logins; ?></td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>

	<tr>
		<td class="row1" valign="top" align="right"><strong><?php echo _AT('minimum'); ?>:</strong></td>
		<td class="row1"><?php
		if ($this->min_total_logins < 99999999) {
			echo $this->min_total_logins; 
		} else {
			echo '0';
		} ?></td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1" valign="top" align="right"><strong><?php   echo _AT('average'); ?>:</strong></td>
		<td class="row1"><?php echo number_format($this->avg_total_logins, 1); ?> <?php   echo _AT('per_day'); ?></td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>

	<tr>
		<td class="row1" valign="top" align="right"><strong><?php   echo _AT('graph'); ?>:</strong></td>
		<td class="row1">
			<table border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td valign="top" class="graph1"><small><?php echo $this->max_total_logins; ?></small></td>

<?php
			foreach ($this->days as $day => $logins) {
			$dd++;
				echo '<td valign="bottom" class="graph"><img src="images/clr.gif" height="'.(($this->max_total_logins*$multiplyer_height) % $block_height + $block_height).'" width="10" alt="" /><br /><img src="images/blue.gif" height="'.($logins[0]*$multiplyer_height).'" width="9" alt="'.$logins[0].' '._AT('guests').' ('.($logins[0]+$logins[1]).' '._AT('total').')" /><br /><img src="images/red.gif" height="'.($logins[1]*$multiplyer_height).'" width="9" alt="'.$logins[1].' '._AT('members').' ('.($logins[1]+$logins[0]).' '._AT('total').')" /><br /><small>'.$dd.'&nbsp;</small></td>';

			} while ($row = mysql_fetch_array($this->result));
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
		<?php foreach ($this->days as $day => $logins):?>
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