<form method="post" action="<?php echo url_rewrite(AT_SOCIAL_BASENAME.'applications.php');?>">
<? 
  if (! empty($this->settings)) {
    $settings = $this->settings;
    foreach ($settings as $key => $setting) {
      $name = ! empty($setting->displayName) ? $setting->displayName : $key;
      $default = isset($setting->default) ? $setting->default : '';
//      $value = isset($vars['application']['user_prefs'][$key]) ? $vars['application']['user_prefs'][$key] : $default;
      echo "<div><div class=\"settings_label\">$name</div>";
      switch ($setting->type) {
        case 'ENUM':
          echo "<select name=\"$key\">\n";
          foreach ($setting->enumValues as $k => $v) {
            $sel = ($k == $value) ? ' SELECTED' : '';
            echo "<option value=\"$k\" $sel>$v</option>\n";
          }
          echo "</select>\n";
          break;
        case 'STRING':
			if (isset($this->user_settings[$key]) && $this->user_settings[$key]!=''){
				$default=$this->user_settings[$key];
			}
			echo "<input type=\"text\" name=\"$key\" value=\"$default\" />\n";
			break;
		case 'HIDDEN':
			//hide these for now so that they don't get saved?
			//echo "<input type=\"hidden\" name=\"$key\" value=\"$default\" />\n";
			break;
        case 'BOOL':
			//TODO add radio boxes here, should they be yes / no?
			break;
        case 'LIST':
			//TODO not sure what to do with this one yet
			break;
        case 'NUMBER':
			echo "<input type=\"text\" name=\"$key\" value=\"$default\" />\n";
			break;
      }
      echo "</div>";
    }
  }
  ?>
  <input type="hidden" name="app_id" value="<?php echo $this->app_id; ?>" />
  <input type="hidden" name="app_settings" value="1" />
  <input type="submit" value="<?php echo _AT('save');?>"/>
</form>