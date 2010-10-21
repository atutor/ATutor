<div>
<h1>Grant access to your private information?</h1>

<form class="input-form" action="approve_authorization.php" method="post">
	<div class="row">
		An application is requesting access to your information. You should
		only approve this request if you trust the application.
	</div>
	<input type="hidden" name="oauth_token"	value="<?php echo htmlspecialchars($this->token); ?>" /> 
	<input type="hidden" name="oauth_callback" value="<?php echo htmlspecialchars($this->callback); ?>" />
	<input class="button" type="submit" value="Approve" />
	<input class="button" type="button" value="Decline" onclick="location.href='/'" />
</form>
<div style="clear: both"></div>
</div>