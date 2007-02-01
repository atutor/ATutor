<br/>
<table class="data static" summary="" style="width: 95%" rules="cols">
<thead>
<tr>
	<th scope="col" width="40%"><?php echo _AT('question'); ?></th>
	<?php for ($i=0; $i< $this->num_choices; $i++): ?>
		<?php
		if(strlen($q['choice_'.$i]) > 15) {
			$q['choice_'.$i] = substr($q['choice_'.$i], 0, 15).'...';
		}
		?>
		<th scope="col"><?php echo htmlspecialchars($this->row['choice_'.$i]); ?></th>
	<?php endfor; ?>
</tr>
</thead>
<tr>
	<td valign="top" rowspan="2"><?php echo $this->row['question']; ?></td>
	<?php for ($j=0; $j<$this->num_choices; $j++): ?>
		<td align="center" valign="top"><?php echo (int) $this->answers[$j]['count']; ?> / <?php echo $this->num_results; ?></td>
	<?php endfor; ?>
</tr>
<tr>
	<?php for ($j=0; $j<$this->num_choices; $j++): ?>
		<td align="center" valign="top"><?php echo $this->num_results ? round($this->answers[$j]['count']/$this->num_results*100) : 0; ?>%</td>
	<?php endfor; ?>
</tr>
</table>