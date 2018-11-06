<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *
 *
 */

  namespace ClicShopping\Apps\Communication\PageManager\Sites\Shop\Pages\Contact\Actions\Contact;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Is;

  use ClicShopping\Apps\Tools\ActionsRecorder\Classes\Shop\ActionRecorder;

  use ClicShopping\Apps\Configuration\TemplateEmail\Classes\ClicShoppingAdmin\TemplateEmailAdmin;

  class Process extends \ClicShopping\OM\PagesActionsAbstract  {

    public function execute()  {
       $CLICSHOPPING_Customer = Registry::get('Customer');
       $CLICSHOPPING_Db = Registry::get('Db');
       $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
       $CLICSHOPPING_Mail = Registry::get('Mail');
       $CLICSHOPPING_Hooks = Registry::get('Hooks');
       $CLICSHOPPING_PageManager = Registry::get('PageManager');

//language
       $CLICSHOPPING_PageManager->loadDefinitions('Sites/Shop/Contact/contact');

       if (isset($_GET['action']) && ($_GET['action'] == 'process') && isset($_POST['formid']) && ($_POST['formid'] == $_SESSION['sessiontoken'])) {
        $error = false;

        $CLICSHOPPING_Hooks->call('Contact', 'PreAction');

        $name = HTML::sanitize($_POST['name']);
        $email_address = HTML::sanitize($_POST['email']);
        $enquiry = HTML::sanitize($_POST['enquiry']);
        $email_subject =  HTML::sanitize($_POST['email_subject']);
        $order_id = HTML::sanitize($_POST['order_id']);
        $send_to = HTML::sanitize($_POST['send_to']);
        $customer_id = HTML::sanitize($_POST['customer_id']);
        $customers_telephone = HTML::sanitize($_POST['customers_telephone']);
        $customer_agree_privacy = HTML::sanitize($_POST['customer_agree_privacy']);

         if (defined('DISPLAY_PRIVACY_CONDITIONS') && DISPLAY_PRIVACY_CONDITIONS == 'true') {
          if ($customer_agree_privacy != 'on') {
            $error = true;
            $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_agreement_check_error'), 'error', 'contact');
          }
        }

        if (!Is::email($email_address)) {
          $error = true;
          $CLICSHOPPING_MessageStack->add($CLICSHOPPING_PageManager->getDef('entry_email_address_check_error'), 'warning', 'contact');
        }

        Registry::set('ActionRecorder', new ActionRecorder('ar_contact_us', ($CLICSHOPPING_Customer->isLoggedOn() ? $CLICSHOPPING_Customer->getID() : null), $name));
        $CLICSHOPPING_ActionRecorder = Registry::get('ActionRecorder');

        if (!$CLICSHOPPING_ActionRecorder->canPerform()) {
          $error = true;
          $CLICSHOPPING_ActionRecorder->record(false);
          $CLICSHOPPING_MessageStack->add($CLICSHOPPING_PageManager->getDef('error_action_recorder',  ['module_action_recorder_contact_us_email_minutes' => (defined('MODULE_ACTION_RECORDER_CONTACT_US_EMAIL_MINUTES') ? (int)MODULE_ACTION_RECORDER_CONTACT_US_EMAIL_MINUTES : 15)]), 'danger', 'contact');
        }

        $template_email_footer = TemplateEmailAdmin::getTemplateEmailTextFooter();

        if ($error === false) {
          $today = date("Y-m-d H:i:s");

           if (!empty(CONTACT_DEPARTMENT_LIST)) {
            $send_to_array = explode("," ,CONTACT_DEPARTMENT_LIST);
            preg_match('/\<[^>]+\>/', $send_to_array[$send_to], $send_email_array);
            $send_to_email= preg_replace ('#>#', '', $send_email_array[0]);
            $send_to_email= preg_replace ('#<#', '', $send_to_email);

            if (!is_null($customer_id)) {
              $num_customer_id = $CLICSHOPPING_PageManager->getDef('entry_customers_id') . $customer_id;
            } else {
              $num_customer_id = '';
            }

            if ($CLICSHOPPING_Customer->isLoggedOn() && !empty($order_id) ) {
              $message_info_admin = $CLICSHOPPING_PageManager->getDef('entry_information_admin');
            }

            $message_to_admin = $email_subject . ' ' . STORE_NAME  . "\n\n" . $message_info_admin . "\n\n" . $CLICSHOPPING_PageManager->getDef('entry_date') . ' ' . $today  .  "\n".    $num_customer_id .  "\n".  $CLICSHOPPING_PageManager->getDef('entry_order') . ' ' . $order_id  .  "\n".  $CLICSHOPPING_PageManager->getDef('entry_name') . ' ' . $name .  "\n".  $CLICSHOPPING_PageManager->getDef('entry_email') . ' ' . $email_address  .  "\n".  $CLICSHOPPING_PageManager->getDef('entry_enquiry_customer_information') . ' ' . $enquiry . "\n\n" . $CLICSHOPPING_PageManager->getDef('entry_admin_read_message') . "\n\n";
            $CLICSHOPPING_Mail->clicMail(preg_replace('/\<[^*]*/', '', $send_to_array[$send_to]), $send_to_email, $email_subject, $message_to_admin, $name, $email_address);
            $contact_department = $send_to_array[$send_to];

// send information to customer
            $message_to_customer = $email_subject . ' ' . STORE_NAME  . "\n\n" . $CLICSHOPPING_PageManager->getDef('entry_date') . ' ' . $today .  "\n".   $num_customer_id .  "\n\n" . $CLICSHOPPING_PageManager->getDef('entry_order') . ' ' . $order_id  .  "\n"  . $CLICSHOPPING_PageManager->getDef('entry_name') . ' ' . $name . "\n" . $CLICSHOPPING_PageManager->getDef('entry_customers_phone') . ' ' .  $customers_telephone   . "\n"  . $CLICSHOPPING_PageManager->getDef('entry_email') . ' ' . $email_address  . "\n"  . $CLICSHOPPING_PageManager->getDef('entry_enquiry_customer') . ' ' .  $enquiry . "\n\n"  . $CLICSHOPPING_PageManager->getDef('entry_additional_information') . "\n\n"  . $template_email_footer;
            $CLICSHOPPING_Mail->clicMail(STORE_OWNER, $email_address, $CLICSHOPPING_PageManager->getDef('entry_email_object_customer'), $message_to_customer, $name, STORE_OWNER_EMAIL_ADDRESS);

          } else {

            $contact_department =  $CLICSHOPPING_PageManager->getDef('text_administrator_department');
            $message_to_admin = $email_subject . ' ' . STORE_NAME  .  "\n\n"  . $CLICSHOPPING_PageManager->getDef('entry_date') . ' ' . $today .  "\n"  .  $CLICSHOPPING_PageManager->getDef('entry_customers_id') . ' ' . $customer_id .  "\n\n"  .  $CLICSHOPPING_PageManager->getDef('entry_name') . ' ' . $name . "\n" . $CLICSHOPPING_PageManager->getDef('entry_customers_phone') . ' ' . $customers_telephone   . "\n"  . $CLICSHOPPING_PageManager->getDef('entry_email') . ' ' . $email_address  . "\n"  . $CLICSHOPPING_PageManager->getDef('entry_enquiry_customer_information') . ' ' .  $enquiry  .  "\n\n" . $CLICSHOPPING_PageManager->getDef('entry_admin_read_message') .  "\n\n";
            $CLICSHOPPING_Mail->clicMail(STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS, $email_subject, $message_to_admin, $name, $email_address);

// send information to customer
            $message_to_customer = $email_subject . ' ' . STORE_NAME  .  "\n\n"   . $CLICSHOPPING_PageManager->getDef('entry_date') . ' ' . $today .  "\n".  $CLICSHOPPING_PageManager->getDef('entry_customers_id') . ' ' . $customer_id .  "\n\n"  .  $CLICSHOPPING_PageManager->getDef('entry_name') . ' ' . $name . "\n" . $CLICSHOPPING_PageManager->getDef('entry_customers_phone') . ' ' . $customers_telephone   . "\n"  . $CLICSHOPPING_PageManager->getDef('entry_email') . ' ' . $email_address  . "\n"  . $CLICSHOPPING_PageManager->getDef('entry_enquiry_customer') . ' ' . $enquiry  . "\n\n"  . $CLICSHOPPING_PageManager->getDef('entry_additional_information')  . "\n\n"  . $template_email_footer;
            $CLICSHOPPING_Mail->clicMail(STORE_OWNER, $email_address, $CLICSHOPPING_PageManager->getDef('entry_email_object_customer'),  $message_to_customer, $name, STORE_OWNER_EMAIL_ADDRESS);
          }

// insert the modification in the databse
          if ($CLICSHOPPING_Customer->isLoggedOn()) {
            if ($order_id != 0) {

              $CLICSHOPPING_Db->save('orders_status_history', ['orders_id' => (int)$order_id,
                                                                'orders_status_invoice_id' => 1,
                                                                'admin_user_name' => '',
                                                                'date_added' => 'now()',
                                                                'customer_notified' => 1,
                                                                'comments' => $enquiry,
                                                                'orders_status_support_id' => 2,
                                                                'evidence' => ''
                                                               ]
                                     );
            }
          }

          $CLICSHOPPING_Hooks->call('Contact', 'Process');

          $CLICSHOPPING_ActionRecorder->record();

          CLICSHOPPING::redirect(null, 'Info&Contact&Success');
        } else {
          $CLICSHOPPING_MessageStack->add($CLICSHOPPING_PageManager->getDef('entry_error_contact'), 'warning', 'contact');

          CLICSHOPPING::redirect(null, 'Info&Contact');
        }
      } else {
         $CLICSHOPPING_MessageStack->add($CLICSHOPPING_PageManager->getDef('entry_error_contact'), 'warning', 'contact');

         CLICSHOPPING::redirect(null, 'Info&Contact');
      }
    }
  }