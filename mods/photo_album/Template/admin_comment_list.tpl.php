<script language="JavaScript" type="text/javascript">
	function checkAll(check){
		for (i=0; i< {ADMIN_NUMBER_OF_COMMENT}; i++){
			var e=eval("document.table_form.commentId"+i);
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
<!-- BEGIN SELECT_PART -->
<div class="row buttons">
<form name="{SELECT_FORM_NAME}" method="get" action="{SELECT_ACTION}">
	<label for="{SELECT_NAME}">{SELECT_LABEL}</label>
    <select name="{SELECT_NAME}" id="{SELECT_NAME}">
    <!-- BEGIN OPTION_PART -->
    	<option value="{OPTION_VALUE}">{OPTION_STRING}</option>
    <!-- END OPTION_PART -->
    </select>
    <input type="submit" class="button" name="{SELECT_SUBMIT}" value="{SELECT_SUBMIT_VALUE}"/>
</form>
</div>
<!-- END SELECT_PART -->


<!-- BEGIN COMMENT_TABLE_PART -->
<form name="{COMMENT_TABLE_FORM_NAME}" method="post" action="{COMMENT_TABLE_ACTION}">
	<!-- BEGIN COMMENT_TABLE_DATA -->
		<div class="row">
  		<input type="checkbox" style="clear:left;float:left;" id="{CHECK_NAME}" name="{CHECK_NAME}" value="{CHECK_VALUE}"/>
  		<table class="data" width="90%"  border="0">
    	<tr>
      		<td width="13%" colspan="2"><label for="{CHECK_NAME}">{COMMENT_TABLE_DATA1}</label></td>
    	</tr>
    	<tr>
      		<td>{COMMENT_TABLE_DATA2}</td>
      		<td>{COMMENT_TABLE_DATA3}</td>
    	</tr>
    	<tr>
    		<td>{COMMENT_TABLE_DATA4}</td>
    	</tr>
     	</table>
  		</div>
  	<!-- END COMMENT_TABLE_DATA --> 
  	<div class="row">
  	<input name="checkPoint" type="checkbox" value="checkPoint" id="checkpoint" onclick="checkAll(checkPoint);"/><label for="checkpoint">{CHECK_ALL_MSG}</label>
  	</div>
  	<div class="row buttons">
  	<!-- BEGIN COMMENT_BUTTON -->
  		<input type="submit" name="{COMMENT_BUTTON_NAME}" value="{COMMENT_BUTTON_VALUE}"/>
  	<!-- END COMMENT_BUTTON -->
  	</div>
</form>
</div>
<!-- END COMMENT_TABLE_PART -->

	<div id="pa_pagination">
		<ul>
			<!-- BEGIN B_DATA_PART -->
			{B_DATA}
			<!-- END B_DATA_PART -->
		</ul>
		</div>