<div id="uploader-contents">	
	<!-- Photo album options and page numbers -->
	<?php if ($this->action_permission || $this->album_info['type_id']==AT_PA_TYPE_COURSE_ALBUM): ?>
	<div class="add_photo">
		<div class="toggle_uploader">
			<input type="button" id="upload_manager" name="upload_manager" value="<?php echo _AT('pa_open_upload_manager'); ?>" onclick="toggleUploadManager()" class="button" />
			<input type="hidden" id="upload_manager_toggle" value="1" />
		</div>

		<div class="input-form" id="ajax_uploader">
			<div class="row" id="upload_button_div">
				<p name="top"><?php echo _AT('pa_upload_blurb');?></p>
				<p class="memory_usage"><?php echo _AT('pa_memory_usage').': '. number_format($this->memory_usage, 2) .'/ '. $this->allowable_memory_usage . ' ' . _AT('mb'); ?></p>
				<label for="add_more_photos" id="upload_button"><?php echo _AT('pa_add_more_photos'); ?></label>
			</div>			
			<div class="row" id="files_pending" style="display:none;">
				<img src="<?php echo AT_PA_BASENAME; ?>images/loading.gif" alt="loading" title="loading"/>
				<span></span>
			</div>
			<div class="row">
				<ul class="files"></ul>
			</div>
			<div class="row" id="files_done" style="display:none;">
				<input type="button" value="<?php echo _AT("upload"); ?>" class="button" onClick="window.location.reload();" />
			</div>
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
					<div class="flc-inlineEditable"><a href=""><strong><?php echo htmlentities_utf82(AT_print(get_display_name($comment_array['member_id']), 'members.full_name')); ?></a></strong>
						<?php 
							if ($this->action_permission || $comment_array['member_id']==$_SESSION['member_id']){
								echo '<span class="flc-inlineEdit-text" id="cid_'.$comment_array['id'].'">'.htmlentities_utf82($comment_array['comment']).'</span>'; 
							} else {
								echo htmlentities_utf82($comment_array['comment'], true); 
							}
						?>
					</div>
					<div class="comment_actions">
						<!-- TODO: if author, add in-line "edit" -->
						<?php echo AT_date(_AT('forum_date_format'), $comment_array['created_date'], AT_DATE_MYSQL_DATETIME);?>
						<?php if ($this->action_permission): ?>
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
			<a href="<?php echo AT_PA_BASENAME.'edit_album.php?id='.$this->album_info['id']; ?>"><?php echo _AT('pa_edit_album'); ?></a><br/>
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

