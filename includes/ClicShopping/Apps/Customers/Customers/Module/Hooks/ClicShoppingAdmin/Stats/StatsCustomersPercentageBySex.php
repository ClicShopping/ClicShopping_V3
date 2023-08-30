<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Customers\Customers\Module\Hooks\ClicShoppingAdmin\Stats;

use ClicShopping\OM\Registry;

use ClicShopping\Apps\Customers\Customers\Customers as CustomersApp;

class StatsCustomersPercentageBySex implements \ClicShopping\OM\Modules\HooksInterface
{
  protected mixed $app;

  public function __construct()
  {
    if (!Registry::exists('Customers')) {
      Registry::set('Customers', new CustomersApp());
    }

    $this->app = Registry::get('Customers');
    $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/Stats/StatsCustomersPercentageBySex');
  }

  /**
   * @return float
   */
  private function statsAverageCustomersMen(): float
  {
    $numberByPerCentMen = '   ';

    $QstatAnalyseCustomersMan = $this->app->db->prepare('select ROUND(((COUNT(customers_id)/(SELECT COUNT(customers_id) FROM :table_customers))*100),2) AS numberByGenderPerCent
                                                            from :table_customers
                                                            where customers_gender = :customers_gender
                                                           ');
    $QstatAnalyseCustomersMan->bindValue(':customers_gender', 'm');

    $QstatAnalyseCustomersMan->execute();

    if (!\is_null($QstatAnalyseCustomersMan->valueDecimal('numberByGenderPerCent'))) {
      $numberByPerCentMen = $QstatAnalyseCustomersMan->valueDecimal('numberByGenderPerCent');
    }

    return $numberByPerCentMen;
  }

  /**
   * @return float
   */
  private function statsAverageCustomersWomen(): float
  {
    $numberByPerCentWomen = '   ';

    $QstatAnalyseCustomersWomen = $this->app->db->prepare('select ROUND(((COUNT(customers_id)/(SELECT COUNT(customers_id) FROM :table_customers))*100),2) AS numberByGenderPerCent
                                                              from :table_customers
                                                              where customers_gender = :customers_gender
                                                             ');
    $QstatAnalyseCustomersWomen->bindValue(':customers_gender', 'f');

    $QstatAnalyseCustomersWomen->execute();

    if (!\is_null($QstatAnalyseCustomersWomen->valueDecimal('numberByGenderPerCent'))) {
      $numberByPerCentWomen = $QstatAnalyseCustomersWomen->valueDecimal('numberByGenderPerCent');
    }

    return $numberByPerCentWomen;
  }

  /**
   * @return string
   */
  public function display(): string
  {
    if ($this->statsAverageCustomersMen() == 0 && $this->statsAverageCustomersWomen() == 0) {
      $output = '';
    } else {
      $output = '
<div class="col-md-2 col-12">
    <div class="card bg-primary">
     <div class="card-body">
      <h6 class="card-title text-white">' . $this->app->getDef('text_customers') . '</h6>
      <div class="card-text">
        <div class="col-sm-12">
          <span class="float-start">
            <i class="bi bi-person-fill text-white"></i>
          </span>
          <span class="float-end">
            <div class="col-sm-12 text-white">' . $this->statsAverageCustomersMen() . '% ' . $this->app->getDef('text_male') . '</div>
            <div class="col-sm-12 text-white">' . $this->statsAverageCustomersWomen() . '% ' . $this->app->getDef('text_female') . '</div>
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