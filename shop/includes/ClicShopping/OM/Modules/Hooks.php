<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *
 *
 */


  namespace ClicShopping\OM\Modules;

  use ClicShopping\OM\Apps;

  class Hooks extends \ClicShopping\OM\ModulesAbstract {
      public function getInfo($app, $key, $data)  {
          $result = [];

          foreach ($data as $code => $class) {
              $class = $this->ns . $app . '\\' . $class;

              if (is_subclass_of($class, 'ClicShopping\OM\Modules\\' . $this->code . 'Interface')) {
                  $result[$app . '\\' . $key . '\\' . $code] = $class;
              }
          }

          return $result;
      }

      public function getClass($module) {
          if (strpos($module, '/') === false) { // TODO core hook compatibility; to remove
              return $module;
          }

          list($vendor, $app, $group, $code) = explode('\\', $module, 4);

          $info = Apps::getInfo($vendor . '\\' . $app);

          if (isset($info['modules'][$this->code][$group][$code])) {
              return $this->ns . $vendor . '\\' . $app . '\\' . $info['modules'][$this->code][$group][$code];
          }
      }

      public function filter($modules, $filter)  {
          $result = [];

          foreach ($modules as $key => $data) {
              if (($key == $filter['site'] . '/' . $filter['group']) && isset($data[$filter['hook']])) {
                  $result[$key] = $data;
              }
          }

          return $result;
      }
  }
