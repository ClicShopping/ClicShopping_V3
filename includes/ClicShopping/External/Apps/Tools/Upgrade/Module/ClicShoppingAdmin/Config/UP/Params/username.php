<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Tools\Upgrade\Module\ClicShoppingAdmin\Config\UP\Params;

class username extends \ClicShopping\Apps\Tools\Upgrade\Module\ClicShoppingAdmin\Config\ConfigParamAbstract
{

  public $default = '';
  public bool $app_configured = true;
  public ?int $sort_order = 20;

  protected function init()
  {
    $this->title = $this->app->getDef('cfg_upgrade_username_title');
    $this->description = $this->app->getDef('cfg_upgrade_username_description');
  }
}
