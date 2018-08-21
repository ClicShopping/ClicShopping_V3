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


  namespace ClicShopping\Apps\Configuration\Modules\Sites\ClicShoppingAdmin\Pages\Home\Actions\Modules;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Cache;
  use ClicShopping\OM\Apps;

  class Remove extends \ClicShopping\OM\PagesActionsAbstract {
    protected $app;

    public function __construct() {
      $this->app = Registry::get('Modules');
    }

    public function execute() {
      $CLICSHOPPING_CfgModule = Registry::get('CfgModulesAdmin');
      $CLICSHOPPING_Modules = Registry::get('Modules');

      $modules = $CLICSHOPPING_CfgModule->getAll();

      $set = (isset($_GET['set']) ? $_GET['set'] : '');

      if (empty($set) || !$CLICSHOPPING_CfgModule->exists($set)) {
        $set = $modules[0]['code'];
      }

      $module_type = $CLICSHOPPING_CfgModule->get($set, 'code');
      $module_directory = $CLICSHOPPING_CfgModule->get($set, 'directory');

      $module_key = $CLICSHOPPING_CfgModule->get($set, 'key');

      $appModuleType = null;

      switch ($module_type) {
        case 'dashboard':
          $appModuleType = 'AdminDashboard';
          break;
        case 'header_tags':
          $appModuleType = 'HeaderTags';
          break;
        case 'payment':
          $appModuleType = 'Payment';
          break;

        case 'shipping':
          $appModuleType = 'Shipping';
          break;

        case 'order_total':
          $appModuleType = 'OrderTotal';
          break;
      }

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
          include($module_directory . $class . $file_extension);
          $module = new $class;
        }
      }

      if (isset($module)) {

        $module->remove();

        $modules_installed = explode(';', constant($module_key));

        if (in_array($class . $file_extension, $modules_installed)) {
          unset($modules_installed[array_search($class . $file_extension, $modules_installed)]);
        }

        Registry::get('Db')->save('configuration', ['configuration_value' => implode(';', $modules_installed)],
                                                   ['configuration_key' => $module_key]
                                 );

        Cache::clear('configuration');

        $CLICSHOPPING_Modules->redirect('Modules&set=' . $set);
      }
    }
  }