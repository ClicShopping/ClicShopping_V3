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

use DirectoryIterator;
use function call_user_func;

/**
 * Class Apps
 *
 * This class provides static methods to handle and retrieve application data, such as obtaining a list of all apps,
 * retrieving specific modules, checking the existence of apps, and managing routes.
 */
class Apps
{

  /**
   * @return array
   */
  public static function getAll(): array
  {
    $result = [];

    $apps_directory = CLICSHOPPING::BASE_DIR . 'Apps';

    if ($vdir = new DirectoryIterator($apps_directory)) {
      foreach ($vdir as $vendor) {
        if (!$vendor->isDot() && $vendor->isDir()) {
          if ($adir = new DirectoryIterator($vendor->getPath() . DIRECTORY_SEPARATOR . $vendor->getFilename())) {
            foreach ($adir as $app) {
              if (!$app->isDot() && $app->isDir() && static::exists($vendor->getFilename() . '\\' . $app->getFilename())) {
                if (($json = static::getInfo($vendor->getFilename() . '\\' . $app->getFilename())) !== false) {
                  $result[] = $json;
                }
              }
            }
          }
        }
      }
    }

    return $result;
  }

  /**
   * @param string $type
   * @param string|null $filter_vendor_app
   * @param array|null $filter
   * @return array
   */
  public static function getModules(string $type, ?string $filter_vendor_app = null, ?array $filter = null): array
  {
    $result = [];

    if (!Registry::exists('ModuleType' . $type)) {
      $class = 'ClicShopping\OM\Modules\\' . $type;

      if (!class_exists($class)) {
        trigger_error('ClicShopping\OM\Apps::getModules(): ' . $type . ' module class not found in ClicShopping\OM\Modules\\');
        return $result;
      }

      Registry::set('ModuleType' . $type, new $class());
    }

    $CLICSHOPPING_Type = Registry::get('ModuleType' . $type);

    $filter_vendor = $filter_app = null;

    if (isset($filter_vendor_app)) {
      if (str_contains($filter_vendor_app, '\\')) {
        [$filter_vendor, $filter_app] = explode('\\', $filter_vendor_app, 2);
      } else {
        $filter_vendor = $filter_vendor_app;
      }
    }

    $vendor_directory = CLICSHOPPING::BASE_DIR . 'Apps';

    if (is_dir($vendor_directory)) {
      if ($vdir = new DirectoryIterator($vendor_directory)) {
        foreach ($vdir as $vendor) {
          if (!$vendor->isDot() && $vendor->isDir() && (!isset($filter_vendor) || ($vendor->getFilename() == $filter_vendor))) {
            if ($adir = new DirectoryIterator($vendor->getPath() . DIRECTORY_SEPARATOR . $vendor->getFilename())) {
              foreach ($adir as $app) {
                if (!$app->isDot() && $app->isDir() && (!isset($filter_app) || ($app->getFilename() == $filter_app)) && static::exists($vendor->getFilename() . '\\' . $app->getFilename()) && (($json = static::getInfo($vendor->getFilename() . '\\' . $app->getFilename())) !== false)) {
                  if (isset($json['modules'][$type])) {
                    $modules = $json['modules'][$type];

                    if (isset($filter)) {
                      $modules = $CLICSHOPPING_Type->filter($modules, $filter);
                    }

                    foreach ($modules as $key => $data) {
                      $result = array_merge($result, $CLICSHOPPING_Type->getInfo($vendor->getFilename() . '\\' . $app->getFilename(), $key, $data));
                    }
                  }
                }
              }
            }
          }
        }
      }
    }

    return $result;
  }

  /**
   * @param string $app
   * @return bool
   */
  public static function exists(string $app): bool
  {
    if (str_contains($app, '\\')) {
      [$vendor, $app] = explode('\\', $app, 2);

      if (class_exists('ClicShopping\Apps\\' . $vendor . '\\' . $app . '\\' . $app)) {
        if (is_subclass_of('ClicShopping\Apps\\' . $vendor . '\\' . $app . '\\' . $app, 'ClicShopping\OM\AppAbstract')) {
          return true;
        } else {
          trigger_error('ClicShopping\OM\Apps::exists(): ' . $vendor . '\\' . $app . ' - App is not a subclass of ClicShopping\OM\AppAbstract and cannot be loaded.');
        }
      }
    } else {
      trigger_error('ClicShopping\OM\Apps::exists(): ' . $app . ' - Invalid format, must be: Vendor\App.');
    }

    return false;
  }

