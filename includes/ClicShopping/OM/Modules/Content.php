<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */


namespace ClicShopping\OM\Modules;

use ClicShopping\OM\Apps;

class Content extends \ClicShopping\OM\ModulesAbstract
{
  public function getInfo($app, $key, $data)
  {
    $result = [];

    foreach ($data as $code => $class) {
      $class = $this->ns . $app . '\\' . $class;

      if (is_subclass_of($class, 'ClicShopping\OM\Modules\\' . $this->code . 'Interface')) {
        $result[$key . '/' . $app . '\\' . $code] = $class;
      }
    }

    return $result;
  }

  public function getClass($module)
  {
    [$group, $code] = explode('/', $module, 2);
    [$vendor, $app, $code] = explode('\\', $code, 3);

    $info = Apps::getInfo($vendor . '\\' . $app);

    if (isset($info['modules'][$this->code][$group][$code])) {
      return $this->ns . $vendor . '\\' . $app . '\\' . $info['modules'][$this->code][$group][$code];
    } else {
      return false;
    }
  }
}
