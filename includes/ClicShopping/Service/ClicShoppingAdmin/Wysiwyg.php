<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Service\ClicShoppingAdmin;

  use ClicShopping\OM\Registry;

  use ClicShopping\Sites\ClicShoppingAdmin\CkEditor4 as CkEditor4;

  class Wysiwyg implements \ClicShopping\OM\ServiceInterface
  {
    public static function start(): bool
    {
        Registry::set('Wysiwyg', new CkEditor4());

      return true;
    }

    public static function stop(): bool
    {
      return true;
    }
  }
