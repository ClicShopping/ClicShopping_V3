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
  namespace ClicShopping\Sites\Shop\Pages\Account\Actions;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Is;

  use ClicShopping\Sites\Shop\Pages\Account\Classes\PasswordReset as Reset;

  class PasswordReset extends \ClicShopping\OM\PagesActionsAbstract {

    public function execute() {
      global $error, $email_address, $password_key;

      $CLICSHOPPING_Breadcrumb = Registry::get('Breadcrumb');
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_Language = Registry::get('Language');

      $CLICSHOPPING_Language->loadDefinitions('password_reset');

      $error = false;

      if ( !isset($_GET['account']) || !isset($_GET['key']) ) {
        $error = true;

        $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('text_no_reset_link_found'), 'danger', 'password_forgotten');
      }

      if ($error === false) {
        $email_address = HTML::sanitize($_GET['account']);
        $password_key = HTML::sanitize($_GET['key']);

        if ( (Is::email($email_address) === false) ) {
          $error = true;

          $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('text_no_email_address_found'), 'danger', 'password_forgotten');

        } elseif (strlen($password_key) != 40) {
          $error = true;

          $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('text_no_reset_link_found'), 'danger', 'password_forgotten');

        } else {
          $Qc = Reset::getPasswordResetCheckEmailAddress();

          if ($Qc !== false) {
            if ((strlen($Qc->value('password_reset_key')) != 40) || ($Qc->value('password_reset_key') != $password_key) || (strtotime($Qc->value('password_reset_date') . ' +1 day') <= time()) ) {
              $error = true;

              $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('text_no_reset_link_found'), 'danger', 'password_forgotten');
            }
          } else {
            $error = true;

            $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('text_no_email_address_found'), 'danger', 'password_forgotten');
          }
        }
      }

      if ($error === true) {
        CLICSHOPPING::redirect('index.php&PasswordForgotten');
      }

// templates
      $this->page->setFile('password_reset.php');
//Content
      $this->page->data['content'] = $CLICSHOPPING_Template->getTemplateFiles('password_reset');
//language
      $CLICSHOPPING_Language->loadDefinitions('password_reset');

      $CLICSHOPPING_Breadcrumb->add(CLICSHOPPING::getDef('navbar_title_1'), CLICSHOPPING::link('index.php', 'Account&Login'));
      $CLICSHOPPING_Breadcrumb->add(CLICSHOPPING::getDef('navbar_title_2'));
    }
  }