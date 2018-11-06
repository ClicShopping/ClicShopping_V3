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

  namespace ClicShopping\Apps\Communication\PageManager\Sites\Shop\Pages\Contact\Actions;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  class Success extends \ClicShopping\OM\PagesActionsAbstract  {

    public function execute()  {
      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_Breadcrumb = Registry::get('Breadcrumb');
      $CLICSHOPPING_PageManager = Registry::get('PageManager');

      exit;
// templates
      $this->page->setFile('success.php');
//Content
      $this->page->data['content'] = $CLICSHOPPING_Template->getTemplateFiles('contact_success');
      $this->page->data['action'] = 'Success';
//language
      $CLICSHOPPING_PageManager->loadDefinitions('Sites/Shop/ContactSuccess/contact_success');

      $CLICSHOPPING_Breadcrumb->add($CLICSHOPPING_PageManager->getDef('navbar_title_1'), CLICSHOPPING::link(null, 'Info&Contact&Success'));
    }
  }