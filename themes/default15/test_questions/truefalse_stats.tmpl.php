<br />
<table class="data static" summary="" style="width: 95%" rules="cols">
<thead>
<tr>
	<th scope="col" width="40%"><?php echo _AT('question'); ?></th>
	<th scope="col" nowrap="nowrap"><?php echo _AT('left_blank'); ?></th>
<?php if ($this->row['answer_0'] == 1): ?>
	<th scope="col"><?php echo _AT('true'); ?><img src="images/checkmark.gif" alt="Correct checkmark" /></th>
	<th scope="col"><?php echo _AT('false'); ?></th>
<?php elseif ($this->row['answer_0'] == 2): ?>
	<th scope="col"><?php echo _AT('true'); ?></th>
	<th scope="col"><?php echo _AT('false'); ?><img src="images/checkmark.gif" alt="Correct checkmark" /></th>
<?php else: ?>
	<th scope="col"><?php echo _AT('true'); ?></th>
	<th scope="col"><?php echo _AT('false'); ?></th>
<?php endif; ?>
</tr>
</thead>
<tr>
	<td valign="top" rowspan="2"><?php echo $this->row['question']; ?></td>
	<td align="center" valign="top"><?php echo $this->num_blanks; ?> / <?php echo $this->num_results; ?></td>
	<td align="center" valign="top"><?php echo $this->num_true; ?> / <?php echo $this->num_results; ?></td>
	<td align="center" valign="top"><?php echo $this->num_false; ?> / <?php echo $this->num_results; ?></td>
</tr>
<tr>
	<td align="center" valign="top"><?php echo $this->num_results ? round($this->num_blanks/$this->num_results*100) : 0; ?>%</td>
	<td align="center" valign="top"><?php echo $this->num_results ? round($this->num_true/$this->num_results*100) : 0; ?>%</td>
	<td align="center" valign="top"><?php echo $this->num_results ? round($this->num_false/$this->num_results*100) : 0; ?>%</td>
</tr>
</table>

