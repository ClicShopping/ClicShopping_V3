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

class logo extends \ClicShopping\Apps\Payment\Stripe\Module\ClicShoppingAdmin\Config\ConfigParamAbstract
{
  public $default = 'stripe_cards.png';
  public int|null $sort_order = 30;

  protected function init()
  {
    $this->title = $this->app->getDef('cfg_stripe_logo_title');
    $this->description = $this->app->getDef('cfg_stripe_logo_desc');
  }
}
