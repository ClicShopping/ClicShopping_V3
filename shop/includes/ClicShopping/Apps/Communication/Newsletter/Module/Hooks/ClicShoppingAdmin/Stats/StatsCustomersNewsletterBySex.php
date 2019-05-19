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

  namespace ClicShopping\Apps\Communication\Newsletter\Module\Hooks\ClicShoppingAdmin\Stats;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Communication\Newsletter\Newsletter as NewslettersApp;

  class StatsCustomersNewsletterBySex implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected $app;

    public function __construct()
    {

      if (!Registry::exists('Newsletters')) {
        Registry::set('Newsletters', new NewslettersApp());
      }

      $this->app = Registry::get('Newsletters');
      $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/Stats/StatsCustomersNewsletterBySex');
    }

    private function statsNewsletterCustomersMen()
    {
      $QstatAnalyseCustomersMan = $this->app->db->prepare('select ROUND(((COUNT(*)/(SELECT COUNT(*) FROM :table_customers))*100),2) AS avgage
                                                           from :table_customers
                                                           where customers_gender = :customers_gender
                                                           and customers_newsletter = 1
                                                         ');
      $QstatAnalyseCustomersMan->bindValue(':customers_gender', 'm');

      $QstatAnalyseCustomersMan->execute();

      if (!is_null($QstatAnalyseCustomersMan->valueDecimal('avgage'))) {
        $statAnalyseCustomersMan = $QstatAnalyseCustomersMan->valueDecimal('avgage');
      }

      return $statAnalyseCustomersMan;
    }


    private function statsNewsletterCustomersWomen()
    {
      $QstatAnalyseCustomersWomen = $this->app->db->prepare('select ROUND(((COUNT(*)/(SELECT COUNT(*) FROM :table_customers))*100),2) AS avgage
                                                              from :table_customers
                                                              where customers_gender = :customers_gender
                                                              and customers_newsletter = 1
                                                             ');
      $QstatAnalyseCustomersWomen->bindValue(':customers_gender', 'f');

      $QstatAnalyseCustomersWomen->execute();

      if (!is_null($QstatAnalyseCustomersWomen->valueDecimal('avgage'))) {
        $statAnalyseCustomersWomen = $QstatAnalyseCustomersWomen->valueDecimal('avgage');
      }

      return $statAnalyseCustomersWomen;
    }


    public function execute()
    {
      if (!defined('CLICSHOPPING_APP_NEWSLETTER_NL_STATUS') || CLICSHOPPING_APP_NEWSLETTER_NL_STATUS == 'False') {
        return false;
      }

      $output = '
  <div class="card col-md-2 cardStatsPrimary">
    <div class="card-block">
      <h4 class="card-title StatsTitle">' . $this->app->getDef('text_average_newsletter') . '</h4>
      <div class="card-text">
        <div class="col-sm-12">
          <span class="float-md-left">
            <i class="fas fa-transgender fa-2x" aria-hidden="true"></i>
          </span>
          <span class="float-md-right">
            <div class="StatsValue">' . $this->statsNewsletterCustomersMen() . '% ' . $this->app->getDef('text_male') . '</div>
            <div class="StatsValue">' . $this->statsNewsletterCustomersWomen() . '% ' . $this->app->getDef('text_female') . '</div>
          </span>
        </div>
      </div>
    </div>
  </div>
      ';

      return $output;
    }
  }