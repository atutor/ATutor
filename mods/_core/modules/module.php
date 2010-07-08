<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }

if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

define('AT_ADMIN_PRIV_MODULES', $this->getAdminPrivilege());

//admin pages
//if (admin_authenticate(AT_ADMIN_PRIV_RSS, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {

           $this->_pages['mods/_core/modules/index.php']['title_var'] = 'modules';
            $this->_pages['mods/_core/modules/index.php']['parent']    = AT_NAV_ADMIN;
            $this->_pages['mods/_core/modules/index.php']['guide']     = 'admin/?p=modules.php';
            $this->_pages['mods/_core/modules/index.php']['children']  = array('mods/_core/modules/install_modules.php');

            $this->_pages['mods/_core/modules/details.php']['title_var'] = 'details';
            $this->_pages['mods/_core/modules/details.php']['parent']    = 'mods/_core/modules/index.php';

            $this->_pages['mods/_core/modules/module_uninstall_step_1.php']['title_var'] = 'module_uninstall';
            $this->_pages['mods/_core/modules/module_uninstall_step_1.php']['parent']    = 'mods/_core/modules/index.php';

            $this->_pages['mods/_core/modules/module_uninstall_step_2.php']['title_var'] = 'module_uninstall';
            $this->_pages['mods/_core/modules/module_uninstall_step_2.php']['parent']    = 'mods/_core/modules/index.php';

            $this->_pages['mods/_core/modules/module_uninstall_step_3.php']['title_var'] = 'module_uninstall';
            $this->_pages['mods/_core/modules/module_uninstall_step_3.php']['parent']    = 'mods/_core/modules/index.php';

            $this->_pages['mods/_core/modules/install_modules.php']['title_var'] = 'install_modules';
            $this->_pages['mods/_core/modules/install_modules.php']['parent']    = 'mods/_core/modules/index.php';
            $this->_pages['mods/_core/modules/install_modules.php']['guide']     = 'admin/?p=modules.php';

            $this->_pages['mods/_core/modules/version_history.php']['title_var'] = 'version_history';
            $this->_pages['mods/_core/modules/version_history.php']['parent']    = 'mods/_core/modules/install_modules.php';

            $this->_pages['mods/_core/modules/module_install_step_1.php']['title_var'] = 'details';
            $this->_pages['mods/_core/modules/module_install_step_1.php']['parent']    = 'mods/_core/modules/install_modules.php';

            $this->_pages['mods/_core/modules/module_install_step_2.php']['title_var'] = 'details';
            $this->_pages['mods/_core/modules/module_install_step_2.php']['parent']    = 'mods/_core/modules/install_modules.php';

            $this->_pages['mods/_core/modules/module_install_step_3.php']['title_var'] = 'details';
            $this->_pages['mods/_core/modules/module_install_step_3.php']['parent']    = 'mods/_core/modules/install_modules.php';

            $this->_pages['mods/_core/modules/confirm.php']['title_var'] = 'confirm';
            $this->_pages['mods/_core/modules/confirm.php']['parent']    = 'mods/_core/modules/add_new.php';
//}

?>