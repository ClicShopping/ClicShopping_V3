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

  namespace ClicShopping\Apps\Tools\Backup\Sites\ClicShoppingAdmin\Pages\Home\Actions\Backup;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  class DeleteConfirm extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected $app;

    public function __construct()
    {
      $this->app = Registry::get('Backup');
    }

    public function execute()
    {
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

      $backup_directory = CLICSHOPPING::BASE_DIR . 'Work/Backups/';

      if (strstr($_GET['file'], '..')) $this->app->redirect('Backup');

      if (unlink($backup_directory . '/' . $_GET['file'])) {
        $CLICSHOPPING_MessageStack->add($this->app->getDef('success_backup_deleted'), 'success');

        $this->app->redirect('Backup');
      }
    }
  }