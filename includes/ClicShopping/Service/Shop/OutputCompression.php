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

  namespace ClicShopping\Service\Shop;

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