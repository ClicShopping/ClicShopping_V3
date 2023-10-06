<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Marketing\Recommendations\Module\ClicShoppingAdmin\Config\PR\Params;

class weighting_sentiment extends \ClicShopping\Apps\Marketing\Recommendations\Module\ClicShoppingAdmin\Config\ConfigParamAbstract
{

  public $default = 1.5;
  public ?int $sort_order = 70;
  public bool $app_configured = true;

  protected function init()
  {
    $this->title = $this->app->getDef('cfg_products_recommendations_weighting_sentiment_title');
    $this->description = $this->app->getDef('cfg_products_recommendations_weighting_sentiment_description');
  }
}
