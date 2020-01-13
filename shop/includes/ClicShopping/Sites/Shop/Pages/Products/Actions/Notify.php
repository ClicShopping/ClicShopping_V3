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

  namespace ClicShopping\Sites\Shop\Pages\Products\Actions;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\HTML;

  class Notify extends \ClicShopping\OM\PagesActionsAbstract
  {
    public function execute()
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_ProductsCommon = Registry::get('ProductsCommon');
      $CLICSHOPPING_Language = Registry::get('Language');
      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_NavigationHistory = Registry::get('NavigationHistory');
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

      $products_id = $CLICSHOPPING_ProductsCommon->getId();

      $Qproduct = $CLICSHOPPING_Db->prepare('select p.products_id
                                             from :table_products p,
                                                  :table_products_description pd,
                                                  :table_products_to_categories p2c,
                                                  :table_categories c
                                             where p.products_status = 1
                                             and p.products_id =:products_id
                                             and pd.language_id = :language_id
                                             and p.products_id = p2c.products_id
                                             and p2c.categories_id = c.categories_id
                                             and c.status = 1
                                            ');
      $Qproduct->bindInt(':products_id', $products_id);
      $Qproduct->bindInt(':language_id', $CLICSHOPPING_Language->getId());
      $Qproduct->execute();

      $product_exists = ($Qproduct->fetch() !== false);

      if ($product_exists === false) {
        CLICSHOPPING::redirect();
      }

// if the customer is not logged on, redirect them to the login page
      if (!$CLICSHOPPING_Customer->isLoggedOn()) {
        $CLICSHOPPING_NavigationHistory->setSnapshot();

        CLICSHOPPING::redirect(null, 'Account&LogIn');
      }
    }
  }

