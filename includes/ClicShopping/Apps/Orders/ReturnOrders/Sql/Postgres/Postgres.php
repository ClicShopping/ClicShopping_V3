<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Orders\ReturnOrders\Sql\Postgres;

use ClicShopping\OM\Cache;
use ClicShopping\OM\Registry;

class Postgres
{
  public function execute()
  {
    $CLICSHOPPING_ReturnOrders = Registry::get('ReturnOrders');
    $CLICSHOPPING_ReturnOrders->loadDefinitions('Sites/ClicShoppingAdmin/install');

    self::installMenuAdministration();
    self::installDb();
  }

  /**
   * @return void
   */
  public static function installMenuAdministration(): void
  {
    $CLICSHOPPING_Db = Registry::get('Db');
    $CLICSHOPPING_ReturnOrders = Registry::get('ReturnOrders');
    $CLICSHOPPING_Language = Registry::get('Language');
    $Qcheck = $CLICSHOPPING_Db->get('administrator_menu', 'app_code', ['app_code' => 'app_orders_return_orders']);

    if ($Qcheck->fetch() === false) {
      $insert_sql_data = ['parent_id' => 4];
      $sql_data_array = [
        'sort_order' => 2,
        'link' => 'index.php?A&Orders\ReturnOrders&ReturnOrders',
        'image' => 'return_orders.png',
        'b2b_menu' => 0,
        'access' => 0,
        'app_code' => 'app_orders_return_orders'
      ];

      $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

      $CLICSHOPPING_Db->save('administrator_menu', $sql_data_array);

      $id = $CLICSHOPPING_Db->lastInsertId();
      $languages = $CLICSHOPPING_Language->getLanguages();

      for ($i = 0, $n = \count($languages); $i < $n; $i++) {
        $language_id = $languages[$i]['id'];
        $sql_data_array = ['label' => $CLICSHOPPING_ReturnOrders->getDef('title_menu')];

        $insert_sql_data = [
          'id' => (int)$id,
          'language_id' => (int)$language_id
        ];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $CLICSHOPPING_Db->save('administrator_menu_description', $sql_data_array);
      }

// 2nd menu
      $insert_sql_data = ['parent_id' => 14];

      $sql_data_array = [
        'sort_order' => 2,
        'link' => 'index.php?A&Orders\\ReturnOrders&Configure',
        'image' => 'return_orders.png',
        'b2b_menu' => 0,
        'access' => 0,
        'app_code' => 'app_orders_return_orders'
      ];

      $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

      $CLICSHOPPING_Db->save('administrator_menu', $sql_data_array);

      $id = $CLICSHOPPING_Db->lastInsertId();
      $languages = $CLICSHOPPING_Language->getLanguages();

      for ($i = 0, $n = \count($languages); $i < $n; $i++) {
        $language_id = $languages[$i]['id'];
        $sql_data_array = ['label' => $CLICSHOPPING_ReturnOrders->getDef('title_menu_return_orders_status')];

        $insert_sql_data = [
          'id' => (int)$id,
          'language_id' => (int)$language_id
        ];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $CLICSHOPPING_Db->save('administrator_menu_description', $sql_data_array);
      }

      Cache::clear('menu-administrator');
    }
  }

  /**
   * return void
   */
  public static function installDb(): void
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $Qcheck = $CLICSHOPPING_Db->query('show tables like ":table_return_orders"');

    $Qcheck = $CLICSHOPPING_Db->query("SELECT to_regclass(':table_return_orders')");

    if ($Qcheck->fetch() === false) {
      $sql = <<<EOD
    CREATE TABLE :table_return_orders (
      return_id serial PRIMARY KEY,
      return_ref varchar(32) NOT NULL,
      order_id int NOT NULL,
      product_id int NOT NULL,
      customer_id int NOT NULL,
      customer_firstname varchar(32) NOT NULL,
      customer_lastname varchar(32) NOT NULL,
      customer_email varchar(96) NOT NULL,
      customer_telephone varchar(32) NOT NULL,
      product_name varchar(255) NOT NULL,
      product_model varchar(64) NOT NULL,
      quantity int NOT NULL,
      opened smallint NOT NULL,
      return_reason_id int NOT NULL,
      return_action_id int NOT NULL,
      return_status_id int NOT NULL,
      comment text NOT NULL,
      date_ordered date NOT NULL,
      date_added timestamp NOT NULL,
      date_modified timestamp NOT NULL,
      archive smallint Default 0 NOT NULL
    ) CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOD;
      $CLICSHOPPING_Db->exec($sql);
    }

