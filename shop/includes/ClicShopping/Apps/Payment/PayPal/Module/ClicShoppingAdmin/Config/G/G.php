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

  namespace ClicShopping\Apps\Payment\PayPal\Module\ClicShoppingAdmin\Config\G;

  class G extends \ClicShopping\Apps\Payment\PayPal\Module\ClicShoppingAdmin\Config\ConfigAbstract
  {
    public $is_installed = true;
    public $sort_order = 100000;

    protected function init()
    {
      $this->title = $this->app->getDef('module_g_title');
      $this->short_title = $this->app->getDef('module_g_short_title');
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
