<?php if ($this->mobile_device_type != IPAD_DEVICE): ?>
<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<div class="input-form">
		
			<h3><?php echo _AT('results_found', $this->num_results); ?></h3>
		<div id="results-hide-show-simple-content" class="hide-show-container-surround">		
			<a id="results-hide-show-link" class="content-expand" href="javascript:void(0);" tabindex="1">Refine Results</a>
		</div>
	
		
	<div id="results-hide-show" role="search"  aria-live="assertive">
	<div id="results-display">
	
		<div class="row">
		<fieldset>
			<legend><?php echo _AT('access'); ?></legend>

			<input type="radio" name="access" value="0" id="s0" <?php if ($_GET['access'] == 0) { echo 'checked="checked"'; } ?> /><label for="s0"><?php echo _AT('public'); ?></label> 

			<input type="radio" name="access" value="1" id="s1" <?php if ($_GET['access'] == 1) { echo 'checked="checked"'; } ?> /><label for="s1"><?php echo _AT('protected'); ?></label> 

			<input type="radio" name="access" value="2" id="s2" <?php if ($_GET['access'] == 2) { echo 'checked="checked"'; } ?> /><label for="s2"><?php echo _AT('private'); ?></label>

			<input type="radio" name="access" value="" id="s" <?php if ($_GET['access'] == '') { echo 'checked="checked"'; } ?> /><label for="s"><?php echo _AT('all'); ?></label>
		</fieldset>
		</div>

		<div class="row">
			<label for="search"><?php echo _AT('search'); ?> (<?php echo _AT('title').', '._AT('description'); ?>)</label><br />
			<input type="text" name="search" id="search" size="20" value="<?php echo htmlspecialchars($_GET['search']); ?>" />
		</div>

		<div class="row buttons">
			<input type="submit" name="filter" value="<?php echo _AT('filter'); ?>" />
			<input type="submit" name="reset_filter" value="<?php echo _AT('reset_filter'); ?>" />
		</div>
	</div>
	</div> <!-- end #results-display -->
	</div> <!-- end #results-hide-show -->
</form>

<?php print_paginator($this->page, $this->num_results, $this->page_string . SEP . $this->order .'='. $col, $this->results_per_page); ?>

<form name="form" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<div class="table-surround">
<table class="data" summary="Course title, instructor, and enrollment." >
<colgroup>
	<?php if ($col == 'title'): ?>
		<col />
		<col class="sort" />
		<col span="6" />
	<?php elseif($col == 'login'): ?>
		<col span="2" />
		<col class="sort" />
		<col span="5" />
	<?php// elseif($col == 'access'): ?>
		<!-- REMOVED FOR MOBILE <col span="3" />
		<col class="sort" />
		<col span="4" /> -->
	<?php //elseif($col == 'created_date'): ?>
		<!-- REMOVED FOR MOBILE<col span="4" />
		<col class="sort" />
		<col span="3" />-->
	<?php //elseif($col == 'cat_name'): ?> 
		<!-- REMOVED FOR MOBILE<col span="5" />
		<col class="sort" />
		<col span="2" /> -->
	<?php endif; ?>
</colgroup>
<thead>
<tr>
	<th scope="col">&nbsp;</th>
	<th scope="col"><a href="mods/_core/courses/admin/courses.php?<?php echo $this->orders[$this->order]; ?>=title<?php echo $page_string; ?>"><?php echo _AT('title');               ?></a></th>
	<th scope="col"><a href="mods/_core/courses/admin/courses.php?<?php echo $this->orders[$this->order]; ?>=login<?php echo $page_string; ?>"><?php echo _AT('Instructor');          ?></a></th>
<!-- REMOVEED FOR MOBILE	<th scope="col"><a href="mods/_core/courses/admin/courses.php?<?php // echo $this->orders[$this->order]; ?>=access<?php echo $page_string; ?>"><?php echo _AT('access');             ?></a></th> -->
<!-- 	<th scope="col"><a href="mods/_core/courses/admin/courses.php?<?php //echo $this->orders[$this->order]; ?>=created_date<?php //echo $page_string; ?>"><?php // echo _AT('created_date'); ?></a></th> -->
<!-- 	<th scope="col"><a href="mods/_core/courses/admin/courses.php?<?php //echo $this->orders[$this->order]; ?>=cat_name<?php //echo $page_string; ?>"><?php //echo _AT('category'); ?></a></th> -->
	<th scope="col"><?php echo _AT('enrolled'); ?></th>
