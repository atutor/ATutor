<br/>
<table class="data static" summary="" style="width: 95%" rules="cols">
<thead>
<tr>
	<th scope="col"><?php echo _AT('question'); ?></th>
	<th scope="col"><?php echo _AT('left_blank'); ?></th>
	<th scope="col"><?php echo _AT('results'); ?></th>
</tr>
</thead>
<tr>
	<td><?php echo $this->row['question']; ?></td>
	<td align="center" width="70" valign="top"><?php echo $this->num_blanks; ?> / <?php echo $this->num_results; ?></td>
	<td align="center" valign="top">
		<?php if ((count($this->answers) - (isset($this->answers[''])?1:0)) > 0): ?>
			<a href="tools/tests/results_quest_long.php?tid=<?php echo intval($_GET['tid']).SEP; ?>qid=<?php echo $this->row['question_id'].SEP.'q='.urlencode($this->row['question']); ?>"><?php echo _AT('view_responses'); ?> (<?php echo (count($this->answers) - (isset($this->answers[''])?1:0)); ?>)</a>
		<?php else: ?>
			<?php echo _AT('none'); ?>		
		<?php endif; ?>
	</td>
</tr>
</table>