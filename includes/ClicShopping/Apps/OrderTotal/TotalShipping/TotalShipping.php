<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\OrderTotal\TotalShipping;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  class TotalShipping extends \ClicShopping\OM\AppAbstract
  {

    protected $api_version = 1;
    protected string $identifier = 'ClicShopping_TotalShipping_V1';

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

        $directory = CLICSHOPPING::BASE_DIR . 'Apps/OrderTotal/TotalShipping/Module/ClicShoppingAdmin/Config';
        $name_space_config = 'ClicShopping\Apps\OrderTotal\TotalShipping\Module\ClicShoppingAdmin\Config';
        $trigger_message = 'ClicShopping\Apps\OrderTotal\TotalShipping\TotalShipping::getConfigModules(): ';

        $this->getConfigApps($result, $directory, $name_space_config, $trigger_message);
      }

      return $result;
    }

    public function getConfigModuleInfo($module, $info)
    {
      if (!Registry::exists('TotalShippingAdminConfig' . $module)) {
        $class = 'ClicShopping\Apps\OrderTotal\TotalShipping\Module\ClicShoppingAdmin\Config\\' . $module . '\\' . $module;
        Registry::set('TotalShippingAdminConfig' . $module, new $class);
      }

      return Registry::get('TotalShippingAdminConfig' . $module)->$info;
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
