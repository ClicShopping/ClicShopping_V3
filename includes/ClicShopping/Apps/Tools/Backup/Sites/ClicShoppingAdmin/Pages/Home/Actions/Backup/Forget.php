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

  class Forget extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected mixed $app;

    public function __construct()
    {
      $this->app = Registry::get('Backup');
    }

    public function execute()
    {
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

      $this->app->db->delete('configuration', ['configuration_key' => 'DB_LAST_RESTORE']);

      $CLICSHOPPING_MessageStack->add($this->app->getDef('success_last_restore_cleared'), 'success');

      $this->app->redirect('Backup');
    }
  }