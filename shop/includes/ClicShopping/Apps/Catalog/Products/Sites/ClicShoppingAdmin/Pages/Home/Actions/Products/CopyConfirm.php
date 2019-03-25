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


  namespace ClicShopping\Apps\Catalog\Products\Sites\ClicShoppingAdmin\Pages\Home\Actions\Products;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Cache;

  use ClicShopping\Apps\Configuration\Administrators\Classes\ClicShoppingAdmin\AdministratorAdmin;

  class CopyConfirm extends \ClicShopping\OM\PagesActionsAbstract {
    protected $app;
    protected $ID;
    protected $categoriesId;
    protected $currentCategoryId;
    protected $copyAs;

    public function __construct(){
      $this->app = Registry::get('Products');

      $this->ID = HTML::sanitize($_POST['products_id']);
      $this->categoriesId = HTML::sanitize($_POST['categories_id']);
      $this->currentCategoryId = HTML::sanitize($_POST['current_category_id']);
      $this->copyAs = $_POST['copy_as'];
    }


    private function Link() {
      if ($this->categoriesId != $this->currentCategoryId) {
        $Qcheck = $this->app->db->prepare('select count(*) as total
                                           from :table_products_to_categories
                                           where products_id = :products_id
                                           and categories_id = :categories_id
                                          ');
        $Qcheck->bindInt(':products_id', $this->ID );
        $Qcheck->bindInt(':categories_id', $this->categoriesId);
        $Qcheck->execute();

        if ($Qcheck->valueInt('total') < 1) {

          $this->app->db->save('products_to_categories', [
                                                          'products_id' => $this->ID,
                                                          'categories_id' => $this->categoriesId
                                                          ]
                              );
        }
      }
    }

    private function Duplicate() {
      $Qproduct = $this->app->db->prepare('select *
                                           from :table_products
                                           where products_id = :products_id
                                          ');
      $Qproduct->bindInt(':products_id', $this->ID );
      $Qproduct->execute();

      $product = $Qproduct->fetch();

      $this->app->db->save('products', [
                                        'products_quantity' => (int)$product['products_quantity'],
                                        'products_model' => $product['products_model'],
                                        'products_ean' => $product['products_ean'],
                                        'products_sku' => $product['products_sku'],
                                        'products_image' => $product['products_image'],
                                        'products_image_zoom' => $product['products_image_zoom'],
                                        'products_price' => (float)$product['products_price'],
                                        'products_date_added' => 'now()',
                                        'products_date_available' => (empty($product['products_date_available']) ? "null" : "'" . $product['products_date_available'] . "'"),
                                        'products_weight' => (float)$product['products_weight'],
                                        'products_price_kilo' => (float)$product['products_price_kilo'],
                                        'products_status' => 0,
                                        'products_tax_class_id' => (int)$product['products_tax_class_id'],
                                        'products_view' => (int)$product['products_view'],
                                        'orders_view' =>  (int)$product['orders_view'],
                                        'products_min_qty_order' => (int)$product['products_min_qty_order'],
                                        'admin_user_name' => AdministratorAdmin::getUserAdmin(),
                                        'products_volume' => $product['products_volume'],
                                        'products_only_online' => (int)$product['products_only_online'],
                                        'products_image_medium' => $product['products_image_medium'],
                                        'products_cost' => (float)$product['products_cost'],
                                        'products_handling' => $product['products_handling'],
                                        'products_packaging' => $product['products_packaging'],
                                        'products_sort_order' => (int)$product['products_sort_order'],
                                        'products_quantity_alert' => (int)$product['products_quantity_alert'],
                                        'products_only_shop' => (int)$product['products_only_shop'],
                                        'products_download_filename' => $product['products_download_filename'] ,
                                        'products_download_public' => $product['products_download_public'],
                                        'products_type' => $product['products_type']
                                      ]
                          );

      $dup_products_id = $this->app->db->lastInsertId();

// ---------------------
// gallery
// ----------------------
      $QproductImages = $this->app->db->prepare('select image,
                                                         htmlcontent,
                                                         sort_order
                                                  from :table_products_images
                                                  where products_id = :products_id
                                                ');
      $QproductImages->bindInt(':products_id',  $this->ID);
      $QproductImages->execute();

      while ($QproductImages->fetch() ) {

        $this->app->db->save('products_images', [
                                                'products_id' => (int)$dup_products_id,
                                                'image' => $QproductImages->value('image'),
                                                'htmlcontent' => $QproductImages->value('htmlcontent'),
                                                'sort_order' => $QproductImages->valueInt('sort_order')
                                                ]
                          );

      }

// ---------------------
// referencement
// ----------------------
      $Qdescription = $this->app->db->prepare('select *
                                               from :table_products_description
                                               where products_id = :products_id
                                               ');
      $Qdescription->bindInt(':products_id', $this->ID);
      $Qdescription->execute();

      while ($Qdescription->fetch() ) {

        $this->app->db->save('products_description', [
                                                      'products_id' => (int)$dup_products_id,
                                                      'language_id' => (int)$Qdescription->valueInt('language_id'),
                                                      'products_name' => $Qdescription->value('products_name'),
                                                      'products_description' => $Qdescription->value('products_description'),
                                                      'products_head_title_tag' => $Qdescription->value('products_head_title_tag'),
                                                      'products_head_desc_tag' => $Qdescription->value('products_head_desc_tag'),
                                                      'products_head_keywords_tag' => $Qdescription->value('products_head_keywords_tag'),
                                                      'products_url' => $Qdescription->value('products_url'),
                                                      'products_viewed' => 0,
                                                      'products_head_tag' => $Qdescription->value('products_head_tag'),
                                                      'products_shipping_delay' => $Qdescription->value('products_shipping_delay'),
                                                      'products_description_summary' => $Qdescription->value('products_description_summary')
                                                      ]
                          );

      }

      $this->app->db->save('products_to_categories', [
                                                      'products_id' => (int)$dup_products_id,
                                                      'categories_id' => (int)$this->categoriesId
                                                      ]
                          );

      $this->newProductsId = $dup_products_id;
    }

    private function productsDuplicate() {
      if ($this->copyAs == 'duplicate') {
        $this->Duplicate();
      }
    }

    private function productsLink() {
      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

      if ($this->copyAs == 'link') {
        if ($this->categoriesId != $this->currentCategoryId) {
          $this->Link();
        } else {
          $CLICSHOPPING_MessageStack->add($this->app->getDef('error_cannot_link_to_same_category'), 'danger');
        }
      }
    }


    public function execute()  {
      $CLICSHOPPING_Hooks = Registry::get('Hooks');

      if (isset($this->ID) && isset($this->categoriesId)) {
        $this->productsDuplicate();
        $this->productsLink();
      }

      Cache::clear('categories');
      Cache::clear('products-also_purchased');
      Cache::clear('products_related');
      Cache::clear('products_cross_sell');
      Cache::clear('upcoming');

      $CLICSHOPPING_Hooks->call('Products','CopyConfirm');

      $this->app->redirect('Products&cPath=' . $this->categoriesId);
    }
  }