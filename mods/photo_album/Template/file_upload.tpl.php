<script language="Javascript" type="text/Javascript" src="{JAVA_SRC}"></script>
<h3>
	{TITLE}
</h3>

<form name="{UPLOAD_FORM}" enctype="multipart/form-data" method="post" action="{UPLOAD_ACTION}">
<div class="input-form">
<!-- BEGIN IMAGE_DISPLAY -->			
	<div class="row">
		<img src="{IMAGE_SRC}" alt="{ALT}" />
	</div>
<!-- END IMAGE_DISPLAY -->		

<!-- BEGIN UPLOAD_PART -->
	<div class="row">
		<p>{MESSAGE}</p>
	</div>
	
	<div class="row">
		{REQUIRED_SYMBOL}<label for="file_field">{FILE_LABEL}</label> <br/><input {FILE_FADE} type="file" name="input_file" id="file_field" size="15"/>
	</div>
	<div class="row buttons">
		<input type="submit" name="upload_image" value="{SUBMIT_MESSAGE}"/>
		<!-- BEGIN SKIP_UPLOAD -->
			<input type="submit" name="skip_upload" value="{SUBMIT_MESSAGE2}"/>
		<!-- END SKIP_UPLOAD -->
		<input type="submit" name="cancel_image" value="{CANCEL_STRING}"/>
	</div>
</div>
</form>
<!-- END UPLOAD_PART -->


