<div>
	<!-- frame that holds the 604px picture -->
	<div class="photo_panel" id="photo_panel">
		<!-- Photo ordering and prev/next -->
		<?php if($this->photo_info['ordering'] <= $this->total_photos): ?>
		<div class="ordering"><?php echo _AT('pa_photo').' '.$this->photo_info['ordering'].' '._AT('pa_of').' '.$this->total_photos ; ?></div>
		<div class="paginator">
			<ul>
				<?php if (isset($this->prev)): ?>
				<li><a href="<?php echo AT_PA_BASENAME.'photo.php?pid='.$this->prev['id'].SEP.'aid='.$this->aid;?>"><img src="<?php echo $_base_href; ?>themes/<?php echo $_SESSION['prefs']['PREF_THEME']?>/images/previous.png" alt="<?php echo _AT('previous'); ?>" title="<?php echo _AT('previous'); ?>"/></a></li>
				<?php endif; ?>
				<?php if (isset($this->next)): ?>
				<li><a href="<?php echo AT_PA_BASENAME.'photo.php?pid='.$this->next['id'].SEP.'aid='.$this->aid;?>"><img src="<?php echo $_base_href; ?>themes/<?php echo $_SESSION['prefs']['PREF_THEME']?>/images/next.png" alt="<?php echo _AT('next'); ?>" title="<?php echo _AT('next'); ?>"/></a></li>
				<?php endif; ?>				
			</ul>
		</div>
		<?php endif; ?>
		<div style="clear:both"></div>

		<img src="<?php echo AT_PA_BASENAME.'get_photo.php?aid='.$this->aid.SEP.'pid='.$this->photo_info['id'].SEP.'size=o'.SEP.'ph='.getPhotoFilePath($this->photo_info['id'], '', $this->photo_info['created_date']);?>" title="<?php echo htmlentities_utf82($this->photo_info['description'], false); ?>" alt="<?php echo htmlentities_utf82($this->photo_info['alt_text']) ;?>" />
		<?php if ($this->action_permission): ?>
		<div class="flc-inlineEditable"><span class="flc-inlineEdit-text"><?php echo htmlentities_utf82($this->photo_info['description']);?></span></div>
		<?php else : ?>
		<div><span><?php echo htmlentities_utf82($this->photo_info['description'], true);?></span></div>
		<?php endif; ?>
	</div>

	<!-- comments -->
	<div class="comment_panel">
		<div class="comment_feeds">
			<?php if(!empty($this->comments)): ?>
			<?php foreach($this->comments as $k=>$comment_array): ?>
				<div class="comment_box">
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

					<div>
						<div class="comment_actions">
							<?php echo AT_date(_AT('forum_date_format'), $comment_array['created_date'], AT_DATE_MYSQL_DATETIME);?>
							<?php if ($this->action_permission || $comment_array['member_id']==$_SESSION['member_id']): ?>
							<a href="<?php echo AT_PA_BASENAME.'delete_comment.php?aid='.$this->aid.SEP.'pid='.$this->photo_info['id'].SEP.'comment_id='.$comment_array['id']?>"><?php echo _AT('delete');?></a>
							<?php endif; ?>
						</div>
					</div>
				</div>
			<?php endforeach; endif; ?>
			<!-- TODO: Add script to check, comment cannot be empty. -->
			<form action="<?php echo AT_PA_BASENAME;?>addComment.php" method="post" class="input-form">
				<div class="row"><label for="comments"><?php echo _AT('comments');?></label></div>
				<div class="row"><textarea name="comment" id="comment_template" onclick="jQuery(this).hide();c=jQuery('#comment');c.show();c.focus();" onkeyup="jQuery(this).hide();c=jQuery('#comment');c.show();c.focus();"><?php echo _AT('pa_write_a_comment'); ?></textarea></div>
				<div class="row"><textarea name="comment" id="comment" style="display:none;"></textarea></div>
				<div class="row">
					<input type="hidden" name="pid" value="<?php echo $this->photo_info['id'];?>" />
					<input type="hidden" name="aid" value="<?php echo $this->aid;?>" />
					<input type="submit" name="submit" value="<?php echo _AT('comment');?>" class="button"/>
				</div>
			</form>
		</div>
		
		<?php if($this->action_permission): ?>
		<div class="photo_actions">
			<a href="<?php echo AT_PA_BASENAME.'edit_photos.php?aid='.$this->aid.SEP.'pid='.$this->photo_info['id']; ?>" class="pa_tool_link"><img src="<?php echo $_base_href; ?>themes/<?php echo $_SESSION['prefs']['PREF_THEME']; ?>/images/edit.gif" alt="" border="0"  class="pa_tool_image"/><?php echo _AT('pa_edit_photo'); ?></a><br/>
			<a href="<?php echo AT_PA_BASENAME.'delete_photo.php?pid='.$this->photo_info['id'].SEP.'aid='.$this->aid;?>"  class="pa_tool_link"><img src="<?php echo $_base_href; ?>themes/<?php echo $_SESSION['prefs']['PREF_THEME']; ?>/images/x.gif" alt="" border="0" class="pa_tool_image"/><?php echo _AT('pa_delete_this_photo'); ?></a><br/>
			<a href="<?php echo AT_PA_BASENAME.'set_profile_picture.php?pid='.$this->photo_info['id'].SEP.'aid='.$this->aid;?>"  class="pa_tool_link"><img src="<?php echo $_base_href; ?>themes/<?php echo $_SESSION['prefs']['PREF_THEME']; ?>/images/profile.gif" alt="" border="0" class="pa_tool_image"/><?php echo _AT('pa_set_profile_pic'); ?></a>
		</div>
		<?php else: ?>
		<div class="photo_actions">
			<p><?php echo _AT('pa_uploaded_by').': '.AT_print(get_display_name($this->photo_info['member_id']), 'members.full_name'); ?></p>
		</div>
		<?php endif; ?>
	</div>
