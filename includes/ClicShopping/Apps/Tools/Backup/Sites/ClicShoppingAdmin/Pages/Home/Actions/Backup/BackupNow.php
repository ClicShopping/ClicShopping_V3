<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Tools\Backup\Sites\ClicShoppingAdmin\Pages\Home\Actions\Backup;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Tools\Backup\Classes\ClicShoppingAdmin\Backup;

  class BackupNow extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected mixed $app;

    public function __construct()
    {
      $this->app = Registry::get('Backup');
    }

    public function execute()
    {
      Backup::backupNow();

      $this->app->redirect('Backup');
    }
  }