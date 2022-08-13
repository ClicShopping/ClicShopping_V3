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

  use ClicShopping\Apps\Configuration\Currency\Classes\Shop\Currencies as CurrenciesClass;

  class Currencies implements \ClicShopping\OM\ServiceInterface
  {
    public static function start(): bool
    {
      Registry::set('Currencies', new CurrenciesClass());

      return true;
    }

    public static function stop(): bool
    {
      return true;
    }
  }
