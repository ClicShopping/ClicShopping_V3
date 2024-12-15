<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\OM\Module\Hooks\Shop\Account;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\HTML;

class AccountGdprDeleteCustomersInformations
{
  /**
   * Generates and returns the HTML output for displaying a GDPR-related option
   * with a checkbox field.
   *
   * @return string The HTML output as a string.
   */
  public function display(): string
  {
    $output = '<div>
                    <ul class="list-group list-group-flush">
                      <li class="list-group-item">
                        <div class="mt-1"></div>
                           ' . CLICSHOPPING::getDef('module_account_customers_gdpr_delete_customers_info') . '
                          <label class="switch">
                            ' . HTML::checkboxField('delete_customers_info', null, null, 'class="success"') . '
                            <span class="slider"></span>
                          </label>
                      </li>
                    </ul>
                  </div>
                 ';
    return $output;
  }
}
