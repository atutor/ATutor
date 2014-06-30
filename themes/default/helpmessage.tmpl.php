<?php  
/*
 * @author Jacek Materna
 *
 *  One Savant variable: $item which is the processed ouput message content according to lang spec.
 */
 
global $_base_href;

?>

<div id="help" class="divClass">
    <a href="#" onclick="return false;" id="delete" class="deleteDiv" style="float:right; clear:right;">
        <!-- <img src="<?php echo $this->img; ?>close_icon.png" alt="<?php echo _AT('close'); ?>"/> -->
         <img src="<?php echo $this->img; ?>close_icon.png" alt="<?php echo _AT('helpme_dismiss'); ?>" title="<?php echo _AT('helpme_dismiss'); ?>" role="link"/>
           
        
        <!-- <img src="<?php echo $_base_href; ?>close_icon.png" alt="<?php echo _AT('helpme_dismiss'); ?>" title="<?php echo _AT('helpme_dismiss'); ?>" role="link"/>
        -->
    </a>

    <?php if (is_array($this->item)) : ?>
        <ul>
        <?php foreach($this->item as $i) : ?>
            <li role="alert"><?php echo $i; ?></li>
        <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <a href="#" onclick="return false;" id="dismiss_all" >
        <img src="<?php echo $_base_href; ?>mods/helpme/images/close.png" alt="<?php echo _AT('helpme_dismiss_all'); ?>" title="<?php echo _AT('helpme_dismiss_all'); ?>" role="link"/>
    </a>

 <div class="helpme_count"><?php echo $this->helpme_count; ?>/<?php echo $this->helpme_total; ?></div>
</div>

