<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Tools\Backup\Module\Hooks\ClicShoppingAdmin\Cronjob;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Customers\Gdpr\Gdpr as GdprApp;
  use ClicShopping\Apps\Tools\Cronjob\Classes\ClicShoppingAdmin\Cron;
  use ClicShopping\Apps\Tools\Backup\Classes\ClicShoppingAdmin\Backup;

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
     *
     */
    private function cronJob() :void
    {
      $cron_id_gdpr = Cron::getCronCode('backup');

      if (isset($_GET['cronId'])) {
        $cron_id = HTML::sanitize($_GET['cronId']);

        Cron::updateCron($cron_id);

        if (isset($cron_id) && $cron_id_gdpr == $cron_id) {
          Backup::backupNow();
        }
      } else {
        Cron::updateCron($cron_id_gdpr);

        if (isset($cron_id_gdpr)) {
          Backup::backupNow();
        }
      }
    }

    public function execute()
    {
      $this->cronJob();
    }
  }