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

  class AccountGdprNoIp
  {
    /**
     * @return string
     */
    private function getIpAddress(): string
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Customer = Registry::get('Customer');

      $Qgdpr = $CLICSHOPPING_Db->prepare('select no_ip_address
                                          from :table_customers_gdpr
                                          where customers_id = :customers_id
                                        ');
      $Qgdpr->bindInt(':customers_id', $CLICSHOPPING_Customer->getID());
      $Qgdpr->execute();

      $ip_address = $Qgdpr->valueInt('no_ip_address');

      return $ip_address;
    }

    /**
     * @return string
     */
    public function display(): string
    {
      $output = '<div>
                    <ul class="list-group list-group-flush">
                      <li class="list-group-item">
                        <div class="separator"></div>
                           ' . CLICSHOPPING::getDef('module_account_customers_gdpr_no_ip_address') . '
                          <label class="switch">
                            ' . HTML::checkboxField('no_ip_address', $this->getIpAddress(), $this->getIpAddress(), 'class="success"') . '
                            <span class="slider"></span>
                          </label>
                      </li>
                    </ul>
                  </div>
                 ';

      return $output;
    }
  }
