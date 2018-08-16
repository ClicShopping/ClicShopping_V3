<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4 
 *
 *
 */

  namespace ClicShopping\Apps\Tools\DefineLanguage\Sites\ClicShoppingAdmin\Pages\Home;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Tools\DefineLanguage\DefineLanguage;

  class Home extends \ClicShopping\OM\PagesAbstract {
    public $app;

    protected function init() {
      $CLICSHOPPING_DefineLanguage = new DefineLanguage();
      Registry::set('DefineLanguage', $CLICSHOPPING_DefineLanguage);

      $this->app = $CLICSHOPPING_DefineLanguage;

      $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
    }
  }
