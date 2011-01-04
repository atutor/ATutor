<?php
/*******
 * doesn't allow this file to be loaded with a browser.
 */
if (!defined('AT_INCLUDE_PATH')) { exit; }

/******
 * this file must only be included within a Module obj
 */
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

/*******
 * assign the instructor and admin privileges to the constants.
 */
define('AT_PRIV_ECOMM',       $this->getPrivilege());
define('AT_ADMIN_PRIV_ECOMM', $this->getAdminPrivilege());


/*******
 * add the admin pages when needed.
 */
if (admin_authenticate(AT_ADMIN_PRIV_ECOMM, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
	$this->_pages[AT_NAV_ADMIN] = array('mods/ecomm/payments_admin.php');
	$this->_pages['mods/ecomm/payments_admin.php']['title_var'] = 'ec_payments';
	$this->_pages['mods/ecomm/payments_admin.php']['parent']    = AT_NAV_ADMIN;
	$this->_pages['mods/ecomm/payments_admin.php']['children'] = array('mods/ecomm/index_admin.php','mods/ecomm/index_admin_approve.php', 'mods/_core/enrolment/admin/index.php');

	$this->_pages['mods/ecomm/index_admin.php']['title_var'] = 'ec_settings';
	$this->_pages['mods/ecomm/index_admin.php']['parent']    = 'mods/ecomm/payments_admin.php';

	$this->_pages['mods/ecomm/index_admin_approve.php']['title_var'] = 'ec_approve_manually';
	$this->_pages['mods/ecomm/index_admin_approve.php']['parent']    = 'mods/ecomm/payments_admin.php';
}

/*******
 * instructor Manage section:
 */
$this->_pages['mods/ecomm/response_ipn.php']['title_var']     = 'ec_payments';
$this->_pages['mods/ecomm/response_user.php']['title_var']    = 'ec_payments';
$this->_pages['mods/ecomm/error_beanstream.php']['title_var']    = 'ec_payments';
$this->_pages['mods/ecomm/success_beanstream.php']['title_var']    = 'ec_payments';
$this->_pages['mods/ecomm/failure_beanstream.php']['title_var']    = 'ec_payments';
$this->_pages['mods/ecomm/index_instructor.php']['title_var'] = 'ec_payments';
$this->_pages['mods/ecomm/index_instructor.php']['parent']    = 'tools/index.php';
$this->_pages['mods/ecomm/index_instructor.php']['children']  = array('mods/_core/enrolment/index.php');
$this->_pages['tools/enrollment/index.php']['children']       = array('mods/ecomm/index_instructor.php');


/* my start page pages */
$this->_pages[AT_NAV_START]  = array('mods/ecomm/index.php');
$this->_pages['mods/ecomm/index.php']['title_var'] = 'ec_payments';
$this->_pages['mods/ecomm/index.php']['parent']    = AT_NAV_START;

$this->_pages['mods/ecomm/payment.php']['title_var'] = 'ec_payments';
$this->_pages['mods/ecomm/payment.php']['parent']    = 'mods/ecomm/index.php';
$this->_pages['mods/ecomm/index.php']['children']    = array('users/index.php','users/browse.php');

$this->_pages['mods/ecomm/failure.php']['title_var'] = 'ec_payments';
$this->_pages['mods/ecomm/invoice.php']['title_var'] = 'ec_payments';
?>