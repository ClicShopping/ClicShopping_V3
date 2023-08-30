<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Catalog\Categories\Module\Hooks\ClicShoppingAdmin\Stats;

use ClicShopping\Apps\Catalog\Categories\Categories as categoriesApp;
use ClicShopping\OM\Registry;

class StatsCategories implements \ClicShopping\OM\Modules\HooksInterface
{
  protected mixed $app;

  public function __construct()
  {
    if (!Registry::exists('Categories')) {
      Registry::set('Categories', new categoriesApp());
    }

    $this->app = Registry::get('Categories');

    $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/Stats/stat_categories');
  }

  /**
   * @return int
   */
  private function getCategoriesOn(): int
  {
    $QCategories = $this->app->db->prepare('select count(categories_id) as count
                                              from :table_categories
                                              where status = 1
                                            ');
    $QCategories->execute();

    return $QCategories->valueInt('count');
  }

  /**
   * @return int
   */
  private function getCategoriesOff(): int
  {
    $QCategories = $this->app->db->prepare('select count(categories_id) as count
                                              from :table_categories
                                              where status = 0
                                            ');
    $QCategories->execute();

    return $QCategories->valueInt('count');
  }

  public function display()
  {
    if (!\defined('CLICSHOPPING_APP_CATEGORIES_CT_STATUS') || CLICSHOPPING_APP_CATEGORIES_CT_STATUS == 'False') {
      return false;
    }

    if ($this->getCategoriesOn() == 0 && $this->getCategoriesOff() == 0) {
      $output = '';
    } else {
      $output = '
<div class="col-md-2 col-12">
    <div class="card bg-warning">
     <div class="card-body">
      <h6 class="card-title text-white">' . $this->app->getDef('text_categories_alert') . '</h6>
      <div class="card-text">
        <div class="col-sm-12">
          <span class="float-start">
            <i class="bi bi-bell-fill text-white"></i>
          </span>
          <span class="float-end">
            <div class="col-sm-12 text-white">' . $this->getCategoriesOn() . ' - ' . $this->app->getDef('text_categories_on') . '</div>
            <div class="col-sm-12 text-white">' . $this->getCategoriesOff() . ' - ' . $this->app->getDef('text_categories_off') . '</div>
          </span>
        </div>
      </div>
    </div>
  </div>
</div>  
      ';
    }

    return $output;
  }
}