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

  use ClicShopping\OM\HTML;

  class ewp_status extends \ClicShopping\Apps\Payment\PayPal\Module\ClicShoppingAdmin\Config\ConfigParamAbstract
  {
    public $default = '-1';
    public $sort_order = 700;

    protected function init()
    {
      $this->title = $this->app->getDef('cfg_ps_ewp_status_title');
      $this->description = $this->app->getDef('cfg_ps_ewp_status_desc');
    }

    public function getInputField()
    {
      $value = $this->getInputValue();

      $input = HTML::radioField($this->key, '1', $value, 'autocomplete="off"') . $this->app->getDef('cfg_ps_ewp_status_true') . '<br /> ';
      $input .= HTML::radioField($this->key, '-1', $value, 'autocomplete="off"') . $this->app->getDef('cfg_ps_ewp_status_false') . '<br />';

      return $input;
    }
  }
