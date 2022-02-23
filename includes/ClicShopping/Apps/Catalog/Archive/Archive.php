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

  namespace ClicShopping\Apps\Catalog\Archive;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  class Archive extends \ClicShopping\OM\AppAbstract
  {

    protected $api_version = 1;
    protected string $identifier = 'ClicShopping_Archive_V1';

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

        $directory = CLICSHOPPING::BASE_DIR . 'Apps/Catalog/Archive/Module/ClicShoppingAdmin/Config';
        $name_space_config = 'ClicShopping\Apps\Catalog\Archive\Module\ClicShoppingAdmin\Config';
        $trigger_message = 'ClicShopping\Apps\Catalog\Archive\Archive::getConfigModules(): ';

        $this->getConfigApps($result, $directory, $name_space_config, $trigger_message);
      }

      return $result;
    }

    public function getConfigModuleInfo($module, $info)
    {
      if (!Registry::exists('ArchiveAdminConfig' . $module)) {
        $class = 'ClicShopping\Apps\Catalog\Archive\Module\ClicShoppingAdmin\Config\\' . $module . '\\' . $module;

        Registry::set('ArchiveAdminConfig' . $module, new $class);
      }

      return Registry::get('ArchiveAdminConfig' . $module)->$info;
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
