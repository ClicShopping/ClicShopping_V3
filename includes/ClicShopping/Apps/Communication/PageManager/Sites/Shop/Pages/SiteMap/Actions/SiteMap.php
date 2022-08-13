<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Communication\PageManager\Sites\Shop\Pages\SiteMap\Actions;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  class SiteMap extends \ClicShopping\OM\PagesActionsAbstract
  {

    public function execute()
    {
      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_Breadcrumb = Registry::get('Breadcrumb');
      $CLICSHOPPING_PageManager = Registry::get('PageManager');

// templates
      $this->page->setFile('sitemap.php');
//Content
      $this->page->data['content'] = $CLICSHOPPING_Template->getTemplateFiles('sitemap');
//language
      $CLICSHOPPING_PageManager->loadDefinitions('Sites/Shop/SiteMap/sitemap');

      $CLICSHOPPING_Breadcrumb->add($CLICSHOPPING_PageManager->getDef('navbar_title'), CLICSHOPPING::link(null, 'Info&SiteMap'));

    }
  }
