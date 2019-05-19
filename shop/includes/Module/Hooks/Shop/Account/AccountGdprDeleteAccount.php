<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\OM\Module\Hooks\Shop\Account;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\HTML;

  class AccountGdprDeleteAccount
  {

    public function display()
    {
      $output = '
          <div class="alert alert-danger" role="alert">
            <div class="form-group">
              <label><strong>' . CLICSHOPPING::getDef('module_account_customers_gdpr_title') . '</strong></label><br />
              <label><strong>' . CLICSHOPPING::getDef('module_account_customers_gdpr_account_intro_delete') . '</strong></label>
              <blockquote>' . CLICSHOPPING::getDef('module_account_customers_gdpr_checkbox') . '
                <label class="checkbox-inline">
                  ' . HTML::checkboxField('delete_customers_account_checkbox', 1) . '
                </label>
              </blockquote>
            </div>
          </div>
        ';

      return $output;
    }
  }
