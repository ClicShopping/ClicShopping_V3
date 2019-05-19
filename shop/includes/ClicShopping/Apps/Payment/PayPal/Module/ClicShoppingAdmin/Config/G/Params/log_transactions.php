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

  namespace ClicShopping\Apps\Payment\PayPal\Module\ClicShoppingAdmin\Config\G\Params;

  use ClicShopping\OM\HTML;

  class log_transactions extends \ClicShopping\Apps\Payment\PayPal\Module\ClicShoppingAdmin\Config\ConfigParamAbstract
  {
    public $default = '1';
    public $sort_order = 500;

    protected function init()
    {
      $this->title = $this->app->getDef('cfg_log_transactions_title');
      $this->description = $this->app->getDef('cfg_log_transactions_desc');
    }

    public function getInputField()
    {
      $value = $this->getInputValue();

      $array_menu = array(array('id' => '1', 'text' => $this->app->getDef('cfg_log_transactions_all')),
        array('id' => '0', 'text' => $this->app->getDef('cfg_log_transactions_errors')),
        array('id' => '-1', 'text' => $this->app->getDef('cfg_log_transactions_disabled'))
      );

      $input = HTML::selectField($this->key, $array_menu, $value, $this->getInputValue());

      return $input;
    }
  }
