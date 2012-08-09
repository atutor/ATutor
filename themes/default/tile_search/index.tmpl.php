<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2010                                      */
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: index.tmpl.php 6614 2006-09-27 19:32:29Z greg $

?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>#search_results" method="post" name="form">
	<div class="input-form">

		<div style="padding:1em;">
		<?php  echo _AT('tile_howto'); ?>
		</div>
		<table width="100%">
			<tr>
				<td width="20%"><label for="words2"><?php echo _AT('keywords'); ?></label></td>
				<td><input type="text" name="keywords" size="100" id="words2" value="<?php echo $_REQUEST['keywords']; ?>" /></td>
			</tr>
			<?php
				// ***
				// ACC
				// Add some content search filters
				// Lesson
				// Pages
			?>
<!--
			<tr>
				<td colspan="2">
					<label for="results_per_page"><?php echo _AT('merlot_results_per_page'); ?></label>
					<select name="results_per_page">
						<option value="5" <?php if ($_REQUEST["results_per_page"] == 5) echo 'selected="selected"' ?>>5</option>
						<option value="10" <?php if ($_REQUEST["results_per_page"] == 10) echo 'selected="selected"' ?>>10</option>
						<option value="15" <?php if ($_REQUEST["results_per_page"] == 15) echo 'selected="selected"' ?>>15</option>
						<option value="20" <?php if ($_REQUEST["results_per_page"] == 20) echo 'selected="selected"' ?>>20</option>
						<option value="25" <?php if ($_REQUEST["results_per_page"] == 25) echo 'selected="selected"' ?>>25</option>
					</select>
				</td>
			</tr>
//-->
		</table>
		
		<div class="row buttons">
				<input type="submit" name="submit" value="<?php echo _AT('search'); ?>" />
		</div>
	</div>
</form>

