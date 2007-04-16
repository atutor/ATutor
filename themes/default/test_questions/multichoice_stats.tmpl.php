<br />
<table class="data static" summary="" style="width: 95%" rules="cols">
<thead>
<tr>
	<th scope="col" width="40%"><?php echo _AT('question'); ?></th>
	<th scope="col" nowrap="nowrap"><?php echo _AT('left_blank'); ?></th>
	<?php for ($i=0; $i<$this->num_choices; $i++): ?>
		<?php if (strlen($this->row['choice_'.$i]) > 15) {
			$this->row['choice_'.$i] = substr($this->row['choice_'.$i], 0, 15).'...';
		}
		?>
		<?php if ($this->row['answer_'.$i]): ?>
			<th scope="col"><?php echo htmlspecialchars($this->row['choice_'.$i]); ?><img src="images/checkmark.gif" alt="" /></th>
		<?php else: ?>
			<th scope="col"><?php echo htmlspecialchars($this->row['choice_'.$i]); ?></th>
		<?php endif; ?>
	<?php endfor; ?>
</tr>
</head>
<tr>
	<td valign="top" rowspan="2"><?php echo $this->row['question']; ?></td>
	<td align="center" valign="top"><?php echo $this->num_blanks; ?> / <?php echo $this->num_results; ?></td>
	<?php for ($j=0; $j< $this->num_choices; $j++): ?>
		<td align="center" valign="top"><?php echo (int) $this->answers[$j]['count']; ?> / <?php echo $this->num_results; ?></td>
	<?php endfor; ?>
</tr>
<tr>
	<td align="center"><?php echo $this->num_results ? round($this->num_blanks/$this->num_results*100) : 0; ?>%</td>
	<?php for ($j=0; $j< $this->num_choices; $j++): ?>
		<td align="center" valign="top"><?php echo $this->num_results ? round($this->answers[$j]['count']/$this->num_results*100) : 0; ?>%</td>
	<?php endfor; ?>
</tr>
</table>