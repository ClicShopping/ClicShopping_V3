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

  namespace ClicShopping\Apps\Tools\DefineLanguage\Sites\ClicShoppingAdmin\Pages\Home\Actions;

  use ClicShopping\OM\Registry;

  class DefineLanguage extends \ClicShopping\OM\PagesActionsAbstract {
    public function execute() {
      $CLICSHOPPING_DefineLanguage = Registry::get('DefineLanguage');

      $this->page->setFile('define_language.php');
      $this->page->data['action'] = 'DefineLanguage';

      $CLICSHOPPING_DefineLanguage->loadDefinitions('Sites/ClicShoppingAdmin/main');
    }
  }