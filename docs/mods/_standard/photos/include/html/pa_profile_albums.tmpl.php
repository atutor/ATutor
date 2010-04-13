<div id="uploader-contents"">
	<!-- Photo album options and page numbers -->
	<?php if ($this->action_permission || $this->album_info['type_id']==AT_PA_TYPE_COURSE_ALBUM): ?>
	<div class="add_profile_photo">
	    <div class="profile_photo">
		    <img src="<?php echo 'get_profile_img.php?id='.$_SESSION['member_id'].SEP.'size=p';?>" title="<?php echo htmlentities_utf82(AT_print(get_display_name($_SESSION['member_id']), 'members.full_name')); ?>" alt="<?php _AT('profile_picture');?>" />
		</div>

		<div class="uploader">
		    <form enctype="multipart/form-data" method="post" action="<?php echo AT_PA_BASENAME. 'albums.php'; ?>" class="">
		        <div class="row">
		            <label for="single_uploader"><?php echo _AT('pa_choose_profile_picture');?></label>
		            <input type="file" id="single_uploader" name="photo">
		        </div>
		        <div class="row">
		            <input type="hidden" name="id" value="<?php echo $this->album_info['id']; ?>"/>
		            <input type="submit" class="button" name="upload" value="<?php echo _AT('upload');?>" />
                </div>
		    </form>
		</div>
	
		<div class="uploader">
		    <form method="post" action="<?php echo 'mods/_standard/profile_pictures/profile_picture.php'; ?>" class="">
		        <div class="row">
		            <label><?php echo _AT('pa_delete_profile_pic_blub'); ?></label>		            
		        </div>
		        <div class="row">
					<input type="hidden" name="delete" value="1" />	
		            <input type="submit" class="button" name="submit" value="<?php echo _AT('delete');?>" />
                </div>
		    </form>
		</div>
				
	</div>
	<?php endif; //action permission?>

	<div class="album_panel">
		<div class="topbar">			
			<?php if($this->num_rows > AT_PA_PHOTOS_PER_PAGE):  ?>
			<div class="paginator">
				<?php print_paginator($this->page, $this->num_rows, 'id='.$this->album_info['id'], AT_PA_PHOTOS_PER_PAGE, AT_PA_PAGE_WINDOW);  ?>
			</div>
			<?php endif; ?>
		</div>
		<?php if(!empty($this->photos)): ?>
		<!-- loop through this -->
		<?php foreach($this->photos as $key=>$photo): ?>
		<div class="photo_frame">
			<a href="<?php echo AT_PA_BASENAME.'photo.php?pid='.$photo['id'].SEP.'aid='.$this->album_info['id'];?>"><img src="<?php echo AT_PA_BASENAME.'get_photo.php?aid='.$this->album_info['id'].SEP.'pid='.$photo['id'].SEP.'ph='.getPhotoFilePath($photo['id'], '', $photo['created_date']);?>" title="<?php echo htmlentities_utf82($photo['description'], false); ?>" alt="<?php echo htmlentities_utf82($photo['alt_text']);?>" /></a>
		</div>
		<?php endforeach; ?>
		<!-- end loop -->
		<div class="album_description">
			<p><?php if($this->album_info['location']!='') echo _AT('location').': '.htmlentities_utf82($this->album_info['location']) .'<br/>';?>
			<?php echo htmlentities_utf82($this->album_info['description']);?></p>
		</div>		
		<?php else: ?>
		<div class="edit_photo_box">
			<p><?php echo _AT('pa_no_photos'); ?></p>
		</div>
		<?php endif; ?>
		<!-- page numbers -->
		<div class="topbar">
			<?php if($this->num_rows > AT_PA_PHOTOS_PER_PAGE):  ?>
			<div class="paginator">
				<?php print_paginator($this->page, $this->num_rows, 'id='.$this->album_info['id'], AT_PA_PHOTOS_PER_PAGE, AT_PA_PAGE_WINDOW);  ?>
			</div>
			<?php endif; ?>
		</div>
	</div>	

	<!-- comments -->
	<div class="comment_panel">
		<div class="comment_feeds">
			<?php if (!empty($this->comments)): ?>
			<?php foreach($this->comments as $k=>$comment_array): ?>
				<div class="comment_box" id="comment_box">
					<!-- TODO: Profile link and img -->
					<?php if ($this->action_permission || $comment_array['member_id']==$_SESSION['member_id']): ?>
					<div class="flc-inlineEditable">
						<a href="profile.php?id=<?php echo $comment_array['member_id'];?>"><strong><?php echo htmlentities_utf82(AT_print(get_display_name($comment_array['member_id']), 'members.full_name')); ?></a></strong>
						<span class="flc-inlineEdit-text" id="<?php echo $comment_array['id'];?>" ><?php echo htmlentities_utf82($comment_array['comment']);?></span>
					</div>
					<?php else: ?>
					<div>
						<a href="profile.php?id=<?php echo $comment_array['member_id'];?>"><strong><?php echo htmlentities_utf82(AT_print(get_display_name($comment_array['member_id']), 'members.full_name')); ?></a></strong>
						<?php echo htmlentities_utf82($comment_array['comment'], true);?>
					</div>
					<?php endif; ?>
					<div class="comment_actions">
						<!-- TODO: if author, add in-line "edit" -->
						<?php echo AT_date(_AT('forum_date_format'), $comment_array['created_date'], AT_DATE_MYSQL_DATETIME);?>
						<?php if ($this->action_permission || $comment_array['member_id']==$_SESSION['member_id']): ?>
						<a href="<?php echo AT_PA_BASENAME.'delete_comment.php?aid='.$this->album_info['id'].SEP.'comment_id='.$comment_array['id']?>"><?php echo _AT('delete');?></a>
						<?php endif; ?>
					</div>
				</div>
			<?php endforeach; endif;?>
			<!-- TODO: Add script to check, comment cannot be empty. -->
			<div>
				<form action="<?php echo AT_PA_BASENAME;?>addComment.php" method="post" class="input-form">
					<div class="row"><label for="comments"><?php echo _AT('comments');?></label></div>
					<div class="row"><textarea name="comment" id="comment_template" onclick="jQuery(this).hide();c=jQuery('#comment');c.show();c.focus();" onkeyup="jQuery(this).hide();c=jQuery('#comment');c.show();c.focus();"><?php echo _AT('pa_write_a_comment'); ?></textarea></div>
					<div class="row"><textarea name="comment" id="comment" style="display:none;"></textarea></div>
					<div class="row">
						<input type="hidden" name="aid" value="<?php echo $this->album_info['id'];?>" />
						<input type="submit" name="submit" value="<?php echo _AT('comment');?>" class="button"/>
					</div>
				</form>
			</div>
		</div>		

		<?php if($this->action_permission): ?>
		<div class="photo_actions">
			<a href="<?php echo AT_PA_BASENAME.'edit_photos.php?aid='.$this->album_info['id']; ?>" class="pa_tool_link"><img src="<?php echo $_base_href; ?>themes/<?php echo $_SESSION['prefs']['PREF_THEME']; ?>/images/edit.gif" alt="" border="0"  class="pa_tool_image"/><?php echo _AT('pa_edit_photos'); ?></a><br/>
			<a href="<?php echo AT_PA_BASENAME.'edit_photos.php?aid='.$this->album_info['id'].SEP.'org=1'; ?>" class="pa_tool_link"><img src="<?php echo $_base_href; ?>themes/<?php echo $_SESSION['prefs']['PREF_THEME']; ?>/images/photos_arrange.png" alt="" border="0"  class="pa_tool_image"/><?php echo _AT('pa_organize_photos'); ?></a><br/>
		</div>
		<?php endif; ?>
	</div>
