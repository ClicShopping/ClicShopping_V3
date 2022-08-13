<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Tools\SecDirPermissions;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  class SecDirPermissions extends \ClicShopping\OM\AppAbstract
  {

    protected $api_version = 1;
    protected string $identifier = 'ClicShopping_SecDirPermissions_V1';

    protected function init()
    {
    }

    /**
     * @return array|mixed
     */
    public function getConfigModules()
    {
      static $result;

      if (!isset($result)) {
        $result = [];

        $directory = CLICSHOPPING::BASE_DIR . 'Apps/Tools/SecDirPermissions/Module/ClicShoppingAdmin/Config';
        $name_space_config = 'ClicShopping\Apps\Tools\SecDirPermissions\Module\ClicShoppingAdmin\Config';
        $trigger_message = 'ClicShopping\Apps\Tools\SecDirPermissions\SecDirPermissions::getConfigModules(): ';

        $this->getConfigApps($result, $directory, $name_space_config, $trigger_message);
      }

      return $result;
    }

    public function getConfigModuleInfo($module, $info)
    {
      if (!Registry::exists('SecDirPermissionsAdminConfig' . $module)) {
        $class = 'ClicShopping\Apps\Tools\SecDirPermissions\Module\ClicShoppingAdmin\Config\\' . $module . '\\' . $module;

        Registry::set('SecDirPermissionsAdminConfig' . $module, new $class);
      }

      return Registry::get('SecDirPermissionsAdminConfig' . $module)->$info;
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
