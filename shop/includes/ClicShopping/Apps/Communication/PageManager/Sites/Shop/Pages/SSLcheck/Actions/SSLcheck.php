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

  namespace ClicShopping\Apps\Communication\PageManager\Sites\Shop\Pages\SSLcheck\Actions;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  class SSLcheck extends \ClicShopping\OM\PagesActionsAbstract {

      public function execute() {
        $CLICSHOPPING_Template = Registry::get('Template');
        $CLICSHOPPING_Breadcrumb = Registry::get('Breadcrumb');
        $CLICSHOPPING_Language = Registry::get('Language');

// templates
        $this->page->setFile('ssl_check.php');
//Content
        $this->page->data['content'] = $CLICSHOPPING_Template->getTemplateFiles('ssl_check');
//language
        $CLICSHOPPING_Language->loadDefinitions('ssl_check');

        $CLICSHOPPING_Breadcrumb->add(CLICSHOPPING::getDef('navbar_title'), CLICSHOPPING::link(null, 'Info&SSLcheck'));

      }
    }
