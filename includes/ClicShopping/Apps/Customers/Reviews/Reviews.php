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

  namespace ClicShopping\Apps\Customers\Reviews;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  class Reviews extends \ClicShopping\OM\AppAbstract
  {
    protected $api_version = 1;
    protected string $identifier = 'ClicShopping_Reviews_V1';

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

        $directory = CLICSHOPPING::BASE_DIR . 'Apps/Customers/Reviews/Module/ClicShoppingAdmin/Config';
        $name_space_config = 'ClicShopping\Apps\Customers\Reviews\Module\ClicShoppingAdmin\Config';
        $trigger_message = 'ClicShopping\Apps\Customers\Reviews\Reviews::getConfigModules(): ';

        $this->getConfigApps($result, $directory, $name_space_config, $trigger_message);
      }

      return $result;
    }

    public function getConfigModuleInfo($module, $info)
    {
      if (!Registry::exists('ReviewsAdminConfig' . $module)) {
        $class = 'ClicShopping\Apps\Customers\Reviews\Module\ClicShoppingAdmin\Config\\' . $module . '\\' . $module;

        Registry::set('ReviewsAdminConfig' . $module, new $class);
      }

      return Registry::get('ReviewsAdminConfig' . $module)->$info;
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
