<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td class="cyan" align="right" valign="middle">
			<?php foreach ($tmpl_nav as $link): ?>
					<a class="cyan" href="<?php echo $link['url'] ?>"><?php echo $link['name'] ?></a>
					<span class="spacer">|</span>
			<?php endforeach; ?>

			</td>
		</tr>
		</table></td>
		</tr>
<tr>
	<td><h2><?php echo $tmpl_section; ?></h2>