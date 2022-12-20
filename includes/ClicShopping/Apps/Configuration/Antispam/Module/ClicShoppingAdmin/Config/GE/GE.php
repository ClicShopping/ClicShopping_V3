<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT

   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Configuration\Antispam\Module\ClicShoppingAdmin\Config\GE;

  class GE extends \ClicShopping\Apps\Configuration\Antispam\Module\ClicShoppingAdmin\Config\ConfigAbstract
  {
    public bool $is_installed = true;
//    public bool $is_uninstallable = true;
    public ?int $sort_order = 100000;

    protected function init()
    {
      $this->title = $this->app->getDef('module_ge_title');
      $this->short_title = $this->app->getDef('module_ge_short_title');
    }

    public function install()
    {
      return false;
    }

    public function uninstall()
    {
      return false;
    }
  }