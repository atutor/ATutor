<div class="seq">
	<?php if (isset($previous_page)): ?>
		Previous Chapter: <a href="<?php echo $previous_page; ?>" title="<?php echo $_pages[$previous_page]; ?> Alt+,"><?php echo $_pages[$previous_page]; ?></a><br /> 
	<?php endif; ?>

	<?php if (isset($next_page)): ?>
		Next Chapter: <a href="<?php echo $next_page; ?>" title="<?php echo $_pages[$next_page]; ?> Alt+."><?php echo $_pages[$next_page]; ?></a>
	<?php endif; ?>
</div>
</body>
</html>