  /**
   * @param $module
   * @param $type
   * @return bool
   */
  public static function getModuleClass(string $module, ?string $type)
  {
    if (!Registry::exists('ModuleType' . $type)) {
      $class = 'ClicShopping\OM\Modules\\' . $type;

      if (!class_exists($class)) {
        trigger_error('ClicShopping\OM\Apps::getModuleClass(): ' . $type . ' module class not found in ClicShopping\OM\Modules\\');

        return false;
      }

      Registry::set('ModuleType' . $type, new $class());
    }

    $CLICSHOPPING_Type = Registry::get('ModuleType' . $type);

    return $CLICSHOPPING_Type->getClass($module);
  }

  /**
   * @param string $app
   * @return bool|mixed
   */
  public static function getInfo(string $app)
  {
    if (str_contains($app, '\\')) {
      [$vendor, $app] = explode('\\', $app, 2);

      $metafile = CLICSHOPPING::BASE_DIR . 'Apps/' . basename($vendor) . DIRECTORY_SEPARATOR . basename($app) . '/clicshopping.json';

      if (is_file($metafile) && (($json = json_decode(file_get_contents($metafile), true)) !== null)) {
        return $json;
      }

      trigger_error('ClicShopping\OM\Apps::getInfo(): ' . $vendor . '\\' . $app . ' - Could not read App information in ' . $metafile . '.');
    } else {
      trigger_error('ClicShopping\OM\Apps::getInfo(): ' . $app . ' - Invalid format, must be: Vendor\App.');
    }

    return false;
  }

  /**
   * Remove specific double request with ? inside url
   * @param array $route
   * @return array
   */
  public static function getRouteValue(array $route): array
  {
    $query = $route; //$_GET

// replace parameter(s)
    $query['?'] = '&';

// rebuild url
    $query_result = http_build_query($query);
    $query_result = str_replace('%3F', '&', $query_result);
    $split_complete = [];

    $split_parameters = explode('&', $query_result);

    foreach ($split_parameters as $value) {
      $final_split = explode('=', $value);

      if (!isset($final_split[1])) {
        $final_split[1] = null;
      }

      $split_complete[$final_split[0]] = $final_split[1];
    }

    $result = $split_complete;

    return $result;
  }

  /**
   * @param null $route
   * @param null $filter_vendor_app
   * @return array|mixed
   */
  public static function getRouteDestination($route = null, $filter_vendor_app = null)
  {
    if (empty($route)) {

      $route = array_keys(static::getRouteValue($_GET));
    }

    $result = $routes = [];

    if (empty($route)) {
      return $result;
    }

    $filter_vendor = $filter_app = null;

    if (isset($filter_vendor_app)) {
      if (str_contains($filter_vendor_app, '\\')) {
        [$filter_vendor, $filter_app] = explode('\\', $filter_vendor_app, 2);
      } else {
        $filter_vendor = $filter_vendor_app;
      }
    }

    $vendor_directory = CLICSHOPPING::BASE_DIR . 'Apps';

    if (is_dir($vendor_directory)) {
      if ($vdir = new DirectoryIterator($vendor_directory)) {
        foreach ($vdir as $vendor) {
          if (!$vendor->isDot() && $vendor->isDir() && (!isset($filter_vendor) || ($vendor->getFilename() == $filter_vendor))) {
            if ($adir = new DirectoryIterator($vendor->getPath() . DIRECTORY_SEPARATOR . $vendor->getFilename())) {

              foreach ($adir as $app) {
                if (!$app->isDot() && $app->isDir() && (!isset($filter_app) || ($app->getFilename() == $filter_app)) && static::exists($vendor->getFilename() . '\\' . $app->getFilename()) && (($json = static::getInfo($vendor->getFilename() . '\\' . $app->getFilename())) !== false)) {
                  if (isset($json['routes'][CLICSHOPPING::getSite()])) {
                    $routes[$json['vendor'] . '\\' . $json['app']] = $json['routes'][CLICSHOPPING::getSite()];
                  }
                }
              }
            }
          }
        }
      }
    }

    return call_user_func(['ClicShopping\Sites\\' . CLICSHOPPING::getSite() . '\\' . CLICSHOPPING::getSite(),
      'resolveRoute'
    ],
      $route, $routes
    );
  }
}
