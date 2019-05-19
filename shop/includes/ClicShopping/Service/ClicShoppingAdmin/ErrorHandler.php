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
  use ClicShopping\OM\FileSystem;
  use ClicShopping\OM\ErrorHandler as ErrorHandlerClass;


  class ErrorHandler implements \ClicShopping\OM\ServiceInterface
  {

    public static function start()
    {
      if (!FileSystem::isWritable(ErrorHandlerClass::getDirectory())) {
        Registry::get('MessageStack')->add('The log directory is not writable. Please allow the web server to write to: ' . FileSystem::displayPath(ErrorHandlerClass::getDirectory()));
      }

      return true;
    }

    public static function stop()
    {
      return true;
    }
  }
