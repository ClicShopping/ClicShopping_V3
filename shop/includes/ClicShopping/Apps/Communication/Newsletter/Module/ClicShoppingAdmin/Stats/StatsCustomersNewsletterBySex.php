<?php
  /**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4 
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

  namespace ClicShopping\Apps\Communication\Newsletter\Module\Hooks\ClicShoppingAdmin\Stats;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Customers\Customers\Customers as CustomersApp;

  class StatsCustomersNewsletterBySex implements \ClicShopping\OM\Modules\HooksInterface {
    protected $app;

    public function __construct() {

      if (!Registry::exists('Customers')) {
        Registry::set('Customers', new CustomersApp());
      }

      $this->app = Registry::get('Customers');
      $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/Stats/StatsCustomersNewsletterBySex');
    }

    private function statsNewsletterCustomersMen() {
      $QstatAnalyseCustomersMan = $this->app->db->prepare('select ROUND(((COUNT(*)/(SELECT COUNT(*) FROM :table_customers))*100),2) AS avgage
                                                          from :table_customers
                                                          where customers_gender = :customers_gender
                                                          and customers_newsletter = 1
                                                         ');
      $QstatAnalyseCustomersMan->bindValue(':customers_gender', 'm');

      $QstatAnalyseCustomersMan->execute();

      if (!is_null($QstatAnalyseCustomersMan->valueDecimal('avgage'))) {
        $statAnalyseCustomersMan =  $QstatAnalyseCustomersMan->valueDecimal('avgage');
      }

      return $statAnalyseCustomersMan;
    }


    private function statsNewsletterCustomersWomen() {
      $QstatAnalyseCustomersWomen = $this->app->db->prepare('select ROUND(((COUNT(*)/(SELECT COUNT(*) FROM :table_customers))*100),2) AS avgage
                                                            from :table_customers
                                                            where customers_gender = :customers_gender
                                                            and customers_newsletter = 1
                                                           ');
      $QstatAnalyseCustomersWomen->bindValue(':customers_gender', 'f');

      $QstatAnalyseCustomersWomen->execute();

      if (!is_null($QstatAnalyseCustomersWomen->valueDecimal('avgage'))) {
        $statAnalyseCustomersWomen =  $QstatAnalyseCustomersWomen->valueDecimal('avgage');
      }

      return $statAnalyseCustomersWomen;
    }


    public function execute() {
      if (!defined('CLICSHOPPING_APP_NEWSLETTER_NL_STATUS') || CLICSHOPPING_APP_NEWSLETTER_NL_STATUS == 'False') {
        return false;
      }

      $output = '
        <div class="col-sm-5 col-md-3">
            <span class="col-md-4 float-md-left">
              <i class="fas fa-transgender fa-2x alert alert-warning" aria-hidden="true"></i>
            </span>
            <span class="col-md-8 float-md-right">
              <div class="col-sm-12 StatsTitle">' . $this->app->getDef('text_average_newsletter') . '</div>
              <div class="col-sm-12 StatsValue">' . $this->statsNewsletterCustomersMen() . '% ' . $this->app->getDef('text_male') . '</div>
              <div class="col-sm-12 StatsValue">' . $this->statsNewsletterCustomersWomen() . '% ' . $this->app->getDef('text_female') . '</div>
            </span>
          </div>
      ';

      return $output;
    }
  }