<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\OM;

use RuntimeException;

/**
 * The ErrorHandler class is responsible for managing PHP error handling initialization
 * and ensuring proper configuration of error-related settings.
 */
class ErrorHandler
{
  public static function initialize()
  {
    ini_set('display_errors', false);
    ini_set('html_errors', false);
    ini_set('ignore_repeated_errors', true);

    if (FileSystem::isWritable(static::getDirectory(), true)) {
      if (!is_dir(static::getDirectory())) {
        if (!mkdir($concurrentDirectory = static::getDirectory(), 0777, true) && !is_dir($concurrentDirectory)) {
          throw new RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }
      }
    }

    if (FileSystem::isWritable(static::getDirectory())) {
      ini_set('log_errors', true);
      ini_set('error_log', static::getDirectory() . 'errors-' . date('Ymd') . '.txt');
    }
  }

  public static function getDirectory(): string
  {
    return CLICSHOPPING::BASE_DIR . 'Work/Log/';
  }
}
