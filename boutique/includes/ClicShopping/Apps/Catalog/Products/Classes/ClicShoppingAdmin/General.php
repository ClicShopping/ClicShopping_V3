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

  namespace ClicShopping\Apps\Catalog\Products\Classes\ClicShoppingAdmin;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Cache;

  class General {

    protected $categorie;
    protected $products;
    protected $lang;
    protected $template;

    public function __construct() {
      $this->products = Registry::get('Products');
      $this->lang = Registry::get('Language');
      $this->template = Registry::get('TemplateAdmin');

    }

/**
 * the category name
 *
 * @param string  $category_id, $language_id
 * @return string $category['products_name'],  name of the categorie
 * @access public
 * osc_get_category_name
 */
    public function getCategoryName($category_id, $language_id) {
      if (!$language_id) $language_id = $this->lang->getId();
      $Qcategory = Registry::get('Db')->get('products_description', 'products_name', ['products_id' => (int)$category_id, 'language_id' => $language_id]);

      return $Qcategory->value('products_name');
    }

////
// Count how many subproducts exist in a category
// TABLES: products
// osc_childs_in_category_count
// pb avec static function

    public function getChildsInCategoryCount($products_id) {
      $products_count = 0;

      $Qproducts = Registry::get('Db')->get('products', 'products_id', ['parent_id' => (int)$products_id]);

      while ($Qproducts->fetch() !== false) {
        $products_count++;

        $products_count += call_user_func(__METHOD__, $Qproducts->valueInt('products_id'));
      }

      return $products_count;
    }

// Count how many products exist in a category
// TABLES: products, products_to_products, products
// osc_products_in_category_count

    public function getProductsInCategoryCount($products_id, $include_deactivated = false) {

      if ($include_deactivated) {
        $Qproducts = $this->products->db->get([
                                                'products p',
                                                'products_to_products p2c'
                                                ], [
                                                'count(*) as total'
                                                ], [
                                                  'p.products_id' => [
                                                    'rel' => 'p2c.products_id'
                                                  ],
                                                  'p2c.products_id' => (int)$products_id
                                                ]
                                              );
      } else {
        $Qproducts = $this->products->db->get([
                                                  'products p',
                                                  'products_to_products p2c'
                                                  ], [
                                                  'count(*) as total'
                                                  ], [
                                                    'p.products_id' => [
                                                      'rel' => 'p2c.products_id'
                                                    ],
                                                    'p.products_status' => '1',
                                                    'p2c.products_id' => (int)$products_id
                                                  ]
                                                );
      }

      $products_count = $Qproducts->valueInt('total');

      $Qchildren = $this->products->db->get('products', 'products_id', ['parent_id' => (int)$products_id]);


      while ($Qchildren->fetch() !== false) {
        $products_count += call_user_func(__METHOD__, $Qchildren->valueInt('products_id'), $include_deactivated);
      }

      return $products_count;
    }

/**
 *  remove category
 *
 * @param string $category_id
 * @return string
 * @access public
 * osc_remove_category
 */
    public function removeCategory($category_id) {
      $QproductsImage = $this->products->db->prepare('select products_image
                                                       from :table_products
                                                       where products_id = :products_id
                                                     ');
      $QproductsImage->bindInt(':products_id',  (int)$category_id);

      $QproductsImage->execute();


// Controle si l'image est utilise sur une autre categorie
      $QduplicateImage = $this->products->db->prepare('select count(*) as total
                                                       from :table_products
                                                       where products_image = :products_image
                                                       ');
      $QduplicateImage->bindValue(':products_image', $QproductsImage->value('products_image'));

      $QduplicateImage->execute();

// Controle si l'image est utilise sur une autre categorie du blog
      $QduplicateBlogImage = $this->products->db->prepare('select count(*) as total
                                                            from :table_blog_products
                                                            where blog_products_image = :blog_products_image
                                                           ');
      $QduplicateBlogImage->bindValue(':blog_products_image', $QproductsImage->value('products_image'));

      $QduplicateBlogImage->execute();

// Controle si l'image est utilise sur les descriptions d'un blog
      $QduplicateImageBlogProductsDescription = $this->products->db->prepare('select count(*) as total
                                                                             from :table_blog_products_description
                                                                             where blog_products_description like :blog_products_description
                                                                           ');
      $QduplicateImageBlogProductsDescription->bindValue(':blog_products_description', '%' . $QproductsImage->value('products_image') .'%');

      $QduplicateImageBlogProductsDescription->execute();

// Controle si l'image est utilise le visuel d'un produit
      $QduplicateImageProducts = $this->products->db->prepare('select count(*) as total
                                                              from :table_products
                                                              where products_image = :products_image
                                                              or products_image_zoom = :products_image_zoom
                                                             ');
      $QduplicateImageProducts->bindValue(':products_image',  $QproductsImage->value('products_image') );
      $QduplicateImageProducts->bindValue(':products_image_zoom', $QproductsImage->value('products_image') );

      $QduplicateImageProducts->execute();

// Controle si l'image est utilise sur les descriptions d'un produit
      $QduplicateImageProductDescription = $this->products->db->prepare('select count(*) as total
                                                                         from :table_products_description
                                                                         where products_description like :blog_products_description
                                                                       ');
      $QduplicateImageProductDescription->bindValue(':blog_products_description', '%' . $QproductsImage->value('products_image') . '%');

      $QduplicateImageProductDescription->execute();

// Controle si l'image est utilisee sur une banniere
      $QduplicateImageBanners = $this->products->db->prepare('select count(*) as total
                                                              from :table_banners
                                                              where banners_image = :banners_image
                                                             ');
      $QduplicateImageBanners->bindValue(':banners_image', $QproductsImage->value('products_image') );

      $QduplicateImageBanners->execute();

// Controle si l'image est utilisee sur les fabricants
      $QduplicateImageManufacturers = $this->products->db->prepare('select count(*) as total
                                                                    from :table_manufacturers
                                                                    where manufacturers_image = :manufacturers_image
                                                                   ');
      $QduplicateImageManufacturers->bindValue(':manufacturers_image', $QproductsImage->value('products_image') );

      $QduplicateImageManufacturers->execute();

// Controle si l'image est utilisee sur les fournisseurs
      $QduplicateImageSuppliers = $this->products->db->prepare('select count(*) as total
                                                                from :table_suppliers
                                                                where suppliers_image = :suppliers_image
                                                               ');
      $QduplicateImageSuppliers->bindValue(':suppliers_image', $QproductsImage->value('products_image') );

      $QduplicateImageSuppliers->execute();

      if (($QduplicateImage->valueInt('total') < 2) &&
        ($QduplicateBlogImage->valueInt('total') == 0) &&
        ($QduplicateImageBlogProductsDescription->valueInt('total') == 0) &&
        ($QduplicateImageProducts->valueInt('total') == 0) &&
        ($QduplicateImageProductDescription->valueInt('total') == 0) &&
        ($QduplicateImageBanners->valueInt('total') == 0) &&
        ($QduplicateImageManufacturers->valueInt('total') == 0) &&
        ($QduplicateImageSuppliers->valueInt('total') == 0)) {

// delete categorie image
        if (file_exists($this->template->getDirectoryPathTemplateShopImages() . $QproductsImage->value('products_image'))) {
          @unlink($this->template->getDirectoryPathTemplateShopImages() . $QproductsImage->value('products_image'));
        }
      }

      $this->products->db->delete('products', ['products_id' => (int)$category_id]);
      $this->products->db->delete('products_description', ['products_id' => (int)$category_id]);
      $this->products->db->delete('products_to_products', ['products_id' => (int)$category_id]);

      Cache::clear('products');
      Cache::clear('products-also_purchased');
      Cache::clear('products_related');
      Cache::clear('products_cross_sell');
      Cache::clear('upcoming');

    }
  }