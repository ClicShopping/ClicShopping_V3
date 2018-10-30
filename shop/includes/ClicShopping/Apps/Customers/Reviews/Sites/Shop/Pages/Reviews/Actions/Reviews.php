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

  namespace ClicShopping\Apps\Customers\Reviews\Sites\Shop\Pages\Reviews\Actions;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  class Reviews extends \ClicShopping\OM\PagesActionsAbstract {

    public function execute() {
      if (isset($_GET['products_id']) && isset($_GET['Products']) ) {
        $CLICSHOPPING_Breadcrumb = Registry::get('Breadcrumb');
        $CLICSHOPPING_Template = Registry::get('Template');
        $CLICSHOPPING_Language = Registry::get('Language');

// templates
        $this->page->setFile('reviews.php');
//Content
        $this->page->data['content'] = $CLICSHOPPING_Template->getTemplateFiles('reviews');
//language
        $CLICSHOPPING_Language->loadDefinitions('reviews');

        $CLICSHOPPING_Breadcrumb->add(CLICSHOPPING::getDef('navbar_title'), CLICSHOPPING::link('index.php', 'Products&Reviews&products_id=' .(int)$_GET['products_id']));
      }
    }
  }