<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */


  namespace ClicShopping\Apps\Configuration\Modules\Sites\ClicShoppingAdmin\Pages\Home\Actions\Modules;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Cache;
  use ClicShopping\OM\Apps;

  use ClicShopping\Apps\Configuration\Modules\Classes\ClicShoppingAdmin\ModulesAdmin;

  class Install extends \ClicShopping\OM\PagesActionsAbstract {
    protected $app;

    public function __construct() {
      $this->app = Registry::get('Modules');
    }

    public function execute() {
      $CLICSHOPPING_CfgModule = Registry::get('CfgModulesAdmin');
      $CLICSHOPPING_Modules = Registry::get('Modules');

      Registry::set('ModulesAdmin', new ModulesAdmin());
      $CLICSHOPPING_ModulesAdmin = Registry::get('ModulesAdmin');

      $modules = $CLICSHOPPING_CfgModule->getAll();

      $set = (isset($_GET['set']) ? $_GET['set'] : '');

      if (empty($set) || !$CLICSHOPPING_CfgModule->exists($set)) {
        $set = $modules[0]['code'];
      }

      $module_type = $CLICSHOPPING_CfgModule->get($set, 'code');
      $module_directory = $CLICSHOPPING_CfgModule->get($set, 'directory');

      $module_key = $CLICSHOPPING_CfgModule->get($set, 'key');

      $appModuleType = $CLICSHOPPING_ModulesAdmin->getSwitchModules($module_type);

      if (strpos($_GET['module'], '\\') !== false) {
        $class = Apps::getModuleClass($_GET['module'], $appModuleType);

        if (class_exists($class)) {
          $file_extension = '';
          $module = new $class();
          $class = $_GET['module'];
        }
      } else {

        $file_extension = substr(CLICSHOPPING::getIndex(), strrpos(CLICSHOPPING::getIndex(), '.'));
        $class = basename($_GET['module']);

        if (is_file($module_directory . $class . $file_extension)) {
          include_once($module_directory . $class . $file_extension);
          $module = new $class;
        }
      }

      if (isset($module)) {
        if ($module->check() > 0) { // remove module if already installed
          $module->remove();
        }

        $module->install();

        $modules_installed = explode(';', constant($module_key));

        if (!in_array($class . $file_extension, $modules_installed)) {
          $modules_installed[] = $class . $file_extension;
        }

        Registry::get('Db')->save('configuration', ['configuration_value' => implode(';', $modules_installed)],
                                                   ['configuration_key' => $module_key]
                                  );

        Cache::clear('configuration');

       $CLICSHOPPING_Modules->redirect('Modules&set=' . $set . '&module=' . $class);
      }
    }
  }