/* Ajax Uploader */
<?php if ($this->action_permission || $this->album_info['type_id']==AT_PA_TYPE_COURSE_ALBUM): ?>
var upload_pending  = 0; //counter for pending files
var ajax_upload = new AjaxUpload('upload_button', {
  // Location of the server-side upload script
  // NOTE: You are not allowed to upload files to another domain
  action: '<?php echo $_base_path. AT_PA_BASENAME; ?>albums.php',
  // File upload name
  name: 'photo',
  // Title 
  title: '<?php echo _AT("pa_add_more_photos"); ?>',
  // Additional data to send
  data: {
    upload : 'ajax',
    id : '<?php echo $this->album_info['id'];?>'
  },
  // Submit file after selection
  autoSubmit: true,
  // The type of data that you're expecting back from the server.
  // HTML (text) and XML are detected automatically.
  // Useful when you are using JSON data as a response, set to "json" in that case.
  // Also set server response type to text/html, otherwise it will not work in IE6
  responseType: false,
  // Fired after the file is selected
  // Useful when autoSubmit is disabled
  // You can return false to cancel upload
  // @param file basename of uploaded file
  // @param extension of that file
  onChange: function(file, extension){},
  // Fired before the file is uploaded
  // You can return false to cancel upload
  // @param file basename of uploaded file
  // @param extension of that file
  onSubmit: function(file, extension) {
	  upload_pending++;
	  if (upload_pending > 0){
		jQuery('#files_pending').show();
		jQuery('#files_done').hide();
	  }
	  jQuery('#files_pending').children('span').text('Loading... '+ (upload_pending)+' Remaining')
  },
  // Fired when file upload is completed
  // WARNING! DO NOT USE "FALSE" STRING AS A RESPONSE!
  // @param file basename of uploaded file
  // @param response server response
  onComplete: function(file, response) {
//	 console.debug(response);
	 // add file to the list
	 response_array = JSON.parse(response);
	 if (response_array.error==true){
		 //error, then refresh URL
//		 console.debug(response_array);
		 //thumbnail
		 img = jQuery('<img>').attr('src', '<?php echo $_base_href . AT_PA_BASENAME . "images/no.png" ?>');	 
		 img.attr('alt', '<?php echo _AT("error"); ?>');
		 img.attr('title', file);

		 //update error log msg
		 file_msg = jQuery('<div>').text(response_array.msg);
		 file_msg.attr('style', 'float:left; width: 80%');
	 } else {
		 //thumbnail
		 img = jQuery('<img>').attr('src', '<?php echo $_base_href . AT_PA_BASENAME; ?>get_photo.php?aid='+response_array.aid+'&pid='+response_array.pid+'&ph='+response_array.ph);	 
		 img.attr('alt', response_array.alt);
		 img.attr('title', file);
		 img.attr('class', 'tn');

		 //update error log msg
		 file_msg = jQuery('<div>').text('<?php echo _AT("pa_processed"); ?>: ' + file + ' (' + response_array.size );
		 file_kb = jQuery('<span>').html('<?php echo _AT("kb"); ?>)');
		 file_kb.appendTo(file_msg);
		 file_msg.attr('style', 'float:left; width: 80%;');
	 }	 

	 //image for the x
	 imgx = jQuery('<img>').attr('src', '<?php echo $_base_href . "images/x.gif" ?>');
	 imgx.attr('title', '<?php echo _AT("remove");?> ' + file);
	 imgx.attr('alt', '<?php echo _AT("remove");?> ' + file);

	 //deletion link
	 a_delete = jQuery('<a>'); 
	 a_delete.attr('href', '<?php echo $_SERVER["REQUEST_URI"]; ?>#top');
	 //a_delete.attr('onclick', 'deletePhoto('+response_array.aid+', '+response_array.pid+', this);');
	 a_delete.click(function(){deletePhoto(response_array.aid, response_array.pid, this)});
	  
	 //img wrapper
	 img_wrapper = jQuery('<div>');
	 img_wrapper.attr('style', 'float:left; ');
	 img.appendTo(img_wrapper);
	 a_delete.appendTo(img_wrapper);
	 imgx.appendTo(a_delete);

	 //formation
	 li = jQuery('<li></li>');
	 li.prependTo('#ajax_uploader .files');
	 file_msg.appendTo(li);
	 img_wrapper.appendTo(li);

	 jQuery('#files_pending').children('span').text('Loading... '+ (--upload_pending)+' Remaining')
	 if (upload_pending == 0){
		jQuery('#files_pending').hide();
		jQuery('#files_done').show();
	  }
  }
});

//Ajax delete
function deletePhoto(aid, pid, thisobj) {
	var thisobj = thisobj;
	//run iff it is a photo
	if(aid > 0 && pid > 0){
		xmlhttp=GetXmlHttpObject();
		if (xmlhttp==null) {
		  alert ("Your browser does not support AJAX!");
		  return;
		}
		var url='<?php echo $_base_href . AT_PA_BASENAME; ?>remove_uploaded_photo.php?aid='+aid+'&pid='+pid;
		xmlhttp.onreadystatechange=function(){
	//		console.debug(xmlhttp);
			if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				jQuery(thisobj).parent().parent().remove();	//delete from DOM tree.
			}
		};
		xmlhttp.open("GET",url,true);
		xmlhttp.send(null);
	} else {
		//simply remove tihs node without running anything in the DB
		jQuery(thisobj).parent().parent().remove();	//delete from DOM tree.
	}
	if(jQuery('#add_more_photos').length){
		jQuery('#add_more_photos').focus();
	} 
}

function GetXmlHttpObject() {
	if (window.XMLHttpRequest) {
	  // code for IE7+, Firefox, Chrome, Opera, Safari
	  return new XMLHttpRequest();
	  }
	if (window.ActiveXObject){
	  // code for IE6, IE5
	  return new ActiveXObject("Microsoft.XMLHTTP");
	  }
	return null;
}


/* 
 * Toggle add more photo display, and the value of the button
 */
function toggleUploadManager(){
		flag = jQuery('#upload_manager_toggle').val();
		if (flag==1){
			jQuery('#upload_manager').val('<?php echo _AT("pa_close_upload_manager"); ?>');
			jQuery('#upload_manager_toggle').val(0);
		} else {
			jQuery('#upload_manager').val('<?php echo _AT("pa_open_upload_manager"); ?>');
			jQuery('#upload_manager_toggle').val(1);
		}
		jQuery('#ajax_uploader').toggle();		
}
<?php endif; ?>
//]]>
</script>
