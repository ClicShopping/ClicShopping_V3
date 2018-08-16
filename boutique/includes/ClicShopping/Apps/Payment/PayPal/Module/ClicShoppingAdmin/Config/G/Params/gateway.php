<?php
  /**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4 
 *
 *
 */

namespace ClicShopping\Apps\Payment\PayPal\Module\ClicShoppingAdmin\Config\G\Params;

use ClicShopping\OM\HTML;

class gateway extends \ClicShopping\Apps\Payment\PayPal\Module\ClicShoppingAdmin\Config\ConfigParamAbstract
{
    public $default = '1';
    public $sort_order = 100;

    protected function init()
    {
        $this->title = $this->app->getDef('cfg_gateway_title');
        $this->description = $this->app->getDef('cfg_gateway_desc');
    }

    public function getInputField()
    {
        $value = $this->getInputValue();

      $input =  HTML::radioField($this->key, '1', $value, 'id="' . $this->key . '1" autocomplete="off"') . $this->app->getDef('cfg_gateway_paypal') . '<br /> ';
      $input .=  HTML::radioField($this->key, '0', $value, 'id="' . $this->key . '0" autocomplete="off"') . $this->app->getDef('cfg_gateway_payflow') . '<br /> ';

      return $input;
    }
}
