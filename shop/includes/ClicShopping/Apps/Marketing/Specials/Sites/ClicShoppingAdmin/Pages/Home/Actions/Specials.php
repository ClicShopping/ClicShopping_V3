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

  namespace ClicShopping\Apps\Marketing\Specials\Sites\ClicShoppingAdmin\Pages\Home\Actions;

  use ClicShopping\OM\Registry;

  class Specials extends \ClicShopping\OM\PagesActionsAbstract {
    public function execute() {
      $CLICSHOPPING_Specials = Registry::get('Specials');

      $this->page->setFile('specials.php');
      $this->page->data['action'] = 'Specials';

      $CLICSHOPPING_Specials->loadDefinitions('Sites/ClicShoppingAdmin/Specials');
    }
  }