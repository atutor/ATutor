<div>
	<div class="input-form">
		<p><?php echo _AT('jb_admin_add_category_blub'); ?></p>
		<form action="" method="post">
			<div class="row">
				<label for="jb_category_name"><?php echo _AT('name'); ?></label>
				<input type="text" name="category_name" id="jb_category_name" />
				<input type="hidden" name="action" value="add" />
				<input class="button" type="submit" name="submit" value="<?php echo _AT("save"); ?>" />
			</div>
		</form>
	</div>

	<div class="admin_categories_container" id="admin_categories_container">
	<p><?php echo _AT('jb_admin_edit_categories_blub'); ?></p>
	<?php if(!empty($this->categories)): ?>
	<?php foreach($this->categories as $category): ?>
		<div class="admin_categories">
			<div class="left">
				<div class="flc-inlineEditable"><span class="flc-inlineEdit-text" id="<?php echo $category['id'];?>"><?php echo $category['name']; ?></span></div>
			</div>
			<div class="right"><a href="<?php echo AT_JB_BASENAME;?>admin/categories.php?submit=delete<?php echo SEP; ?>action=delete<?php echo SEP;?>cid=<?php echo $category['id']; ?>" ><?php echo _AT('delete'); ?></a></div>
		</div>
	<?php endforeach; endif; ?>
	</div>
</div>


<script type="text/javascript">
jQuery(document).ready(function () {
	//the ATutor undo function
	var undo = function (that, targetContainer) {
					var markup = "<span class='flc-undo' aria-live='polite' aria-relevant='all'>" +
					  "<span class='flc-undo-undoContainer'>[<a href='#' class='flc-undo-undoControl'><?php echo _AT('pa_undo'); ?></a>]</span>" +
					  "<span class='flc-undo-redoContainer'>[<a href='#' class='flc-undo-redoControl'><?php echo _AT('pa_redo'); ?></a>]</span>" +
					"</span>";
					var markupNode = jQuery(markup);
					targetContainer.append(markupNode);
					return markupNode;
				};
	var click_here_to_edit = '<?php echo _AT("pa_click_here_to_edit"); ?>';
	var click_item_to_edit = '<?php echo _AT("pa_click_item_to_edit"); ?>';

	/* inline edit for photo panel description */
    fluid.inlineEdits("#admin_categories_container", {
		componentDecorators: {
			type: "fluid.undoDecorator",
			options: {
				renderer: undo
			}
		},		
		defaultViewText: click_here_to_edit,
		useTooltip: true,
		tooltipText: click_item_to_edit, 
		listeners: {
			modelChanged: function(model, oldModel, source){
				/* for undo/redo model change */
				if (model != oldModel && source != undefined){
					viewNode = source.component.container.children('.flc-inlineEdit-text')[0];
					rtn = jQuery.post("<?php echo $_base_path. AT_JB_BASENAME.'admin/categories.php';?>", 
						{"submit":"ajax",
						 "action":"edit",
						 "cid":viewNode.id, 
						 "category_name":model.value},
						  function(data){}, 
						  "json");
				}
			},
			afterFinishEdit : function (newValue, oldValue, editNode, viewNode) {
				if (newValue != oldValue){
					rtn = jQuery.post("<?php echo $_base_path. AT_JB_BASENAME.'admin/categories.php';?>", 
							{"submit":"ajax",
							 "action":"edit",
							 "cid":viewNode.id, 
							 "category_name":newValue},
							  function(data){}, 
							  "json");
				}
			}
		}
	});
});
</script>
