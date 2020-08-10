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

  namespace ClicShopping\Apps\Catalog\Categories\Module\Hooks\ClicShoppingAdmin\DashboardShortCut;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;

  use ClicShopping\Apps\Catalog\Categories\Categories as categoriesApp;

  class DashboardShortCutCategories implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected $app;

    public function __construct()
    {
      if (!Registry::exists('Categories')) {
        Registry::set('Categories', new categoriesApp());
      }

      $this->app = Registry::get('Categories');

      $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/DashboardShortCut/dashboard_shortcut_categories');
    }

    public function display(): string
    {
      if (!defined('CLICSHOPPING_APP_CATEGORIES_CT_STATUS') || CLICSHOPPING_APP_CATEGORIES_CT_STATUS == 'False') {
        return false;
      }

      $output = HTML::link(CLICSHOPPING::link(null, 'A&Catalog\Categories&Categories'), null, 'class="btn btn-primary btn-sm" role="button"><span class="fas fa-list" title="' . $this->app->getDef('heading_short_categories') . '"') . ' ';

      return $output;
    }
  }