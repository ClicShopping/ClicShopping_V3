<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Customers\Gdpr\Module\ClicShoppingAdmin\Config\GD\Params;

class date extends \ClicShopping\Apps\Customers\Gdpr\Module\ClicShoppingAdmin\Config\ConfigParamAbstract
{

  public $default = '180';
  public ?int $sort_order = 20;
  public bool $app_configured = true;

  protected function init()
  {
    $this->title = $this->app->getDef('cfg_gdpr_date_title');
    $this->description = $this->app->getDef('cfg_gdpr_date_description');
  }
}
