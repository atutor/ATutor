# sql file for ATutor Ecommerce module

CREATE TABLE `payments` (
`payment_id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
`approved` TINYINT NOT NULL DEFAULT '0',
`transaction_id` CHAR( 100 ) NOT NULL ,
`member_id` MEDIUMINT UNSIGNED NOT NULL ,
`course_id` MEDIUMINT UNSIGNED NOT NULL ,
`amount` DECIMAL( 7, 2 ) NOT NULL DEFAULT '0'
) ENGINE=MYISAM ;


CREATE TABLE `ec_course_fees` (
  `course_id` smallint( 8 )  NOT  NULL ,
  `course_fee` DECIMAL( 7, 2 ) NOT  NULL DEFAULT '0',
  `auto_approve` tinyint( 1 )  default NULL ,
  `auto_email` tinyint( 1 )  default NULL ,
 PRIMARY  KEY ( `course_id` )
) ENGINE=MyISAM;



INSERT INTO `language_text` VALUES ('en', '_module','ec_transaction_id','Transaction ID',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_settings','Settings',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_payments','Payments',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_approve_manually','Approve Manually',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ecomm','Payments',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_confirm_info','Review the following information before proceeding. If the information is correct, click the Pay by Credit Card button (or Pay by Cheque if enabled) to move on to the next step.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_course','Course',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_amount','Amount',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_firstname','First Name',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_lastname','Last Name',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_organization','Organization',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_email','Email',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_address','Address',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_postal','Postal/Zip Code',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_telephone','Telephone',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_country','Country',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_comments','Comments',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_payment_message','%s has registered in the course %s. To review the payment, login to ATutor as an Administrator then click on the Payments tab.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_invalid_fields','The following fields are invalid',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_requirements','Requirements to proceed',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_requirements_ssl','SSL enabled, with 128-bit encryption. ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_requirements_cookies','Cookies enabled.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_requirements_javascript','Javascript enabled.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_requirements_comment','Note that most current browsers will have these enabled by default. If you are unable to complete the transaction, check these settings in your browser to be sure they are enabled.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_modify','Modify ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_select_creditcard','Select the Pay by Credit Card button below to move to the secure credit card payment site. Following a payment,  a printable receipt will be generated and email will be sent to the payee with details of the transaction. Or, select Pay by Cheque to send payment by regular mail.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_paybycredit','Pay by Credit Card',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_paybycheque','Pay by Cheque',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_date','Date Paid',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_acceptvisa','Accepting Visa',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_acceptmastercard','Accepting Master Card',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_payfeesfor','Pay Fees',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_fees','Fees',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_complete_thisinfo','Complete the information below. ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_next_toproceed','Use the <em>Next Step</em> button to review your information before proceding to the secure credit card payment site.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_purchaser_info','Purchaser Information',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_required','Required ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_next_step','Next Step ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_location_text','Enter the following settings for your credit card processing service. ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_location','Credit card processing location URL ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_payments','ATutor Payments ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_payment_confirmation','Payment Confirmation ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_payment_received','An ATutor Payment Received ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_payment_retrieve_admin','An ATutor payment has been received. To review the payment login to ATutor as the administrator, choose the Payments tab, then select Review Payments  ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_this_course_fee','Course Fee ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_vendor_id','Vendor ID assigned by credit card payment service ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_password','Password assigned by credit card payment service ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_currency','Currency ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_currency_symbol','Currency Symbol ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_currency_other','Other Currency ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_gateway','Payment Gateway ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_course_fee','Fee to charge for this course ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_auto_approve','Auto approve enrollment when fee has been paid ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_payment_made','Course Fees Received ',NOW(),'');

INSERT INTO `language_text` VALUES ('en', '_module','ec_enrollpay_confirmed_manual','Your payment has been received, and you have been enrolled in <strong>%s</strong>. You should receive confirmation by email, and access to the course, once approved by the instructor. ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_enrollpay_confirmed_auto','Your payment has been received, and you have been enrolled in <strong>%s</strong>. You can now <a href="login.php?course=%s">login to the course</a>. ',NOW(),'');

INSERT INTO `language_text` VALUES ('en', '_module','ec_payment_mail_instruction','Course fees have been received for the course: %s. Login as the course instructor and review the payment through the Manage tab, then choose Payments. ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_payment_mail_instruction','Course fees have been recieved for course %. Login to the Payments utility to review the payment, and to approved the enrollment if Auto Approve Enrollment has not been set. ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_payment_made','Fees Received ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_allow_instructors','Allow instructors to manage payments ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_cancel','Cancel',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_auto_email','Email instructor when a payment has been received',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_requirements_comments','Note that most current browsers will have these enabled by default. If you are unable to complete the transaction, check these settings in your browser to be sure they are enabled.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_remove','Remove/Un-Enroll',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_this_course_fee','Course Fee',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_payment_made','Payment Received',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_enroll_approved','Enrollment Approved',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_action','Action',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_full_payment_recieved','Fees Paid',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_make_payment','Make Payment',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_student_name','Name',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_payment_failed','Payment Failed/Cancelled',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_course_name','Course Title',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_balance_due','Balance Due',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_amount_recieved','Amount Received',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_email_admin','Email payment notices to the administrator',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_admin_payment_mail_instruction','A payment has been made for course: %s. To review the payment login to ATutor as an administrator and select the Payments tab.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_login','login',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_payments_settings','Payment Settings',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_payments_received','Payments Received',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_return_to_payments','Return to Payments',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_start_date','Start Date',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_end_date','End Date',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_set_date','Set Dates',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_invoice','Invoice',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_print_invoice','Print_invoice',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_paybypaypal','Pay by PayPal',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_store_log','Keep transaction log ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_log_file','Full path to writable transaction log file. (required if log is enable, create writable  file manually if necessary) ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_export_data','Export Data Displayed Below',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_date_picker','Select a date range to display. (e.g. 2007-2-6)',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_contact_email','EMail notification of payments to: (if different from the ATutor contact email, set in System Preferences)',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_contact_address','Address where cheques should be sent. (leave empty to disabled cheque payments)',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_date_picker','The table below shows by default the past 30 payments. Use the date selectors to select a specific date range.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_cc_number','Credit Card Number',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_cc_expiry_month','Expiry Month',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_cc_expiry_year','Expiry Year',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_more_info_required','<strong>The following additional information is needed to complete your payment request:</strong><br/>',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_province','State/Province',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_cc_cvd_number','Card CVD Number',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_cc_cvd_info','(3 or 4 digit number on the back of the card)',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_cc_cvd_number','Card CVD Number',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_INFOS_EC_NO_PAID_COURSES','You have not enrolled in any courses that require fees to be paid. <a href="./users/browse.php">Browse</a> courses. ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_FEEDBACK_EC_ADD_SAVED','Payment settings were successfully saved. ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_INFOS_EC_NO_STUDENTS_ENROLLED','No students have requested enrollment. ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_FEEDBACK_EC_PAYMENT_CONFIRMED_AUTO','Your payment has been received, and your course enrollment has been approved. You may now <a href="login.php?course=%s" style="color:red;">login to the course</a>. ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_FEEDBACK_EC_PAYMENT_CONFIRMED_MANUAL','Your payment has been received. You will receive a notice by email when your enrollment in the course has been manually approved. ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_FEEDBACK_EC_COURSE_PAYMENT_SETTINGS_SAVED','Course payment settings have been saved.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_FEEDBACK_EC_COURSE_PAYMENT_SETTINGS_NOT_SAVED','Unable to save course payment settings. Contact your system administrator to report the problem',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_ERROR_EC_PAYMENT_FAILED','The payment was cancelled or  failed.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_INFOS_EC_PAYMENTS_TURNED_OFF','Course fees are being managed by the systems administrator.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_ERROR_EC_INVOICE_NOT_FOUND','Invoice number cannot be found.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_ERROR_EC_INVOICE_APPROVED','Invoice number has already been approved.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_FEEDBACK_ACTION_PENDING_CC_CONFIRM','Your enrollment is conditional on your credit card payment being approved',NOW(),'');
REPLACE INTO `language_text` VALUES ('en', '_msgs','AT_FEEDBACK_APPROVAL_PENDING','Your enrolment request has been made. To complete your enrolment, open the <a href="mods/ecomm/index.php" style="color:red;">Payments tab</a> above, then click on <a href="mods/ecomm/index.php" style="color:red;">Make Payment</a> next to the listing for the course you enrolled in.',NOW(),'');
