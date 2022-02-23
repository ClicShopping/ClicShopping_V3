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

  namespace ClicShopping\Apps\Marketing\Specials;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  class Specials extends \ClicShopping\OM\AppAbstract
  {

    protected $api_version = 1;
    protected string $identifier = 'ClicShopping_Specials_V1';

    protected function init()
    {
    }

    public function getConfigModules()
    {
      static $result;

      if (!isset($result)) {
        $result = [];

        $directory = CLICSHOPPING::BASE_DIR . 'Apps/Marketing/Specials/Module/ClicShoppingAdmin/Config';
        $name_space_config = 'ClicShopping\Apps\Marketing\Specials\Module\ClicShoppingAdmin\Config';
        $trigger_message = 'ClicShopping\Apps\Marketing\Specials\Specials::getConfigModules(): ';

        $this->getConfigApps($result, $directory, $name_space_config, $trigger_message);
      }

      return $result;
    }

    public function getConfigModuleInfo($module, $info)
    {
      if (!Registry::exists('SpecialsAdminConfig' . $module)) {
        $class = 'ClicShopping\Apps\Marketing\Specials\Module\ClicShoppingAdmin\Config\\' . $module . '\\' . $module;

        Registry::set('SpecialsAdminConfig' . $module, new $class);
      }

      return Registry::get('SpecialsAdminConfig' . $module)->$info;
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
