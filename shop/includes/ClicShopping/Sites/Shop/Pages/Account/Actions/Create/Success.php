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

  namespace ClicShopping\Sites\Shop\Pages\Account\Actions\Create;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  class Success extends \ClicShopping\OM\PagesActionsAbstract  {

    public function execute()  {
      global $origin_href;

      $CLICSHOPPING_NavigationHistory = Registry::get('NavigationHistory');

      if ( $CLICSHOPPING_NavigationHistory->hasSnapshot() ) {
        $origin_href = $CLICSHOPPING_NavigationHistory->getSnapshotURL();
        $CLICSHOPPING_NavigationHistory->resetSnapshot();
      } else {
        $origin_href = CLICSHOPPING::redirect('index.php');
      }
    }
  }