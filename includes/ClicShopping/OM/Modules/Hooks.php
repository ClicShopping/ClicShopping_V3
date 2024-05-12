<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\OM\Modules;

use ClicShopping\OM\Apps;

class Hooks extends \ClicShopping\OM\ModulesAbstract
{
  /**
   * @param $app
   * @param $key
   * @param $data
   * @return array
   */
  public function getInfo($app, $key, $data)
  {
    $result = [];

    foreach ($data as $code => $class) {
      $class = $this->ns . $app . '\\' . $class;

      if (is_subclass_of($class, 'ClicShopping\OM\Modules\\' . $this->code . 'Interface')) {
        $result[$app . '\\' . $key . '\\' . $code] = $class;
      }
    }

    return $result;
  }

  /**
   * @param $module
   * @return false|string
   */
  public function getClass($module)
  {
    if (!str_contains($module, '/')) {
      return $module;
    }

    [$vendor, $app, $group, $code] = explode('\\', $module, 4);

    $info = Apps::getInfo($vendor . '\\' . $app);

    if (isset($info['modules'][$this->code][$group][$code])) {
      return $this->ns . $vendor . '\\' . $app . '\\' . $info['modules'][$this->code][$group][$code];
    } else {
      return false;
    }
  }

  /**
   * @param $modules
   * @param $filter
   * @return array|mixed
   */
  public function filter($modules, $filter)
  {
    $result = [];

    foreach ($modules as $key => $data) {
      if (($key === $filter['site'] . DIRECTORY_SEPARATOR . $filter['group']) && isset($data[$filter['hook']])) {
        $result[$key] = $data;
      }
    }

    return $result;
  }
}
