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


  namespace ClicShopping\Apps\Catalog\Categories\Module\ClicShoppingAdmin\Config\CT\Params;

  use ClicShopping\OM\HTML;

  class status extends \ClicShopping\Apps\Catalog\Categories\Module\ClicShoppingAdmin\Config\ConfigParamAbstract
  {
    public $default = 'True';
    public $sort_order = 10;

    protected function init()
    {
      $this->title = $this->app->getDef('cfg_products_categories_status_title');
      $this->description = $this->app->getDef('cfg_products_categories_status_description');
    }

    public function getInputField()
    {
      $value = $this->getInputValue();

      $input = HTML::radioField($this->key, 'True', $value, 'id="' . $this->key . '1" autocomplete="off"') . $this->app->getDef('cfg_products_categories_status_true') . ' ';
      $input .= HTML::radioField($this->key, 'False', $value, 'id="' . $this->key . '2" autocomplete="off"') . $this->app->getDef('cfg_products_categories_status_false');

      return $input;
    }
  }