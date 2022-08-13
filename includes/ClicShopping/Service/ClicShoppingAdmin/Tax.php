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

  use ClicShopping\Sites\ClicShoppingAdmin\Tax as TaxClass;

  class Tax implements \ClicShopping\OM\ServiceInterface
  {
    public static function start(): bool
    {
      Registry::set('Tax', new TaxClass());

      return true;
    }

    public static function stop(): bool
    {
      return true;
    }
  }
