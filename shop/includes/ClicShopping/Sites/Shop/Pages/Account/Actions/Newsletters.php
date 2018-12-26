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
  namespace ClicShopping\Sites\Shop\Pages\Account\Actions;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  class Newsletters extends \ClicShopping\OM\PagesActionsAbstract {

    public function execute() {
      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_Breadcrumb= Registry::get('Breadcrumb');
      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_NavigationHistory = Registry::get('NavigationHistory');
      $CLICSHOPPING_Language = Registry::get('Language');
      $CLICSHOPPING_Hooks = Registry::get('Hooks');

      $CLICSHOPPING_Hooks->call('Newsletters', 'PreAction');

      if (!$CLICSHOPPING_Customer->isLoggedOn()) {
        $CLICSHOPPING_NavigationHistory->setSnapshot();
        CLICSHOPPING::redirect(null, 'Account&LogIn');
      }

// templates
      $this->page->setFile('newsletters.php');
//Content
      $this->page->data['content'] = $CLICSHOPPING_Template->getTemplateFiles('account_newsletters');
//language
      $CLICSHOPPING_Language->loadDefinitions('account_newsletters');

      $CLICSHOPPING_Breadcrumb->add(CLICSHOPPING::getDef('navbar_title_1'), CLICSHOPPING::link(null, 'Account&Main'));
      $CLICSHOPPING_Breadcrumb->add(CLICSHOPPING::getDef('navbar_title_2'), CLICSHOPPING::link(null, 'Account&Newsletters'));
    }
  }