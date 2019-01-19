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

  namespace ClicShopping\Apps\Catalog\Products\Classes\ClicShoppingAdmin;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Upload;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Cache;
  use ClicShopping\OM\HTTP;

  use ClicShopping\Sites\ClicShoppingAdmin\HTMLOverrideAdmin;
  use ClicShopping\Apps\Configuration\Weight\Classes\ClicShoppingAdmin\WeightAdmin;

  use ClicShopping\Apps\Catalog\Products\Classes\ClicShoppingAdmin\ImageResample;
  use ClicShopping\Apps\Configuration\Administrators\Classes\ClicShoppingAdmin\AdministratorAdmin;

  class ProductsAdmin {

    protected $products_quantity_unit_id;
    protected $language_id;
    protected $product_id;
    protected $id;
    protected $action;
    protected $text;
    protected $price;
    protected $db;
    protected $template;
    protected $hooks;
    protected $lang;

    public function __construct()  {
      $this->db = Registry::get('Db');
      $this->template = Registry::get('TemplateAdmin');
      $this->hooks = Registry::get('Hooks');
      $this->lang = Registry::get('Language');
    }

    public function get($id) {
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


    private function saveProductsDescription($id, $action) {
      $languages = $this->lang->getLanguages();

      for ($i=0, $n=count($languages); $i<$n; $i++) {
        $language_id = $languages[$i]['id'];

        $sql_data_array = ['products_name' => HTML::sanitize($_POST['products_name'][$language_id]),
                            'products_description' => $_POST['products_description'][$language_id],
                            'products_head_title_tag' => HTML::sanitize($_POST['products_head_title_tag'][$language_id]),
                            'products_head_desc_tag' => HTML::sanitize($_POST['products_head_desc_tag'][$language_id]),
                            'products_head_keywords_tag' => HTML::sanitize($_POST['products_head_keywords_tag'][$language_id]),
                            'products_url' => HTML::sanitize($_POST['products_url'][$language_id]),
                            'products_head_tag' => HTML::sanitize($_POST['products_head_tag'][$language_id]),
                            'products_shipping_delay' => HTML::sanitize($_POST['products_shipping_delay'][$language_id]),
                            'products_description_summary' => HTML::sanitize($_POST['products_description_summary'][$language_id])
                           ];


        if ( is_numeric($id) && $action == 'Insert') {

          $insert_sql_data = ['products_id' => (int)$id,
                              'language_id' => (int)$language_id
                             ];

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          $this->db->save('products_description', $sql_data_array);

//update products
        } else {

          $this->db->save('products_description', $sql_data_array, ['products_id' => (int)$id,
                                                                    'language_id' => (int)$language_id
                                                                    ]
                         );
        } // end action
      } //end for
    }


    private function getProductModel() {
      if (empty($_POST['products_model'])) {
        $rand = rand();
        $products_model = CONFIGURATION_PREFIX_MODEL . $rand;
      } else {
        $products_model = HTML::sanitize($_POST['products_model']);
      }

      return $products_model;
    }

    private function getProductSKU() {
      if(empty($_POST['products_sku'])) {
        $products_sku = $this->getProductModel();
      } elseif($_POST['products_sku'] != $this->getProductModel()) {
        $products_sku = HTML::sanitize($_POST['products_sku']);
      } else {
        $products_sku = $this->getProductModel();
      }

      return $products_sku;
    }

    private function getProductEAN() {
      if(empty($_POST['products_ean'])) {
        $products_ean = $this->getProductSKU();
      } elseif($_POST['products_ean'] != $this->getProductSKU()) {
        $products_ean = HTML::sanitize($_POST['products_ean']);
      } else {
        $products_ean = $this->getProductSKU();
      }

      return $products_ean;
    }

    private function saveFileUpload() {
      if (!is_null($_POST['products_download_filename'])) {
        $upload_file = new Upload('products_download_filename', $this->template->getPathDownloadShopDirectory(), null, array('zip', 'doc', 'pdf', 'odf', 'xls',  'mp3', 'mp4', 'avi'));

        if ( $upload_file->check() && $upload_file->save() ) {
          $error = false;
        }

        if ( $error === false ) {
          $sql_data_array['products_download_filename'] = $this->template->getPathDownloadShopDirectory() . $upload_file->getFilename();
        } else {
          $sql_data_array['products_download_filename'] = '';
        }

        if ($upload_file->check()) {
          $file = HTML::removeFileAccents($_POST['products_download_filename']);
          $sql_data_array['products_download_filename'] = $file;
        }
      }

      return $sql_data_array['products_download_filename'];
    }

/**
 * generate a radom string
 *@param int length of the random
 *@return $randomString
*/
    public function getGenerateRandomString($length = 10) {
      $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
      $charactersLength = strlen($characters);
      $randomString = '';
      for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
      }
      return $randomString;
    }

/**
 * Normal,medium or big image
 *
 */
    private function getImage() {
        Registry::set('ImageResample', new ImageResample());
        $CLICSHOPPING_ImageResample = Registry::get('ImageResample');

        if (isset($_GET['pID'])) {
          $Qimages = $this->db->prepare('select products_image,
                                                products_image_zoom,
                                                products_image_medium
                                         from :table_products
                                         where products_id = :products_id
                                        ');
          $Qimages->bindInt(':products_id', $_GET['pID']);
          $Qimages->execute();

          $product_update_image = $Qimages->value('products_image');
          $product_update_image_zoom = $Qimages->value('products_image_zoom');
          $product_update_image_medium = $Qimages->value('products_image_medium');
        }

        $rand_image = $this->getGenerateRandomString();

        $root_images_dir = $this->template->getDirectoryPathTemplateShopImages() . 'products/';

        $error = true;

// image resample
        $new_dir_products_image_without_accents = HTML::removeFileAccents($_POST['new_directory_products_image']);
        $new_dir_products_image = strtolower($new_dir_products_image_without_accents);

        if(empty($new_dir_products_image)) {
          $dir_products_image = 'products/';
        } else {
          $dir_products_image = 'products/' . $new_dir_products_image . '/';
        }

// create directory for image resample
        if (!empty($new_dir_products_image) && !is_dir($new_dir_products_image)) {
// depend server configuration
          mkdir($root_images_dir . $new_dir_products_image, 0755, true);
          chmod($root_images_dir . $new_dir_products_image, 0755);
        }


// load originale image
        $image = new Upload('products_image_resize', $this->template->getDirectoryPathTemplateShopImages() . $dir_products_image, null, ['gif', 'jpg', 'png', 'jpeg']);
// When the image is updated

        if ($image->check() && $image->save()) {
          $error = false;
        }

        if ($error === false) {
          $sql_data_array['image'] = $dir_products_image . $image->getFilename();
        } else {
          $sql_data_array['image'] = '';
        }

        if ($image->check()) {
          $image_name = $image->getFilename();
          $CLICSHOPPING_ImageResample->load($this->template->getDirectoryPathTemplateShopImages() . $dir_products_image . $image_name);

// Zoom image
          $CLICSHOPPING_ImageResample->resizeToWidth(BIG_IMAGE_WIDTH);

          if (empty(BIG_IMAGE_WIDTH)) {
            $big_image_width = 'big';
          } else {
            $big_image_width = BIG_IMAGE_WIDTH;
          }

          $big_image_width = str_replace(' ', '', $big_image_width);
          $big_image_resized = $dir_products_image . $big_image_width . '_' . $rand_image . '_' . $image_name;

          $CLICSHOPPING_ImageResample->save($this->template->getDirectoryPathTemplateShopImages() . $big_image_resized);

// medium image
          $CLICSHOPPING_ImageResample->resizeToWidth(MEDIUM_IMAGE_WIDTH);

          if (empty(MEDIUM_IMAGE_WIDTH)) {
            $medium_image_width = 'medium';
          } else {
            $medium_image_width = MEDIUM_IMAGE_WIDTH;
          }

          $medium_image_width = str_replace(' ', '', $medium_image_width);
          $medium_image_resized = $dir_products_image . $medium_image_width . '_' . $rand_image . '_' . $image_name;
          $CLICSHOPPING_ImageResample->save($this->template->getDirectoryPathTemplateShopImages() . $medium_image_resized);

// small image
          $CLICSHOPPING_ImageResample->resizeToWidth((int)SMALL_IMAGE_WIDTH);

          if (empty((int)SMALL_IMAGE_WIDTH)) {
            $small_image_width = 'small';
          } else {
            $small_image_width = (int)SMALL_IMAGE_WIDTH;
          }

          $small_image_width = str_replace(' ', '', $small_image_width);
          $small_image_resized = $dir_products_image . $small_image_width . '_' . $rand_image . '_' . $image_name;

          $CLICSHOPPING_ImageResample->save($this->template->getDirectoryPathTemplateShopImages() . $small_image_resized);

// delete the orginal files
          if (file_exists($this->template->getDirectoryPathTemplateShopImages() . $dir_products_image . $image_name)) {
            @unlink($this->template->getDirectoryPathTemplateShopImages() . $dir_products_image . $image_name);
          }
        }

// Ajoute ou efface l'image dans la base de donnees
        if ($_POST['delete_image'] == 'yes') {
          $this->products_image = $sql_data_array['products_image'] = null;
          $this->products_image_zoom = $sql_data_array['products_image_zoom'] = null;
          $this->products_image_medium = $sql_data_array['products_image_medium'] = null;
        } else {

          if ((isset($_POST['products_image']) && !is_null($_POST['products_image'])) || $small_image_resized != '') {

// Insertion images des produits via l'editeur FCKeditor (fonctionne sur les nouveaux produits et editions produits)
            $products_image_name = HTML::sanitize($_POST['products_image']);
            $products_image_name = htmlspecialchars($products_image_name);
            $products_image_name = str_replace($this->template->getDirectoryShopTemplateImages(), '', $products_image_name);
            $products_image_name_end = strstr($products_image_name, '&quot;');
            $products_image_name = str_replace($products_image_name_end, '', $products_image_name);
            $products_image_name = str_replace($this->template->getDirectoryShopSources(), '', $products_image_name);

// small image
            if (!empty($small_image_resized)) {
              $this->products_image = $small_image_resized;
            } else {
              $this->products_image = $products_image_name;
            }
          } else {
            $this->products_image = $product_update_image;
          }

// big image
          if ((isset($_POST['products_image_zoom'])) || $big_image_resized != '') {
            $products_image_zoom_name = HTML::sanitize($_POST['products_image_zoom']);
            $products_image_zoom_name = htmlspecialchars($products_image_zoom_name);
            $products_image_zoom_name = str_replace($this->template->getDirectoryShopTemplateImages(), '', $products_image_zoom_name);
            $products_image_zoom_name_end = strstr($products_image_zoom_name, '&quot;');
            $products_image_zoom_name = str_replace($products_image_zoom_name_end, '', $products_image_zoom_name);
            $products_image_zoom_name = str_replace($this->template->getDirectoryShopSources(), '', $products_image_zoom_name);

            if (!empty($big_image_resized)) {
              $this->products_image_zoom = $big_image_resized;
            } else {
              $this->products_image_zoom = $products_image_zoom_name;
            }
          } else {
            $this->products_image_zoom = $product_update_image_zoom;
          }

// medium image
          if ((isset($_POST['products_image_medium']) && !is_null($_POST['products_image_medium'])) || $medium_image_resized != '') {
            $products_image_medium_name = HTML::sanitize($_POST['products_image_medium']);
            $products_image_medium_name = htmlspecialchars($products_image_medium_name);
            $products_image_medium_name = str_replace($this->template->getDirectoryShopTemplateImages(), '', $products_image_medium_name);
            $products_image_medium_name_end = strstr($products_image_medium_name, '&quot;');
            $products_image_medium_name = str_replace($products_image_medium_name_end, '', $products_image_medium_name);
            $products_image_medium_name = str_replace($this->template->getDirectoryShopSources(), '', $products_image_medium_name);

            if (!empty($medium_image_resized)) {
              $this->products_image_medium = $medium_image_resized;
            } else {
              $this->products_image_medium = $products_image_medium_name;
            }
          } else {
            $this->products_image_medium = $product_update_image_medium;
          }
        }
    }

/**
 * Save gallery image
 * @param $id int products_id
 */
    public function saveGalleryImage($id) {
      $root_images_dir = $this->template->getDirectoryPathTemplateShopImages() . 'products/';

      $error = true;

// gallery
      $new_dir_without_accents = HTML::removeFileAccents($_POST['new_directory']);
      $new_dir = strtolower($new_dir_without_accents);
      $dir = 'products/' . (!empty($new_dir) ? $new_dir : $_POST['directory']);

      if (!empty($new_dir) && !is_dir($new_dir)) {
// depend server configuration
        mkdir($root_images_dir  . $new_dir, 0755, true);
        chmod($root_images_dir  . $new_dir, 0755);
        $separator = '/';
      }

      if (!is_null($_POST['directory'])) {
        $separator = '/';
      }

      $pi_sort_order = 0;
      $piArray = array(0);

      foreach ($_FILES as $key => $value) {
// Update existing large product images

        if (preg_match('/^products_image_large_([0-9]+)$/', $key, $matches)) {
          $pi_sort_order++;

          $sql_data_array = ['htmlcontent' => $_POST['products_image_htmlcontent_' . $matches[1]],
                             'sort_order' => (int)$pi_sort_order
                            ];

          $image = new Upload($key, $this->template->getDirectoryPathTemplateShopImages() . $dir, null, array('gif', 'jpg', 'png'));

          if ( $image->check() && $image->save() ) {
            $error = false;
          }

          if ( $error === false ) {
            $sql_data_array['image'] = $dir . $separator . $image->getFilename();
          }

          $this->db->save('products_images', $sql_data_array, ['products_id' => (int)$id,
                                                               'id' => (int)$matches[1]
                                                              ]
                          );

          $piArray[] = (int)$matches[1];

        } elseif (preg_match('/^products_image_large_new_([0-9]+)$/', $key, $matches)) {
  // Insert new large product images
          $sql_data_array = ['products_id' => (int)$id,
                             'htmlcontent' => $_POST['products_image_htmlcontent_new_' . $matches[1]]
                            ];

          $image = new Upload($key, $this->template->getDirectoryPathTemplateShopImages()  . $dir, null, ['gif', 'jpg', 'png']);

          if ( $image->check() && $image->save() ) {
            $error = false;
          }

          $pi_sort_order++;

          if ( $error === false ) {
            $sql_data_array['image'] = $dir . $separator . $image->getFilename();
          }

          $sql_data_array['sort_order'] = (int)$pi_sort_order;
          $this->db->save('products_images', $sql_data_array);
          $piArray[] = $this->db->lastInsertId();
        } // end preg_match
      } // end foreach


//=======================================================================================
// bug supprimer products_image automatiquement
//
//========================================================================================

      $Qimages = $this->db->prepare('select image
                                     from :table_products_images
                                     where products_id = :products_id
                                     and id not in (' . implode(', ', $piArray) . ')
                                    ');
      $Qimages->bindInt(':products_id', $id);
      $Qimages->execute();

      if ($Qimages->fetch() !== false) {
        do {
          $Qcheck = $this->db->get('products_images', 'count(*) as total', ['image' => $Qimages->value('image')]);

          if ($Qcheck->valueInt('total') < 2) {
            if (file_exists($this->template->getDirectoryPathTemplateShopImages() . $Qimages->value('image'))) {
              unlink($this->template->getDirectoryPathTemplateShopImages() . $Qimages->value('image'));
            }
          }
        } while ($Qimages->fetch());

        $Qdel = $this->db->prepare('delete from :table_products_images
                                    where products_id = :products_id
                                    and id not in (' . implode(', ', $piArray) . ')
                                   ');
        $Qdel->bindInt(':products_id', $id);
        $Qdel->execute();
      }
    }

/**
 * getInfoImage
 *
 * @param string $image, $alt, $width, $height
 * @return string $image, the image value
 * @access public
*/

  public function getInfoImage($image, $alt, $width = '130', $height = '130') {
    if (!empty($image) && (file_exists($this->template->getDirectoryPathTemplateShopImages() . $image)) ) {
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
 * @access public
 */
    public function getproductPackaging($id) {
      if (!is_null($_SESSION['ProductAdminId'])) {
        $id =  $_SESSION['ProductAdminId'];

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
 * @param string  $products_quantity_unit_id, $language_id
 * @return string $products_quantity_unit_['products quantity unit_title'],  name of the he products quantity unit
 * @access public
 */
    public function getProductsQuantityUnitTitle($products_quantity_unit_id, $language_id = '') {

      if (!$language_id) $language_id = $this->lang->getId();

      $QproductsQuantityUnitTitle = $this->db->prepare('select products_quantity_unit_title
                                                        from :table_products_quantity_unit
                                                        where products_quantity_unit_id = :products_quantity_unit_id
                                                        and language_id = :language_id
                                                      ');

      $QproductsQuantityUnitTitle->bindInt(':products_quantity_unit_id', (int)$products_quantity_unit_id );
      $QproductsQuantityUnitTitle->bindInt(':language_id', (int)$language_id );

      $QproductsQuantityUnitTitle->execute();

      return $QproductsQuantityUnitTitle->value('products_quantity_unit_title');
    }

/**
  * Products model
  *
  * @param string  $product_id
  * @return string $product['products_model'], products model
  * @access public
*/
    public function getProductsModel($id) {

      $QproductsModel = $this->db->prepare('select products_model
                                            from :table_products
                                            where products_id = :products_id
                                           ');

      $QproductsModel->bindInt(':products_id', $id );

      $QproductsModel->execute();

      return $QproductsModel->value('products_model');
    }

/**
 * Shipping delay of the product
 *
 * @param string  $product_id, $language_id
 * @return string $product['products_shipping_delay'], url of the product
 * @access public
 */
    public function getProductsShippingDelay($id, $language_id) {
      $Qproduct = $this->db->prepare('select products_shipping_delay
                                     from :table_products_description
                                     where products_id = :products_id
                                     and language_id = :language_id
                                   ');
      $Qproduct->bindInt(':products_id', $id);
      $Qproduct->bindInt(':language_id', (int)$language_id);

      $Qproduct->execute();

      return $Qproduct->value('products_shipping_delay');
    }

/**
 * Description summary
 *
 * @param string  $product_id, $language_id
 * @return string $product['products_description'], description name
 * @access public
 */
    public function getProductsDescriptionSummary($product_id, $language_id) {

      if (!$language_id) $language_id = $this->lang->getId();

      $Qproduct = $this->db->prepare('select products_description_summary
                                     from :table_products_description
                                     where products_id = :products_id
                                     and language_id = :language_id
                                  ');
      $Qproduct->bindInt(':products_id', (int)$product_id);
      $Qproduct->bindInt(':language_id', (int)$language_id);

      $Qproduct->execute();

      return $Qproduct->value('products_description_summary');
    }

/**
 * GetProductsImage : image inside the catalog
 *
 * @param string$product_id :id of the product
 * @return string  image of the product
 * @access public
 */

    public function getProductsImage($product_id) {
      $Qproduct = Registry::get('Db')->get('products', 'products_image', ['products_id' => (int)$product_id]);
      return $Qproduct->value('products_image');
    }

/**
 * Directory of image
 *
 * @param string $filename : name of the file
 * @return string $directory_array, the directories name in css directory
 * @access public
 */

    public function getDirectoryProducts() {
      $template_directory = CLICSHOPPING::getConfig('dir_root', 'Shop') . 'sources/images/products/';

      $weeds = ['.', '..', '_notes'];
      $directories = array_diff(scandir($template_directory), $weeds);

      $directory_array[0] = ['id' => '',
                             'text' => CLICSHOPPING::getDef('select_datas')
                            ];

      foreach($directories as $directory) {
        if(is_dir($template_directory.$directory)) {
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
 * @param string  $product_id, $language_id
 * @return string $product['products_name'], name of the product
 * @access public
 */
    public function getProductsName($product_id, $language_id = 0) {

      if ($language_id == 0) $language_id = $this->lang->getId();
      $Qproduct = Registry::get('Db')->get('products_description', 'products_name', ['products_id' => (int)$product_id,
                                                                                     'language_id' => (int)$language_id]
                                          );

      return $Qproduct->value('products_name');
    }

/**
 * Description Name
 *
 * @param string  $product_id, $language_id
 * @return string $product['products_description'], description name
 * @access public
 */
    public function getProductsDescription($product_id, $language_id) {

      if ($language_id == 0) $language_id = $this->lang->getId();
      $Qproduct = Registry::get('Db')->get('products_description', 'products_description', ['products_id' => (int)$product_id,
                                                                                            'language_id' => (int)$language_id
                                                                                           ]
                                          );

      return $Qproduct->value('products_description');
    }


/**
 * Supplier DropDown
 *
 * @param string
 * @return string $supplier, elements of supplier in dropdown
 * @access public
 */

    public function supplierDropDown() {
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
 * @access public
 */
    public function removeProduct($product_id) {

      $Qimage = $this->db->prepare('select products_image,
                                          products_image_zoom,
                                          products_image_medium,
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
                                          ');
      $QduplicateImage->bindValue(':products_image', $Qimage->value('products_image') );
      $QduplicateImage->bindValue(':products_image_zoom', $Qimage->value('products_image_zoom') );
      $QduplicateImage->bindValue(':products_image_medium', $Qimage->value('products_image_medium') );

      $QduplicateImage->execute();

      $duplicate_image = $QduplicateImage->fetch();

// Controle si l'image est utilisee sur une categorie
      $QduplicateImageCategories = $this->db->prepare('select count(*) as total
                                                       from :table_categories
                                                       where categories_image = :products_image
                                                       or categories_image = :products_image_zoom
                                                       or categories_image = :products_image_medium
                                                      ');
      $QduplicateImageCategories->bindValue(':products_image', $Qimage->value('products_image') );
      $QduplicateImageCategories->bindValue(':products_image_zoom', $Qimage->value('products_image_zoom') );
      $QduplicateImageCategories->bindValue(':products_image_medium', $Qimage->value('products_image_medium') );

      $QduplicateImageCategories->execute();

      $duplicate_image_categories = $QduplicateImageCategories->fetch();


// Controle si l'image est utiliee sur les descriptions d'un produit
      $QduplicateImageProductDescription = $this->db->prepare('select count(*) as total
                                                               from :table_products_description
                                                               where products_description like :products_description
                                                               or products_description like :products_description1
                                                               or products_description like :products_description2
                                                              ');
      $QduplicateImageProductDescription->bindValue(':products_description', '%' . $Qimage->value('products_image') . '%' );
      $QduplicateImageProductDescription->bindValue(':products_description1', '%' . $Qimage->value('products_image_zoom') . '%' );
      $QduplicateImageProductDescription->bindValue(':products_description2', '%' . $Qimage->value('products_image_medium') . '%' );

      $QduplicateImageProductDescription->execute();

      $duplicate_image_product_description = $QduplicateImageProductDescription->fetch();


// Controle si l'image est utilisee sur une banniere
      $QduplicateImageBanners = $this->db->prepare('select count(*) as total
                                                     from :table_banners
                                                     where banners_image = :products_image
                                                     or banners_image = :products_image_zoom
                                                     or banners_image = :products_image_medium
                                                    ');
      $QduplicateImageBanners->bindValue(':products_image', $Qimage->value('products_image') );
      $QduplicateImageBanners->bindValue(':products_image_zoom', $Qimage->value('products_image_zoom') );
      $QduplicateImageBanners->bindValue(':products_image_medium', $Qimage->value('products_image_medium') );

      $QduplicateImageBanners->execute();

      $duplicate_image_banners = $QduplicateImageBanners->fetch();


// Controle si l'image est utilisee sur les fabricants
      $QduplicateImageManufacturers = $this->db->prepare('select count(*) as total
                                                         from :table_manufacturers
                                                         where manufacturers_image = :products_image
                                                         or manufacturers_image = :products_image_zoom
                                                         or manufacturers_image = :products_image_medium
                                                        ');
      $QduplicateImageManufacturers->bindValue(':products_image', $Qimage->value('products_image') );
      $QduplicateImageManufacturers->bindValue(':products_image_zoom', $Qimage->value('products_image_zoom') );
      $QduplicateImageManufacturers->bindValue(':products_image_medium', $Qimage->value('products_image_medium') );

      $QduplicateImageManufacturers->execute();

      $duplicate_image_manufacturers = $QduplicateImageManufacturers->fetch();


// Controle si l'image est utilisee sur les fabricants
      $QduplicateImageSuppliers = $this->db->prepare('select count(*) as total
                                                     from :table_suppliers
                                                     where suppliers_image  = :products_image
                                                     or suppliers_image  = :products_image_zoom
                                                     or suppliers_image  = :products_image_medium
                                                    ');
      $QduplicateImageSuppliers->bindValue(':products_image', $Qimage->value('products_image') );
      $QduplicateImageSuppliers->bindValue(':products_image_zoom', $Qimage->value('products_image_zoom') );
      $QduplicateImageSuppliers->bindValue(':products_image_medium', $Qimage->value('products_image_medium') );

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
          @unlink($this->template->getDirectoryPathTemplateShopImages() . $Qimage->value('products_image'));
        }
        if (file_exists($this->template->getDirectoryPathTemplateShopImages() . $Qimage->value('products_image_zoom'))) {
          @unlink($this->template->getDirectoryPathTemplateShopImages() . $Qimage->value('products_image_zoom'));
        }
        if (file_exists($this->template->getDirectoryPathTemplateShopImages() . $Qimage->value('products_image_medium'))) {
          @unlink($this->template->getDirectoryPathTemplateShopImages() . $Qimage->value('products_image_medium'));
        }
      }

      $Qimages = $this->db->get('products_images', 'image', ['products_id' => (int)$product_id]);


      if ($Qimages->fetch() !== false) {
        do {
          $Qduplicate = $this->db->get('products_images', 'id', ['image' => $Qimages->value('image'),
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
              @unlink($this->template->getDirectoryPathTemplateShopImages() . $Qimages->value('image'));
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
      $Qdelete->bindInt(':products_id_att', (int)$product_id. '{%') ;
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

      $this->hooks->call('Products','RemoveProduct');

      Cache::clear('categories');
      Cache::clear('products-also_purchased');
      Cache::clear('upcoming');
    }


/**
 * url of the product
 *
 * @param string  $product_id, $language_id
 * @return string $Qproduct->value('products_url'), url of the product
 * @access public
 */
    public function getProductsUrl($product_id, $language_id) {

      if ($language_id == 0) $language_id = $this->lang->getId();
      $Qproduct = Registry::get('Db')->get('products_description', 'products_url', ['products_id' => (int)$product_id, 'language_id' => (int)$language_id]);

      return $Qproduct->value('products_url');
    }


/**
  * Return the manufacturers URL in the needed language
  *
  * @param string $manufacturer_id, $language_id
  * @return string $Qmanufacturer->value('manufacturers_url'), url of manufacturers
  * @access public
*/
    public function getManufacturerUrl($manufacturer_id, $language_id) {

      if ($language_id == 0) $language_id = $this->lang->getId();
      $Qmanufacturer = Registry::get('Db')->get('manufacturers_info', 'manufacturers_url', ['manufacturers_id' => (int)$manufacturer_id, 'languages_id' => (int)$language_id]);

      return $Qmanufacturer->value('manufacturers_url');
    }

/**
 * cloneProductsInOtherCategory
 *
 * @return
 * @access private
 *
 */
    private function cloneProductsInOtherCategory($id) {
      $multi_clone_categories_id_to = HTML::sanitize($_POST['clone_categories_id_to']);

      $Qproducts = $this->db->prepare('select *
                                      from :table_products
                                      where products_id = :products_id
                                     ');
      $Qproducts->bindInt(':products_id', $id);

      $Qproducts->execute();

      for ($i=0; $i < count($multi_clone_categories_id_to); $i++) {

// clonage dans la categorie
        $clone_categories_id_to = $multi_clone_categories_id_to[$i];

        // copy du produit
        $this->db->save('products', [
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
                                    'products_status' => 0,
                                    'products_tax_class_id' => (int)$Qproducts->valueInt('products_tax_class_id'),
                                    'products_view' => (int)$Qproducts->valueInt('products_view'),
                                    'orders_view' => (int)$Qproducts->valueInt('orders_view'),
                                    'products_min_qty_order' => (int)$Qproducts->valueInt('products_min_qty_order'),
                                    'products_dimension_width' => (float)$Qproducts->value('products_dimension_width'),
                                    'products_dimension_height' => (float)$Qproducts->value('products_dimension_height'),
                                    'products_dimension_depth' => (float)$Qproducts->value('products_dimension_depth'),
                                    'products_dimension_type' => $Qproducts->value('products_dimension_type'),
                                    'admin_user_name' =>  AdministratorAdmin::getUserAdmin(),
                                    'products_volume' => $Qproducts->value('products_volume'),
                                    'products_only_online' => (int)$Qproducts->valueInt('products_only_online'),
                                    'products_image_medium' => $Qproducts->value('products_image_medium'),
                                    'products_cost' => (float)$Qproducts->value('products_cost'),
                                    'products_handling' => (int)$Qproducts->value('products_handling'),
                                    'products_packaging' => (int)$Qproducts->valueInt('products_packaging'),
                                    'products_sort_order' => (int)$Qproducts->valueInt('products_sort_order'),
                                    'products_quantity_alert' => (int)$Qproducts->valueInt('products_quantity_alert'),
                                    'products_only_shop' => (int)$Qproducts->valueInt('products_only_shop'),
                                    'products_type' => HTML::sanitize($_POST['products_type'])
                                  ]
                      );
        $dup_products_id = $this->db->lastInsertId();

// ---------------------
// gallery
// ----------------------
        $QproductImage = $this->db->prepare('select *
                                              from :table_products_images
                                              where products_id = :products_id
                                            ');
        $QproductImage->bindInt(':products_id', (int)$id);

        $QproductImage->execute();

        while ($QproductImage->fetch() ) {

          $this->db->save('products_images', [
                                                'products_id' =>  (int)$dup_products_id,
                                                'image' => $QproductImage->value('image'),
                                                'htmlcontent' => $QproductImage('htmlcontent'),
                                                'sort_order' => (int)$QproductImage->valueInt('sort_order')
                                              ]
                          );
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

        while ($Qdescription->fetch() ) {

          $this->db->save('products_description', [
                                                    'products_id' => (int)$dup_products_id,
                                                    'language_id' =>  (int)$Qdescription->valueInt('language_id'),
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

// ---------------------
// insertion table
// ----------------------
        $this->db->save('products_to_categories', [
                                                    'products_id' => (int)$dup_products_id,
                                                    'categories_id' =>  (int)$clone_categories_id_to
                                                  ]
                       );

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

        // Gets all of the customers groups
        while ($QcustomersGroup->fetch() ) {

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
              $products_model_group  = HTML::sanitize($_POST['products_model_group' . $QcustomersGroup->valueInt('customers_group_id')]);
              $products_quantity_fixed_group  = HTML::sanitize($_POST['products_quantity_fixed_group' . $QcustomersGroup->valueInt('customers_group_id')]);

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
            $Qupdate->bindInt(':customers_group_id',  (int)$Qattributes->valueInt('customers_group_id'));
            $Qupdate->bindInt(':products_id',  (int)$clone_products_id);

            $Qupdate->execute();


// Prix TTC B2B ----------
            if ( ($_POST['price' . $QcustomersGroup->valueInt('customers_group_id')] <> $Qattributes->valueDecimal('customers_group_price')) && ($Qattributes->valueInt('customers_group_id') == $QcustomersGroup->valueInt('customers_group_id')) ) {

              $Qupdate = $this->db->prepare('update :table_products_groups
                                                set customers_group_price = :customers_group_price,
                                                    products_price = :products_price
                                                where customers_group_id = :customers_group_id
                                                and products_id = :products_id
                                              ');
              $Qupdate->bindInt(':customers_group_price', $_POST['price' . $QcustomersGroup->valueInt('customers_group_id')]);
              $Qupdate->bindInt(':products_price', $_POST['products_price']);
              $Qupdate->bindInt(':customers_group_id', (int)$Qattributes->valueInt('customers_group_id'));
              $Qupdate->bindInt(':products_id',  (int)$clone_products_id);

              $Qupdate->execute();

            } elseif (($_POST['price' . $QcustomersGroup->valueInt('customers_group_id')] == $Qattributes->valueInt('customers_group_id'))) {
//              $attributes = $Qattributes->fetch();
            }
// Prix + Afficher Prix public + Afficher Produit + Autoriser Commande
          } elseif ($_POST['price' . $QcustomersGroup->valueInt('customers_group_id')] != '') {

            $this->db->save('products_groups', [
                                                'products_id' => (int)$clone_products_id,
                                                'products_price' => (float)$_POST['products_price'],
                                                'customers_group_id' => (int)$QcustomersGroup->valueInt('customers_group_id'),
                                                'customers_group_price' => (float)$_POST['price' . $QcustomersGroup->valueInt('customers_group_id')],
                                                'price_group_view' => (int)$_POST['price_group_view' . $QcustomersGroup->valueInt('customers_group_id')],
                                                'products_group_view' => (int)$_POST['products_group_view' . $QcustomersGroup->valueInt('customers_group_id')],
                                                'orders_group_view' => (int)$_POST['orders_group_view' . $QcustomersGroup->valueInt('customers_group_id')],
                                                'products_quantity_unit_id_group' => (int)$_POST['products_quantity_unit_id_group' . $QcustomersGroup->valueInt('customers_group_id')],
                                                'products_model_group' =>  $_POST['products_model_group' . $QcustomersGroup->valueInt('customers_group_id')],
                                                'products_quantity_fixed_group' => (int)$_POST['products_quantity_fixed_group' . $QcustomersGroup->valueInt('customers_group_id')],
                                              ]
                          );

          }
        } // end while

        $this->hooks->call('Products', 'CloneProducts');
      } //End for
    }


/**
 * Search products
 * @param, $keywords, keyword to search
 * @return $Qproducts, result of search
 * @access public
 */

    public function getSearch($keywords = null) {
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
                                                                     p.products_volume,
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
                                         limit :page_set_offset, :page_set_max_results
                                      ');

        $Qproducts->bindInt(':language_id', $this->lang->getId() );
        $Qproducts->bindValue(':search', '%' . $keywords . '%');
        $Qproducts->setPageSet((int)MAX_DISPLAY_SEARCH_RESULTS_ADMIN);
        $Qproducts->execute();
      } else {
        if (isset($_POST['cPath'])) {
          $current_category_id = HTML::sanitize($_POST['cPath']);
        } else {
          $current_category_id = HTML::sanitize($_GET['cPath']);
        }

        $Qproducts = $this->db->prepare('select  SQL_CALC_FOUND_ROWS p.products_id,
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

        $Qproducts->bindInt(':categories_id', (int)$current_category_id );
        $Qproducts->bindInt(':language_id', $this->lang->getId() );
        $Qproducts->setPageSet((int)MAX_DISPLAY_SEARCH_RESULTS_ADMIN);
        $Qproducts->execute();
      }

      return $Qproducts;
    }

/**
 * save products
 * @param, $id, id of the products, $action, insert or update products
 * @return
 * @access public
 */


    public function save($id = null, $action) {

//---------------------------------------------------------------------------------------------
//  Prepare
//---------------------------------------------------------------------------------------------

      $products_date_available = HTML::sanitize($_POST['products_date_available']);

      $products_date_available = (date('Y-m-d') < $products_date_available) ? $products_date_available : 'null';

      $current_category_id = HTML::sanitize($_POST['move_to_category_id']);

// Definir la position 0 ou 1 pour --> products_view : Affichage Produit Grand public - orders_view : Autorisation Commande
      if (HTML::sanitize($_POST['products_view']) == 1) {
        $products_view = 1;
      } else {
        $products_view = 0;
      }

      if (HTML::sanitize($_POST['orders_view']) == 1) {
        $orders_view = 1;
      } else {
        $orders_view = 0;
      }

// Gestion de l'affichage concernant la le prix / kg
      if (HTML::sanitize($_POST['products_price_kilo']) == 1) {
        $products_price_kilo = 1;
      } else {
        $products_price_kilo = 0;
      }

// Gestion de l'affichage concernant les produits uniquement online ou en boutique (physique)
      if (HTML::sanitize($_POST['products_only_online']) == 1) {
        $products_only_online = 1;
      } else {
        $products_only_online = 0;
      }

// Gestion de l'affichage concernant les produits uniquement en boutique (physique)
      if (HTML::sanitize($_POST['products_only_shop']) == 1) {
        $products_only_shop = 1;
      } else {
        $products_only_shop = 0;
      }

// Gestion de l'affichage concernant le telechargementde fichier publix / privee

      if (HTML::sanitize($_POST['products_download_public']) == 1) {
        $products_download_public = 1;
      } else {
        $products_download_public = 0;
      }

// manual price B2B
      if ($_POST['products_percentage'] == 'on') {
        $_POST['products_percentage'] = 0;
      }

// Affichage des produits, autorisation de commander et mode B2B en automatique mis par defaut en valeur 1 dans la cas de la B2B desactivee.
      if (MODE_B2B_B2C == 'false') {
        $products_view = 1;
        $orders_view = 1;
        $_POST['products_percentage'] = 1;
      }

      $products_model = $this->getProductModel();

      $products_sku = $this->getProductSKU();
      $products_ean = $this->getProductEAN();


      if (is_numeric($_POST['products_status'])) {
        $products_status = HTML::sanitize($_POST['products_status']);
      } else {
        $products_status = 0;
      }

      $sql_data_array = ['products_quantity' => (int)HTML::sanitize($_POST['products_quantity']),
                        'products_ean' => HTML::sanitize($products_ean),
                        'products_model' => HTML::sanitize($products_model),
                        'products_sku' => HTML::sanitize($products_sku),
                        'products_price' => (float)HTML::sanitize($_POST['products_price']),
                        'products_date_available' => $products_date_available,
                        'products_weight' => (float)HTML::sanitize($_POST['products_weight']),
                        'products_price_kilo' => HTML::sanitize($products_price_kilo),
                        'products_status' => (int)HTML::sanitize($products_status),
                        'products_percentage' => (int)HTML::sanitize($_POST['products_percentage']),
                        'products_view' => (int)$products_view,
                        'orders_view' => (int)$orders_view,
                        'products_tax_class_id' => (int)HTML::sanitize($_POST['products_tax_class_id']),
                        'products_min_qty_order' => (int)$_POST['products_min_qty_order'],
                        'products_dimension_width'  => (float)HTML::sanitize($_POST['products_dimension_width']),
                        'products_dimension_height'  => (float)HTML::sanitize($_POST['products_dimension_height']),
                        'products_dimension_depth' => (float)HTML::sanitize($_POST['products_dimension_depth']),
                        'products_dimension_type'  => HTML::sanitize($_POST['products_dimension_type']),
                        'admin_user_name'  => AdministratorAdmin::getUserAdmin(),
                        'products_volume' => HTML::sanitize($_POST['products_volume']),
                        'products_only_online'  => (int)HTML::sanitize($products_only_online),
                        'products_cost' => (float)HTML::sanitize($_POST['products_cost']),
                        'products_handling' => (float)HTML::sanitize($_POST['products_handling']),
                        'products_packaging' => (int)HTML::sanitize($_POST['products_packaging']),
                        'products_sort_order' => (int)HTML::sanitize($_POST['products_sort_order']),
                        'products_quantity_alert' => (int)HTML::sanitize($_POST['products_quantity_alert']),
                        'products_only_shop'  => (int)HTML::sanitize($products_only_shop),
                        'products_download_public'  => (int)HTML::sanitize($products_download_public),
                        'products_type' => HTML::sanitize($_POST['products_type'])
                       ];

// Download file
      $this->saveFileUpload();
// image
      $this->getImage($id);

      $sql_data_array['products_image_medium'] = $this->products_image_medium;
      $sql_data_array['products_image_zoom'] = $this->products_image_zoom;
      $sql_data_array['products_image'] = $this->products_image;

//---------------------------------------------------------------------------------------------
//  Save Data
//---------------------------------------------------------------------------------------------
//update
      if ( is_numeric($id) && $action == 'Update') {
        $update_sql_data = ['products_last_modified' => 'now()'];
        $sql_data_array = array_merge($sql_data_array, $update_sql_data);

        $this->db->save('products', $sql_data_array, ['products_id' => (int)$id]);

        $Qupdate = $this->db->prepare('update :table_products_to_categories
                                        set categories_id = :categories_id
                                        where products_id = :products_id
                                      ');
        $Qupdate->bindInt(':products_id', (int)$id);
        $Qupdate->bindInt(':categories_id',(int)$current_category_id);
        $Qupdate->execute();

        if (isset($_POST['clone_categories_id_to'])) {
          $this->cloneProductsInOtherCategory($id);
        }

      } else {
//insert
        $insert_sql_data = ['products_date_added' => 'now()'];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $this->db->save('products', $sql_data_array);

        $id = $this->db->lastInsertId();

        $this->db->save('products_to_categories', [ 'products_id' => (int)$id,
                                                    'categories_id' => (int)$current_category_id
                                                  ]
                       );

//for hooks
        $_POST['insertId'] = $id; // take the new id of the product
      }

      $this->saveGalleryImage($id);
      $this->saveProductsDescription($id, $action);

      $this->hooks->call('Products','Save');
    }


/**
 * Count how many products exist in a category
 * TABLES: products, products_to_products, products
*/
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
  }