<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Customers\Reviews\Module\ClicShoppingAdmin\Config\RV\Params;

class review_number extends \ClicShopping\Apps\Customers\Reviews\Module\ClicShoppingAdmin\Config\ConfigParamAbstract
{

  public $default = '10';
  public ?int $sort_order = 30;
  public bool $app_configured = true;

  protected function init()
  {
    $this->title = $this->app->getDef('cfg_reviews_review_number_title');
    $this->description = $this->app->getDef('cfg_reviews_review_number_description');
  }
}
