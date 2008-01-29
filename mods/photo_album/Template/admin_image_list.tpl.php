<script language="JavaScript" type="text/javascript">
	function checkAll(check){
		for (i=0; i< {ADMIN_NUMBER_OF_IMAGE}; i++){
			var e=eval("document.table_form.imageId"+i);
			if (e!=undefined){
				if (check.checked==true){
					e.checked=true;
				} else {
					e.checked=false;
				}
			}		
		}
	}
</script>

<h3>
	{TITLE}
</h3>
<div id="pa_config_notice">{CONFIG_STRING} : {CONFIG_VALUE}</div>
<div class="input-form">
<div class="row buttons">
<!-- BEGIN SELECT_PART -->
<form name="{SELECT_FORM_NAME}" method="get" action="{SELECT_ACTION}">
	<label for="{SELECT_NAME}">{SELECT_LABEL}</label>
    <select name="{SELECT_NAME}" id="{SELECT_NAME}">
    <!-- BEGIN OPTION_PART -->
    	<option value="{OPTION_VALUE}">{OPTION_STRING}</option>
    <!-- END OPTION_PART -->
    </select>
    <input type="submit" class="button" name="{SELECT_SUBMIT}" value="{SELECT_SUBMIT_VALUE}"/>
</form>

<!-- END SELECT_PART -->

<!-- BEGIN ADD_PART -->
	<form name="{ADD_FORM_NAME}" method="post" action="{ADD_ACTION}">
		<input type="submit" class="button" name="add_button" value="{ADD_VALUE}"/>
		<input type="hidden" name="choose" value="{ADD_HIDDEN_VALUE}"/>
		<input type="hidden" name="mode" value="add"/>
	</form>
<!-- END ADD_PART -->
</div>


<!-- BEGIN IMAGE_TABLE_PART -->
<form name="{IMAGE_TABLE_FORM_NAME}" method="post" action="{IMAGE_TABLE_ACTION}">
	<!-- BEGIN IMAGE_TABLE_DATA -->
		<div class="row">
  		<input type="checkbox" style="clear:left;float:left;" name="{CHECK_NAME}" value="{CHECK_VALUE}" id="{CHECK_NAME}"/>
  		<table class="data" width="90%"  border="0">
    	<tr>
      		<td width="13%" rowspan="4"><label for="{CHECK_NAME}">{IMAGE_TABLE_DATA1}</label></td>
      		<td>{IMAGE_TABLE_DATA2}</td>
    	</tr>
    	<tr>
      		<td>{IMAGE_TABLE_DATA3}</td>
    	</tr>
    	<tr>
      		<td>{IMAGE_TABLE_DATA4}</td>
   		</tr>
    	<tr>
     		<td>{IMAGE_TABLE_DATA5}</td>
   		</tr>
    	<tr>
    		<td>{IMAGE_TABLE_DATA6}</td>
    	</tr>
  		</table>
  		</div>
  	<!-- END IMAGE_TABLE_DATA --> 
  	<div class="row">
  	<input name="checkPoint" type="checkbox" value="checkPoint" id="checkpoint" onclick="checkAll(checkPoint);"/> 
  	<label for="checkpoint">{CHECK_MESSAGE}</label>
  	</div>
  	
  	<div class="row buttons">
  	<!-- BEGIN IMAGE_BUTTON -->
  		<input type="submit" name="{IMAGE_BUTTON_NAME}" value="{IMAGE_BUTTON_VALUE}"/>
  	<!-- END IMAGE_BUTTON -->
  	</div>
</form>
  	</div>
<!-- END IMAGE_TABLE_PART -->

	<div id="pa_pagination">
		<ul>
			<!-- BEGIN B_DATA_PART -->
			{B_DATA}
			<!-- END B_DATA_PART -->
		</ul>
		</div>

