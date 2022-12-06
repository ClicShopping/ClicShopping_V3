<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Configuration\Api;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\FileSystem;

  class Api extends \ClicShopping\OM\AppAbstract
  {

    protected $api_version = 1;
    protected string $identifier = 'ClicShopping_Api_V1';

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

        $directory = CLICSHOPPING::BASE_DIR . 'Apps/Configuration/Api/Module/ClicShoppingAdmin/Config';
        $name_space_config = 'ClicShopping\Apps\Configuration\Api\Module\ClicShoppingAdmin\Config';
        $trigger_message = 'ClicShopping\Apps\Configuration\Api\Api::getConfigModules(): ';

        $this->getConfigApps($result, $directory, $name_space_config, $trigger_message);
      }

      return $result;
    }

    public function getConfigModuleInfo($module, $info)
    {
      if (!Registry::exists('ApiAdminConfig' . $module)) {
        $class = 'ClicShopping\Apps\Configuration\Api\Module\ClicShoppingAdmin\Config\\' . $module . '\\' . $module;

        Registry::set('ApiAdminConfig' . $module, new $class);
      }

      return Registry::get('ApiAdminConfig' . $module)->$info;
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

    /**
     * @param string $message
     * @param string $version
     */
      public function logUpdate(string $message, string $version)
    {
      if (FileSystem::isWritable(CLICSHOPPING::BASE_DIR . 'Work/Log')) {
        file_put_contents(CLICSHOPPING::BASE_DIR . 'Work/Log/apiLog-' . $version . '.log', '[' . date('d-M-Y H:i:s') . '] ' . $message . "\n", FILE_APPEND);
      }
    }
  }
