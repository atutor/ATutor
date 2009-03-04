<form method="post">
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
          echo "<input type=\"text\" name=\"$key\" value=\"$default\" />\n";
          break;
        case 'HIDDEN':
          echo "<input type=\"hidden\" name=\"$key\" value=\"$default\" />\n";
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
    echo "<br /><input type=\"submit\" value=\"Save\" />\n</form>\n";
  }
  ?>