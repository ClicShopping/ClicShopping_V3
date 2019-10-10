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

  namespace ClicShopping\Service\ClicShoppingAdmin;

  use ClicShopping\OM\Registry;

  use ClicShopping\Sites\ClicShoppingAdmin\Composer as ComposerClass;

  class Composer implements \ClicShopping\OM\ServiceInterface
  {

    public static function start(): bool
    {
        if (!Registry::exists('Composer')) {
          Registry::set('Composer', new ComposerClass());
        }

        return true;
    }

    public static function stop(): bool
    {
      return true;
    }
  }
