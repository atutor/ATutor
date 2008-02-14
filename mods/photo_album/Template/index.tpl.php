<!-- BEGIN IMAGE_START -->

			<h3 style="clear:right;">
				{IMAGE_PAGE_TITLE}
			</h3>

		<div style="width: 90%; margin-right: auto; margin-left: auto;">		
			<ul id="navlist">
				<li><a href="{MAIN_URL}" title="{MAIN_TITLE}" class="active"><strong>{MAIN_TITLE}</strong></a></li>
				<li><a href="{MY_PHOTO_URL}" title="{MY_PHOTO_TITLE}">{MY_PHOTO_TITLE}</a></li>
				<li><a href="{MY_COMMENT_URL}" title="{MY_COMMENT_TITLE}">{MY_COMMENT_TITLE}</a></li>
			</ul>
		</div>	
		
		<div name="form1" id="form1" class="input-form" style="width:95%;border:thin black solid;">

				<!-- the following block is a Fluid image container, replacing the orginal, beginning with 
					a form named reorder-form, -->		
			<form name="{SAVE_FORM_NAME}" method="post" action="{SAVE_ACTION}" id="reorder-form">			
			<!-- IMAGE CONTAINER -->
			<div  id="gallery:::gallery-thumbs:::" tabindex="0" class="image-container"
			xhtml10:role="wairole:grid"
			aaa:multiselectable="false"
			aaa:readonly="false"
			aaa:activedescendent="gallery:::gallery-thumbs:::lightbox-cell:0:"
				aaa:disabled="false">

				<!-- BEGIN IMAGE_DISPLAY -->
				<div  class="float orderable-default" id="gallery:::gallery-thumbs:::lightbox-cell:{TABINDEX}:"
						role="wairole:gridcell"
						aaa:selected="true"
						aaa:readonly="false"
						aaa:disabled="false"
						aaa:grab="supported"
						aaa:dropeffect="move">
					<div>
						<div class="image-inner-container">
							<a href="{LINK}" >
							<img id="fluid.img.{TABINDEX}" src="{IMAGE_SRC}" border="0" alt="{IMAGE_ALT}"/>
							</a>
						</div>
						<div class="caption image-title">
							<a href="{LINK}" >{IMAGE_TITLE}</a>
						</div>
			
						<input name="{IMAGE_ID}" id="gallery:::gallery-thumbs:::lightbox-cell:{TABINDEX}:reorder-index" value="{TABINDEX}" type="hidden"/>      
					</div>					
				</div>
				<!-- END IMAGE_DISPLAY -->
		
				<!-- BEGIN IMAGE_ADD_BUTTON -->
				<div class="row buttons" style="clear:both;">
					<!-- form name="{FORM_NAME}" method="post" action="{ACTION}" -->
					<input type="submit" name="save" value="{SAVE_STRING}" onsubmit="reordering_pa('save_form');" />
					<input type="hidden" name="mode" value="save" />
					<input type="hidden" name="current_page_num" value="{CURRENT_PAGE_NUM}" />
					</form>	

					<form name="{FORM_NAME}" method="post" action="{ACTION}">
					<input type="submit" name="add" value="{ADD_STRING}"/>
					<input type="hidden" name="mode" value="add"/>
					<input type="hidden" name="choose" value="{CHOOSE_VALUE}"/>
					</form>	
				</div>
				<!-- END IMAGE_ADD_BUTTON -->
				<div id="pa_pagination">
					<ul>
					<!-- BEGIN B_DATA_PART -->
						{B_DATA}
					<!-- END B_DATA_PART -->
					</ul>
				</div>
		</div>
	</div>
<!-- END IMAGE_START -->
