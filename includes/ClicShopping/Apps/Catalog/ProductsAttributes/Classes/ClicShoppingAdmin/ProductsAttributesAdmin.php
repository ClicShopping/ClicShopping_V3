<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Catalog\ProductsAttributes\Classes\ClicShoppingAdmin;

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;
use ClicShopping\OM\Upload;

use ClicShopping\Apps\Catalog\Products\Classes\ClicShoppingAdmin\ImageResample;

class ProductsAttributesAdmin
{
  private mixed $lang;
  private mixed $db;
  public mixed $app;

  public function __construct()
  {
    $this->lang = Registry::get('Language');
    $this->db = Registry::get('Db');
    $this->app = Registry::get('ProductsAttributes');
  }

  /**
   * products options - attributes
   *
   * @param int $options_id
   * @return string $values_values['products_options_values_name'], the value of the option name
   */
  public function getOptionsName(int $options_id): string
  {
    $sql_array = [
      'products_options_id' => (int)$options_id,
      'language_id' => (int)$this->lang->getId()
    ];

    $Qoptions = Registry::get('Db')->get('products_options', 'products_options_name', $sql_array);

    return $Qoptions->value('products_options_name');
  }

  /**
   * products options name - attributes
   *
   * @param int $values_id
   * @return string $values_values['products_options_values_name'], the name value of the option name
   */
  public function getValuesName(int $values_id): string
  {
    $sql_array = [
      'products_options_values_id' => (int)$values_id,
      'language_id' => (int)$this->lang->getId()
    ];

    $Qvalues = Registry::get('Db')->get('products_options_values', 'products_options_values_name', $sql_array);

    return $Qvalues->value('products_options_values_name');
  }

  /**
   * @return
   */
  public function uploadImage()
  {
    $CLICSHOPPING_Template = Registry::get('TemplateAdmin');

    Registry::set('ImageResampleNew', new ImageResample());
    $CLICSHOPPING_ImageResample = Registry::get('ImageResampleNew');

    $CLICSHOPPING_Image = Registry::get('Image');

    $dir_products_image = 'attributes_options/';

    $error = true;

// load originale image
    $image = new Upload('products_image_resize', $CLICSHOPPING_Template->getDirectoryPathTemplateShopImages() . $dir_products_image, null, ['gif', 'jpg', 'png', 'jpeg', 'webp']);

// When the image is updated
    if ($image->check() && $image->save()) {
      $error = false;
    } else {
      $error = true;
    }

    if ($error === false && $image->check()) {
      $filename_image_name = $image->getFilename();
      $CLICSHOPPING_ImageResample->load($CLICSHOPPING_Template->getDirectoryPathTemplateShopImages() . $dir_products_image . $filename_image_name);

      $CLICSHOPPING_ImageResample->resizeToWidth(50);

      $image_name = HTML::removeFileAccents($filename_image_name);
      $image_name = HTML::replaceString(' ', '', $image_name);

      $image_ext = 'opt';
      $rand_image = $CLICSHOPPING_Image->getGenerateRandomString();

      $image = $dir_products_image . $image_ext . '_' . $rand_image . '_' . $image_name;

      $CLICSHOPPING_ImageResample->save($CLICSHOPPING_Template->getDirectoryPathTemplateShopImages() . $image);

// delete the orginal files
      if (file_exists($CLICSHOPPING_Template->getDirectoryPathTemplateShopImages() . $dir_products_image . $image_name)) {
        unlink($CLICSHOPPING_Template->getDirectoryPathTemplateShopImages() . $dir_products_image . $image_name);
      }

      $products_image_name = $CLICSHOPPING_Image->CleanImageName($image);

      return $products_image_name;
    }
  }

  /**
   * Set attribut option type
   * @return array
   */
  public function setAttributeType(): array
  {
    $products_options_type = [
      ['id' => 'select',
       'text' => $this->app->getDef('text_select')
      ],
      ['id' => 'radio',
       'text' => $this->app->getDef('text_radio')
      ]
    ];

    return $products_options_type;
  }
}