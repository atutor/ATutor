<script language="Javascript" type="text/Javascript" src="{JAVA_SRC}"></script>

<!-- BEGIN IMAGE_DISPLAY -->
	<form name="{TEXT_FORM}" method="post" action="{TEXT_ACTION}">			
	<div class="input-form">
		<div class="row">
			<img src="{IMAGE_SRC}" alt="{ALT}"/>
		</div>
		<div class="row" >{MESSAGE}</div>
		
		<div class="row">
			<div class="required">*</div><label for="title_field">{TITLE_MESSAGE} </label> <br/> <input {TITLE_FADE} type="text" id="title_field" name="title" size="50" maxlength="23" value="{TITLE_VALUE}"/>
		</div>
		<div class="row">
			<label for="text_field">{DESC_MESSAGE}</label> <br/>
			<textarea name="description" cols="55" rows="4" id="text_field">{DESC_VALUE}</textarea>
		</div>
		<!-- BEGIN ALT_PART -->
			<div class="row">
				<div class="required">*</div><label for="alt_field">{ALT_MESSAGE}</label> <br/> <input {ALT_FADE} type="text" id="alt_field" name="alt" size="50" maxlength="30" value="{ALT_VALUE}"/>
			</div>
		<!-- END ALT_PART -->
		<div class="row buttons">
			<input type="submit" name="submit" value="{SUBMIT_MESSAGE}"/>
			<input type="submit" name="cancel_image" value="{CANCEL_STRING}"/>
		</div>
	</div>
	</form> 
<!-- END IMAGE_DISPLAY -->

<!-- BEGIN COMMENT -->
	<form name="{COMMENT_FORM}" method="post" action="{COMMENT_ACTION}"> 
	<div class="input-form">
		<div class="row"><p>{COMMENT_MESSAGE}</p></div>
		<div class="row">
			<div class="required">*</div><label for="comment_field">{COMMENT_LABEL}</label><br/>
			<textarea class="textarea {COMMENT_FADE}" name="comment" cols="40" rows="4" id="comment_field">{COMMENT_VALUE}</textarea>
		</div>
		<div class="row buttons">
			<input type="submit" name="submit" value="{SUBMIT_VALUE}" />
			<input type="submit" name="cancel_comment" value="{CANCEL_STRING}"/>
		</div>
	</div>
	</form>
<!-- END COMMENT -->
	


