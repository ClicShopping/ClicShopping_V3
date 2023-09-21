<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */


namespace ClicShopping\Apps\Communication\PageManager\Sites\ClicShoppingAdmin\Pages\Home\Actions\PageManager;

use ClicShopping\OM\Cache;
use ClicShopping\OM\Registry;

use ClicShopping\Apps\Communication\PageManager\Classes\ClicShoppingAdmin\Status;

class SetFlag extends \ClicShopping\OM\PagesActionsAbstract
{

  public function execute()
  {

    $CLICSHOPPING_PageManager = Registry::get('PageManager');
    $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

    $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;

    Status::getPageManagerStatus($_GET['id'], $_GET['flag']);

    $CLICSHOPPING_MessageStack->add($CLICSHOPPING_PageManager->getDef('success_page_manager_status_updated'), 'success');

    Cache::clear('boxe_page_manager_primary-');
    Cache::clear('boxe_page_manager_secondary-');
    Cache::clear('page_manager_display_header_menu-');
    Cache::clear('page_manager_display_footer_menu-');
    Cache::clear('page_manager_display_footer-');
    Cache::clear('boxe_page_manager_display_information-');
    Cache::clear('boxe_page_manager_display_title-');

    $CLICSHOPPING_PageManager->redirect('PageManager&page=' . $page . '&bID=' . (int)$_GET['id']);
  }
}