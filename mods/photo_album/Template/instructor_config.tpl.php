<h3>{TITLE}</h3>
<p>{CONFIG_NOTE}</p>
<div id="pa_config_notice">{CONFIG_STRING} : {CONFIG_VALUE}</div>

<div class="input-form">
<div class="row" style="text-align:right;">
<form name="{FORM_NAME}" method="post" action="{ACTION}">
<input name="radiobutton" type="radio" value="{RADIO_VALUE1}" id="radio1" {CHECKED1} /><label for="radio1">{RADIO_STRING1}</label>
<input name="radiobutton" type="radio" value="{RADIO_VALUE2}" id="radio2" {CHECKED2} /><label for="radio2">{RADIO_STRING2}</label>
<div class="buttons" style="display:inline;">
<input type="submit" name="submit" value="{SUBMIT_VALUE}"/>
</div>
</form>
</div>
</div>