</div>

<script type="text/javascript">
jQuery(document).ready(function () {
	//the ATutor undo function
	var undo = function (that, targetContainer) {
					var markup = "<span class='flc-undo' aria-live='polite' aria-relevant='all'>" +
					  "<span class='flc-undo-undoContainer'><a href='#' class='flc-undo-undoControl'>[<?php echo _AT('pa_undo'); ?>]</a></span>" +
					  "<span class='flc-undo-redoContainer'><a href='#' class='flc-undo-redoControl'>[<?php echo _AT('pa_redo'); ?>]</a></span>" +
					"</span>";
					var markupNode = jQuery(markup);
					targetContainer.append(markupNode);
					return markupNode;
				};
	var pa_click_here_to_edit = '<?php echo _AT("pa_click_here_to_edit"); ?>';
	var pa_click_item_to_edit = '<?php echo _AT("pa_click_item_to_edit"); ?>';

	/* inline edit for photo panel description */
    fluid.inlineEdits("#photo_panel", {
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
					rtn = jQuery.post("<?php echo $_base_path. AT_PA_BASENAME.'edit_photos.php';?>", 
						{"submit":"ajax",
						 "pid":<?php echo $this->photo_info['id'];?>, 
						 "aid":<?php echo $this->aid;?>, 
						 "description_<?php echo $this->photo_info['id'];?>":model.value,
						 "alt_text_<?php echo $this->photo_info['id'];?>":"<?php echo trim($this->photo_info['alt_text']);?>"},
						  function(data){}, 
						  "json");
				}
			},
			afterFinishEdit : function (newValue, oldValue, editNode, viewNode) {
				if (newValue != oldValue){
					rtn = jQuery.post("<?php echo $_base_path. AT_PA_BASENAME.'edit_photos.php';?>", 
							{"submit":"ajax",
							 "pid":<?php echo $this->photo_info['id'];?>, 
							 "aid":<?php echo $this->aid;?>, 
							 "description_<?php echo $this->photo_info['id'];?>":newValue,
							 "alt_text_<?php echo $this->photo_info['id'];?>":"<?php echo trim($this->photo_info['alt_text']);?>"},
							  function(data){}, 
							  "json");
				}
			}
		}
	});

	/* inline edit for photo album comments */
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
					commentID = source.component.locate("text").attr("id");
					rtn = jQuery.post("<?php echo $_base_path. AT_PA_BASENAME.'edit_comment.php';?>", 
						{"submit":"submit",
						 "pid":<?php echo $this->photo_info['id'];?>, 
						 "aid":<?php echo $this->aid;?>, 
						 "cid":commentID, 
						 "comment":model.value},
						  function(data){}, 
						  "json");
				}
			},
			afterFinishEdit : function (newValue, oldValue, editNode, viewNode) {
				if (newValue != oldValue){
					rtn = jQuery.post("<?php echo $_base_path. AT_PA_BASENAME.'edit_comment.php';?>", 
							{"submit":"submit",
							 "pid":<?php echo $this->photo_info['id'];?>, 
							 "aid":<?php echo $this->aid;?>, 
							 "cid":viewNode.id, 
							 "comment":newValue},
							  function(data){}, 
							  "json");
				}
			}
		}
	});
});
</script>
