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


  namespace ClicShopping\Apps\Tools\ActionsRecorder\Sites\ClicShoppingAdmin\Pages\Home\Actions\ActionsRecorder;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  use ClicShopping\Apps\Tools\ActionsRecorder\Classes\ClicShoppingAdmin\ActionsRecorder;

  class Expire extends \ClicShopping\OM\PagesActionsAbstract {
    protected $app;

    public function __construct() {
      $this->app = Registry::get('ActionsRecorder');
    }

    public function execute() {
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

      $modules_array = [];
      $modules_list_array = array(array('id' => '',
                                        'text' => $this->app->getDef('txt_all_modules')
                                      )
                                  );

      $Qmodules = $this->app->db->get('action_recorder', 'distinct module', null, 'module');

      while ($Qmodules->fetch()) {
        $modules_array[] = $Qmodules->value('module');

        $modules_list_array[] = ['id' => $Qmodules->value('module'),
                                 'text' => (is_object($GLOBALS[$Qmodules->value('module')]) ? $GLOBALS[$Qmodules->value('module')]->title : $Qmodules->value('module'))
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

        foreach ($modules_array as $module) {
          if (is_object($GLOBALS[$module])) {
            $expired_entries += $GLOBALS[$module]->expireEntries();
          }
        }
      }

      $CLICSHOPPING_MessageStack->add($this->app->getDef('success_expired_entries', ['expired_entries' =>  $expired_entries]), 'success');

      $this->app->redirect('ActionsRecorder');
    }
  }