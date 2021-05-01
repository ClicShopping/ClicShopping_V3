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

  use ClicShopping\Apps\Catalog\Products\Classes\ClicShoppingAdmin\Image as ImageClass;

  class Image implements \ClicShopping\OM\ServiceInterface
  {
    public static function start(): bool
    {
      Registry::set('Image', new ImageClass());

      return true;
    }

    public static function stop(): bool
    {
      return true;
    }
  }
