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

  namespace ClicShopping\Apps\Catalog\Manufacturers\Classes\ClicShoppingAdmin;

  use ClicShopping\OM\Registry;

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
     * @param int|null $id
     * @return mixed
     */
    public static function getManufacturerName(?int $id = null)
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
      }
    }
  }