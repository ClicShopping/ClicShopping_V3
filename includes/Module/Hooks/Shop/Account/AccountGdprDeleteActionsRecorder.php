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

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  class AccountGdprDeleteActionsRecorder
  {
    /**
     * @return string
     */
    public function display(): string
    {
      $output = '';
      $output .= '<div>
                    <ul class="list-group list-group-flush">
                      <li class="list-group-item">
                        <div class="separator"></div>
                           ' . CLICSHOPPING::getDef('module_account_customers_gdpr_delete_action_recorder') . '
                          <label class="switch">
                            ' . HTML::checkboxField('action_recorder', null, null, 'class="success"') . '
                            <span class="slider"></span>
                          </label>
                      </li>
                    </ul>
                  </div>
                 ';

      return $output;
    }
  }
