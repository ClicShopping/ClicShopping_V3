<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\OM\Module\Hooks\Shop\Api;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  class ApiDeleteManufacturer
  {
    /**
     * @param int $id
     * @return void
     */
    private static function deleteManufacturer(int $id) :void
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Hooks = Registry::get('Hooks');

      $Qcheck = $CLICSHOPPING_Db->prepare('select manufacturers_id
                                           from :table_manufacturers
                                           where manufacturers_id = :manufacturers_id
                                          ');

      $Qcheck->bindInt(':manufacturers_id', $id);
      $Qcheck->execute();

      if ($Qcheck->fetch()) {
        $sql_array = [
          'manufacturers_id' => (int)$id,
        ];

        $CLICSHOPPING_Db->delete('manufacturers', $sql_array);
        $CLICSHOPPING_Db->delete('manufacturers_info', $sql_array);

        $Qupdate = $CLICSHOPPING_Db->prepare('update :table_products
                                              set manufacturers = :manufacturers,
                                                  products_status = 0
                                              where manufacturers = :manufacturers1
                                            ');
        $Qupdate->bindInt(':manufacturers', '');
        $Qupdate->bindInt(':manufacturers1', $id);

        $Qupdate->execute();

        $CLICSHOPPING_Hooks->call('Manufacturers', 'Delete');
      }
    }

    public function execute()
    {
      if (isset($_GET['mId'], $_GET['manufacturers'])) {
        $id = HTML::sanitize($_GET['mId']);

        return static::deleteManufacturer($id);
      } else {
        return false;
      }
    }
  }