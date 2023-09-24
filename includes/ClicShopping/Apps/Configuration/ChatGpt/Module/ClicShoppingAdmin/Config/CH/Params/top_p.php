<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\ChatGpt\Module\ClicShoppingAdmin\Config\CH\Params;

class top_p extends \ClicShopping\Apps\Configuration\ChatGpt\Module\ClicShoppingAdmin\Config\ConfigParamAbstract
{
  public $default = '1';
  public ?int $sort_order = 50;
  public bool $app_configured = true;

  protected function init()
  {
    $this->title = $this->app->getDef('cfg_chatgpt_top_p_title');
    $this->description = $this->app->getDef('cfg_chatgpt_top_p_description');
  }
}
