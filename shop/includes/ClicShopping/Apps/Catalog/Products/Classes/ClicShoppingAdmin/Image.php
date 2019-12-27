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

  use ClicShopping\Apps\Catalog\Products\Classes\ClicShoppingAdmin\ImageResample;

  class Image
  {
    private $rootImagesDir;
    private $db;
    private $template;
    private $imageResample;
    
    public function __construct()
    {
      $this->template = Registry::get('TemplateAdmin');
      $this->db = Registry::get('Db');
      $this->rootImagesDir = $this->template->getDirectoryPathTemplateShopImages() . 'products/';

      Registry::set('ImageResample', new ImageResample());
      $this->imageResample = Registry::get('ImageResample');
    }

  /*
   * generate a radom string
   * @param int length of the random
   * @return $randomString
   */
    public function getGenerateRandomString(int $length = 10): string
    {
      $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
      $charactersLength = strlen($characters);
      $randomString = '';

      for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
      }
      return $randomString;
    }

    /**
     * Clean image
     * @param string $image_name
     * @return string
     */
    public function cleanImageName(string $image_name): string
    {
      $CLICSHOPPING_Template = Registry::get('TemplateAdmin');

      $products_image_name = HTML::sanitize($image_name);
      $products_image_name = htmlspecialchars($products_image_name, ENT_QUOTES | ENT_HTML5);
      $products_image_name = HTML::replaceString($CLICSHOPPING_Template->getDirectoryShopTemplateImages(), '', $products_image_name);
      $products_image_name_end = strstr($products_image_name, '&quot;');
      $products_image_name = HTML::replaceString($products_image_name_end, '', $products_image_name);
      $products_image_name = HTML::replaceString($CLICSHOPPING_Template->getDirectoryShopSources(), '', $products_image_name);

      return $products_image_name;
    }

    /**
     *  createDirectory
     * @return string
     */
    private function createDirectory(): string
    {
      if (isset($_POST['new_directory_products_image']) && !empty($_POST['new_directory_products_image'])) {
        $new_dir_products_image_without_accents = HTML::removeFileAccents($_POST['new_directory_products_image']);
        $new_dir_products_image = strtolower($new_dir_products_image_without_accents);
        $new_dir_products_image = HTML::replaceString(' ', '_', $new_dir_products_image);

        if (!is_dir($new_dir_products_image)) {
          @mkdir($this->rootImagesDir . $new_dir_products_image, 0755, true);
          @chmod($this->rootImagesDir . $new_dir_products_image, 0755);
        }
        if (isset($_POST['directory_products_image']) && !empty($_POST['directory_products_image'])) {
          $new_dir_products_image = $new_dir_products_image . '/' . HTML::sanitize($_POST['directory_products_image']);
        }

      } else {
        $new_dir_products_image = HTML::sanitize($_POST['directory_products_image']);
      }

      if (empty($new_dir_products_image)) {
        $dir_products_image = 'products/' . $new_dir_products_image;
      } else {
        $dir_products_image = 'products/' . $new_dir_products_image . '/';
      }

      return $dir_products_image;
    }

    /**
     * @param string $image
     * @return string
     */
    protected function getImageExtensionWebp(string $image): string
    {
      if (CONFIGURATION_CONVERT_IMAGE == 'true') {
        $p = pathinfo($this->template->getDirectoryPathTemplateShopImages() . $image);
        $ext = strtolower($p['extension']);

        $big_image_resized_path = $this->template->getDirectoryPathTemplateShopImages() . $image;

        if ($ext != 'webp') {
          if ($img = imagecreatefromstring(file_get_contents($big_image_resized_path))) {
            $image = str_replace($ext, 'webp', $image);

            $this->imageResample->save($this->template->getDirectoryPathTemplateShopImages() . $image);
            imagedestroy($img);
          }
        }

        unlink($big_image_resized_path);
      }

      return $image;
    }

    /**
     * Normal,medium or big image
     *
     */
    public function getImage()
    {
      if (isset($_GET['pID'])) {
        $Qimages = $this->db->prepare('select products_image,
                                              products_image_medium,
                                              products_image_zoom,
                                              products_image_small
                                       from :table_products
                                       where products_id = :products_id
                                      ');
        $Qimages->bindInt(':products_id', $_GET['pID']);
        $Qimages->execute();

        $product_update_image = $Qimages->value('products_image');
        $product_update_image_zoom = $Qimages->value('products_image_zoom');
        $product_update_image_medium = $Qimages->value('products_image_medium');
        $product_update_image_small = $Qimages->value('products_image_small');
      } else {
        $product_update_image = '';
        $product_update_image_zoom = '';
        $product_update_image_medium = '';
        $product_update_image_small= '';
      }

      $dir_products_image = $this->createDirectory();
      $rand_image = $this->getGenerateRandomString();

      $error = true;
//
// load originale image
//
      $image = new Upload('products_image_resize', $this->template->getDirectoryPathTemplateShopImages() . $dir_products_image, null, ['gif', 'jpg', 'png', 'jpeg', 'webp']);

      if ($image->check() && $image->save()) {
        $error = false;
      }

      if ($error === false) {
        $sql_data_array['image'] = $dir_products_image . $image->getFilename();
      } else {
        $sql_data_array['image'] = '';
      }

      if ($image->check()) {
        $filename_image_name = $image->getFilename();

        $this->imageResample->load($this->template->getDirectoryPathTemplateShopImages() . $dir_products_image . $filename_image_name);

//get image type
        $ext = exif_imagetype($this->template->getDirectoryPathTemplateShopImages() . $dir_products_image . $filename_image_name);

        $image_name = HTML::removeFileAccents($filename_image_name);
        $image_name = HTML::replaceString(' ', '', $image_name);

//
// Zoom image
//
        if (empty(BIG_IMAGE_WIDTH)) {
          $big_catalg_image_with = 640;
        } else {
          $big_catalg_image_with = (int)BIG_IMAGE_WIDTH;
        }

        $this->imageResample->resizeToWidth($big_catalg_image_with);

        if (empty($big_catalg_image_with)) {
          $big_image_width = '_big';
        } else {
          $big_image_width = $big_catalg_image_with;
        }

        $big_image_resized = $dir_products_image . $big_image_width . '_' . $rand_image . '_' . $image_name;

        $this->imageResample->save($this->template->getDirectoryPathTemplateShopImages() . $big_image_resized, $ext);

        $big_image_resized = $this->getImageExtensionWebp($big_image_resized);
//
// medium image
//
        if (empty(MEDIUM_IMAGE_WIDTH)) {
          $medium_catalog_image_with = 250;
        } else {
          $medium_catalog_image_with = (int)MEDIUM_IMAGE_WIDTH;
        }

        $this->imageResample->resizeToWidth($medium_catalog_image_with);

        if (empty($medium_catalog_image_with)) {
          $medium_image_width = '_medium';
        } else {
          $medium_image_width = $medium_catalog_image_with;
        }

        $medium_image_resized = $dir_products_image . $medium_catalog_image_with . '_' . $rand_image . '_' . $image_name;

        $this->imageResample->save($this->template->getDirectoryPathTemplateShopImages() . $medium_image_resized, $ext);

        $medium_image_resized = $this->getImageExtensionWebp($medium_image_resized);
//
// medium image
//
        if (empty(SMALL_IMAGE_WIDTH)) {
          $small_catalog_image_with = 130;
        } else {
          $small_catalog_image_with = (int)SMALL_IMAGE_WIDTH;
        }

        $this->imageResample->resizeToWidth($small_catalog_image_with);

        if (empty($small_catalog_image_with)) {
          $small_image_width = '_small';
        } else {
          $small_image_width = (int)$small_catalog_image_with;
        }

        $small_image_resized = $dir_products_image . $small_image_width . '_' . $rand_image . '_' . $image_name;

        $this->imageResample->save($this->template->getDirectoryPathTemplateShopImages() . $small_image_resized, $ext);

        $small_image_resized = $this->getImageExtensionWebp($small_image_resized);

//
// Admin Image
//
        if (empty(SMALL_IMAGE_WIDTH_ADMIN)) {
          $small_admin_image_with = 70;
        } else {
          $small_admin_image_with = (int)SMALL_IMAGE_WIDTH_ADMIN;
        }

        $this->imageResample->resizeToWidth($small_admin_image_with);

        if (empty($small_admin_image_with)) {
          $small_image_admin_width = '_small_admin';
        } else {
          $small_image_admin_width = (int)$small_admin_image_with;
        }

        $small_image_admin_resized = $dir_products_image . $small_image_admin_width . '_' . $rand_image . '_' . $image_name;

        $this->imageResample->save($this->template->getDirectoryPathTemplateShopImages() . $small_image_admin_resized, $ext);

        $small_image_admin_resized = $this->getImageExtensionWebp($small_image_admin_resized);

//
// delete the orginal files
//
        if (file_exists($this->template->getDirectoryPathTemplateShopImages() . $dir_products_image . $filename_image_name)) {
          @unlink($this->template->getDirectoryPathTemplateShopImages() . $dir_products_image . $filename_image_name);
        }
      } else {
        $big_image_resized = '';
        $medium_image_resized = '';
        $small_image_resized = '';
        $small_image_admin_resized = '';
      }

      if (isset($_POST['delete_image'])) {
        $this->products_image = $sql_data_array['products_image'] = null;
        $this->products_image_zoom = $sql_data_array['products_image_zoom'] = null;
        $this->products_image_medium = $sql_data_array['products_image_medium'] = null;
        $this->products_image_small = $sql_data_array['products_image_mall'] = null;

      } else {
        if ((isset($_POST['products_image']) && !is_null($_POST['products_image'])) || !empty($small_image_resized) || !empty($small_image_admin_resized)) {
          $products_image_name = $this->cleanImageName($_POST['products_image']);
//
// small image catalog
//
          if (!empty($small_image_resized)) {
            $this->products_image = $small_image_resized;
          } else {
            $this->products_image = $products_image_name;
          }
        } else {
          $this->products_image = $product_update_image;
        }
//
// big image
//
        if (!empty($big_image_resized)) {
          $products_image_zoom_name = $this->cleanImageName($big_image_resized);

          if (!empty($big_image_resized)) {
            $this->products_image_zoom = $big_image_resized;
          } else {
            $this->products_image_zoom = $products_image_zoom_name;
          }
        } else {
          $this->products_image_zoom = $product_update_image_zoom;
        }

//
// medium image
//
        if (!empty($medium_image_resized)) {
          $products_image_medium_name = $this->cleanImageName($medium_image_resized);

          if (!empty($medium_image_resized)) {
            $this->products_image_medium = $medium_image_resized;
          } else {
            $this->products_image_medium = $products_image_medium_name;
          }
        } else {
          $this->products_image_medium = $product_update_image_medium;
        }

//
// small admin image
//
        if (!empty($small_image_admin_resized)) {
          $products_image_small_name_name = $this->cleanImageName($small_image_admin_resized);

          if (!empty($small_image_admin_resized)) {
            $this->products_image_small = $small_image_admin_resized;
          } else {
            $this->products_image_small = $products_image_small_name_name;
          }
        } else {
          $this->products_image_small = $product_update_image_small;
        }
      }
    }

    /**
     * Product Image
     * @return string
     */
    public function productsImage(): string
    {
      return $this->products_image;
    }

    /**
     * Products Image medium
     * @return string
     */
    public function productsImageMedium(): string
    {
      return $this->products_image_medium;
    }

    /**
     * Products Image Zoom
     * @return string
     */
    public function productsImageZoom(): string
    {
      return $this->products_image_zoom;
    }

    /**
     * Products Image Zoom
     * @return string
     */
    public function productsSmallImage(): string
    {
      return $this->products_image_small;
    }

    /**
     * Save gallery image
     * @param $id int products_id
     */
    public function saveGalleryImage(int $id)
    {
      $root_images_dir = $this->rootImagesDir;

      $error = true;

// gallery
      if (isset($_POST['new_directory'])) {
        $new_dir_without_accents = HTML::removeFileAccents($_POST['new_directory']);
        $new_dir = HTML::replaceString(' ', '_', $new_dir_without_accents);
        $new_dir = strtolower($new_dir);
        $dir = 'products/' . (!empty($new_dir) ? $new_dir : $_POST['directory']);
      } else {
        $dir = '';
      }

      if (!empty($new_dir) && !is_dir($new_dir)) {
// depend server configuration
        @mkdir($root_images_dir . $new_dir, 0755, true);
        chmod($root_images_dir . $new_dir, 0755);

        $separator = '/';
      }

      if (isset($_POST['directory']) && !is_null($_POST['directory'])) {
        $separator = '/';
      }

      $pi_sort_order = 0;
      $piArray = [0];

      foreach ($_FILES as $key => $value) {
// Update existing large product images

        if (preg_match('/^products_image_large_([0-9]+)$/', $key, $matches)) {
          $pi_sort_order++;

          $sql_data_array = ['htmlcontent' => $_POST['products_image_htmlcontent_' . $matches[1]],
            'sort_order' => (int)$pi_sort_order
          ];

          $image = new Upload($key, $this->template->getDirectoryPathTemplateShopImages() . $dir, null, ['gif', 'jpg', 'png', 'webp']);

          if ($image->check() && $image->save()) {
            $error = false;
          }

          if ($error === false) {
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

          $image = new Upload($key, $this->template->getDirectoryPathTemplateShopImages() . $dir, null, ['gif', 'jpg', 'jepg', 'png', 'webp']);

          if ($image->check() && $image->save()) {
            $error = false;
          }

          $pi_sort_order++;

          if ($error === false) {
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
     * get image for listing admin use image
     * @param int $id
     * @return string
     */
    public function getSmallImageAdmin(int $id): string
    {
      $CLICSHOPPING_Template = Registry::get('TemplateAdmin');

      $Qimages = $this->db->prepare('select products_image,
                                             products_image_small
                                     from :table_products
                                     where products_id = :products_id
                                    ');
      $Qimages->bindInt(':products_id', $id);
      $Qimages->execute();

      if (!empty($Qimages->value('products_image_small'))) {
        $small_image = $Qimages->value('products_image_small');
      } else {
        $small_image = $Qimages->value('products_image');
      }

      $small_image = HTML::image($CLICSHOPPING_Template->getDirectoryShopTemplateImages() . $small_image, $Qimages->value('products_name'), (int)SMALL_IMAGE_WIDTH_ADMIN, (int)SMALL_IMAGE_HEIGHT_ADMIN);

      return $small_image;
    }
  }