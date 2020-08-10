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

  namespace ClicShopping\Apps\Payment\Stripe\Module\ClicShoppingAdmin\Config\ST\Params;

  class private_key extends \ClicShopping\Apps\Payment\Stripe\Module\ClicShoppingAdmin\Config\ConfigParamAbstract {
    public $default = '';
    public $sort_order = 40;

    protected function init() {
      $this->title = $this->app->getDef('cfg_stripe_private_key_title');
      $this->description = $this->app->getDef('cfg_stripe_private_key_desc');
    }
  }
