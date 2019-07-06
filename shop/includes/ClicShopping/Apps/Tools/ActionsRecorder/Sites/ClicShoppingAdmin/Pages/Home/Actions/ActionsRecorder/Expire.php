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


  namespace ClicShopping\Apps\Tools\ActionsRecorder\Sites\ClicShoppingAdmin\Pages\Home\Actions\ActionsRecorder;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  use ClicShopping\Apps\Tools\ActionsRecorder\Classes\ClicShoppingAdmin\ActionsRecorder;

  class Expire extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected $app;

    public function __construct()
    {
      $this->app = Registry::get('ActionsRecorder');
    }

    public function execute()
    {
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_Template = Registry::get('TemplateAdmin');

      $file_extension = substr(CLICSHOPPING::getIndex(), strrpos(CLICSHOPPING::getIndex(), '.'));
      $directory_array = [];

      if ($dir = @dir(CLICSHOPPING::getConfig('dir_root', 'Shop') . 'includes/modules/action_recorder/')) {
        while ($file = $dir->read()) {
          if (!is_dir(CLICSHOPPING::getConfig('dir_root', 'Shop') . 'includes/modules/action_recorder/' . $file)) {
            if (substr($file, strrpos($file, '.')) == $file_extension) {
              $directory_array[] = $file;
            }
          }
        }
        sort($directory_array);
        $dir->close();
      }

      for ($i = 0, $n = count($directory_array); $i < $n; $i++) {
        $file = $directory_array[$i];

//    $CLICSHOPPING_Language->loadDefinitions($CLICSHOPPING_Template->getPathLanguageShopDirectory() . '/' . $CLICSHOPPING_Language->get('directory') . '/modules/action_recorder'  . $module_type . '/' . pathinfo($file, PATHINFO_FILENAME));

        include($CLICSHOPPING_Template->getDirectoryPathModuleShop() . '/action_recorder/' . $file);

        $class = substr($file, 0, strrpos($file, '.'));
        if (class_exists($class)) {
          $GLOBALS[$class] = new $class;
        }
      }


      $modules_array = [];
      $modules_list_array = array(array('id' => '',
        'text' => $this->app->getDef('txt_all_modules')
        )
      );

      $Qmodules = $this->app->db->get('action_recorder', 'distinct module', null, 'module');

      while ($Qmodules->fetch()) {
        $modules_array[] = $Qmodules->value('module');

        if (isset($GLOBALS[$Qmodules->value('module')]) && is_object($GLOBALS[$Qmodules->value('module')])) {
          $module_title = $GLOBALS[$Qmodules->value('module')]->title;
        } else {
          $module_title = $Qmodules->value('module');
        }

        $modules_list_array[] = ['id' => $Qmodules->value('module'),
          'text' => $module_title
        ];
      }

      $expired_entries = 0;

      if (isset($_GET['module']) && in_array($_GET['module'], $modules_array)) {
        if (is_object($GLOBALS[$_GET['module']])) {
          $expired_entries += $GLOBALS[$_GET['module']]->expireEntries();
        } else {
          $expired_entries = $this->app->db->delete('action_recorder', ['module' => $_GET['module']]);
        }
      } else {
        if (is_array($modules_array)) {
          foreach ($modules_array as $module) {
            if (isset($GLOBALS[$module]) && is_object($GLOBALS[$module])) {
              $expired_entries += $GLOBALS[$module]->expireEntries();
            }
          }
        }
      }

      $CLICSHOPPING_MessageStack->add($this->app->getDef('success_expired_entries', ['expired_entries' => $expired_entries]), 'success');

      $this->app->redirect('ActionsRecorder');
    }
  }