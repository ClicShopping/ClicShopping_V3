<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Sites\Shop\Pages\Account\Actions;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  class CreatePro extends \ClicShopping\OM\PagesActionsAbstract
  {

    public function execute()
    {
      $CLICSHOPPING_Breadcrumb = Registry::get('Breadcrumb');
      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_Language = Registry::get('Language');

// templates
      $this->page->setFile('create_account_pro.php');
//Content
      $this->page->data['content'] = $CLICSHOPPING_Template->getTemplateFiles('create_account_pro');
//language
      $CLICSHOPPING_Language->loadDefinitions('create_account_pro');

      $CLICSHOPPING_Breadcrumb->add(CLICSHOPPING::getDef('navbar_title'), CLICSHOPPING::link(null, 'Account&CreatePro'));
    }
  }