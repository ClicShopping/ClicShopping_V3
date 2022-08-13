<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Marketing\Featured\Sites\Shop\Pages\Featured;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Marketing\Featured\Featured as FeaturedApp;

  class Featured extends \ClicShopping\OM\PagesAbstract
  {
    public mixed $app;

    protected function init()
    {

      if (!Registry::exists('Featured')) {
        Registry::set('Featured', new FeaturedApp());
      }

      $CLICSHOPPING_Featured = Registry::get('Featured');

      $CLICSHOPPING_Featured->loadDefinitions('Sites/Shop/main');
    }
  }
