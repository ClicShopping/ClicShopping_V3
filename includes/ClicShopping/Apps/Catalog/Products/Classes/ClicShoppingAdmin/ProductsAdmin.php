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

  namespace ClicShopping\Apps\Catalog\Products\Classes\ClicShoppingAdmin;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Upload;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Cache;
  use ClicShopping\OM\HTTP;

  use ClicShopping\Apps\Configuration\Administrators\Classes\ClicShoppingAdmin\AdministratorAdmin;

  class ProductsAdmin
  {
    protected $id;
    protected $db;
    protected $template;
    protected $hooks;
    protected $lang;

    public function __construct()
    {
      $this->db = Registry::get('Db');
      $this->template = Registry::get('TemplateAdmin');
      $this->hooks = Registry::get('Hooks');
      $this->lang = Registry::get('Language');
      $this->image = Registry::get('Image');
    }

    /**
     * Get all products information
     * @param $id products_id
     * @return array, table data
     *
     */
    public function get(int $id): array
    {
      $Qproducts = $this->db->prepare('select p.*,
                                              date_format(p.products_date_available, \'%Y-%m-%d\') as products_date_available,
                                              pd.*
                                      from :table_products p,
                                           :table_products_description pd
                                      where p.products_id = :products_id
                                      and p.products_id = pd.products_id
                                      and pd.language_id = :language_id'
                                      );

      $Qproducts->bindInt(':products_id', $id);
      $Qproducts->bindInt(':language_id', $this->lang->getId());
      $Qproducts->execute();

      $data = $Qproducts->toArray();

      return $data;
    }

    /**
     * Save the product description
     * @param $id , produts_id
     * @param $action , save or insert
     * @access private
     */
    private function saveProductsDescription(int $id, string $action)
    {
      $languages = $this->lang->getLanguages();

      for ($i = 0, $n = \count($languages); $i < $n; $i++) {
        $language_id = $languages[$i]['id'];

        $sql_data_array = [
          'products_name' => HTML::sanitize($_POST['products_name'][$language_id]),
          'products_description' => $_POST['products_description'][$language_id],
          'products_head_title_tag' => HTML::sanitize($_POST['products_head_title_tag'][$language_id]),
          'products_head_desc_tag' => HTML::sanitize($_POST['products_head_desc_tag'][$language_id]),
          'products_head_keywords_tag' => HTML::sanitize($_POST['products_head_keywords_tag'][$language_id]),
          'products_url' => HTML::sanitize($_POST['products_url'][$language_id]),
          'products_head_tag' => HTML::sanitize($_POST['products_head_tag'][$language_id]),
          'products_shipping_delay' => HTML::sanitize($_POST['products_shipping_delay'][$language_id]),
          'products_description_summary' => HTML::sanitize($_POST['products_description_summary'][$language_id])
        ];

        if (is_numeric($id) && $action == 'Insert') {
          $insert_sql_data = [
            'products_id' => (int)$id,
            'language_id' => (int)$language_id
          ];

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          $this->db->save('products_description', $sql_data_array);
//update products
        } else {
          $update_sql_data = [
            'products_id' => (int)$id,
            'language_id' => (int)$language_id
          ];

          $this->db->save('products_description', $sql_data_array, $update_sql_data);
        } // end action
      } //end for
    }

    /**
     * @return string
     */
    public function getProductModel(): string
    {
      if (empty($_POST['products_model'])) {
        $rand = rand();
        $products_model = CONFIGURATION_PREFIX_MODEL . $rand;
      } else {
        $products_model = HTML::sanitize($_POST['products_model']);
      }

      return $products_model;
    }

    public function getProductSKU(): string
    {
      if (empty($_POST['products_sku'])) {
        $products_sku = $this->getProductModel();
      } elseif ($_POST['products_sku'] != $this->getProductModel()) {
        $products_sku = HTML::sanitize($_POST['products_sku']);
      } else {
        $products_sku = $this->getProductModel();
      }

      return $products_sku;
    }

    public function getProductEAN(): string
    {
      if (empty($_POST['products_ean'])) {
        $products_ean = $this->getProductSKU();
      } elseif ($_POST['products_ean'] != $this->getProductSKU()) {
        $products_ean = HTML::sanitize($_POST['products_ean']);
      } else {
        $products_ean = $this->getProductSKU();
      }

      return $products_ean;
    }

    /**
     * @return mixed|string
     */
    private function saveFileUpload(): ?string
    {
      $array_extension = ['zip', 'doc', 'pdf', 'odf', 'xls', 'mp3', 'mp4', 'avi', 'png', 'jpg', 'gif'];

      $upload_file = new Upload('products_download_filename', $this->template->getPathDownloadShopDirectory(), null, $array_extension);

      if ($upload_file->check() && $upload_file->save()) {
        $products_download_filename = $upload_file->getFilename();
        $file = HTML::removeFileAccents($products_download_filename);
      } else {
        $file = null;
      }

      return $file;
    }

    /**
     * getInfoImage
     *
     * @param string $image , $alt, $width, $height
     * @param $alt
     * @param string $width
     * @param string $height
     * @return string $image, the image value
     *
     */

    public function getInfoImage($image, $alt, string $width = '130', string $height = '130'): string
    {
      if (!empty($image) && (file_exists($this->template->getDirectoryPathTemplateShopImages() . $image))) {
        $image = HTML::image($this->template->getDirectoryShopTemplateImages() . $image, $alt, $width, $height);
      } else {
        $image = HTML::image(HTTP::getShopUrlDomain() . 'images/nophoto.png', CLICSHOPPING::getDef('text_image_nonexistent'), $width, $height);
      }

      return $image;
    }

    /**
     * Select the product packaging
     *
     * @param string
     * @return $product_packaging, the packaging selected
     *
     */
    public function getproductPackaging(int $id): string
    {
      if (!is_null($_SESSION['ProductAdminId'])) {
        $id = $_SESSION['ProductAdminId'];

        $QproductAdmin = $this->db->prepare('select products_packaging
                                             from :table_products
                                             where products_id = :products_id
                                            ');
        $QproductAdmin->bindInt(':products_id', $id);
        $QproductAdmin->execute();

        $packaging = $QproductAdmin->valueInt('products_packaging');
      } else {
        $QproductAdmin = $this->db->prepare('select products_packaging
                                             from :table_products
                                             where products_id = :products_id
                                            ');
        $QproductAdmin->bindInt(':products_id', $id);
        $QproductAdmin->execute();

        $packaging = $QproductAdmin->valueInt('products_packaging');
      }

      if ($packaging == 1) {
        $product_packaging = 'New product';
      } elseif ($packaging == 2) {
        $product_packaging = 'Product repackaged';
      } else {
        $product_packaging = 'Product used';
      }

      return $product_packaging;
    }

    /**
     * the products quantity unit title
     *
     * @param string $products_quantity_unit_id , $language_id
     * @return string $products_quantity_unit_['products quantity unit_title'],  name of the he products quantity unit
     *
     */
    public function getProductsQuantityUnitTitle($products_quantity_unit_id = '', $language_id = '')
    {

      if (!$language_id) $language_id = $this->lang->getId();

      $QproductsQuantityUnitTitle = $this->db->prepare('select products_quantity_unit_title
                                                        from :table_products_quantity_unit
                                                        where products_quantity_unit_id = :products_quantity_unit_id
                                                        and language_id = :language_id
                                                      ');

      $QproductsQuantityUnitTitle->bindInt(':products_quantity_unit_id', $products_quantity_unit_id);
      $QproductsQuantityUnitTitle->bindInt(':language_id', $language_id);

      $QproductsQuantityUnitTitle->execute();

      return $QproductsQuantityUnitTitle->value('products_quantity_unit_title');
    }

    /**
     * Products model
     *
     * @param string $product_id
     * @return string $product['products_model'], products model
     *
     */
    public function getProductsModel($id = ''): string
    {
      $QproductsModel = $this->db->prepare('select products_model
                                            from :table_products
                                            where products_id = :products_id
                                           ');

      $QproductsModel->bindInt(':products_id', $id);

      $QproductsModel->execute();

      return $QproductsModel->value('products_model');
    }

    /**
     * Shipping delay of the product
     *
     * @param string|int|null $product_id , $language_id
     * @return string|bool $product['products_shipping_delay'], url of the product
     *
     */
    public function getProductsShippingDelay(string|int|null $id = null, int $language_id) :string|bool
    {
      if (!is_null($id)) {
        $Qproduct = $this->db->prepare('select products_shipping_delay
                                       from :table_products_description
                                       where products_id = :products_id
                                       and language_id = :language_id
                                     ');
        $Qproduct->bindInt(':products_id', $id);
        $Qproduct->bindInt(':language_id', $language_id);
  
        $Qproduct->execute();
  
        return $Qproduct->value('products_shipping_delay');
      } else {
        return false;
      }
    }

    /**
     * Description summary
     *
     * @param string|int|null $product_id , $language_id
     * @return string|bool $product['products_description'], description name
     *
     */
    public function getProductsDescriptionSummary(string|int|null $product_id, int $language_id)
    {
      if (!is_null($product_id)) {
        if (!$language_id) $language_id = $this->lang->getId();
  
        $Qproduct = $this->db->prepare('select products_description_summary
                                       from :table_products_description
                                       where products_id = :products_id
                                       and language_id = :language_id
                                    ');
        $Qproduct->bindInt(':products_id', $product_id);
        $Qproduct->bindInt(':language_id', $language_id);
  
        $Qproduct->execute();
  
        return $Qproduct->value('products_description_summary');
      }
    }

    /**
     * GetProductsImage : image inside the catalog
     *
     * @param string $product_id :id of the product
     * @return string  image of the product
     *
     */

    public function getProductsImage($product_id = ''): string
    {
      $Qproduct = Registry::get('Db')->get('products', 'products_image', ['products_id' => (int)$product_id]);

      return $Qproduct->value('products_image');
    }

    /**
     * Directory of image
     *
     * @param string $filename : name of the file
     * @return string $directory_array, the directories name in css directory
     *
     */

    public function getDirectoryProducts(): array
    {
      $template_directory = CLICSHOPPING::getConfig('dir_root', 'Shop') . 'sources/images/products/';

      $weeds = ['.', '..', '_notes'];

      $directories = array_diff(scandir($template_directory), $weeds);
      $directory_array = [];

      $directory_array[0] = [
        'id' => '',
        'text' => CLICSHOPPING::getDef('select_datas')
      ];

      foreach ($directories as $directory) {
        if (is_dir($template_directory . $directory)) {
          $directory_array[] = ['id' => $directory,
            'text' => $directory
          ];
        }
      }

      return $directory_array;
    }

    /**
     * Name of the products
     *
     * @param $product_id , int $language_id
     * @return string $product['products_name'], name of the product
     *
     */
    public function getProductsName($product_id = '', int $language_id = 0): string
    {
      if ($language_id == 0) $language_id = $this->lang->getId();
      $Qproduct = Registry::get('Db')->get('products_description', 'products_name', ['products_id' => (int)$product_id,
          'language_id' => (int)$language_id]
      );

      return $Qproduct->value('products_name');
    }

    /**
     * Description Name
     *
     * @param string|int|null $product_id , $language_id
     * @return string|bool $product['products_description'], description name
     *
     */
    public function getProductsDescription(string|int|null $product_id, int $language_id): string|bool
    {
      if (!is_null($product_id)) {
      
      if ($language_id == 0) $language_id = $this->lang->getId();
      
      $sql_array = [
        'products_id' => (int)$product_id,
        'language_id' => (int)$language_id
        ];
      
      $Qproduct = Registry::get('Db')->get('products_description', 'products_description', $sql_array);

      return $Qproduct->value('products_description');
      } else {
        return false;
      }
    }

    /**
     * Supplier DropDown
     *
     * @param string
     * @return string $supplier, elements of supplier in dropdown
     *
     */

    public function supplierDropDown(): array
    {
      $supplier = array(array('id' => '',
        'text' => CLICSHOPPING::getDef('text_none'))
      );

      $Qsupplier = $this->db->prepare('select suppliers_id,
                                              suppliers_name
                                       from :table_suppliers
                                       order by suppliers_name
                                      ');
      $Qsupplier->execute();

      while ($Qsupplier->fetch() !== false) {
        $supplier[] = ['id' => $Qsupplier->valueInt('suppliers_id'),
          'text' => $Qsupplier->value('suppliers_name')
        ];
      }

      return $supplier;
    }

    /**
     * product : remove product
     *
     * @param string $product_id
     * @return
     *
     */
    public function removeProduct(int $product_id)
    {
      $Qimage = $this->db->prepare('select products_image,
                                          products_image_zoom,
                                          products_image_medium,
                                          products_image_small,
                                          products_model,
                                          products_ean
                                   from :table_products
                                   where products_id = :products_id
                                  ');
      $Qimage->bindInt(':products_id', (int)$product_id);
      $Qimage->execute();

// Controle si l'image est utilisee le visuel d'un autre produit
      $QduplicateImage = $this->db->prepare('select count(*) as total
                                           from :table_products
                                           where products_image = :products_image
                                           or products_image_zoom = :products_image_zoom
                                           or products_image_medium = :products_image_medium
                                           or products_image_small = :products_image_small
                                          ');
      $QduplicateImage->bindValue(':products_image', $Qimage->value('products_image'));
      $QduplicateImage->bindValue(':products_image_zoom', $Qimage->value('products_image_zoom'));
      $QduplicateImage->bindValue(':products_image_medium', $Qimage->value('products_image_medium'));
      $QduplicateImage->bindValue(':products_image_small', $Qimage->value('products_image_small'));

      $QduplicateImage->execute();

      $duplicate_image = $QduplicateImage->fetch();

// Controle si l'image est utilisee sur une categorie
      $QduplicateImageCategories = $this->db->prepare('select count(*) as total
                                                       from :table_categories
                                                       where categories_image = :products_image
                                                       or categories_image = :products_image_zoom
                                                       or categories_image = :products_image_medium
                                                       or categories_image = :products_image_small
                                                      ');
      $QduplicateImageCategories->bindValue(':products_image', $Qimage->value('products_image'));
      $QduplicateImageCategories->bindValue(':products_image_zoom', $Qimage->value('products_image_zoom'));
      $QduplicateImageCategories->bindValue(':products_image_medium', $Qimage->value('products_image_medium'));
      $QduplicateImageCategories->bindValue(':products_image_small', $Qimage->value('products_image_small'));

      $QduplicateImageCategories->execute();

      $duplicate_image_categories = $QduplicateImageCategories->fetch();

// Controle si l'image est utiliee sur les descriptions d'un produit
      $QduplicateImageProductDescription = $this->db->prepare('select count(*) as total
                                                               from :table_products_description
                                                               where products_description like :products_description
                                                               or products_description like :products_description1
                                                               or products_description like :products_description2
                                                               or products_description like :products_description3
                                                              ');
      $QduplicateImageProductDescription->bindValue(':products_description', '%' . $Qimage->value('products_image') . '%');
      $QduplicateImageProductDescription->bindValue(':products_description1', '%' . $Qimage->value('products_image_zoom') . '%');
      $QduplicateImageProductDescription->bindValue(':products_description2', '%' . $Qimage->value('products_image_medium') . '%');
      $QduplicateImageProductDescription->bindValue(':products_description3', '%' . $Qimage->value('products_image_small') . '%');

      $QduplicateImageProductDescription->execute();

      $duplicate_image_product_description = $QduplicateImageProductDescription->fetch();


// Controle si l'image est utilisee sur une banniere
      $QduplicateImageBanners = $this->db->prepare('select count(*) as total
                                                     from :table_banners
                                                     where banners_image = :products_image
                                                     or banners_image = :products_image_zoom
                                                     or banners_image = :products_image_medium
                                                     or banners_image = :products_image_small
                                                    ');

      $QduplicateImageBanners->bindValue(':products_image', $Qimage->value('products_image'));
      $QduplicateImageBanners->bindValue(':products_image_zoom', $Qimage->value('products_image_zoom'));
      $QduplicateImageBanners->bindValue(':products_image_medium', $Qimage->value('products_image_medium'));
      $QduplicateImageBanners->bindValue(':products_image_small', $Qimage->value('products_image_small'));

      $QduplicateImageBanners->execute();

      $duplicate_image_banners = $QduplicateImageBanners->fetch();


// Controle si l'image est utilisee sur les fabricants
      $QduplicateImageManufacturers = $this->db->prepare('select count(*) as total
                                                         from :table_manufacturers
                                                         where manufacturers_image = :products_image
                                                         or manufacturers_image = :products_image_zoom
                                                         or manufacturers_image = :products_image_medium
                                                         or manufacturers_image = :products_image_small
                                                        ');
      $QduplicateImageManufacturers->bindValue(':products_image', $Qimage->value('products_image'));
      $QduplicateImageManufacturers->bindValue(':products_image_zoom', $Qimage->value('products_image_zoom'));
      $QduplicateImageManufacturers->bindValue(':products_image_medium', $Qimage->value('products_image_medium'));
      $QduplicateImageManufacturers->bindValue(':products_image_small', $Qimage->value('products_image_small'));

      $QduplicateImageManufacturers->execute();

      $duplicate_image_manufacturers = $QduplicateImageManufacturers->fetch();


// Controle si l'image est utilisee sur les fabricants
      $QduplicateImageSuppliers = $this->db->prepare('select count(*) as total
                                                     from :table_suppliers
                                                     where suppliers_image  = :products_image
                                                     or suppliers_image  = :products_image_zoom
                                                     or suppliers_image  = :products_image_medium
                                                     or suppliers_image  = :products_image_small
                                                    ');
      $QduplicateImageSuppliers->bindValue(':products_image', $Qimage->value('products_image'));
      $QduplicateImageSuppliers->bindValue(':products_image_zoom', $Qimage->value('products_image_zoom'));
      $QduplicateImageSuppliers->bindValue(':products_image_medium', $Qimage->value('products_image_medium'));
      $QduplicateImageSuppliers->bindValue(':products_image_small', $Qimage->value('products_image_small'));

      $QduplicateImageSuppliers->execute();

      $duplicate_image_suppliers = $QduplicateImageSuppliers->fetch();

      if (($duplicate_image['total'] < 2) &&
        ($duplicate_image_categories['total'] == 0) &&
        ($duplicate_image_product_description['total'] == 0) &&
        ($duplicate_image_banners['total'] == 0) &&
        ($duplicate_image_manufacturers['total'] == 0) &&
        ($duplicate_image_suppliers['total'] == 0)) {
// delete product image and product image zoom
        if (file_exists($this->template->getDirectoryPathTemplateShopImages() . $Qimage->value('products_image'))) {
          unlink($this->template->getDirectoryPathTemplateShopImages() . $Qimage->value('products_image'));
        }

        if (file_exists($this->template->getDirectoryPathTemplateShopImages() . $Qimage->value('products_image_zoom'))) {
          unlink($this->template->getDirectoryPathTemplateShopImages() . $Qimage->value('products_image_zoom'));
        }

        if (file_exists($this->template->getDirectoryPathTemplateShopImages() . $Qimage->value('products_image_medium'))) {
          unlink($this->template->getDirectoryPathTemplateShopImages() . $Qimage->value('products_image_medium'));
        }

        if (file_exists($this->template->getDirectoryPathTemplateShopImages() . $Qimage->value('products_image_small'))) {
          unlink($this->template->getDirectoryPathTemplateShopImages() . $Qimage->value('products_image_small'));
        }
      }

      $Qimages = $this->db->get('products_images', 'image', ['products_id' => (int)$product_id]);


      if ($Qimages->fetch() !== false) {
        do {
          $Qduplicate = $this->db->get('products_images', 'id', [
            'image' => $Qimages->value('image'),
            'products_id' => [
              'op' => '!=',
              'val' => (int)$product_id
            ]
          ],
            null,
            1
          );

          if ($Qduplicate->fetch() === false) {
            if (file_exists($this->template->getDirectoryPathTemplateShopImages() . $Qimages->value('image'))) {
              unlink($this->template->getDirectoryPathTemplateShopImages() . $Qimages->value('image'));
            }
          }
        } while ($Qimages->fetch());

        $this->db->delete('products_images', ['products_id' => (int)$product_id]);
      }

      $this->db->delete('products', ['products_id' => (int)$product_id]);
      $this->db->delete('products_description', ['products_id' => (int)$product_id]);
      $this->db->delete('products_to_categories', ['products_id' => (int)$product_id]);

      $this->db->delete('products_attributes', ['products_id' => (int)$product_id]);
      $this->db->delete('products_notifications', ['products_id' => (int)$product_id]);

      $Qdelete = $this->db->prepare('delete
                                     from :table_customers_basket
                                     where products_id = :products_id
                                     or products_id like :products_id_att
                                  ');
      $Qdelete->bindInt(':products_id', (int)$product_id);
      $Qdelete->bindInt(':products_id_att', (int)$product_id . '{%');
      $Qdelete->execute();

      $Qdel = $this->db->prepare('delete
                                  from :table_customers_basket_attributes
                                  where products_id = :products_id
                                  or products_id like :products_id_att
                                 ');
      $Qdel->bindInt(':products_id', (int)$product_id);
      $Qdel->bindInt(':products_id_att', (int)$product_id . '{%');
      $Qdel->execute();

// for hooks
      $_POST['remove_id'] = $product_id;

      $this->hooks->call('Products', 'RemoveProduct');

      Cache::clear('categories');
      Cache::clear('products-also_purchased');
      Cache::clear('upcoming');
    }

    /**
     * url of the product
     *
     * @param int|string $product_id , $language_id
     * @param int $language_id
     * @return string $Qproduct->value('products_url'), url of the product
     *
     */
    public function getProductsUrl(int|string $product_id, int $language_id): string|bool
    {
      if (((is_null($language_id)) || $language_id == 0) && !is_null($product_id)) {
        $language_id = $this->lang->getId();

        $Qproduct = Registry::get('Db')->get('products_description', 'products_url', ['products_id' => (int)$product_id, 'language_id' => (int)$language_id]);

        return $Qproduct->value('products_url');
      } else {
        return false;
      }
    }

    /**
     * Return the manufacturers URL in the needed language
     *
     * @param string|int|null $manufacturer_id , $language_id
     * @return string $Qmanufacturer->value('manufacturers_url'), url of manufacturers
     *
     */
    public function getManufacturerUrl(string|int|null $manufacturer_id, int $language_id): string|bool
    {
      if (!is_null($manufacturer_id)) {
        if ($language_id == 0) $language_id = $this->lang->getId();
        $Qmanufacturer = Registry::get('Db')->get('manufacturers_info', 'manufacturers_url', ['manufacturers_id' => (int)$manufacturer_id, 'languages_id' => (int)$language_id]);
  
        return $Qmanufacturer->value('manufacturers_url');
      } else {
        return false;
      }
    }

    /**
    * getCountProductsToCategory count the products into category
    * @param $id - products id of the products
    * @param $categories_id - category id
    *
    */
    public function getCountProductsToCategory(int $id, int $categories_id): int
    {
      $Qcheck = $this->db->prepare('select count(*) as total
                                           from :table_products_to_categories
                                           where products_id = :products_id
                                           and categories_id = :categories_id
                                          ');
      $Qcheck->bindInt(':products_id', $id);
      $Qcheck->bindInt(':categories_id', $categories_id);
      $Qcheck->execute();

      return $Qcheck->valueInt('total');
    }

    /**
     * Prepare to clone a products in other category for products page
     * @param $id - products id of the products
     * @param $categories_id - category id
     */
    private function prepageCloneProducts(int $id, int $categories_id)
    {
      $new_category = $categories_id;

      if (is_array($new_category) && isset($new_category)) {
        foreach ($new_category as $value_id) {
          $this->cloneProductsInOtherCategory($id, $value_id);
        }
      }
    }

    /**
     * cloneProductsInOtherCategory
     * @param $id - products id of the products
     * @param $categories_id - category id
     *
     */
    public function cloneProductsInOtherCategory(int $id, int $categories_id)
    {
      $multi_clone_categories_id_to = [];

      $multi_clone_categories_id_to[] = $categories_id;

      $Qproducts = $this->db->prepare('select *
                                      from :table_products
                                      where products_id = :products_id
                                     ');
      $Qproducts->bindInt(':products_id', $id);

      $Qproducts->execute();

      for ($i = 0, $iMax = \count($multi_clone_categories_id_to); $i < $iMax; $i++) {
        $clone_categories_id_to = $multi_clone_categories_id_to[$i];

        $sql_array = [
          'parent_id' => (int)$Qproducts->valueInt('parent_id'),
          'has_children' => (int)$Qproducts->valueInt('has_children'),
          'products_quantity' => (int)$Qproducts->valueInt('products_quantity'),
          'products_model' => $Qproducts->value('products_model'),
          'products_ean' => $Qproducts->value('products_ean'),
          'products_sku' => $Qproducts->value('products_sku'),
          'products_image' => $Qproducts->value('products_image'),
          'products_image_zoom' => $Qproducts->value('products_image_zoom'),
          'products_price' => (float)$Qproducts->value('products_price'),
          'products_date_added' => 'now()',
          'products_date_available' => (empty($Qproducts->value('products_date_available')) ? "null" : "'" . $Qproducts->value('products_date_available') . "'"),
          'products_weight' => (float)$Qproducts->value('products_weight'),
          'products_price_kilo' => (float)$Qproducts->value('products_price_kilo'),
          'products_status' => $Qproducts->value('products_status'),
          'products_tax_class_id' => (int)$Qproducts->valueInt('products_tax_class_id'),
          'products_view' => (int)$Qproducts->valueInt('products_view'),
          'orders_view' => (int)$Qproducts->valueInt('orders_view'),
          'products_min_qty_order' => (int)$Qproducts->valueInt('products_min_qty_order'),
          'admin_user_name' => AdministratorAdmin::getUserAdmin(),
          'products_only_online' => (int)$Qproducts->valueInt('products_only_online'),
          'products_image_medium' => $Qproducts->value('products_image_medium'),
          'products_cost' => (float)$Qproducts->value('products_cost'),
          'products_handling' => (int)$Qproducts->value('products_handling'),
          'products_packaging' => (int)$Qproducts->valueInt('products_packaging'),
          'products_sort_order' => (int)$Qproducts->valueInt('products_sort_order'),
          'products_quantity_alert' => (int)$Qproducts->valueInt('products_quantity_alert'),
          'products_image_small' => $Qproducts->value('products_image_small'),
        ];

// copy du produit
        $this->db->save('products', $sql_array);
        $dup_products_id = $this->db->lastInsertId();

// ---------------------
// gallery
// ----------------------
        $QproductImage = $this->db->prepare('select *
                                              from :table_products_images
                                              where products_id = :products_id
                                            ');
        $QproductImage->bindInt(':products_id', $id);

        $QproductImage->execute();

        while ($QproductImage->fetch()) {
          $sql_array = ['products_id' => (int)$dup_products_id,
            'image' => $QproductImage->value('image'),
            'htmlcontent' => $QproductImage->value('htmlcontent'),
            'sort_order' => $QproductImage->valueInt('sort_order')
          ];

          $this->db->save('products_images', $sql_array);
        }

// ---------------------
// Description clonage
// ----------------------
        $Qdescription = $this->db->prepare('select language_id,
                                                    products_name,
                                                    products_description,
                                                    products_description_summary,
                                                    products_head_title_tag,
                                                    products_head_desc_tag,
                                                    products_head_keywords_tag,
                                                    products_url,
                                                    products_head_tag,
                                                    products_shipping_delay
                                             from :table_products_description
                                             where products_id = :products_id
                                            ');
        $Qdescription->bindInt(':products_id', $id);

        $Qdescription->execute();

        while ($Qdescription->fetch()) {
          $sql_array = [
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
          ];

          $this->db->save('products_description', $sql_array);
        }

// ---------------------
// insertion table
// ----------------------
        $sql_array = [
          'products_id' => (int)$dup_products_id,
          'categories_id' => (int)$clone_categories_id_to
        ];

        $this->db->save('products_to_categories', $sql_array);

        $clone_products_id = $dup_products_id;
        $_POST['clone_products_id'] = $clone_products_id; // for hooks

// ---------------------
// groupe client clonage
// ----------------------
        $QcustomersGroup = $this->db->prepare('select distinct customers_group_id,
                                                               customers_group_name,
                                                               customers_group_discount
                                               from :table_customers_groups
                                               where customers_group_id >  0
                                               order by customers_group_id
                                              ');
        $QcustomersGroup->execute();

        while ($QcustomersGroup->fetch()) {
          $Qattributes = $this->db->prepare('select g.customers_group_id,
                                                     g.customers_group_price,
                                                     p.products_price
                                              from :table_products_groups g,
                                                   :table_products p
                                              where p.products_id = :products_id
                                              and p.products_id =g.products_id
                                              and g.customers_group_id = :customers_group_id
                                              order by g.customers_group_id
                                            ');
          $Qattributes->bindInt(':products_id', (int)$clone_products_id);
          $Qattributes->bindInt(':customers_group_id', (int)$QcustomersGroup->valueInt('customers_group_id'));

          $Qattributes->execute();

          if ($Qattributes->rowCount() > 0) {
// Definir la position 0 ou 1 pour --> Affichage Prix public + Affichage Produit + Autorisation Commande
// L'Affichage des produits, autorisation de commander et affichage des prix mis par defaut en valeur 1 dans la cas de la B2B desactive.
            if (MODE_B2B_B2C == 'true') {
              if (HTML::sanitize($_POST['price_group_view' . $QcustomersGroup->valueInt('customers_group_id')]) == 1) {
                $price_group_view = 1;
              } else {
                $price_group_view = 0;
              }

              if (HTML::sanitize($_POST['products_group_view' . $QcustomersGroup->valueInt('customers_group_id')]) == 1) {
                $products_group_view = 1;
              } else {
                $products_group_view = 0;
              }

              if (HTML::sanitize($_POST['orders_group_view' . $QcustomersGroup->valueInt('customers_group_id')]) == 1) {
                $orders_group_view = 1;
              } else {
                $orders_group_view = 0;
              }

              $products_quantity_unit_id_group = HTML::sanitize($_POST['products_quantity_unit_id_group' . $QcustomersGroup->valueInt('customers_group_id')]);
              $products_model_group = HTML::sanitize($_POST['products_model_group' . $QcustomersGroup->valueInt('customers_group_id')]);
              $products_quantity_fixed_group = HTML::sanitize($_POST['products_quantity_fixed_group' . $QcustomersGroup->valueInt('customers_group_id')]);
            } else {
              $price_group_view = 1;
              $products_group_view = 1;
              $orders_group_view = 1;
              $products_quantity_unit_id_group = 0;
              $products_model_group = '';
              $products_quantity_fixed_group = 1;
            } //end MODE_B2B_B2C

            $Qupdate = $this->db->prepare('update :table_products_groups
                                            set price_group_view = :price_group_view,
                                                products_group_view = :products_group_view,
                                                orders_group_view = :orders_group_view,
                                                products_quantity_unit_id_group = :products_quantity_unit_id_group,
                                                products_model_group = :products_model_group,
                                                products_quantity_fixed_group = :products_quantity_fixed_group
                                            where customers_group_id = :customers_group_id
                                            and products_id = :products_id
                                            ');
            $Qupdate->bindInt(':price_group_view', $price_group_view);
            $Qupdate->bindInt(':products_group_view', $products_group_view);
            $Qupdate->bindInt(':orders_group_view', $orders_group_view);
            $Qupdate->bindInt(':products_quantity_unit_id_group', $products_quantity_unit_id_group);
            $Qupdate->bindValue(':products_model_group', $products_model_group);
            $Qupdate->bindValue(':products_quantity_fixed_group', $products_quantity_fixed_group);
            $Qupdate->bindInt(':customers_group_id', (int)$Qattributes->valueInt('customers_group_id'));
            $Qupdate->bindInt(':products_id', (int)$clone_products_id);

            $Qupdate->execute();

// Prix TTC B2B ----------
            if (($_POST['price' . $QcustomersGroup->valueInt('customers_group_id')] <> $Qattributes->valueDecimal('customers_group_price')) && ($Qattributes->valueInt('customers_group_id') == $QcustomersGroup->valueInt('customers_group_id'))) {

              $Qupdate = $this->db->prepare('update :table_products_groups
                                            set customers_group_price = :customers_group_price,
                                                products_price = :products_price
                                            where customers_group_id = :customers_group_id
                                            and products_id = :products_id
                                          ');
              $Qupdate->bindInt(':customers_group_price', $_POST['price' . $QcustomersGroup->valueInt('customers_group_id')]);
              $Qupdate->bindInt(':products_price', $_POST['products_price']);
              $Qupdate->bindInt(':customers_group_id', (int)$Qattributes->valueInt('customers_group_id'));
              $Qupdate->bindInt(':products_id', (int)$clone_products_id);

              $Qupdate->execute();

            } elseif (($_POST['price' . $QcustomersGroup->valueInt('customers_group_id')] == $Qattributes->valueInt('customers_group_id'))) {
//              $attributes = $Qattributes->fetch();
            }
// Prix + Afficher Prix public + Afficher Produit + Autoriser Commande
          } elseif ($_POST['price' . $QcustomersGroup->valueInt('customers_group_id')] != '') {
            $sql_array = [
              'products_id' => (int)$clone_products_id,
              'products_price' => (float)$_POST['products_price'],
              'customers_group_id' => (int)$QcustomersGroup->valueInt('customers_group_id'),
              'customers_group_price' => (float)$_POST['price' . $QcustomersGroup->valueInt('customers_group_id')],
              'price_group_view' => (int)$_POST['price_group_view' . $QcustomersGroup->valueInt('customers_group_id')],
              'products_group_view' => (int)$_POST['products_group_view' . $QcustomersGroup->valueInt('customers_group_id')],
              'orders_group_view' => (int)$_POST['orders_group_view' . $QcustomersGroup->valueInt('customers_group_id')],
              'products_quantity_unit_id_group' => (int)$_POST['products_quantity_unit_id_group' . $QcustomersGroup->valueInt('customers_group_id')],
              'products_model_group' => $_POST['products_model_group' . $QcustomersGroup->valueInt('customers_group_id')],
              'products_quantity_fixed_group' => (int)$_POST['products_quantity_fixed_group' . $QcustomersGroup->valueInt('customers_group_id')],
            ];

            $this->db->save('products_groups', $sql_array);
          }
        } // end while

        $this->hooks->call('Products', 'CloneProducts');
      } //End for
    }

    /**
     * Search products
     * @param, $keywords, keyword to search
     * @return $Qproducts, result of search
     *
     */

    public function getSearch($keywords = null, $current_category_id = 0)
    {
      if (isset($keywords) && !empty($keywords)) {
        $keywords = HTML::sanitize($keywords);

        $Qproducts = $this->db->prepare('select SQL_CALC_FOUND_ROWS  p.products_id,
                                                                     pd.products_name,
                                                                     p.products_model,
                                                                     p.products_ean,
                                                                     p.products_sku,
                                                                     p.products_quantity,
                                                                     p.products_image,
                                                                     p.products_price,
                                                                     p.products_date_added,
                                                                     p.products_last_modified,
                                                                     p.products_date_available,
                                                                     p.products_status,
                                                                     p.admin_user_name,
                                                                     p.products_quantity_unit_id,
                                                                     p2c.categories_id,
                                                                     p.products_sort_order,
                                                                     p.products_download_filename
                                         from :table_products p,
                                              :table_products_description pd,
                                              :table_products_to_categories p2c
                                         where p.products_id = pd.products_id
                                         and pd.language_id = :language_id
                                         and p.products_id = p2c.products_id
                                         and p.products_archive = 0
                                         and (pd.products_name like :search
                                              or  p.products_model like :search
                                              or p.products_ean like :search
                                             )
                                         order by pd.products_name
                                      ');

        $Qproducts->bindInt(':language_id', $this->lang->getId());
        $Qproducts->bindValue(':search', '%' . $keywords . '%');

        $Qproducts->execute();
      } else {
        $Qproducts = $this->db->prepare('select SQL_CALC_FOUND_ROWS p.products_id,
                                                                     pd.products_name,
                                                                     p.products_model,
                                                                     p.products_ean,
                                                                     p.products_sku,
                                                                     p.products_quantity,
                                                                     p.products_image,
                                                                     p.products_price,
                                                                     p.products_date_added,
                                                                     p.products_last_modified,
                                                                     p.products_date_available,
                                                                     p.products_status,
                                                                     p.admin_user_name,
                                                                     p.products_sort_order,
                                                                     p.products_download_filename,
                                                                     p2c.categories_id
                                           from :table_products p,
                                                :table_products_description pd,
                                                :table_products_to_categories p2c
                                           where p.products_id = pd.products_id
                                           and pd.language_id = :language_id
                                           and p.products_id = p2c.products_id
                                           and p2c.categories_id = :categories_id
                                           and p.products_archive = 0
                                           order by pd.products_name
                                           limit :page_set_offset, :page_set_max_results
                                        ');

        $Qproducts->bindInt(':categories_id', (int)$current_category_id);
        $Qproducts->bindInt(':language_id', $this->lang->getId());
        $Qproducts->setPageSet((int)MAX_DISPLAY_SEARCH_RESULTS_ADMIN);
        $Qproducts->execute();
      }

      return $Qproducts;
    }

    /**
     * save products
     * @param, $id, id of the products, $action, insert or update products
     * @return
     *
     */

    public function save(string|int|null $id, $action)
    {
      $products_date_available = HTML::sanitize($_POST['products_date_available']);
      $products_date_available = (date('Y-m-d') < $products_date_available) ? $products_date_available : 'null';

      if (isset($_POST['products_view']) && HTML::sanitize($_POST['products_view']) == 1) {
        $products_view = 1;
      } else {
        $products_view = 0;
      }

      if (isset($_POST['orders_view']) && HTML::sanitize($_POST['orders_view']) == 1) {
        $orders_view = 1;
      } else {
        $orders_view = 0;
      }

// display price / kg
      if (isset($_POST['products_price_kilo']) && HTML::sanitize($_POST['products_price_kilo']) == 1) {
        $products_price_kilo = 1;
      } else {
        $products_price_kilo = 0;
      }

// display products online
      if (isset($_POST['products_only_online']) && HTML::sanitize($_POST['products_only_online']) == 1) {
        $products_only_online = 1;
      } else {
        $products_only_online = 0;
      }

// display products store (physical)
      if (isset($_POST['products_only_shop']) && HTML::sanitize($_POST['products_only_shop']) == 1) {
        $products_only_shop = 1;
      } else {
        $products_only_shop = 0;
      }

// display products file public or private
      if (isset($_POST['products_download_public']) && HTML::sanitize($_POST['products_download_public']) == 1) {
        $products_download_public = 1;
      } else {
        $products_download_public = 0;
      }

// manual price B2B
      if (isset($_POST['products_percentage']) && $_POST['products_percentage'] == 'on') {
        $products_percentage = 0;
      } else {
        $products_percentage = 1;
      }

// Affichage des produits, autorisation de commander et mode B2B en automatique mis par defaut en valeur 1 dans la cas de la B2B desactivee.
      if (MODE_B2B_B2C == 'false') {
        $products_view = 1;
        $orders_view = 1;
        $products_percentage = 1;
      }

      $products_model = $this->getProductModel();

      $products_sku = $this->getProductSKU();
      $products_ean = $this->getProductEAN();

      if (isset($_POST['products_status'])) {
        $products_status = HTML::sanitize($_POST['products_status']);
      } else {
        $products_status = 0;
      }

      $sql_data_array = [
        'products_quantity' => (int)HTML::sanitize($_POST['products_quantity']),
        'products_ean' => HTML::sanitize($products_ean),
        'products_model' => HTML::sanitize($products_model),
        'products_sku' => HTML::sanitize($products_sku),
        'products_price' => (float)HTML::sanitize($_POST['products_price']),
        'products_date_available' => $products_date_available,
        'products_weight' => (float)HTML::sanitize($_POST['products_weight']),
        'products_price_kilo' => HTML::sanitize($products_price_kilo),
        'products_status' => (int)HTML::sanitize($products_status),
        'products_percentage' => (int)$products_percentage,
        'products_view' => (int)$products_view,
        'orders_view' => (int)$orders_view,
        'products_tax_class_id' => (int)HTML::sanitize($_POST['products_tax_class_id']),
        'products_min_qty_order' => (int)$_POST['products_min_qty_order'],
        'admin_user_name' => AdministratorAdmin::getUserAdmin(),
        'products_only_online' => (int)HTML::sanitize($products_only_online),
        'products_cost' => (float)HTML::sanitize($_POST['products_cost']),
        'products_handling' => (float)HTML::sanitize($_POST['products_handling']),
        'products_packaging' => (int)HTML::sanitize($_POST['products_packaging']),
        'products_sort_order' => (int)HTML::sanitize($_POST['products_sort_order']),
        'products_quantity_alert' => (int)HTML::sanitize($_POST['products_quantity_alert']),
        'products_only_shop' => (int)HTML::sanitize($products_only_shop),
        'products_download_public' => (int)HTML::sanitize($products_download_public),
        'products_type' => HTML::sanitize($_POST['products_type'])
      ];

// Download file
      $sql_data_array['products_download_filename'] = $this->saveFileUpload();
// image
      $this->image->getImage();

      $sql_data_array['products_image'] = $this->image->productsImage();
      $sql_data_array['products_image_medium'] = $this->image->productsImageMedium();
      $sql_data_array['products_image_zoom'] = $this->image->productsImageZoom();
      $sql_data_array['products_image_small'] = $this->image->productsSmallImage();
//---------------------------------------------------------------------------------------------
//  Save Data
//---------------------------------------------------------------------------------------------
//update
      if (is_numeric($id) && !is_null($id) && $action == 'Update') {
        $update_sql_data = ['products_last_modified' => 'now()'];
        $sql_data_array = array_merge($sql_data_array, $update_sql_data);

        $this->db->save('products', $sql_data_array, ['products_id' => (int)$id]);
      } else {
//insert
        $insert_sql_data = ['products_date_added' => 'now()'];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $this->db->save('products', $sql_data_array);

        $id = $this->db->lastInsertId();

//for hooks
        $_POST['insertId'] = $id; // take the new id of the product
      }

      $this->image->saveGalleryImage($id);
      $this->saveProductsDescription($id, $action);

      if (isset($_POST['clone_categories_id_to'])) {
        $categories_id = $_POST['clone_categories_id_to'];
        $this->prepageCloneProducts($id, $categories_id);
      }

      $this->hooks->call('Products', 'Save');
    }

    /**
     * Count how many products exist in a category
     * TABLES: products, products_to_products, products
     */
    public function getProductsInCategoryCount(int $products_id, bool $include_deactivated = false): int
    {

      if ($include_deactivated) {
        $Qproducts = $this->products->get([
          'products p',
          'products_to_products p2c'
        ], [
          'count(*) as total'
        ], [
            'p.products_id' => [
              'rel' => 'p2c.products_id'
            ],
            'p2c.products_id' => $products_id
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
            'p2c.products_id' => $products_id
          ]
        );
      }

      $products_count = $Qproducts->valueInt('total');

      $Qchildren = $this->db->prepare->get('products', 'products_id', ['parent_id' => $products_id]);

      while ($Qchildren->fetch() !== false) {
        $products_count += \call_user_func(__METHOD__, $Qchildren->valueInt('products_id'), $include_deactivated);
      }

      return $products_count;
    }

    /**
     * @param int $products_id
     * @return bool
     */
    public function checkProductStatus(?int $products_id) :bool
    {
      $Qstatus = $this->db->prepare('select products_status 
                                    from :table_products 
                                    where products_id = :products_id
                                   ');
      $Qstatus->bindInt(':products_id', $products_id);
      $Qstatus->execute();

      if ($Qstatus->fetch()) {
        if ($Qstatus->valueInt('products_status') == 0) {
          return false;
        } else {
          return true;
        }
      } else {
        return false;
      }
    }
  }