<!-- 	<th scope="col"><?php //echo _AT('alumni'); ?></th> -->
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="8"><input type="submit" name="view" value="<?php echo _AT('view'); ?>" /> 
					<input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" /> 
					<input type="submit" name="backups" value="<?php echo _AT('backups'); ?>" /> 
					<input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" /></td>
</tr>
</tfoot>
<tbody>
<?php if ($this->num_rows): ?>
	<?php foreach($this->rows_course_list as $row){ ?>
		<tr onkeydown="document.form['m<?php echo $row['course_id']; ?>'].checked = true; rowselect(this);" onmousedown="document.form['m<?php echo $row['course_id']; ?>'].checked = true; rowselect(this);" id="r_<?php echo $row['course_id']; ?>">
			<td><input type="radio" name="id" value="<?php echo $row['course_id']; ?>" id="m<?php echo $row['course_id']; ?>" /></td>
			<td><label for="m<?php echo $row['course_id']; ?>"><?php echo AT_print($row['title'], 'courses.title'); ?></label></td>
			<td><?php echo AT_print($row['login'],'members.login'); ?></td>
		<!-- REMOVED FOR MOBILE	<td><?php //echo _AT($row['access']); ?></td> -->
		<!-- 	<td><?php //echo AT_date($startend_date_long_format, $row['created_date'], AT_DATE_UNIX_TIMESTAMP); ?></td> -->
		<!--	<td><?php //echo ($row['cat_name'] ? $row['cat_name'] : '-')?></td>-->
			<td><?php echo ($this->enrolled[$row['course_id']]['y'] ? $this->enrolled[$row['course_id']]['y'] : 0); ?></td>
		<!-- 	<td><?php //echo ($this->enrolled[$row['course_id']]['a'] ? $this->enrolled[$row['course_id']]['a'] : 0); ?></td> -->
		</tr>
	<?php } ?>
<?php else: ?>
	<tr>
		<td colspan="8"><?php echo _AT('none_found'); ?></td>
	</tr>
<?php endif; ?>
</tbody>
</table>
</div>
</form>
<?php endif; ?> <!--  end template for smartphone -->
<?php if ($this->mobile_device_type == IPAD_DEVICE): ?> 

<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<div class="input-form">
		
			<h3><?php echo _AT('results_found', $this->num_results); ?></h3>
		<div id="hide-show-container">		
			<a id="results-hide-show-link" class="content-expand" href="javascript:void(0);" tabindex="1"><?php echo _AT('refine_results'); ?></a>
		</div>
	
		
	<div id="results-hide-show" role="search"  aria-live="assertive">
	<div id="results-display">
	
		<div class="row">
		<fieldset>
			<legend><?php echo _AT('access'); ?></legend>

			<input type="radio" name="access" value="0" id="s0" <?php if ($_GET['access'] == 0) { echo 'checked="checked"'; } ?> /><label for="s0"><?php echo _AT('public'); ?></label> 

			<input type="radio" name="access" value="1" id="s1" <?php if ($_GET['access'] == 1) { echo 'checked="checked"'; } ?> /><label for="s1"><?php echo _AT('protected'); ?></label> 

			<input type="radio" name="access" value="2" id="s2" <?php if ($_GET['access'] == 2) { echo 'checked="checked"'; } ?> /><label for="s2"><?php echo _AT('private'); ?></label>

			<input type="radio" name="access" value="" id="s" <?php if ($_GET['access'] == '') { echo 'checked="checked"'; } ?> /><label for="s"><?php echo _AT('all'); ?></label>
		</fieldset>
		</div>

		<div class="row">
			<label for="search"><?php echo _AT('search'); ?> (<?php echo _AT('title').', '._AT('description'); ?>)</label><br />
			<input type="text" name="search" id="search" size="20" value="<?php echo htmlspecialchars($_GET['search']); ?>" />
		</div>

		<div class="row buttons">
			<input type="submit" name="filter" value="<?php echo _AT('filter'); ?>" />
			<input type="submit" name="reset_filter" value="<?php echo _AT('reset_filter'); ?>" />
		</div>
	</div>
	</div> <!-- end #results-display -->
	</div> <!-- end #results-hide-show -->
</form>

<?php print_paginator($this->page, $this->num_results, $this->page_string . SEP . $this->order .'='. $col, $this->results_per_page); ?>

