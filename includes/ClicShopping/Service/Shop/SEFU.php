<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Service\Shop;

  class SEFU implements \ClicShopping\OM\ServiceInterface
  {
    /**
     * @return mixed|string
     */
    private static function getPathInfo() {
      $path_info = $_SERVER['PATH_INFO'] ?? ($_SERVER['ORIG_PATH_INFO'] ?? '');

      return $path_info;
    }

    public static function start(): bool
    {
      $path_info = static::getPathInfo() ;

      if (isset($path_info) && (\strlen($path_info) > 1)) {
        $parameters = explode('/', substr($path_info, 1));

        $_GET = [];
        $GET_array = [];

        foreach ($parameters as $parameter) {
          $param_array = explode('-', $parameter, 2);

          if (!isset($param_array[1])) {
            $param_array[1] = '';
          }

          if (str_contains($param_array[0], '[]')) {
            $GET_array[substr($param_array[0], 0, -2)][] = $param_array[1];
          } else {
            $_GET[$param_array[0]] = $param_array[1];
          }
        }

        if (\count($GET_array) > 0) {
          foreach ($GET_array as $key => $value) {
            $_GET[$key] = $value;
          }
        }
      }

      return true;
    }

    public static function stop(): bool
    {
      return true;
    }

    /**
     * @return mixed|null
     */
    public static function getUrlValue()
    {
      $path_info = static::getPathInfo() ;
      $value_language = null;

      if (isset($path_info) && (\strlen($path_info) > 1)) {
        $parameters = explode('/', substr($path_info, 1));

        foreach ($parameters as $parameter) {
          $param_array = explode('-', $parameter, 2);

          if ($param_array[0] == 'language') {
            $value_language = $param_array[1];
          } else {
            $value_language = null;
          }
        }

        return $value_language;
      }
    }
  }