<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Customers\Gdpr\Module\Hooks\ClicShoppingAdmin\Cronjob;

use ClicShopping\Apps\Customers\Gdpr\Gdpr as GdprApp;
use ClicShopping\Apps\Tools\Cronjob\Classes\ClicShoppingAdmin\Cron;
use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

use ClicShopping\Apps\Customers\Gdpr\Classes\ClicShoppingAdmin\Gdpr;

class Process implements \ClicShopping\OM\Modules\HooksInterface
{
  protected mixed $app;

  public function __construct()
  {
    if (!Registry::exists('Gdpr')) {
      Registry::set('Gdpr', new GdprApp());
    }

    $this->app = Registry::get('Gdpr');
  }

  /**
   * @return array|bool
   */
  public function getExpires(): array|bool
  {
    $date = date('Y-m-d', strtotime('+ ' . CLICSHOPPING_APP_CUSTOMERS_GDPR_GD_DATE . ' days'));

    $Qcustomers = $this->app->db->prepare('select  c.customers_id,
                                                     c.customers_email_address,
                                                     ci.customers_info_date_of_last_logon
                                                from :table_customers c,
                                                     :table_customers_info ci
                                                where  c.customers_id = ci.customers_info_id
                                                and customers_info_date_of_last_logon <= :date
                                              ');

    $Qcustomers->bindValue(':date', $date);
    $Qcustomers->execute();

    return $Qcustomers->fetchAll();
  }

  /**
   *
   */
  private function cronJob(): void
  {
    $results = $this->getExpires();

    foreach ($results as $result) {
      $cron_id_gdpr = Cron::getCronCode('gdpr');

      if (isset($_GET['cronId'])) {
        $cron_id = HTML::sanitize($_GET['cronId']);

        Cron::updateCron($cron_id);

        if (isset($cron_id) && $cron_id_gdpr == $cron_id) {
          $customer_id = $result['customers_id'];
          Gdpr::deleteCustomersData($customer_id);
        }
      } else {
        Cron::updateCron($cron_id_gdpr);

        if (isset($cron_id_gdpr)) {
          $customer_id = $result['customers_id'];
          Gdpr::deleteCustomersData($customer_id);
        }
      }
    }
  }

  public function execute()
  {
    $this->cronJob();
  }
}