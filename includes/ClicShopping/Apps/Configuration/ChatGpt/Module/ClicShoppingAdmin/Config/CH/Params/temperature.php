<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Configuration\ChatGpt\Module\ClicShoppingAdmin\Config\CH\Params;

  class temperature extends \ClicShopping\Apps\Configuration\ChatGpt\Module\ClicShoppingAdmin\Config\ConfigParamAbstract
  {
    public $default = '0.5';
    public ?int $sort_order = 40;
    public bool $app_configured = true;

    protected function init()
    {
      $this->title = $this->app->getDef('cfg_chatgpt_temperature_title');
      $this->description = $this->app->getDef('cfg_chatgpt_temperature_description');
    }
  }
