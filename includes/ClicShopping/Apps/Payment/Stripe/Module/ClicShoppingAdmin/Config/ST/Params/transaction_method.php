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

  namespace ClicShopping\Apps\Payment\Stripe\Module\ClicShoppingAdmin\Config\ST\Params;

  use ClicShopping\OM\HTML;

  class transaction_method extends \ClicShopping\Apps\Payment\Stripe\Module\ClicShoppingAdmin\Config\ConfigParamAbstract
  {
    public $default = 'automatic';
    public ?int $sort_order = 50;

    protected function init()
    {
      $this->title = $this->app->getDef('cfg_stripe_transaction_method_title');
      $this->description = $this->app->getDef('cfg_stripe_transaction_method_description');
    }

    public function getInputField()
    {
      $value = $this->getInputValue();

      $input = HTML::radioField($this->key, 'automatic', $value, 'id="' . $this->key . '1" autocomplete="off"') . $this->app->getDef('cfg_stripe_transaction_method_authorization') . ' ';
      $input .= HTML::radioField($this->key, 'manual', $value, 'id="' . $this->key . '2" autocomplete="off"') . $this->app->getDef('cfg_stripe_transaction_method_manual');

      return $input;
    }
  }