<?php
if (isset($this->result_list))
{
	if ($this->result_list['status'] == 'fail')  // failed, display error
	{
		echo '	<div id="search_results"><span style="color:red">'._AT('error').': <br /></div>';
		if (is_array($this->result_list['error']))
		{
			foreach ($this->result_list['error'] as $error)
				echo $error."<br />";
		}
		echo "</span>";
	}
	else  // success, display results
	{
		if (is_array($this->result_list))
		{
			$num_results = $this->result_list["summary"]["numOfTotalResults"];
			$num_pages = max(ceil($num_results / $this->results_per_page), 1);
			
			echo '	<div id="search_results">';
			echo "		<h2>". _AT('results')." <small>(".$this->startRecNumber. " - " .$this->result_list["summary"]["lastResultNumber"]." out of ".$num_results.")</small></h2>";
	
			print_paginator($page, $num_results, htmlspecialchars($this-> page_str), $this->results_per_page);
	
			foreach ($this->result_list as $key=>$result)
			{
				if (is_int($key))
				{
	?>
	
	<dl class="browse-result">
<?php if (isset($this->instructor_role)) { ?>
		<dt class="tr_results_tools">&nbsp;
          <a href="<?php echo AT_TILE_EXPORT_CP_URL.$result['courseID']; ?>">
            <img src="<?php echo AT_BASE_HREF. 'images/download.png'?>" alt="<?php echo _AT('download_content_package'); ?>" title="<?php echo _AT("download_content_package").' '.$result['title']; ?>" border="0">
          </a>&nbsp;
          <a href="mods/_standard/tile_search/import.php?tile_course_id=<?php echo $result['courseID']; ?>&title=<?php echo urlencode($result['title']); ?>">
            <img src="<?php echo AT_BASE_HREF. 'images/archive.gif'?>" alt="<?php echo _AT('import'); ?>" title="<?php echo _AT("import").' '.$result['title']; ?>" border="0">
          </a>&nbsp;
          <?php
			// ***
			// ACC
			// Add some content search filters
			// Lesson
			// Pages
			$tool		= @get_headers($GLOBALS['_config']['transformable_uri'] . 'oauth/tool.php');

			if($tool[0] != 'HTTP/1.1 404 Not Found'){
			?>
          <!--<a href="mods/_standard/tile_search/import.php?tile_course_id=<?php echo $result['courseID']; ?>&title=<?php echo urlencode($result['title']); ?>&url=<?php echo urlencode(AT_TILE_VIEW_COURSE_URL.$result['courseID']); ?>">-->
          <a href="mods/_standard/tile_search/import.php?tile_course_id=<?php echo $result['courseID']; ?>&title=<?php echo urlencode($result['title']); ?>&mode=LCL&desc=<?php echo urlencode($result['description']); ?>">
            <img src="<?php echo AT_BASE_HREF. 'images/add-course.png'?>" alt="<?php echo _AT('import'); ?>" style="border-left:1px solid #CCC; padding-left: 15px;" title="<?php echo _AT("import").' '.$result['title']; ?>" border="0">
          </a>&nbsp;
          <?php
			}
		  ?>
        </dt>
<?php }?>
		<dd>
		  <h3 style="margin-left:-2.5em;">
		    <a href="<?php echo AT_TILE_VIEW_COURSE_URL.$result['courseID']; ?>" target="_new" ><?php echo htmlspecialchars($result['title']); ?></a>
		  </h3>
		</dd> 
		<dt><?php echo _AT("author"); ?></dt>
		<dd><?php if ($result['authorName']=='') echo _AT('unknown'); else echo htmlspecialchars($result['authorName']); ?></dd>

		<dt><?php echo _AT("creation_date"); ?></dt>
		<dd><?php echo $result['creationDate']; ?></dd>
	
		<dt><?php echo _AT("description"); ?></dt>
		<dd><?php if ($result['description']=='') echo _AT('na'); else if (strlen($result['description']) > 120) echo substr(htmlspecialchars($result['description']), 0, 120). "..."; else echo htmlspecialchars($result['description']); ?></dd>

		<dt><?php echo _AT("creative_commons"); ?></dt>
		<dd><?php if ($result['creativeCommons'] == "true") echo _AT('yes'); else echo _AT('no'); ?></dd>

	</dl>
	<?php
		// ***
		// ACC
		/*
	?>
	<div>
    	<?php
    		echo $result['courseID'];


    	?>
    </div>
	<dl class="browse-result">
		<dt>
			<a href="#">
				<img src="<?php echo AT_BASE_HREF. 'images/folder.gif'?>" alt="<?php echo _AT('folder'); ?>" title="<?php echo _AT("folder").' '.$result['title']; ?>" border="0" />&nbsp;
				Folder name
			</a>
		</dt>

		<dt>
			<img src="<?php echo AT_BASE_HREF. 'images/tree/tree_end.gif'?>" alt="<?php echo _AT('file'); ?>" title="<?php echo _AT("file").' '.$result['title']; ?>" border="0" />
			<a href="#">
				<img src="<?php echo AT_BASE_HREF. 'images/file_types/generic.gif'?>" alt="<?php echo _AT('file'); ?>" title="<?php echo _AT("file").' '.$result['title']; ?>" border="0" />&nbsp;
				File name
			</a>
		</dt>
		<dt>
			<img src="<?php echo AT_BASE_HREF. 'images/tree/tree_end.gif'?>" alt="<?php echo _AT('file'); ?>" title="<?php echo _AT("file").' '.$result['title']; ?>" border="0" />
			<a href="#">
				<img src="<?php echo AT_BASE_HREF. 'images/file_types/generic.gif'?>" alt="<?php echo _AT('file'); ?>" title="<?php echo _AT("file").' '.$result['title']; ?>" border="0" />&nbsp;
				File name
			</a>
		</dt>
		
		<dt>
			<a href="#">
				<img src="<?php echo AT_BASE_HREF. 'images/folder.gif'?>" alt="<?php echo _AT('folder'); ?>" title="<?php echo _AT("folder").' '.$result['title']; ?>" border="0" />&nbsp;
				Folder name
			</a>
		</dt>

		<dt>
			<img src="<?php echo AT_BASE_HREF. 'images/tree/tree_end.gif'?>" alt="<?php echo _AT('file'); ?>" title="<?php echo _AT("file").' '.$result['title']; ?>" border="0" />
			<a href="#">
				<img src="<?php echo AT_BASE_HREF. 'images/file_types/generic.gif'?>" alt="<?php echo _AT('file'); ?>" title="<?php echo _AT("file").' '.$result['title']; ?>" border="0" />&nbsp;
				File name
			</a>
		</dt>
	</dl>

	<br />
	<?php
		 */
				}
			}
			echo '</div>';
		}
	}
}

?>
