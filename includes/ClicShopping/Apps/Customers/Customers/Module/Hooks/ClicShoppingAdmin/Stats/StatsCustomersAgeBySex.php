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
    private function statsAgeCustomersMen() :int
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
    private function statsAgeCustomersWomen() :int
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


    public function display() :string
    {
      $output = '
  <div class="col-md-2 m-1">
    <div class="card cardStatsInfo">
      <h4 class="card-title StatsTitle">' . $this->app->getDef('text_average_age') . '</h4>
      <div class="card-text">
        <div class="col-sm-12 StatsValue">
          <span class="col-md-4 float-start">
            <i class="bi bi-calendar"></i>
          </span>
          <span class="col-md-8 float-end">
            <div class="col-sm-12 StatsValue">' . $this->statsAgeCustomersMen() . ' - ' . $this->app->getDef('text_male') . '</div>
            <div class="col-sm-12 StatsValue">' . $this->statsAgeCustomersWomen() . ' - ' . $this->app->getDef('text_female') . '</div>
          </span>
        </div>
      </div>
    </div>
  </div>
      ';

      return $output;
    }
  }