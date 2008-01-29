<h3 style="clear:right;">
	{TITLE}
</h3>

<div style="width: 95%; margin-right: auto; margin-left: auto;">		
	<ul id="navlist">
		<li><a href="{MAIN_URL}" title="{MAIN_TITLE}">{MAIN_TITLE}</a></li>
		<li><a href="{MY_PHOTO_URL}" title="{MY_PHOTO_TITLE}">{MY_PHOTO_TITLE}</a></li>
		<li><a href="{MY_COMMENT_URL}" title="{MY_COMMENT_TITLE}"  class="active"><strong>{MY_COMMENT_TITLE}</strong></a></li>
	</ul>
</div>	

<div class="input-form" style="width: 95%; margin-right: auto; margin-left: auto;">
	<div style="width: 95%; margin-right: auto; margin-left: auto; margin-top:.5em;">
	<!-- BEGIN SELECT_PART -->
		<a href="{DESTINATION}">{LINK_TEXT}</a>
	<!-- END SELECT_PART -->
	</div>

	<!-- BEGIN COMMENT_TABLE_DATA -->
	<div class="row">
		<table class="data" width="90%"  border="0">
    	<tr>
      		<td width="13%" colspan="2">{COMMENT_TABLE_DATA1}</td>
    	</tr>
    	<tr>
      		<td width="50%">{COMMENT_TABLE_DATA2}</td>
      		<td>{COMMENT_TABLE_DATA3}</td>
    	</tr>
    	<tr>
    		<td>{COMMENT_TABLE_DATA4}</td>
    	</tr>
    	</table>
    </div>
 	<!-- END COMMENT_TABLE_DATA --> 
</div>

	<div id="pa_pagination">
		<ul>
			<!-- BEGIN B_DATA_PART -->
			{B_DATA}
			<!-- END B_DATA_PART -->
		</ul>
	</div>