    $Qcheck = $CLICSHOPPING_Db->query("SELECT to_regclass(':table_return_orders_history')");

    if ($Qcheck->fetch() === false) {
      $sql = <<<EOD
    CREATE TABLE :table_return_orders_history (
      return_history_id serial PRIMARY KEY,
      return_id int NOT NULL,
      return_status_id int NOT NULL,
      notify smallint NOT NULL,
      comment text NOT NULL,
      date_added timestamp NOT NULL,
      admin_user_name varchar(64)
    ) CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOD;
      $CLICSHOPPING_Db->exec($sql);
    }

    $Qcheck = $CLICSHOPPING_Db->query("SELECT to_regclass(':table_return_orders_reason')");

    if ($Qcheck->fetch() === false) {
      $sql = <<<EOD
    CREATE TABLE :table_return_orders_reason (
      return_reason_id serial PRIMARY KEY,
      language_id int NOT NULL DEFAULT 0,
      name varchar(128) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

    INSERT INTO :table_return_orders_reason VALUES
    (1, 1, 'Non-compliant package'),
    (2, 1, 'Received Wrong Item'),
    (3, 1, 'Order Error'),
    (4, 1, 'Do not meet my expectations'),
    (5, 1, 'Others'),
    (1, 2, 'Colis non conforme'),
    (2, 2, 'Mauvais produit reçu'),
    (3, 2, 'Erreur commande'),
    (4, 2, 'Ne répond pas à mes attentes'),
    (5, 2, 'Autres');

    ALTER SEQUENCE :table_return_orders_reason_return_reason_id_seq RESTART WITH 11;
EOD;
      $CLICSHOPPING_Db->exec($sql);
    }

    $Qcheck = $CLICSHOPPING_Db->query("SELECT to_regclass(':table_return_orders_status')");

    if ($Qcheck->fetch() === false) {
      $sql = <<<EOD
    CREATE TABLE :table_return_orders_status (
      return_status_id serial PRIMARY KEY,
      language_id int NOT NULL DEFAULT 0,
      name varchar(32) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

    INSERT INTO :table_return_orders_status VALUES
    (1, 1, 'Pending'),
    (2, 1, 'Awaiting Products'),
    (3, 1, 'Complete'),
    (1, 2, 'En attente'),
    (2, 2, 'en attente du produit'),
    (3, 2, 'Complété');

    ALTER SEQUENCE :table_return_orders_status_return_status_id_seq RESTART WITH 7;
EOD;
      $CLICSHOPPING_Db->exec($sql);
    }

    $Qcheck = $CLICSHOPPING_Db->query("SELECT to_regclass(':table_return_orders_action')");

    if ($Qcheck->fetch() === false) {
      $sql = <<<EOD
    CREATE TABLE :table_return_orders_action (
      return_action_id serial PRIMARY KEY,
      language_id int NOT NULL DEFAULT 0,
      name varchar(64) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

    INSERT INTO :table_return_orders_action VALUES
    (1, 1, 'no action'),
    (2, 1, 'Refunded'),
    (3, 1, 'Credit Issued'),
    (4, 1, 'Replacement Sent'),
    (1, 2, 'Aucune action'),
    (2, 2, 'Remboursé'),
    (3, 2, 'Problème crédit'),
    (4, 2, 'Remplacement envoyé');

    ALTER SEQUENCE :table_return_orders_action_return_action_id_seq RESTART WITH 9;
EOD;
      $CLICSHOPPING_Db->exec($sql);
    }
  }
}
