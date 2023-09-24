<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Catalog\Manufacturers\Classes\ClicShoppingAdmin;

use ClicShopping\OM\Registry;
use function is_null;

class ManufacturerAdmin
{
  /**
   * the manufacturer_description
   * @param int|null $manufacturers_id
   * @param int $language_id
   * @return string
   */
  public static function getManufacturerDescription(?int $manufacturers_id, int $language_id): string
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $Qmanufacturers = $CLICSHOPPING_Db->prepare('select manufacturer_description
                                                    from :table_manufacturers_info
                                                    where manufacturers_id = :manufacturers_id
                                                    and languages_id = :language_id
                                                  ');

    $Qmanufacturers->bindInt(':manufacturers_id', $manufacturers_id);
    $Qmanufacturers->bindInt(':language_id', $language_id);
    $Qmanufacturers->execute();

    return $Qmanufacturers->value('manufacturer_description');
  }

  /**
   * @param int $id
   * @return string
   */
  public static function getManufacturerNameById(int $id): string
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $Qmanufacturers = $CLICSHOPPING_Db->prepare('select manufacturers_name
                                                  from :table_manufacturers
                                                  where manufacturers_id = :manufacturers_id
                                                ');
    $Qmanufacturers->bindInt(':manufacturers_id', $id);
    $Qmanufacturers->execute();

    $result = $Qmanufacturers->value('manufacturers_name');

    return $result;
  }

  /**
   * @param int|null $id
   * @return mixed
   */
  public static function getManufacturerName(?int $id = null): array|string
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    if (!is_null($id)) {
      $Qproducts = $CLICSHOPPING_Db->prepare('select manufacturers_id
                                                from :table_products
                                                where products_id = :products_id
                                              ');
      $Qproducts->bindInt(':products_id', $id);

      $Qproducts->execute();

      $Qmanufacturers = $CLICSHOPPING_Db->prepare('select manufacturers_id,
                                                           manufacturers_name
                                                    from :table_manufacturers
                                                    where manufacturers_id = :manufacturers_id
                                                  ');
      $Qmanufacturers->bindInt(':manufacturers_id', $Qproducts->valueInt('manufacturers_id'));
      $Qmanufacturers->execute();

      $result = $Qmanufacturers->fetchAll();

      return $result;
    } else {
      return '';
    }
  }

  /**
   * the manufacturer name
   *
   * @param string|null $manufacturer_name
   * @return int|string manufacturer_id
   */
  public static function getManufacturerId(?string $manufacturer_name = null): int|string
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    if (!is_null($manufacturer_name)) {
      $Qmanufacturers = $CLICSHOPPING_Db->prepare('select manufacturers_id
                                                    from :table_manufacturers
                                                    where manufacturers_name = :manufacturers_name
                                                    limit 1
                                                   ');
      $Qmanufacturers->bindValue(':manufacturers_name', $manufacturer_name);

      $Qmanufacturers->execute();

      return $Qmanufacturers->valueInt('manufacturers_id');
    } else {
      return $manufacturers_id = 0;
    }
  }
}
