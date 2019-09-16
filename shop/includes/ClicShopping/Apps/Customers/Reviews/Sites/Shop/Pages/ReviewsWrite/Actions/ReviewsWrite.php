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

  namespace ClicShopping\Apps\Customers\Reviews\Sites\Shop\Pages\ReviewsWrite\Actions;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  class ReviewsWrite extends \ClicShopping\OM\PagesActionsAbstract
  {

    public function execute()
    {

      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_ProductsCommon = Registry::get('ProductsCommon');
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_NavigationHistory = Registry::get('NavigationHistory');
      $CLICSHOPPING_Language = Registry::get('Language');
      $CLICSHOPPING_Breadcrumb = Registry::get('Breadcrumb');

      if (!$CLICSHOPPING_Customer->isLoggedOn()) {
        $CLICSHOPPING_NavigationHistory->setSnapshot();
        CLICSHOPPING::redirect(null, 'Account&LogIn');
      }

      if (!isset($_GET['products_id'])) {
        CLICSHOPPING::redirect(null, CLICSHOPPING::getAllGET(array('action')));
      }

      $Qproducts = $CLICSHOPPING_Db->prepare('select products_id,
                                                      products_quantity as in_stock,
                                                      products_image
                                                from :table_products
                                                where products_id = :products_id
                                                and products_status = 1
                                                and products_view = 1
                                              ');
      $Qproducts->bindInt(':products_id', $CLICSHOPPING_ProductsCommon->getID());

      $Qproducts->execute();

      if ($Qproducts->fetch() === false) {
        CLICSHOPPING::redirect(null, CLICSHOPPING::getAllGET(array('action')));
      }

// templates
      $this->page->setFile('reviews_write.php');
//Content
      $this->page->data['content'] = $CLICSHOPPING_Template->getTemplateFiles('product_reviews_write');

//language
      $CLICSHOPPING_Language->loadDefinitions('product_reviews_write');

      $CLICSHOPPING_Breadcrumb->add(CLICSHOPPING::getDef('navbar_title'), CLICSHOPPING::link(null, 'Products&ReviewsWrite&products_id=' . $CLICSHOPPING_ProductsCommon->getID()));

    }
  }