<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Communication\PageManager\Sites\Shop\Pages\Content\Actions;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\HTML;
use ClicShopping\OM\HTTP;
use ClicShopping\OM\Registry;

use ClicShopping\Apps\Communication\PageManager\PageManager as PageManagerApp;

class Content extends \ClicShopping\OM\PagesActionsAbstract
{
  public function execute()
  {
    $CLICSHOPPING_Template = Registry::get('Template');
    $CLICSHOPPING_Breadcrumb = Registry::get('Breadcrumb');
    $CLICSHOPPING_PageManagerShop = Registry::get('PageManagerShop');

    if (!Registry::exists('PageManager')) {
      Registry::set('PageManager', new PageManagerApp());
    }

    $CLICSHOPPING_PageManager = Registry::get('PageManager');
    $this->app = $CLICSHOPPING_PageManager;

    $CLICSHOPPING_PageManager->loadDefinitions('Sites/Shop/Content/content');

// Recuperation de la valeur id de order.php
    if (isset($_GET['pagesId'])) {
      $id = HTML::sanitize($_GET['pagesId']);

      if (!empty($CLICSHOPPING_PageManagerShop->pageManagerDisplayInformation($id))) {
        $page_title = $CLICSHOPPING_PageManagerShop->pageManagerDisplayTitle($id);

        $CLICSHOPPING_Breadcrumb->add($page_title, CLICSHOPPING::link(null, 'Info&Content&pagesId=' . $id));
// templates
        $this->page->setFile('content.php');
//Content
        $this->page->data['content'] = $CLICSHOPPING_Template->getTemplateFiles('page_manager');

        $this->page->data['content'];
      } else {
        $url = HTTP::redirect(HTTP::getShopUrlDomain() . 'index.php');
        header('Location: ' . $url);
      }
    } else {
      $CLICSHOPPING_PageManager->redirect();
    }
  }
}
