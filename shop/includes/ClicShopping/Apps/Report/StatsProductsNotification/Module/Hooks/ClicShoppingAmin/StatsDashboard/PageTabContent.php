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
  namespace ClicShopping\Apps\Report\StatsProductsNotification\Module\Hooks\ClicShoppingAdmin\StatsDashboard;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Report\StatsProductsNotification\StatsProductsNotification as StatsProductsNotificationApp;

  class PageTabContent implements \ClicShopping\OM\Modules\HooksInterface {
    protected $app;

    public function __construct() {
      if (!Registry::exists('StatsProductsNotification')) {
        Registry::set('StatsProductsNotification', new StatsProductsNotificationApp());
      }

      $this->app = Registry::get('StatsProductsNotification');

      $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/StatsDashboard/page_tab_content');
    }

    private function statsCountCustomersNotifications() {
      $QcustomersTotalNotification = $this->app->db->prepare('select count(products_id) as count
                                                              from :table_products_notifications
                                                              limit 1
                                                              ');
      $QcustomersTotalNotification->execute();

      $customers_total_notification = $QcustomersTotalNotification->valueInt('count');

      return $customers_total_notification;
    }


    public function display() {

      if (!defined('CLICSHOPPING_APP_STATS_PRODUCTS_NOTIFICATION_PN_STATUS') || CLICSHOPPING_APP_STATS_PRODUCTS_NOTIFICATION_PN_STATUS == 'False') {
        return false;
      }

      if ($this->statsCountCustomersNotifications() != 0) {
        $output = '';

        $content = '
        <div class="row">
          <div class="col-md-11 mainTable">
            <div class="form-group row">
              <label for="' . $this->app->getDef('box_entry_notification') . '" class="col-9 col-form-label"><a href="' . $this->app->link('StatsProductsNotification') . '">' . $this->app->getDef('box_entry_notification') . '</a></label>
              <div class="col-md-3">
                ' . $this->statsCountCustomersNotifications() . '
              </div>
            </div>
          </div>
        </div>
        ';

        $output = <<<EOD
  <!-- ######################## -->
  <!--  Start Count customer Notification      -->
  <!-- ######################## -->
             {$content}
  <!-- ######################## -->
  <!--  Start Count customer Notification      -->
  <!-- ######################## -->
EOD;
        return $output;
      }
    }
  }