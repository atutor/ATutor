<div>
	<table>
		<thead>			
			<th><?php echo _AT('id'); ?></th>
			<th><?php echo _AT('jb_title'); ?></th>
			<th><?php echo _AT('jb_employer'); ?></th>
			<th><?php echo _AT('jb_categories'); ?></th>
			<th><?php echo _AT('description'); ?></th>
			<th><?php echo _AT('created_date'); ?></th>
			<th><?php echo _AT('jb_closing_date'); ?></th>			
		</thead>

		<tbody>
			<?php 
				if (!empty($this->job_posts)):
					foreach ($this->job_posts as $row): 
						if (!empty($this->bookmark_posts) && in_array($row['id'], $this->bookmark_posts)){
							$bookmark_icon = '*';
						} else {
							$bookmark_icon = '';
						}
			?>
			<tr>
				<td><a href="<?php echo AT_JB_BASENAME.'view_post.php?jid='.$row['id'];?>" title="<?php echo _AT('jb_view_job_post'); ?>"><?php echo $bookmark_icon . $row['id']; ?></a></td>
				<td><a href="<?php echo AT_JB_BASENAME.'view_post.php?jid='.$row['id'];?>" title="<?php echo $row['title']; ?>"><?php echo $row['title']; ?></a></td>
				<td><?php 
						$employer = new Employer($row['employer_id']);
						echo $employer->getName(); 
					?>
				</td>
				<td>
				<?php if(is_array($row['categories'])):
						foreach($row['categories'] as $category): 
				?>
				<span><?php echo $this->job_obj->getCategoryNameById($category);?></span> ; 
				<?php endforeach; else: ?>
				<span><?php echo $this->job_obj->getCategoryNameById($row['categories']);?></span>
				<?php endif; ?>
				</td>
				<td><?php echo $row['description']; ?></td>				
				<td><?php echo $row['created_date']; ?></td>
				<td><?php echo $row['closing_date']; ?></td>
			</tr>
			<?php endforeach; endif; ?>
		</tbody>
	</table>
</div>