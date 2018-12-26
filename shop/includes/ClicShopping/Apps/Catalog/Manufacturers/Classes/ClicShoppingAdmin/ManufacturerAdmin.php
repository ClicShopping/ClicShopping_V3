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

  namespace ClicShopping\Apps\Catalog\Manufacturers\Classes\ClicShoppingAdmin;

  use ClicShopping\OM\Registry;

  class ManufacturerAdmin {

    protected $manufacturers_id;
    protected $language_id;
    protected $db;

    public function __construct() {
      $this->db = Registry::get('Db');
    }

/**
 * the manufacturer_description
 *
 * @param string  $manufacturer_id, $language_id
 * @return string $manufacturer['manufacturer_description'],  description of the manufacturer
 * @access public
 */
    public function getManufacturerDescription($manufacturers_id, $language_id) {

      $Qmanufacturers = $this->db->prepare('select manufacturer_description
                                              from :table_manufacturers_info
                                              where manufacturers_id = :manufacturers_id
                                              and languages_id = :language_id
                                            ');

      $Qmanufacturers->bindInt(':manufacturers_id', (int)$manufacturers_id);
      $Qmanufacturers->bindInt(':language_id', (int)$language_id);
      $Qmanufacturers->execute();

      return $Qmanufacturers->value('manufacturer_description');
    }
  }