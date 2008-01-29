<div class="input-form">
	<div class="row"><p>{MESSAGE}</p></div>
	<!-- BEGIN IMAGE_DISPLAY -->			
		<div class="row" style="margin-left:auto; width:90%; margin-right:auto;">
			<img src="{IMAGE_SRC}" alt="{ALT}"/>
		</div>
	<!-- END IMAGE_DISPLAY -->		

	<!-- BEGIN TABLE -->
	<table class="data" width="500"  border="5">
		<tr>
			<td colspan="2" height="50">{DESC}</td>
		</tr>	
		<tr>
			<td width="40%">{NAME_STRING} : {NAME}</td>
			<td width="60%">{DATE_STRING} : {DATE}</td>
		</tr>
	</table>
	<!-- END TABLE -->
			
	<div class="row buttons" style="margin-top:1.0em;">
		<form name="{CANCEL_FORM}" method="post" action="{CANCEL_ACTION}">
			<input type="submit" name="no" value="{CANCEL_DISPLAY}"/>
		</form>	

		<form name="{CONFIRM_FORM}" method="post" action="{CONFIRM_ACTION}">
			<input type="submit" name="confirm" value="{CONFIRM_DISPLAY}"/>
		</form>
	</div>
</div>

			
