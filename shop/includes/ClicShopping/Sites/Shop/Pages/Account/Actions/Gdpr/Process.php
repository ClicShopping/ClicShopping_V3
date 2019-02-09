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

  namespace ClicShopping\Sites\Shop\Pages\Account\Actions\Gdpr;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  class Process extends \ClicShopping\OM\PagesActionsAbstract  {

    public function execute()  {
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_Hooks = Registry::get('Hooks');
      $CLICSHOPPING_Template = Registry::get('Template');

      if (isset($_POST['action']) && ($_POST['action'] == 'process') && isset($_POST['formid']) && ($_POST['formid'] == $_SESSION['sessiontoken'])) {
        $process = false;

        if ($process === false) {
          $source_folder = CLICSHOPPING::getConfig('dir_root', 'Shop') . 'includes/Module/Hooks/Shop/Account/';

          $files_get = $CLICSHOPPING_Template->getSpecificFiles($source_folder, 'AccountGdprCall*');

          if (is_array($files_get)) {
            foreach ($files_get as $value) {
              if (!empty($value['name'])) {
                $CLICSHOPPING_Hooks->call('Account', $value['name']);
              }
            }
          }

          $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('text_success_gdpr'), 'success', 'main');
        } else {
          $CLICSHOPPING_MessageStack->add(CLICSHOPPING::getDef('text_error_delete'), 'danger', 'main');
        }
      }

      CLICSHOPPING::redirect(null, 'Account&Main');
    }
  }