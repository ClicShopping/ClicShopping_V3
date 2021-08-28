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

  class StatsCustomersB2B2B2C implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected mixed $app;

    public function __construct()
    {
      if (!Registry::exists('Customers')) {
        Registry::set('Customers', new CustomersApp());
      }

      $this->app = Registry::get('Customers');
      $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/Stats/StatsCustomersB2B2B2C');
    }

    /**
     * @return int
     */
    private function statsCustomersB2C() :int
    {
      $QstatCustomersB2C = $this->app->db->prepare('select count(customers_id) as count
                                                    from :table_customers
                                                    where customers_group_id = 0
                                                   ');
      $QstatCustomersB2C->execute();

      $statCustomersB2C = $QstatCustomersB2C->valueDecimal('count');

      return $statCustomersB2C;
    }

    /**
     * @return mixed
     */
    private function statCustomersB2B() :int
    {
      $QstatCustomersB2B = $this->app->db->prepare('select count(customers_id) as count
                                                    from :table_customers
                                                    where customers_group_id > 0
                                                   ');
      $QstatCustomersB2B->execute();

      $statCustomersB2B = $QstatCustomersB2B->valueDecimal('count');

      return $statCustomersB2B;
    }

    /**
     * @return string
     */
    public function display() :string
    {
      $output = '
  <div class="col-md-2 m-1">
    <div class="card cardStatsSuccess">
      <h4 class="card-title StatsTitle">' . $this->app->getDef('text_customers') . '</h4>
      <div class="card-text">
        <div class="col-sm-12 StatsValue">
          <span class="col-md-4 float-start">
            <i class="bi bi-person-fill"></i>
          </span>
          <span class="col-md-8 float-end">
            <div class="col-sm-12 StatsValue">' . $this->statsCustomersB2C() . ' - ' . $this->app->getDef('text_b2c') . '</div>
            <div class="col-sm-12 StatsValue">' . $this->statCustomersB2B() . ' - ' . $this->app->getDef('text_b2b') . '</div>
          </span>
        </div>
      </div>
    </div>
  </div>
      ';

      return $output;
    }
  }