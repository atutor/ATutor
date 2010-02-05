<div id="uploader-contents">
	<!-- Photo album options and page numbers -->
	<div class="topbar">
		<div class="summary">
				<?php if (!empty($this->photos)): ?>
				<a href="<?php echo AT_PA_BASENAME.'edit_photos.php?aid='.$this->album_info['id']; ?>"><?php echo _AT('edit_photos');?></a> | 
				<a href="<?php echo AT_PA_BASENAME.'edit_photos.php?aid='.$this->album_info['id'].SEP.'org=1'; ?>"><?php echo _AT('organize_photos');?></a> |
				<?php endif; ?>
				<a href="<?php echo $_SERVER["REQUEST_URI"]; ?>#top" onclick="jQuery('#ajax_uploader').toggle();"><?php echo _AT("add_more_photos"); ?></a> |
		</div>
		<div class="paginator">
			<?php print_paginator($this->page, $this->num_rows, 'id='.$this->album_info['id'], AT_PA_PHOTOS_PER_PAGE, AT_PA_PAGE_WINDOW);  ?>
		</div>
	</div>

	<div class="add_photo">
		<!--
		<div class="input-form">
			<form action="<?php echo AT_PA_BASENAME;?>albums.php" enctype="multipart/form-data" name="add_photos" method="post">
				<div class="row">
					<p><?php echo _AT('add_more_photos');?></p>
				</div>
				<div class="row">
					<input type="file" name="photo" />
					
					<input type="hidden" name="id" value="<?php echo $this->album_info['id'];?>" />
					<input type="submit" name="upload" value="<?php echo _AT("upload");?>"class="button"/> 
				</div>
			</form>
		</div>
		-->
		<div class="input-form" id="ajax_uploader" style="float:left; display:none;">
			<div class="row" id="upload_button_div">
				<p name="top"><?php echo _AT('upload_blurb');?></p>
				<input id="upload_button" type="button" value="<?php echo _AT("add_more_photos"); ?>" class="button"/>				
			</div>
			<div class="row" id="files_done" style="display:none;">
				<input type="button" value="<?php echo _AT("upload"); ?>" class="button" onClick="window.location.reload();" />
			</div>			
			<div class="row" id="files_pending" style="display:none;">
				<img src="<?php echo AT_PA_BASENAME; ?>images/loading.gif" alt="loading" title="loading"/>
				<span></span>
			</div>
			<div class="row" style="max-height: 210px; overflow-y: auto;">
				<ul class="files"></ul>
			</div>
		</div>
	</div>

	<div class="album_panel">
		<!-- loop through this -->
		<?php foreach($this->photos as $key=>$photo): ?>
		<div class="photo_frame">
			<a href="<?php echo AT_PA_BASENAME.'photo.php?pid='.$photo['id'].SEP.'aid='.$this->album_info['id'];?>"><img src="<?php echo AT_PA_BASENAME.'get_photo.php?aid='.$this->album_info['id'].SEP.'pid='.$photo['id'].SEP.'ph='.getPhotoFilePath($photo['id'], '', $photo['created_date']);?>" title="<?php echo htmlentities_utf8($photo['description'], false); ?>" alt="<?php echo htmlentities_utf8($photo['alt_text']);?>" /></a>
		</div>
		<?php endforeach; ?>
		<div class="album_description">
			<p><?php if($this->album_info['location']!='') echo _AT('location').': '.htmlentities_utf8($this->album_info['location']) .'<br/>';?>
			<?php echo htmlentities_utf8($this->album_info['description']);?></p>
		</div>
		<!-- end loop -->
	</div>

	<!-- page numbers -->
	<div class="topbar">
		<div class="paginator">
			<?php print_paginator($this->page, $this->num_rows, 'id='.$this->album_info['id'], AT_PA_PHOTOS_PER_PAGE, AT_PA_PAGE_WINDOW);  ?>
		</div>
	</div>

	<!-- comments -->
	<div class="comment_panel">
		<div class="comment_feeds">
			<?php if (!empty($this->comments)): ?>
			<?php foreach($this->comments as $k=>$comment_array): ?>
				<div class="comment_box" id="comment_box">
					<!-- TODO: Profile link and img -->
					<div class="flc-inlineEditable"><a href=""><strong><?php echo htmlentities_utf8(AT_print(get_display_name($comment_array['member_id']), 'members.full_name')); ?></a></strong>
						<?php 
							if ($this->action_permission || $comment_array['member_id']==$_SESSION['member_id']){
								echo '<span class="flc-inlineEdit-text" id="cid_'.$comment_array['id'].'">'.htmlentities_utf8($comment_array['comment']).'</span>'; 
							} else {
								echo htmlentities_utf8($comment_array['comment']); 
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
					<div class="row"><textarea name="comment" id="comment_template" onclick="this.style.display='none';c=document.getElementById('comment');c.style.display='block';c.focus();">Write a comment...</textarea></div>
					<div class="row"><textarea name="comment" id="comment" style="display:none;"></textarea></div>
					<div class="row">
						<input type="hidden" name="aid" value="<?php echo $this->album_info['id'];?>" />
						<input type="submit" name="submit" value="<?php echo _AT('comment');?>" class="button"/>
					</div>
				</form>
			</div>
		</div>		
	</div>
</div>


<script type="text/javascript">
/* Fluid inline editor */
jQuery(document).ready(function () {
	fluid.inlineEdits(".comment_feeds", {
		componentDecorators: {
			type: "fluid.undoDecorator"
		},
		useTooltip: true,
		listeners: {
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
var upload_pending  = 0; //counter for pending files
var ajax_upload = new AjaxUpload('upload_button', {
  // Location of the server-side upload script
  // NOTE: You are not allowed to upload files to another domain
  action: '<?php echo $_base_path. AT_PA_BASENAME; ?>albums.php',
  // File upload name
  name: 'photo',
  // Title 
  title: '<?php echo _AT("add_more_photos"); ?>',
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
		 file_msg.attr('style', 'float:left; width: 50%');
	 } else {
		 //thumbnail
		 img = jQuery('<img>').attr('src', '<?php echo $_base_href . AT_PA_BASENAME; ?>get_photo.php?aid='+response_array.aid+'&pid='+response_array.pid+'&ph='+response_array.ph);	 
		 img.attr('alt', response_array.alt);
		 img.attr('title', file);
		 img.attr('class', 'tn');

		 //update error log msg
		 file_msg = jQuery('<div>').text('<?php echo _AT("uploaded"); ?>: ' + file);
		 file_msg.attr('style', 'float:left; width: 50%');
	 }	 

	 //image for the x
	 imgx = jQuery('<img>').attr('src', '<?php echo $_base_href . "images/x.gif" ?>');
	 imgx.attr('title', '<?php echo _AT("remove");?>');
	 imgx.attr('alt', '<?php echo _AT("remove");?>');

	 //deletion link
	 a_delete = jQuery('<a>'); 
	 a_delete.attr('href', '<?php echo $_SERVER["REQUEST_URI"]; ?>#top');
	 a_delete.attr('title', file);
	 a_delete.attr('onClick', 'deletePhoto('+response_array.aid+', '+response_array.pid+', this)');	 
	  
	 //img wrapper
	 img_wrapper = jQuery('<div>');
	 img_wrapper.attr('style', 'float:left; width: 50%');
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

</script>
