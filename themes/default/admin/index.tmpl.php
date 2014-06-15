<?php global $_config; $_config_defaults;?>


<div class="admin_container"> 
	<?php 
	$this->path_length = strlen($this->base_path);

	echo '<ol id="tools">';
	foreach ($this->top_level_pages as $page_info) {
		echo '<li class="top-tool"><a href="' . $page_info['url'] . '">' . $page_info['title'] . '</a>  ';

		$page_info['url'] = substr($page_info['url'], $this->path_length);

		if ($this->pages[$page_info['url']]['children']) {
			echo '<ul class="child-top-tool">';
			foreach ($this->pages[$page_info['url']]['children'] as $child) {
				echo ' <li class="child-tool"><a href="'.$child.'">'._AT($this->pages[$child]['title_var']).'</a></li>';
			}
			echo '</ul>';
		}
		echo '</li>'; //end top-tool
	}
	echo '</ol>';
	
?>

</div>  <!-- end "container" -->

<div class="admin_container_r">

	<div class="input-form">
	
		<form method="get" action="mods/_core/users/instructor_requests.php">
			<div class="row">
				<h3><?php echo _AT('instructor_requests'); ?></h3>
				
				<?php 
				if(is_array($this->row_instructor)){
				foreach($this->row_instructor as $key => $value): ?>
				
				<p><?php echo _AT('instructor_requests_text', $value['cnt']); ?></p>
				<?php endforeach;
				}?>
			</div>

			<div class="row buttons">
				<input type="submit" name="submit" value="<?php echo _AT('view'); ?>" />
			</div>
			
		</form>
		
	</div>

	<?php if(!defined('IS_SUBSITE')){ ?>
	<div class="input-form">
		<form method="get" action="mods/_standard/patcher/index_admin.php">
			<div class="row">
				<h3><?php echo _AT('available_patches'); ?></h3>
				<p><?php echo _AT('available_patches_text', $this->cnt); ?></p>
			</div>

			<div class="row buttons">
				<input type="submit" name="submit" value="<?php echo _AT('view'); ?>" />
			</div>
		</form>
	</div>
	<?php } ?>
	
	<div class="input-form">
		<div class="row">
			<h3><?php echo _AT('statistics_information'); ?></h3>

			<dl class="col-list">
				<?php if ($this->db_size): ?>
					<dt><?php echo _AT('database'); ?>:</dt>
					<dd><?php echo number_format($this->db_size/AT_KBYTE_SIZE/AT_KBYTE_SIZE,2); ?> <acronym title="<?php echo _AT('megabytes'); ?>"><?php echo _AT('mb'); ?></acronym></dd>
				<?php endif; ?>

				<?php if ($this->du_size): ?>
					<dt><?php echo _AT('disk_usage'); ?>:</dt>
					<dd><?php echo number_format($this->du_size/AT_KBYTE_SIZE,2); ?> <acronym title="<?php echo _AT('megabytes'); ?>"><?php echo _AT('mb'); ?></acronym></dd>
				<?php endif; ?>

				<dt><?php echo _AT('courses'); ?>:</dt>
				<dd><?php echo $this->num_courses; ?></dd>

				<dt><?php echo _AT('users'); ?>:</dt>
				<dd><?php echo $this->num_users; ?></dd>

				<dt><?php echo _AT('atutor_version'); ?>:</dt>
                <?php
                    require('../svn.php');
                    if (!empty($svn_data)) {
                        $svn_data   = explode("\n", $svn_data);
                        if (substr($svn_data[1], 0, 1) == 'r') {
                            $svn_data = $svn_data[1];
                        } else if (substr($svn_data[2], 0, 1) == 'r') {
                            $svn_data = $svn_data[2];
                        }

                        if (count($svn_data) > 1) {
                            $build = 'unknown';
                            $build_date = date('Y-m-d H:i:s');
                        } else {
                            $svn_data   = explode(' ', $svn_data);

                            $build      = $svn_data[0];
                            $build_date = $svn_data[4] .' '. $svn_data[5];
                        }
                        $build_str = '(' . $build . ' - '.$build_date . ')';
                    }
                ?>
				<dd><?php echo _AT('atutor_version_text', VERSION . $build_str, urlencode(VERSION)); ?></dd>

				<dt><?php echo _AT('php_version'); ?>:</dt>
				<dd><?php echo PHP_VERSION; ?></dd>

				<dt><?php echo _AT('mysql_version'); ?>:</dt>
				<dd><?php echo $this->db_version; ?></dd>

				<dt><?php echo _AT('os'); ?>:</dt>
				<dd><?php echo @php_uname('s') . ' ' . @php_uname('r'); ?></dd>
			</dl>
		</div>
	</div>
	<div class="input-form">
			<div class="row">
				<h3><?php echo _AT('social_switch'); ?></h3>
				<p><?php echo _AT('social_switch_text'); ?></p>
			</div>
			<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<fieldset>
			<legend id="social_networking"><?php echo _AT('social_switch'); ?></legend>(<?php echo _AT('default'); ?>: <?php echo ($_config_defaults['just_social'] ? _AT('just_social') : _AT('social_and_lms')); ?>)
			<div class="row">
			<input type="radio" name="just_social" value="1" id="social_y" <?php if($_config['just_social']) { echo 'checked="checked"'; }?>  /><label for="social_y"><?php echo _AT('just_social'); ?></label> <br /><input type="radio" name="just_social" value="0" id="social_n" <?php if(!$_config['just_social']) { echo 'checked="checked"'; }?>  /><label for="social_n"><?php echo _AT('social_and_lms'); ?></label>
			</div>

			<div class="row buttons">
				<input type="submit" name="social_submit" value="<?php echo _AT('save'); ?>" />
			</div>
			</fieldset>
			</form>

	</div>
</div> 
