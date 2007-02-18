# sql file for ATutor Ecommerce module

CREATE TABLE `ec_shop` (
  `shopid` int(11) NOT NULL auto_increment,
  `member_id` int(8) default NULL,
  `firstname` varchar(50) default NULL,
  `lastname` varchar(50) default NULL,
  `email` varchar(100) NOT NULL,
  `organization` varchar(255) default NULL,
  `address` text,
  `postal` varchar(15) default NULL,
  `telephone` varchar(20) default NULL,
  `country` varchar(25) default NULL,
  `receipt` enum('0','1') default NULL,
  `miraid` varchar(12) NOT NULL,
  `date` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `approval` int(8) NOT NULL,
  `course_name` varchar( 255 ) NOT NULL ,
  `amount` float unsigned default NULL,
  `comments` text,
  `course_id` int(8) unsigned default NULL,
  PRIMARY KEY  (`shopid`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;


 CREATE  TABLE  `atutorsvn`.`ec_course_fees`(
  `course_id` smallint( 8  )  NOT  NULL ,
  `course_fee` float NOT  NULL ,
  `auto_approve` tinyint( 1  )  default NULL ,
  `auto_email` tinyint( 1  )  default NULL ,
 PRIMARY  KEY (  `course_id`  )
) ENGINE  =  MyISAM  DEFAULT CHARSET  = latin1;


INSERT INTO `language_text` VALUES ('en', '_module','ecomm','Payments',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','payments_gateway','Payments Gateway',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_confirm_info','Confirm the following information before proceeding. If the information is correct, click the Confirm button to move on to the next step. Otherwise use Modify button to return and make corrections.',NOW(),'');
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
INSERT INTO `language_text` VALUES ('en', '_module','ec_invalid_fields','The following fields are invalid: ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_requirements','Requirements to proceed: ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_requirements_ssl','SSL enabled, with 128-bit encryption. ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_requirements_cookies','Cookies enabled.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_requirements_javascript','Javascript enabled.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_requirements_comment','Note that most current browsers will have these enabled by default. If you are unable to complete the transaction, check these settings in your browser to be sure they are enabled.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_confirm','Confirm',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_modify','Modify ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_select_creditcard','Select the Pay by Credit Card button below to move to the secure credit card payment site. Following a payment,  a printable receipt will be generated and email will be sent to the payee with details of the transaction.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_paybycredit','Pay by Credit Card',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_acceptvisa','Accepting Visa',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_acceptmastercard','Accepting Master Card',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_payfeesfor','Pay Fees',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_complete_thisinfo','Complete the information below. ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_next_toproceed','Click Next Step to review your information before proceding to the secure credit card payment site.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_purchaser_info','Purchaser Information:  ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_required','Required ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_next_step','Next Step ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_location_text','Enter the following settings for your credit card processing service. ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_location','Credit card processing location URL ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_payments','ATutor Payments ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_payment_confirmation','Payment Confirmation ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_payment_received','An ATutor Payment Received ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_payment_retrieve_admin','An ATutor payment has been received. To review the payment login to ATutor as the administrator, choose the Payments tab, then select Review Payments  ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_this_course_fee','Fees Due : ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_vendor_id','Vendor ID assigned by credit card payment service: ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_password','Password assigned by credit card payment service: ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_currency_symbol','Currency Symbol (e.g. $): ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_currency','Currency (e.g USD): ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_instr_currency','(Currency  USD): ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_course_fee','Fee to charge for this course: ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_auto_approve','Auto approve enrollment when fee has been paid: ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_payment_made','Course Fees Received ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_payment_mail_instruction','Course Fees Received ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_payment_mail_instruction','Course fees have been recieved for course %. Login to the Payments utility to review the payment, and to approved the enrolment if Auto Approve Enrolment has not been set. ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_payment_made','Course Fees Received ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_allow_instructors','Allow instructors to manage payments ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_cancel','Cancel',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_auto_email','Email instructor when a payment has been received:',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_requirements_comments','Note that most current browsers will have these enabled by default. If you are unable to complete the transaction, check these settings in your browser to be sure they are enabled.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_remove','Remove/Un-Enroll',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_course_name','Course Name',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_this_course_fee','Course Fee',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_payment_made','Payment Received',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_enroll_approved','Enrolment Approved',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_action','Action',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_full_payment_recieved','Full Payment Received',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_make_payment','Make Payment',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_student_name','Name',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_payment_failed','Payment Failed/Cancelled',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_course_name','Course Title',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_balance_due','Balance Due.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_amount_recieved','Amount Received.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_email_admin','Email payment notices to the administrator.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_admin_payment_mail_instruction','A payment has been made. To review the payment login to ATutor as an administrator and select the Payments tab.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_login','Login.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_payments_settings','Payment Settings.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','ec_payments_received','Payments Received.',NOW(),'');

INSERT INTO `language_text` VALUES ('en', '_msgs','AT_INFOS_EC_NO_PAID_COURSES','You have not enrolled in any courses that require fees to be paid. <a href="./users/browse.php">Browse</a> existing courses. ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_FEEDBACK_EC_ADD_SAVED','Payment settings were successfully saved. ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_INFOS_EC_NO_STUDENTS_ENROLLED','No students have requested enrollment in this course. ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_FEEDBACK_EC_PAYMENT_CONFIRMED_AUTO','Your payment has been received, and your course enrolment has been approved. You may now login to the course. ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_FEEDBACK_EC_PAYMENT_CONFIRMED_MANUAL','Your payment has been received. You will receive a notice by email when your enrolment in the course has been manually approved. ',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_FEEDBACK_EC_COURSE_PAYMENT_SETTINGS_SAVED','Course payment setting have been saved.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_FEEDBACK_EC_COURSE_PAYMENT_SETTINGS_NOT_SAVED','Unable to save course payment setting. Contact your system administrator to report the problem',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_ERROR_EC_PAYMENT_FAILED','The payment was cancelled or  failed.',NOW(),'');

UPDATE  `language_text` set text = 'Your request has been made. You will be notifed when your request has been approved. If course fees are pending, they will be listed under the <a href="mods/ecomm/index_mystart.php">Payments</a> tab above, where they can be paid.' WHERE term = 'AT_FEEDBACK_APPROVAL_PENDING' AND language_code='en';
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_INFOS_EC_PAYMENTS_TURNED_OFF','Course fees are being managed by the systems administrator.',NOW(),'');

EC_PAYMENTS_TURNED_OFF