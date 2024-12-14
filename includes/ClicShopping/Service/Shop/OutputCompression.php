<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Service\Shop;
/**
 * This class provides a service for handling output compression in a shop context.
 * It uses gzip compression if enabled and the required zlib extension is loaded,
 * and if no headers have already been sent.
 */
class OutputCompression implements \ClicShopping\OM\ServiceInterface
{
  public static function start(): bool
  {
// configure gzip compression if it is enabled
    if ((GZIP_COMPRESSION == 'true') && extension_loaded('zlib') && !headers_sent()) {
      if ((int)ini_get('zlib.output_compression') < 1) {
        ini_set('zlib.output_handler', '');
        ini_set('zlib.output_compression', 1);
      }
      ini_set('zlib.output_compression_level', GZIP_LEVEL);
    };

    return false;
  }

  public static function stop(): bool
  {
    return true;
  }
}