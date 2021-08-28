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

  class ResetCacheTemp extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected mixed $app;

    public function execute()
    {
      $this->app = Registry::get('Upgrade');

      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

      if (FileSystem::isWritable(CLICSHOPPING::BASE_DIR . 'Work/Cache/Github/Temp')) {
        $cache_file = glob(CLICSHOPPING::BASE_DIR . 'Work/Cache/Github/Temp/*.json');

        foreach ($cache_file as $value) {
          unlink($value);
        }

        $CLICSHOPPING_MessageStack->add($this->app->getDef('success_deleted_installed'), 'success', 'update');
      } else {
        $CLICSHOPPING_MessageStack->add($this->app->getDef('error_directory_not_writable'), 'danger', 'update');
      }

      $this->app->redirect('Upgrade');
    }
  }