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

      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_Breadcrumb = Registry::get('Breadcrumb');
      $CLICSHOPPING_Language = Registry::get('Language');

// templates
        $this->page->setFile('create_account_success.php');
//Content
        $this->page->data['content'] = $CLICSHOPPING_Template->getTemplateFiles('create_account_success');
//language
        $CLICSHOPPING_Language->loadDefinitions('create_account_success');

        $CLICSHOPPING_Breadcrumb->add(CLICSHOPPING::getDef('nav_bar_title_1'),  CLICSHOPPING::link('index.php', 'Account&Create'));
        $CLICSHOPPING_Breadcrumb->add(CLICSHOPPING::getDef('nav_bar_title_2'), CLICSHOPPING::link('index.php', 'Account&Create&Success'));
    }
  }