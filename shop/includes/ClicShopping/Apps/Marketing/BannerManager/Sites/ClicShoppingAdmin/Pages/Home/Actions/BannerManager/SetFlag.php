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

  namespace ClicShopping\Apps\Marketing\BannerManager\Sites\ClicShoppingAdmin\Pages\Home\Actions\BannerManager;

  use ClicShopping\OM\Registry;

  use  ClicShopping\Apps\Marketing\BannerManager\Classes\ClicShoppingAdmin\Status;

  class SetFlag extends \ClicShopping\OM\PagesActionsAbstract {

    public function execute()  {

      $CLICSHOPPING_BannerManager = Registry::get('BannerManager');
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

      if ( ($_GET['flag'] == 0) || ($_GET['flag'] == 1) ) {
        Status::setBannerStatus($_GET['bID'], $_GET['flag']);
      } else {
        $CLICSHOPPING_MessageStack->add($CLICSHOPPING_BannerManager->getDef('error_unknown_status_flag'), 'error');
      }

      $CLICSHOPPING_BannerManager->redirect('BannerManager&page=' . $_GET['page']);
    }
  }