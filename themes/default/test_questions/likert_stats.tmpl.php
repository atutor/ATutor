<br />
<table class="data static" summary="" style="width: 95%" rules="cols">
<thead>
<tr>
	<th scope="col" width="40%"><?php echo _AT('question');	?></th>
	<th scope="col"><?php echo _AT('left_blank'); ?></th>
	<th scope="col"><?php echo _AT('average'); ?></th>
	<?php for ($i=0; $i<$this->num_choices; $i++): ?>
		<th scope="col" title="<?php echo $this->row['choice_'.$i]; ?>"><?php echo ($i+1); ?></th>
	<?php endfor; ?>
</tr>
</thead>
<tr>
	<td valign="top" rowspan="2"><?php echo $this->row['question']; ?></td>
	<td align="center" width="70" valign="top"><?php echo $this->num_blanks;?> / <?php echo $this->num_results; ?></td>
	<td align="center" width="70" valign="top"><?php echo $this->average;?> / <?php echo $this->num_choices; ?></td>
	<?php for ($j=0; $j<$this->num_choices; $j++): ?>
		<td align="center" valign="top"><?php echo (int) $this->answers[$j]['count']; ?> / <?php echo $this->num_results; ?></td>
	<?php endfor; ?>
</tr>
<tr>
	<td align="center"><?php echo $this->num_results ? round($this->num_blanks/$this->num_results*100) : 0; ?>%</td>
	<td align="center">-</td>
	<?php for ($j=0; $j<$this->num_choices; $j++): ?>
		<td align="center" valign="top"><?php echo $this->num_results ? round($this->answers[$j]['count']/$this->num_results*100) : 0; ?>%</td>
	<?php endfor; ?>
</tr>
</table>