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

  class PasswordReset extends \ClicShopping\OM\PagesActionsAbstract {

    public function execute() {
      $CLICSHOPPING_Breadcrumb = Registry::get('Breadcrumb');
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_Language = Registry::get('Language');
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Language->loadDefinitions('password_reset');

      $error = false;

      if ( !isset($_GET['account']) || !isset($_GET['key']) ) {
        $error = true;

        $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('text_no_reset_link_found'), 'danger', 'header');
      }

      if ($error === false) {
        $email_address = HTML::sanitize($_GET['account']);
        $password_key = HTML::sanitize($_GET['key']);

        if ( (Is::email($email_address) === false) ) {
          $error = true;

          $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('text_no_email_address_found'), 'danger', 'header');

        } elseif (strlen($password_key) != 40) {
          $error = true;

          $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('text_no_reset_link_found'), 'danger', 'header');
        } else {
          $Qcheck = $CLICSHOPPING_Db->prepare('select c.customers_id,
                                                      c.customers_email_address,
                                                      ci.password_reset_key,
                                                      ci.password_reset_date
                                               from :table_customers c,
                                                    :table_customers_info ci
                                               where c.customers_email_address = :customers_email_address
                                               and c.customers_id = ci.customers_info_id
                                               and c.customer_guest_account = 0
                                               limit 1
                                             ');

          $Qcheck->bindValue(':customers_email_address', $email_address);
          $Qcheck->execute();

          if ($Qcheck !== false) {
            if ((strlen($Qcheck->value('password_reset_key')) != 40) || ($Qcheck->value('password_reset_key') != $password_key) || (strtotime($Qcheck->value('password_reset_date') . ' +1 day') <= time()) ) {
              $error = true;

              $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('text_no_reset_link_found'), 'danger', 'header');
            }
          } else {
            $error = true;

            $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('text_no_email_address_found'), 'danger', 'header');
          }
        }
      }

      if ($error === true) {
        CLICSHOPPING::redirect(CLICSHOPPING::getConfig('bootstrap_file') . '&PasswordForgotten');
      }

// templates
      $this->page->setFile('password_reset.php');
//Content
      $this->page->data['content'] = $CLICSHOPPING_Template->getTemplateFiles('password_reset');
//language
      $CLICSHOPPING_Language->loadDefinitions('password_reset');

      $CLICSHOPPING_Breadcrumb->add(CLICSHOPPING::getDef('navbar_title_1'), CLICSHOPPING::link(null, 'Account&Login'));
      $CLICSHOPPING_Breadcrumb->add(CLICSHOPPING::getDef('navbar_title_2'));
    }
  }