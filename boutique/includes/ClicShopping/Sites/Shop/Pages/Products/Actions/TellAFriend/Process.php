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

  namespace ClicShopping\Sites\Shop\Pages\Products\Actions\TellAFriend;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Is;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\HTTP;

  use ClicShopping\Apps\Configuration\TemplateEmail\Classes\Shop\TemplateEmail;
  use ClicShopping\Apps\Tools\ActionsRecorder\Classes\Shop\ActionRecorder;

  class Process extends \ClicShopping\OM\PagesActionsAbstract  {

    public function execute()  {

      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_Mail = Registry::get('Mail');
      $CLICSHOPPING_ProductsCommon = Registry::get('ProductsCommon');
      $CLICSHOPPING_Hooks = Registry::get('Hooks');

      if (isset($_POST['action']) && ($_POST['action'] == 'process')  && isset($_POST['formid']) && ($_POST['formid'] == $_SESSION['sessiontoken'])) {
        $error = false;

        $to_email_address = HTML::sanitize($_POST['to_email_address']);
        $to_name = HTML::sanitize($_POST['to_name']);
        $from_email_address = HTML::sanitize($_POST['from_email_address']);
        $from_name = HTML::sanitize($_POST['from_name']);
        $message = HTML::sanitize($_POST['message']);
        $antispam = HTML::sanitize($_POST['antispam']);
        $customer_agree_privacy = HTML::sanitize($_POST['customer_agree_privacy']);

        if (DISPLAY_PRIVACY_CONDITIONS == 'true') {
          if ($customer_agree_privacy != 'on') {
            $error = true;

            $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_agreement_check_error'), 'error', 'friend');
          }
        }

        if (empty($from_name)) {
          $error = true;

          $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('error_from_name'), 'error', 'friend');
        }

        if (!Is::email($from_email_address)) {
          $error = true;

          $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('error_from_address'), 'error', 'friend');
        }

        if (empty($to_name)) {
          $error = true;

          $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('error_to_name'), 'error', 'friend');
        }

        if (!Is::email($to_email_address)) {
          $error = true;

          $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('error_to_address'), 'error', 'friend');
        }

// Recaptcha
        if (defined('MODULES_HEADER_TAGS_GOOGLE_RECAPTCHA_TELL_FRIEND')) {
          if (MODULES_HEADER_TAGS_GOOGLE_RECAPTCHA_TELL_FRIEND == 'True') {
            $google_recaptcha = $CLICSHOPPING_Hooks->output('AllShop', 'GoogleRecaptchaProcess');
          }
        }

        Registry::set('ActionRecorder', new ActionRecorder('ar_tell_a_friend', ($CLICSHOPPING_Customer->isLoggedOn() ? $CLICSHOPPING_Customer->getID() : null), $from_name));
        $CLICSHOPPING_ActionRecorder = Registry::get('ActionRecorder');

        if (!$CLICSHOPPING_ActionRecorder->canPerform()) {
          $error = true;

          $CLICSHOPPING_ActionRecorder->record(false);

          $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('error_action_recorder', ['module_action_recorder_tell_a_friend_email_minutes' => (defined('MODULE_ACTION_RECORDER_TELL_A_FRIEND_EMAIL_MINUTES') ? (int)MODULE_ACTION_RECORDER_TELL_A_FRIEND_EMAIL_MINUTES : 15)]), 'danger', 'friend');
        }

        if (!Is::email($to_email_address)) {
          $error = true;
          $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('error_to_address'), 'error', 'friend');

        } elseif (!Is::ValidateAntiSpam( (int)$antispam) ) {
          $error = true;

          $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('entry_email_address_check_error_number'), 'error', 'friend');
        }

        if ($error === false) {
          $email_subject = CLICSHOPPING::getDef('text_email_subject', ['from_name' => $from_name, 'store_name' => STORE_NAME]);
          $email_body = CLICSHOPPING::getDef('text_email_intro', [ 'to_name' => $to_name, 'from_name' => $from_name, 'products_name' => $CLICSHOPPING_ProductsCommon->getProductsName(), 'store_name' => STORE_NAME]) . "\n\n";

          if (!empty($message)) {
            $email_body .= $message . "\n\n";
          }

          $email_body .= CLICSHOPPING::getDef('text_email_link', ['url_product' => CLICSHOPPING::link('index.php', 'products&Product&products_id=' . (int)$CLICSHOPPING_ProductsCommon->getID())]) . "\n\n";
          $email_body .=  CLICSHOPPING::getDef('text_email_signature', ['store_name' => STORE_NAME . "\n" . HTTP::getShopUrlDomain() . "\n", 'store_owner_email_address' => STORE_OWNER_EMAIL_ADDRESS]);

          $email_body .= TemplateEmail::getTemplateEmailSignature();

          $CLICSHOPPING_Mail->clicMail($to_name, $to_email_address, $email_subject, $email_body, $from_name, $from_email_address);

          $CLICSHOPPING_ActionRecorder->record();

          $CLICSHOPPING_Hooks->call('TellAFriend', 'Process');

          $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('text_email_successful_sent', ['products_name' => $CLICSHOPPING_ProductsCommon->getProductsName(), 'to_name' => HTML::outputProtected($to_name)]), 'success', 'header');

          CLICSHOPPING::redirect('index.php', 'Products&Description&products_id=' . (int)$CLICSHOPPING_ProductsCommon->getID() );
        }

        // revoir cette partie
      }
    }
  }