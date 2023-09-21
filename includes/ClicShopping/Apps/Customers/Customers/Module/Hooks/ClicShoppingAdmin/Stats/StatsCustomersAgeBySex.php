<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Customers\Customers\Module\Hooks\ClicShoppingAdmin\Stats;

use ClicShopping\OM\Registry;

use ClicShopping\Apps\Customers\Customers\Customers as CustomersApp;

class StatsCustomersAgeBySex implements \ClicShopping\OM\Modules\HooksInterface
{
  protected mixed $app;

  public function __construct()
  {
    if (!Registry::exists('Customers')) {
      Registry::set('Customers', new CustomersApp());
    }

    $this->app = Registry::get('Customers');
    $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/Stats/StatsCustomersAgeBySex');
  }

  /**
   * @return int
   */
  private function statsAgeCustomersMen(): int
  {
    $statAnalyseCustomersMan = '    ';

    $QstatAnalyseCustomersMan = $this->app->db->prepare('select ROUND(AVG(TIMESTAMPDIFF(YEAR,(customers_dob), now())),0) AS avgage
                                                          from :table_customers
                                                          where customers_gender = :customers_gender
                                                         ');
    $QstatAnalyseCustomersMan->bindValue(':customers_gender', 'm');

    $QstatAnalyseCustomersMan->execute();

    if (!\is_null($QstatAnalyseCustomersMan->valueDecimal('avgage'))) {
      $statAnalyseCustomersMan = $QstatAnalyseCustomersMan->valueDecimal('avgage');
    }

    return $statAnalyseCustomersMan;
  }

  /**
   * @return int
   */
  private function statsAgeCustomersWomen(): int
  {
    $statAnalyseCustomersWomen = '    ';

    $QstatAnalyseCustomersWomen = $this->app->db->prepare('select ROUND(AVG(TIMESTAMPDIFF(YEAR,(customers_dob), now())),0) AS avgage
                                                              from :table_customers
                                                              where customers_gender = :customers_gender
                                                             ');
    $QstatAnalyseCustomersWomen->bindValue(':customers_gender', 'f');

    $QstatAnalyseCustomersWomen->execute();

    if (!\is_null($QstatAnalyseCustomersWomen->valueDecimal('avgage'))) {
      $statAnalyseCustomersWomen = $QstatAnalyseCustomersWomen->valueDecimal('avgage');
    }

    return $statAnalyseCustomersWomen;
  }

  /**
   * @return string
   */
  public function display(): string
  {
    if ($this->statsAgeCustomersMen() == 0 && $this->statsAgeCustomersWomen() == 0) {
      $output = '';
    } else {
      $output = '
<div class="col-md-2 col-12">
  <div class="card bg-info">
    <div class="card-body">
      <h6 class="card-title text-white">' . $this->app->getDef('text_average_age') . '</h6>
      <div class="card-text">
        <div class="col-sm-12">
          <span class="float-start">
            <i class="bi bi-calendar text-white"></i>
          </span>
          <span class="float-end">
            <div class="col-sm-12 text-white">' . $this->statsAgeCustomersMen() . ' - ' . $this->app->getDef('text_male') . '</div>
            <div class="col-sm-12 text-white">' . $this->statsAgeCustomersWomen() . ' - ' . $this->app->getDef('text_female') . '</div>
          </span>
        </div>
      </div>
    </div>
  </div>
</div>
      ';
    }

    return $output;
  }
}