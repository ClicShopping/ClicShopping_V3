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


  namespace ClicShopping\Apps\Tools\Upgrade\Sites\ClicShoppingAdmin\Pages\Home\Actions\Upgrade;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\FileSystem;
  use ClicShopping\OM\CLICSHOPPING;

  use ClicShopping\Apps\Tools\Upgrade\Classes\ClicShoppingAdmin\Github;

  class CoreUpgrade extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected $app;

    public function __construct()
    {

      $CLICSHOPPING_Upgrade = Registry::get('Upgrade');
      $this->app = $CLICSHOPPING_Upgrade;
    }

    public function execute()
    {

      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_Github = new Github();

      if (FileSystem::isWritable(CLICSHOPPING::BASE_DIR . 'Sites/Work/OnlineUpdates')) {
        $CLICSHOPPING_Github->UpgradeClicShoppingCore();
        $CLICSHOPPING_MessageStack->add($this->app->getDef('success_core_installed'), 'success');
      } else {
        $CLICSHOPPING_MessageStack->add($this->app->getDef('error_directory_not_writable'), 'danger');
      }

      $this->app->redirect('Upgrade');
    }
  }