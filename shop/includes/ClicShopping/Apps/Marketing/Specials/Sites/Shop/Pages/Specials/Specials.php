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

  namespace ClicShopping\Apps\Marketing\Specials\Sites\Shop\Pages\Specials;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Marketing\Specials\Specials as SpecialsApp;

  class Specials extends \ClicShopping\OM\PagesAbstract {
    public $app;

    protected function init()  {

      if (!Registry::exists('Specials')) {
        Registry::set('Specials', new SpecialsApp());
      }

      $CLICSHOPPING_Specials = Registry::get('Specials');

      $CLICSHOPPING_Specials->loadDefinitions('Sites/Shop/main');
    }
  }
