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

class AccountGdprDeleteAccount
{
  /**
   * Generates and returns the HTML output for displaying a GDPR account delete section.
   *
   * The output includes a title, an introductory message, and a checkbox field
   * for confirming the deletion of all reviews.
   *
   * @return string The generated HTML string for the GDPR account delete section.
   */
  public function display(): string
  {
    $output = '<div class="mt-1"></div>
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
