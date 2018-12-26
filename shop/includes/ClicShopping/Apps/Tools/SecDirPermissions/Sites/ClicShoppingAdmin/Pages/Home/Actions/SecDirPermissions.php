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

  namespace ClicShopping\Apps\Tools\SecDirPermissions\Sites\ClicShoppingAdmin\Pages\Home\Actions;

  use ClicShopping\OM\Registry;

  class SecDirPermissions extends \ClicShopping\OM\PagesActionsAbstract {
    public function execute() {
      $CLICSHOPPING_SecDirPermissions = Registry::get('SecDirPermissions');

      $this->page->setFile('sec_dir_permissions.php');

      $CLICSHOPPING_SecDirPermissions->loadDefinitions('Sites/ClicShoppingAdmin/main');
    }
  }