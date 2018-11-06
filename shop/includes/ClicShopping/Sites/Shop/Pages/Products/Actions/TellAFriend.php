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

  namespace ClicShopping\Sites\Shop\Pages\Products\Actions;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  class TellAFriend extends \ClicShopping\OM\PagesActionsAbstract {

    public function execute() {
      $CLICSHOPPING_ProductsCommon = Registry::get('ProductsCommon');
      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_Breadcrumb= Registry::get('Breadcrumb');
      $CLICSHOPPING_NavigationHistory = Registry::get('NavigationHistory');
      $CLICSHOPPING_Language = Registry::get('Language');

      if (!$CLICSHOPPING_Customer->isLoggedOn() && (ALLOW_GUEST_TO_TELL_A_FRIEND == 'false')) {
        $CLICSHOPPING_NavigationHistory->setSnapshot();
        CLICSHOPPING::redirect(null, 'Account&LogIn');
      }

      if ( isset($_GET['products_id']) && !empty($_GET['products_id']) ) {
        if ( empty($CLICSHOPPING_ProductsCommon->getProductsName() )) {
          CLICSHOPPING::redirect(null, 'Products&Description&products_id=' . $CLICSHOPPING_ProductsCommon->getId());
        }
      }

// templates
      $this->page->setFile('tell_a_friend.php');
//Content
      $this->page->data['content'] = $CLICSHOPPING_Template->getTemplateFiles('tell_a_friend');
//language
      $CLICSHOPPING_Language->loadDefinitions('tell_a_friend');

      $CLICSHOPPING_Breadcrumb->add(CLICSHOPPING::getDef('navbar_title'), CLICSHOPPING::link(null, 'Products&Description&TellAFriend&products_id=' . (int)$CLICSHOPPING_ProductsCommon->getId()));
    }
  }
