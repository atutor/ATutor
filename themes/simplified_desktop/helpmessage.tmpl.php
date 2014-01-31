<?php  
/*
 * @author Jacek Materna
 *
 *  One Savant variable: $item which is the processed ouput message content according to lang spec.
 */
 
global $_base_href;

?>

<div id="help" class="divClass">
    <?php if (is_array($this->item)) : ?>
        <ul>
        <?php foreach($this->item as $i) : ?>
            <li role="alert"><?php echo $i; ?></li>
        <?php endforeach; ?>
        </ul>
    <?php endif; ?>
 <div class="helpme_count"><?php echo $this->helpme_count; ?>/<?php echo $this->helpme_total; ?></div>
</div>

