<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

  namespace ClicShopping\Apps\Catalog\Categories\Module\Hooks\ClicShoppingAdmin\Stats;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Catalog\Categories\Categories as categoriesApp;

  class StatsCategories implements \ClicShopping\OM\Modules\HooksInterface {
    protected $app;

    public function __construct()   {
      if (!Registry::exists('Categories')) {
        Registry::set('Categories', new categoriesApp());
      }

      $this->app = Registry::get('Categories');

      $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/Stats/stat_categories');
    }

    private function getCategoriesOn() {
      $QCategories = $this->app->db->prepare('select count(*) as count
                                              from :table_categories
                                              where status = 1
                                            ');
      $QCategories->execute();

      return $QCategories->valueInt('count');
    }

    private function getCategoriesOff() {
      $QCategories = $this->app->db->prepare('select count(*) as count
                                              from :table_categories
                                              where status = 0
                                            ');
      $QCategories->execute();

      return $QCategories->valueInt('count');
    }

    public function display() {
      if (!defined('CLICSHOPPING_APP_CATEGORIES_CT_STATUS') || CLICSHOPPING_APP_CATEGORIES_CT_STATUS == 'False') {
        return false;
      }

        $output = '
  <div class="card col-md-2 cardStatsWarning">
    <div class="card-block">
      <h4 class="StatsTitle">' . $this->app->getDef('text_categories_alert') . '</h4>
      <div class="card-text">
        <div class="col-sm-12">
          <span class="float-md-left">
            <i class="fas fa-bell-slash fa-2x" aria-hidden="true"></i>
          </span>
          <span class="float-md-right">
            <div class="col-sm-12 StatsValue">' .  $this->getCategoriesOn() . ' - ' . $this->app->getDef('text_categories_on') . '</div>
            <div class="col-sm-12 StatsValue">' .  $this->getCategoriesOff() . ' - ' . $this->app->getDef('text_categories_off') . '</div>
          </span>
        </div>
      </div>
    </div>
  </div>
      ';

      return $output;
    }
  }