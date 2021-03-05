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

  namespace ClicShopping\Apps\Marketing\Favorites;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  class Favorites extends \ClicShopping\OM\AppAbstract
  {

    protected $api_version = 1;
    protected $identifier = 'ClicShopping_Favorites_V1';

    protected function init()
    {
    }

    public function getConfigModules()
    {
      static $result;

      if (!isset($result)) {
        $result = [];

        $directory = CLICSHOPPING::BASE_DIR . 'Apps/Marketing/Favorites/Module/ClicShoppingAdmin/Config';

        if ($dir = new \DirectoryIterator($directory)) {
          foreach ($dir as $file) {
            if (!$file->isDot() && $file->isDir() && is_file($file->getPathname() . '/' . $file->getFilename() . '.php')) {
              $class = 'ClicShopping\Apps\Marketing\Favorites\Module\ClicShoppingAdmin\Config\\' . $file->getFilename() . '\\' . $file->getFilename();

              if (is_subclass_of($class, 'ClicShopping\Apps\Marketing\Favorites\Module\ClicShoppingAdmin\Config\ConfigAbstract')) {
                $sort_order = $this->getConfigModuleInfo($file->getFilename(), 'sort_order');
                if ($sort_order > 0) {
                  $counter = $sort_order;
                } else {
                  $counter = \count($result);
                }

                while (true) {
                  if (isset($result[$counter])) {
                    $counter++;

                    continue;
                  }

                  $result[$counter] = $file->getFilename();

                  break;
                }
              } else {
                trigger_error('ClicShopping\Apps\Marketing\Favorites\Favorites::getConfigModules(): ClicShopping\Apps\Marketing\Favorites\Module\ClicShoppingAdmin\Config\\' . $file->getFilename() . '\\' . $file->getFilename() . ' is not a subclass of ClicShopping\Apps\Marketing\Favorites\Module\ClicShoppingAdmin\Config\ConfigAbstract and cannot be loaded.');
              }
            }
          }

          ksort($result, SORT_NUMERIC);
        }
      }

      return $result;
    }

    public function getConfigModuleInfo($module, $info)
    {
      if (!Registry::exists('FavoritesAdminConfig' . $module)) {
        $class = 'ClicShopping\Apps\Marketing\Favorites\Module\ClicShoppingAdmin\Config\\' . $module . '\\' . $module;

        Registry::set('FavoritesAdminConfig' . $module, new $class);
      }

      return Registry::get('FavoritesAdminConfig' . $module)->$info;
    }


    public function getApiVersion()
    {
      return $this->api_version;
    }

    public function getIdentifier()
    {
      return $this->identifier;
    }
  }
