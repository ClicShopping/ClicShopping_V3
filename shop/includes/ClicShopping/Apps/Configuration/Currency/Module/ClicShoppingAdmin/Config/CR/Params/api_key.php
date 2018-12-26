<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4 
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

  namespace ClicShopping\Apps\Configuration\Currency\Module\ClicShoppingAdmin\Config\CR\Params;

  class api_key extends \ClicShopping\Apps\Configuration\Currency\Module\ClicShoppingAdmin\Config\ConfigParamAbstract {

    public $default = '';
    public $sort_order = 20;

    protected function init() {
      $this->title = $this->app->getDef('cfg_products_currency_api_key_title');
      $this->description = $this->app->getDef('cfg_products_currency_api_key_description');
    }
  }
