<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Payment\Stripe\Module\ClicShoppingAdmin\Config\ST\Params;

class key_webhook_endpoint extends \ClicShopping\Apps\Payment\Stripe\Module\ClicShoppingAdmin\Config\ConfigParamAbstract
{
  public $default = '';
  public int|null $sort_order = 48;

  protected function init()
  {
    $this->title = $this->app->getDef('cfg_stripe_key_webhook_endpoint_title');
    $this->description = $this->app->getDef('cfg_stripe_key_webhook_endpoint_desc');
  }
}