</div>


<script type="text/javascript">
//<![CDATA[
/* Fluid inline editor */
jQuery(document).ready(function () {
	//the ATutor undo function
	var undo = function (that, targetContainer) {
					var markup = "<span class='flc-undo' aria-live='polite' aria-relevant='all'>" +
					  "<span class='flc-undo-undoContainer'>[<a href='#' class='flc-undo-undoControl'><?php echo _AT('pa_undo'); ?></a>]</span>" +
					  "<span class='flc-undo-redoContainer'>[<a href='#' class='flc-undo-redoControl'><?php echo _AT('pa_redo'); ?></a>]</span>" +
					"</span>";
					var markupNode = jQuery(markup);
					targetContainer.append(markupNode);
					return markupNode;
				};
	var pa_click_here_to_edit = '<?php echo _AT("pa_click_here_to_edit"); ?>';
	var pa_click_item_to_edit = '<?php echo _AT("pa_click_item_to_edit"); ?>';

	fluid.inlineEdits(".comment_feeds", {
		componentDecorators: {
			type: "fluid.undoDecorator",
			options: {
				renderer: undo
			}
		},
		defaultViewText: pa_click_here_to_edit,
		useTooltip: true,
		tooltipText: pa_click_item_to_edit, 
		listeners: {
			modelChanged: function(model, oldModel, source){
				/* for undo/redo model change */
				if (model != oldModel && source != undefined){
					viewNode = source.component.container.children('.flc-inlineEdit-text')[0];
					rtn = jQuery.post("<?php echo $_base_path. AT_PA_BASENAME.'edit_comment.php';?>", 
						{"submit":"submit",
						 "aid":<?php echo $this->album_info['id'];?>, 
						 "cid":viewNode.id, 
						 "comment":model.value},
						  function(data){}, 
						  "json");
				}
			},
			afterFinishEdit : function (newValue, oldValue, editNode, viewNode) {
				if (newValue != oldValue){
					rtn = jQuery.post("<?php echo $_base_path. AT_PA_BASENAME.'edit_comment.php';?>", 
							{"submit":"submit",
							 "aid":<?php echo $this->album_info['id'];?>, 
							 "cid":viewNode.id, 
							 "comment":newValue},
							  function(data){}, 
							  "json");
				}
			}
		}
	});
});
//]]>
</script>
