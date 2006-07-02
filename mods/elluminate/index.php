<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
$_custom_css = $_base_path . 'mods/elluminate/module.css'; // use a custom stylesheet
require (AT_INCLUDE_PATH.'header.inc.php');
?>
<div id="elluminate">
<?php if($_GET['guest'] != '1'){ ?>
            <table width="35%" border="0" cellspacing="0" cellpadding="0" class="logintable" align="center">
              <tr>
                <td>
                  <p class="sideindent1"><img src="mods/elluminate/elluminate_logo.gif" align="center" alt=""><br> <?php echo _AT('elluminate_loginname'); ?><?php echo _AT('elluminate_passwordln'); ?></p>
                  <table width="100%" border="0" cellspacing="0" cellpadding="6">
                    <tr>
                      <td>
                        <form name="loginform" method="post" action="<?php echo $_config['elluminate']; ?>">
                          <label for="userName" class="normalbody"><?php echo _AT('elluminate_name'); ?>:</label><br> <input type="text" name="username" class="fieldOff" onFocus="doBg('username');" onBlur="dontBg('username');"><br>

                          <label for="password" class="normalbody"><?php echo _AT('elluminate_password'); ?>:</label><br> <input type="password" name="password" class="fieldOff" onFocus="doBg('password');" onBlur="dontBg('password');"><br>
                          <br><br>
                          <input button type="submit" name="submit" value="Login" class="button">
                        </form><br />
			<?php if($_config['elluminate_pw'] != ''){ ?>
				<a href="<?php echo $_SERVER['PHP_SELF']; ?>?guest=1"><?php echo _AT('elluminate_guest'); ?></a>
			<?php } ?>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
<?php }else { ?>
	<table width="35%" border="0" cellspacing="0" cellpadding="0" class="logintable" align="center">
              <tr>
                <td>
                  <p class="sideindent1"><img src="mods/elluminate/elluminate_logo.gif" align="center" alt=""><br>                       <?php echo _AT('elluminate_loginname'); ?><br /></p>
                  <table width="100%" border="0" cellspacing="0" cellpadding="6">
                    <tr>
                      <td>
                        <form name="loginform" method="post" action="<?php echo $_config['elluminate']; ?>">

			<label for="userName" class="normalbody">   <?php echo _AT('elluminate_name'); ?></label><br> <input type="text" name="username" class="fieldOff" onFocus="doBg('username');" onBlur="dontBg('username');"><br>
                          <input type="hidden" name="password" value="<?php echo  $_config['elluminate_pw']; ?>">
                          <br><br>

                          <input button type="submit" name="submit" value="Login" class="button">
                        </form>

                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
<?php } ?>
</div>
<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>