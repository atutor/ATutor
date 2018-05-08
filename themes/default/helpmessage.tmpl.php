<?php  
/*
 * @author Jacek Materna
 *
 *  One Savant variable: $item which is the processed ouput message content according to lang spec.
 */
 
global $_base_href;

?>

<div id="help" class="divClass" tabindex="0">
<div class="deleteDiv">
<a href="#" onclick="return false;" id="revisit"  tabindex="-1">
<img src="<?php echo $this->img; ?>previous.png" alt="<?php echo _AT('helpme_revisit'); ?>" title="<?php echo _AT('helpme_revisit'); ?>" role="link"  tabindex="0"/>
</a>
    <a href="#" onclick="return false;" id="delete"  tabindex="-1">
         <img src="<?php echo $this->img; ?>close_icon.png" alt="<?php echo _AT('helpme_dismiss'); ?>" title="<?php echo _AT('helpme_dismiss'); ?>" role="link" tabindex="0"/>
    </a>
</div>
    <?php if (is_array($this->item)) : ?>
        <ul>
        <?php foreach($this->item as $i) : ?>
            <li aria-live="polite" style="display:none;"><?php echo $i; ?></li>
        <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <div class="msg_buttons">
    <div class="helpme_count" aria-live="polite"><?php echo $this->helpme_count; ?>/<?php echo $this->helpme_total; ?></div>
    <a href="#" onclick="return false;" id="dismiss_all" tabindex="-1">
        <img src="<?php echo $_base_href; ?>mods/_standard/helpme/images/close.png" alt="<?php echo _AT('helpme_dismiss_all'); ?>" title="<?php echo _AT('helpme_dismiss_all'); ?>" role="link" tabindex="0"/>
    </a>
    <a href="#" onclick="return false;" id="helpme_reset"  tabindex="-1">
        <img src="<?php echo $_base_href; ?>mods/_standard/helpme/images/reload.png" alt="<?php echo _AT('helpme_reset'); ?>" title="<?php echo _AT('helpme_reset'); ?>" role="link" tabindex="0"/>
    </a>
 
 </div>
</div>

