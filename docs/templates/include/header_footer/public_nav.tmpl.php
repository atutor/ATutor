<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td class="cyan" align="right" valign="middle">
				<?php if ($tmpl_page == HOME_URL && HOME_URL !='') : ?>
					<u><?php echo _AT('home') ?></u> <span class="spacer">|</span>
				<?php elseif (HOME_URL!=''): ?>
					<a class="cyan" href="<?php echo HOME_URL ?>"><?php echo _AT('home') ?></a> <span class="spacer">|</span>
				<?php endif; ?>
				
				<?php if ($tmpl_page == 'register'): ?>
					<u><?php echo _AT('register') ?></u>
				<?php else: ?>
					<a class="cyan" href="registration.php"><?php echo _AT('register') ?></a>
				<?php endif; ?>

				<span class="spacer">|</span>
				
				<?php if ($tmpl_page == 'browse'): ?>
					<u><?php echo _AT('browse_courses') ?></u>
				<?php else: ?>
					<a class="cyan" href="browse.php"><?php echo _AT('browse_courses') ?></a>
				<?php endif; ?>

				<span class="spacer">|</span>
				
				<?php if ($tmpl_page == 'login'): ?>
					<u><?php echo _AT('login') ?></u>
				<?php else: ?>
					<a class="cyan" href="login.php"><?php echo _AT('login') ?></a>
				<?php endif; ?>
				
				<span class="spacer">|</span>
				
				<?php if ($tmpl_page == 'password'): ?>
					<u><?php echo _AT('password_reminder') ?></u>
				<?php else: ?>
					<a class="cyan" href="password_reminder.php"><?php echo _AT('password_reminder') ?></a>
				<?php endif; ?>
			</td>
		</tr>
		</table></td>
		</tr>
<tr>
	<td>