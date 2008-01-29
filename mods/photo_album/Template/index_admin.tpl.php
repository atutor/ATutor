<h3>{TITLE}</h3>
<div class="row">
	<p>{MESSAGE}</p>
</div>

<div class="input-form">
<div class="row">
<form name="{FORM_NAME}" method="post" action="{FORM_ACTION}">
  <div style="display:none;"><label for="{SELECT_NAME}">Select</label></div>
  <select name="{SELECT_NAME}" id="{SELECT_NAME}">
    <!-- BEGIN OPTION_VALUE -->
  	<option value="{VALUE}">{TEXT}</option>
    <!-- END OPTION_VALUE -->
  </select>
  <input type="submit" class="button" name="{SUBMIT_NAME}" value="{SUBMIT_VALUE}"/>
</form>
</div>
</div>
