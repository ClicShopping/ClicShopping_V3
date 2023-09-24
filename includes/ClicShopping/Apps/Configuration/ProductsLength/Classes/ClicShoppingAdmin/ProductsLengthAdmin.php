<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */


namespace ClicShopping\Apps\Configuration\ProductsLength\Classes\ClicShoppingAdmin;

use ClicShopping\OM\Registry;
use function is_null;

class ProductsLengthAdmin extends \ClicShopping\Apps\Configuration\ProductsLength\Classes\Shop\ProductsLength
{
  protected $products_length_classes = [];
  protected $precision = 2;

  public function __construct($precision = null)
  {
  }

  /**
   * @param $id
   * @param $language_id
   * @return mixed
   */
  public static function getTitle($id, $language_id = null)
  {
    return parent::getTitle($id, $language_id);
  }

  /**
   * @return array
   */
  public static function getClasses()
  {
    return parent::getClasses();
  }

  /**
   * @param $value
   * @param $class
   * @return string
   */
  public function display($value, $class)
  {
    return parent::display($value, $class);
  }

  /**
   * Drop down of the class title
   * @return array
   */
  public static function getClassesPullDown(): array
  {
    $CLICSHOPPING_Language = Registry::get('Language');
    $CLICSHOPPING_Db = Registry::get('Db');

    $Qclasses = $CLICSHOPPING_Db->prepare('select products_length_class_id, 
                                                    products_length_class_title 
                                              from :table_products_length_classes 
                                              where language_id = :language_id 
                                              order by products_length_class_title
                                            ');
    $Qclasses->bindInt(':language_id', $CLICSHOPPING_Language->getID());
    $Qclasses->execute();

    $classes = [];

    while ($Qclasses->fetch() !== false) {
      $classes[] = [
        'id' => $Qclasses->valueInt('products_length_class_id'),
        'text' => $Qclasses->value('products_length_class_title')
      ];
    }

    return $classes;
  }

  /**
   * Display a products_length title
   * @param int|null $id
   * @return string
   */
  public static function getLengthProductsTitle(int $id = null): string
  {
    $CLICSHOPPING_Db = Registry::get('Db');
    $CLICSHOPPING_Language = Registry::get('Language');

    if (!is_null($id)) {
      $Qlength = $CLICSHOPPING_Db->prepare('select products_length_class_title
                                               from :table_products_length_classes
                                               where products_length_class_id = :products_length_class_id
                                               and language_id = :language_id
                                               ');
      $Qlength->bindInt(':products_length_class_id', $id);
      $Qlength->bindInt(':language_id', $CLICSHOPPING_Language->getID());

      $Qlength->execute();

      $result = $Qlength->value('products_length_class_title');

      return $result;
    }
  }
}