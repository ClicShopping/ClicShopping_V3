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
    protected string $identifier = 'ClicShopping_Favorites_V1';

    protected function init()
    {
    }

    /**
     * @return array|mixed
     */
    public function getConfigModules(): mixed
    {
      static $result;

      if (!isset($result)) {
        $result = [];

        $directory = CLICSHOPPING::BASE_DIR . 'Apps/Marketing/Favorites/Module/ClicShoppingAdmin/Config';
        $name_space_config = 'ClicShopping\Apps\Marketing\Favorites\Module\ClicShoppingAdmin\Config';
        $trigger_message = 'ClicShopping\Apps\Marketing\Favorites\Favorites::getConfigModules(): ';

        $this->getConfigApps($result, $directory, $name_space_config, $trigger_message);
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

     /**
     * @return string
     */
    public function getIdentifier() :String
    {
      return $this->identifier;
    }
  }
