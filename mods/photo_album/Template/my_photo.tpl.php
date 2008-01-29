<h3 style="clear:right;">
	{TITLE}
</h3>

<div style="width: 95%; margin-right: auto; margin-left: auto;">		
	<ul id="navlist">
		<li><a href="{MAIN_URL}" title="{MAIN_TITLE}">{MAIN_TITLE}</a></li>
		<li><a href="{MY_PHOTO_URL}" title="{MY_PHOTO_TITLE}" class="active"><strong>{MY_PHOTO_TITLE}</strong></a></li>
		<li><a href="{MY_COMMENT_URL}" title="{MY_COMMENT_TITLE}">{MY_COMMENT_TITLE}</a></li>
	</ul>
</div>	

	<div class="input-form"  style="width: 95%; margin-right: auto; margin-left: auto;">

		<div style="width: 95%; margin-right: auto; margin-left: auto; margin-top:.5em;">
		<!-- BEGIN SELECT_PART -->
			<a href="{DESTINATION}">{LINK_TEXT}</a>
		<!-- END SELECT_PART -->
		</div>

	<!-- BEGIN IMAGE_DATA -->
		<div class="img_border">
		 <h4>{IMAGE_DATA1}</h4>
		<p>{IMAGE_DATA2}</p>
		<p>{IMAGE_DATA3}</p>
			<form name="{FORM_NAME}" method="post" action="{ACTION}">
				<input type="hidden" name="mode" value="edit"/>
				<input type="hidden" name="choose" value="{CHOOSE_VALUE}"/>
				<input type="hidden" name="image_id" value="{IMAGE_ID}"/>
				<div class="buttons">
				<input type="submit" name="submit" value="{EDIT_VALUE}"/>
				</div>
			</form>	
		
		</div>
 	<!-- END IMAGE_DATA --> 
		<div class="img_spacer">
			&nbsp;
		</div>
	</div>

	<div id="pa_pagination">
		<ul>
			<!-- BEGIN B_DATA_PART -->
			{B_DATA}
			<!-- END B_DATA_PART -->
		</ul>
	</div>
