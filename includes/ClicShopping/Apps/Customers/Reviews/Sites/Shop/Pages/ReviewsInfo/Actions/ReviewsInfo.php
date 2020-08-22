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

  namespace ClicShopping\Apps\Customers\Reviews\Sites\Shop\Pages\ReviewsInfo\Actions;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  class ReviewsInfo extends \ClicShopping\OM\PagesActionsAbstract
  {
    public function execute()
    {
      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_Breadcrumb = Registry::get('Breadcrumb');
      $CLICSHOPPING_ProductsCommon = Registry::get('ProductsCommon');
      $CLICSHOPPING_Language = Registry::get('Language');
      $CLICSHOPPING_Reviews = Registry::get('Reviews');

      if (!isset($_GET['products_id']) && !is_numeric($CLICSHOPPING_ProductsCommon->getId())) {
        CLICSHOPPING::redirect();
      }

      if (isset($_GET['reviews_id'])) {
        $review = $CLICSHOPPING_Reviews->getDataReviews($_GET['reviews_id']);

        $date_added = $review['date_added'];
        $customers_name = HTML::outputProtected(substr($review['customers_name'], 0, -4)) . ' ...';
        $reviews_rating = $review['reviews_rating'];
        $reviews_text = $review['reviews_text'];
        $products_name = HTML::link(CLICSHOPPING::link(null, 'Products&Description&products_id=' . $CLICSHOPPING_ProductsCommon->getID()) . '" itemprop="url" class="productTitle"', '<span itemprop="name">' . HTML::outputProtected($CLICSHOPPING_ProductsCommon->getProductsName()) . '</span>');

        $product_price = $CLICSHOPPING_ProductsCommon->getCustomersPrice();
        $button_small_view_details = HTML::button(CLICSHOPPING::getDef('button_details'), '', CLICSHOPPING::link(null, 'Products&Description&products_id=' . $CLICSHOPPING_ProductsCommon->getID()), 'info', null, 'sm');

        if (!is_null($CLICSHOPPING_ProductsCommon->getProductsImage($CLICSHOPPING_ProductsCommon->getID()))) {
          $products_image = '<h1>' . HTML::image($CLICSHOPPING_Template->getDirectoryTemplateImages() . $CLICSHOPPING_ProductsCommon->getProductsImage($CLICSHOPPING_ProductsCommon->getID()), $CLICSHOPPING_ProductsCommon->getProductsName($CLICSHOPPING_ProductsCommon->getID()), (int)SMALL_IMAGE_WIDTH, (int)SMALL_IMAGE_HEIGHT, 'hspace="5" vspace="5"') . '</h1>';
        }

        if ($CLICSHOPPING_ProductsCommon->getProductsArchive() === 1 && is_numeric($CLICSHOPPING_ProductsCommon->getId())) {
          $product_price = '';
          $product_not_sell = CLICSHOPPING::getDef('products_not_sell');
        }

  // templates
        $this->page->setFile('reviews_info.php');
  //Content
        $this->page->data['content'] = $CLICSHOPPING_Template->getTemplateFiles('product_reviews_info');

  //language
        $CLICSHOPPING_Language->loadDefinitions('product_reviews_info');

        $all_get = CLICSHOPPING::getAllGET([
          'Products',
          'Reviews'
        ]);

        $CLICSHOPPING_Breadcrumb->add(CLICSHOPPING::getDef('navbar_title'), CLICSHOPPING::link(null, $all_get));
      }
    }
  }