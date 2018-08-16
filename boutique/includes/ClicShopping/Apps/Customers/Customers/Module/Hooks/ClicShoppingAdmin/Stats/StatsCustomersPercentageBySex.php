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

  namespace ClicShopping\Apps\Customers\Customers\Module\Hooks\ClicShoppingAdmin\Stats;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Customers\Customers\Customers as CustomersApp;

  class StatsCustomersPercentageBySex implements \ClicShopping\OM\Modules\HooksInterface {
    protected $app;

    public function __construct() {

      if (!Registry::exists('Customers')) {
        Registry::set('Customers', new CustomersApp());
      }

      $this->app = Registry::get('Customers');
      $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/Stats/StatsCustomersPercentageBySex');

    }

    private function statsAverageCustomersMen() {

      $QstatAnalyseCustomersMan = $this->app->db->prepare('select ROUND(((COUNT(*)/(SELECT COUNT(*) FROM :table_customers))*100),2) AS numberByGenderPerCent
                                                            from :table_customers
                                                            where customers_gender = :customers_gender
                                                           ');
      $QstatAnalyseCustomersMan->bindValue(':customers_gender', 'm');

      $QstatAnalyseCustomersMan->execute();

      if (!is_null($QstatAnalyseCustomersMan->valueDecimal('numberByGenderPerCent'))) {
        $numberByPerCentMen = $QstatAnalyseCustomersMan->valueDecimal('numberByGenderPerCent');
      }

      return $numberByPerCentMen;
    }


    private function statsAverageCustomersWomen() {

      $QstatAnalyseCustomersWomen = $this->app->db->prepare('select ROUND(((COUNT(*)/(SELECT COUNT(*) FROM :table_customers))*100),2) AS numberByGenderPerCent
                                                              from :table_customers
                                                              where customers_gender = :customers_gender
                                                             ');
      $QstatAnalyseCustomersWomen->bindValue(':customers_gender', 'f');

      $QstatAnalyseCustomersWomen->execute();

      if (!is_null($QstatAnalyseCustomersWomen->valueDecimal('numberByGenderPerCent'))) {
        $numberByPerCentWomen = $QstatAnalyseCustomersWomen->valueDecimal('numberByGenderPerCent');
      }

      return $numberByPerCentWomen;
    }


    public function execute() {

      $output = '
  <div class="card col-md-2 cardStatsPrimary">
    <div class="card-block">
      <h4 class="card-title StatsTitle">' . $this->app->getDef('text_customers') . '</h4>
      <div class="card-text">
        <div class="col-sm-12 StatsValue">
          <span class="col-md-4 float-md-left">
            <i class="fas fa-transgender fa-2x" aria-hidden="true"></i>
          </span>
          <span class="col-md-8 float-md-right">
            <div class="col-sm-12 StatsValue">' .  $this->statsAverageCustomersMen() . '% ' . $this->app->getDef('text_male') . '</div>
            <div class="col-sm-12 StatsValue">' .  $this->statsAverageCustomersWomen() . '% ' . $this->app->getDef('text_female') . '</div>
          </span>
        </div>
      </div>
    </div>
  </div>
      ';

      return $output;
    }
  }