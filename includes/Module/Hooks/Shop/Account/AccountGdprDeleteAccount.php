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
    /**
     * @return string
     */
    public function display(): string
    {
      $output = '<div class="separator"></div>
                  <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                      <div class="alert alert-danger" role="alert">
                      <label><strong>' . CLICSHOPPING::getDef('module_account_customers_gdpr_account_delete_title') . '</strong></label><br />
                      <label><strong>' . CLICSHOPPING::getDef('module_account_customers_gdpr_account_intro_delete') . '</strong></label>
                      <blockquote>
                         ' . CLICSHOPPING::getDef('module_account_customers_gdpr_checkbox') . '
                        <label class="switch">
                          ' . HTML::checkboxField('delete_all_reviews', null, null, 'class="success"') . '
                          <span class="slider"></span>
                        </label>                           
                      </blockquote>
                      </div>
                    </li>
                  </ul>                  
                  ';
      return $output;
    }
  }
