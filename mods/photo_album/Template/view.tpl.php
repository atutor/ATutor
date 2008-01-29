<div class="pa_result_success">{RESULT}</div>
	
<h3>{IMAGE_TITLE_STRING} : {IMAGE_TITLE}</h3>

<!-- BEGIN IMAGE -->
<div class="input-form">
	<div class="row">
		<img src="{IMAGE_SRC}" alt="{ALT}"/>
	</div>	
	
	<!-- BEGIN IMAGE_MODIFY_BUTTONS -->
	<div class="row buttons">
	<form name="{EDIT_FORM}" method="post" action="{EDIT_ACTION}">
	<input type="submit" name="edit" value="{EDIT_DISPLAY}"/>
	<input type="hidden" name="mode" value="edit"/>
	<input type="hidden" name="image_id" value="{IMAGE_ID}"/>
	<input type="hidden" name="choose" value="{IMAGE_CHOOSE}"/>
	</form>
	
	<form name="{DEL_FORM}" method="post" action="{DEL_ACTION}">
	<input type="submit" name="delete" value="{DEL_DISPLAY}"/>
	<input type="hidden" name="mode" value="delete"/>
	<input type="hidden" name="image_id" value="{IMAGE_ID}"/>
	<input type="hidden" name="choose" value="{IMAGE_CHOOSE}"/>
	</form>
	</div>
	<!-- END IMAGE_MODIFY_BUTTONS -->
	
<!-- END IMAGE -->

<!-- BEGIN TABLE -->			
	<h4 class="row">{IMAGE_DISPLAY}</h4>
	<div class="row">
	<table class="data" width="500"  border="5">
		<tr>
    		<td colspan="2" height="50">{IMAGE_DESC}</td>
		</tr>	
		<tr>
    		<td width="40%">{IMAGE_NAME_STRING} : {IMAGE_NAME}</td>
    		<td width="60%">{IMAGE_DATE_STRING} : {IMAGE_DATE}</td>
 		</tr>
	</table>
	</div>
<!-- END TABLE -->
<!-- BEGIN ADD_COMMENT_BUTTON -->
	<div class="row buttons">
	<form name="{ADD_FORM}" method="post" action="{ADD_ACTION}">
	<input type="submit" name="button" value="{ADD_DISPLAY}"/>
	<input type="hidden" name="mode" value="add"/>
	<input type="hidden" name="choose" value="{COMMENT_CHOOSE}"/>
	</form>
	</div>
<!-- END ADD_COMMENT_BUTTON -->
</div>

<!-- BEGIN COMMENT_HEAD -->
	<div class="input-form">
	<h4 class="row">{COMMENT_DISPLAY}</h4>
	
	<!-- BEGIN COMMENT_START -->
		<div class="row">
			<span class="invisible">
				{MESSAGE}
			</span>
		</div>
		<div class="row">
		<table class="data {COLOR}">
			<tr>
				<td colspan="2" height="50">{COMMENT_VALUE}</td>
			</tr>	
			<tr>
   				<td width="40%">Name : {COMMENT_NAME}</td>
   				<td width="60%">Date : {COMMENT_DATE}</td>
			</tr>
		</table>
		</div>
		{CONTROL_BUTTONS}
		
	<!-- END COMMENT_START -->
	</div>
<!-- END COMMENT_HEAD -->
