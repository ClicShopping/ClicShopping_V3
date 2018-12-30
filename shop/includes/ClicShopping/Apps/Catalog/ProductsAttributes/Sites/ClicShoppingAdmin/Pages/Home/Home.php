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

  namespace ClicShopping\Apps\Catalog\ProductsAttributes\Sites\ClicShoppingAdmin\Pages\Home;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Catalog\ProductsAttributes\ProductsAttributes;

  class Home extends \ClicShopping\OM\PagesAbstract {
    public $app;

    protected function init() {
      $CLICSHOPPING_ProductsAttributes = new ProductsAttributes();
      Registry::set('ProductsAttributes', $CLICSHOPPING_ProductsAttributes);

      $this->app = Registry::get('ProductsAttributes');

      $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
    }
  }
