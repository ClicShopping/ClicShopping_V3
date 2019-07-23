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

  namespace ClicShopping\Apps\Payment\PayPal\Module\ClicShoppingAdmin\Config\PS\Params;

  use ClicShopping\OM\CLICSHOPPING;

  class ewp_working_directory extends \ClicShopping\Apps\Payment\PayPal\Module\ClicShoppingAdmin\Config\ConfigParamAbstract
  {

    public $default = CLICSHOPPING::BASE_DIR . 'Work/Log/';
    public $sort_order = 1200;

    protected function init()
    {
      $this->title = $this->app->getDef('cfg_ps_ewp_working_directory_title');
      $this->description = $this->app->getDef('cfg_ps_ewp_working_directory_desc');
    }
  }
