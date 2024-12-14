<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Service\ClicShoppingAdmin;

use ClicShopping\OM\ErrorHandler as ErrorHandlerClass;
use ClicShopping\OM\FileSystem;
use ClicShopping\OM\Registry;

/**
 * @namespace ClicShopping\Service\ClicShoppingAdmin
 *
 * The ErrorHandler class implements the ServiceInterface to provide start and stop functionalities for the error handler service.
 */
class ErrorHandler implements \ClicShopping\OM\ServiceInterface
{
  public static function start(): bool
  {
    if (!FileSystem::isWritable(ErrorHandlerClass::getDirectory())) {
      Registry::get('MessageStack')->add('The log directory is not writable. Please allow the web server to write to: ' . FileSystem::displayPath(ErrorHandlerClass::getDirectory()));
    }

    return true;
  }

  public static function stop(): bool
  {
    return true;
  }
}