<form name="form" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<div class="table-surround">
<table class="data" summary="<?php echo _AT('course_title_instructor_enrolment'); ?>" >
<colgroup>
	<?php if ($col == 'title'): ?>
		<col />
		<col class="sort" />
		<col span="6" />
	<?php elseif($col == 'login'): ?>
		<col span="2" />
		<col class="sort" />
		<col span="5" />
	<?php// elseif($col == 'access'): ?>
		<!-- REMOVED FOR MOBILE <col span="3" />
		<col class="sort" />
		<col span="4" /> -->
	<?php //elseif($col == 'created_date'): ?>
		<!-- REMOVED FOR MOBILE<col span="4" />
		<col class="sort" />
		<col span="3" />-->
	<?php //elseif($col == 'cat_name'): ?> 
		<!-- REMOVED FOR MOBILE<col span="5" />
		<col class="sort" />
		<col span="2" /> -->
	<?php endif; ?>
</colgroup>
<thead>
<tr>
	<th scope="col">&nbsp;</th>
	<th scope="col"><a href="mods/_core/courses/admin/courses.php?<?php echo $this->orders[$this->order]; ?>=title<?php echo $page_string; ?>"><?php echo _AT('title');               ?></a></th>
	<th scope="col"><a href="mods/_core/courses/admin/courses.php?<?php echo $this->orders[$this->order]; ?>=login<?php echo $page_string; ?>"><?php echo _AT('Instructor');          ?></a></th>
<!-- REMOVEED FOR MOBILE	<th scope="col"><a href="mods/_core/courses/admin/courses.php?<?php // echo $this->orders[$this->order]; ?>=access<?php echo $page_string; ?>"><?php echo _AT('access');             ?></a></th> -->
<!-- 	<th scope="col"><a href="mods/_core/courses/admin/courses.php?<?php //echo $this->orders[$this->order]; ?>=created_date<?php //echo $page_string; ?>"><?php // echo _AT('created_date'); ?></a></th> -->
<!-- 	<th scope="col"><a href="mods/_core/courses/admin/courses.php?<?php //echo $this->orders[$this->order]; ?>=cat_name<?php //echo $page_string; ?>"><?php //echo _AT('category'); ?></a></th> -->
	<th scope="col"><?php echo _AT('enrolled'); ?></th>
<!-- 	<th scope="col"><?php //echo _AT('alumni'); ?></th> -->
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="8"><input type="submit" name="view" value="<?php echo _AT('view'); ?>" /> 
					<input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" /> 
					<input type="submit" name="backups" value="<?php echo _AT('backups'); ?>" /> 
					<input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" /></td>
</tr>
</tfoot>
<tbody>
<?php if ($this->num_rows): ?>
	<?php foreach($this->rows_course_list as $row){ ?>
		<tr onkeydown="document.form['m<?php echo $row['course_id']; ?>'].checked = true; rowselect(this);" onmousedown="document.form['m<?php echo $row['course_id']; ?>'].checked = true; rowselect(this);" id="r_<?php echo $row['course_id']; ?>">
			<td><input type="radio" name="id" value="<?php echo $row['course_id']; ?>" id="m<?php echo $row['course_id']; ?>" /></td>
			<td><label for="m<?php echo $row['course_id']; ?>"><?php echo AT_print($row['title'], 'courses.title'); ?></label></td>
			<td><?php echo AT_print($row['login'],'members.login'); ?></td>
		<!-- REMOVED FOR MOBILE	<td><?php //echo _AT($row['access']); ?></td> -->
		<!-- 	<td><?php //echo AT_date($startend_date_long_format, $row['created_date'], AT_DATE_UNIX_TIMESTAMP); ?></td> -->
		<!--	<td><?php //echo ($row['cat_name'] ? $row['cat_name'] : '-')?></td>-->
			<td><?php echo ($this->enrolled[$row['course_id']]['y'] ? $this->enrolled[$row['course_id']]['y'] : 0); ?></td>
		<!-- 	<td><?php //echo ($this->enrolled[$row['course_id']]['a'] ? $this->enrolled[$row['course_id']]['a'] : 0); ?></td> -->
		</tr>
	<?php } ?>
<?php else: ?>
	<tr>
		<td colspan="8"><?php echo _AT('none_found'); ?></td>
	</tr>
<?php endif; ?>
</tbody>
</table>
</div>
</form>
<?php endif; ?> <!--  end template for tablet -->