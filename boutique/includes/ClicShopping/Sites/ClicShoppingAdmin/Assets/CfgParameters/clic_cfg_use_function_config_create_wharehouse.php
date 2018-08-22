<?php
  /**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *
 *
 */

  use ClicShopping\OM\Registry;

/**
 * Wharehouse and Inventory creation
 *
 * @param string
 * @return string
 * @access public
 */

  function clic_cfg_use_function_config_create_wharehouse() {

    $CLICSHOPPING_ODOO = Registry::get('Odoo');
    $CLICSHOPPING_Db = Registry::get('Db');

    if ((defined('CLICSHOPPING_APP_WEBSERVICE_ODOO_WHAREHOUSE_CONFIG') && CLICSHOPPING_APP_WEBSERVICE_ODOO_WHAREHOUSE_CONFIG == 'configure') && (defined('CLICSHOPPING_APP_WEBSERVICE_ODOO_ACTIVATE_WEB_SERVICE') && CLICSHOPPING_APP_WEBSERVICE_ODOO_ACTIVATE_WEB_SERVICE == 'True')) {


      $company_id = $CLICSHOPPING_ODOO->getSearchCompanyIdOdoo();

      $date = date("Y-m-d H:i:s");

// Stock Management
      $ids = $CLICSHOPPING_ODOO->odooSearchByTwoCriteria('company_id', '=', $company_id, 'stock.warehouse', 'int', 'name', '=',  'ClicShopping', 'string');
// read id company odoo
      $field_list = array('id');

      $company_id_wharehouse = $CLICSHOPPING_ODOO->readOdoo($ids, $field_list, 'stock.warehouse');
      $company_id_wharehouse = $company_id_wharehouse[0][id];

      if (empty($company_id_wharehouse)) {

        $values = ["name" => new xmlrpcval('ClicShopping', "string"),
                   "code" => new xmlrpcval('CL', "string"),
                   "partner_id" => new xmlrpcval($company_id, "int"),
                   ];

        $CLICSHOPPING_ODOO->createOdoo($values, "stock.warehouse");

      } else {

        $values = ["name" => new xmlrpcval('ClicShopping', "string"),
                   "code" => new xmlrpcval('CL', "string"),
                   "partner_id" => new xmlrpcval($company_id, "int"),
                  ];

        $CLICSHOPPING_ODOO->updateOdoo($company_id_wharehouse, $values, "stock.warehouse");

      }

// Inventory creation
// search location name and id in stock location
      $ids = $CLICSHOPPING_ODOO->odooSearch('name', '=', "CL", 'stock.location');

      $field_list = ['id',
                     'location_id',
                     'name',
                    ];

      $Qstock_location = $CLICSHOPPING_ODOO->readOdoo($ids, $field_list, 'stock.location');
      $stock_location_id = $Qstock_location[0][id];
      $stock_location_name = $Qstock_location[0][name];

      $stock_location_id = $stock_location_id + 1;

      $values = ["name" => new xmlrpcval("ClicShopping", "string"),
                  "date" => new xmlrpcval($date, "string"),
                  "company_id" => new xmlrpcval($company_id, "int"),
                  "location_id" => new xmlrpcval($stock_location_id, "int"),
                  "filter" => new xmlrpcval('none', "string"),
                ];

      $CLICSHOPPING_ODOO->createOdoo($values, "stock.inventory");


      $Qupdate = $CLICSHOPPING_Db->prepare('update :table_configuration
                                      set configuration_value = :configuration_value
                                      where configuration_key = :configuration_key
                                    ');
      $Qupdate->bindValue(':configuration_value', 'false');
      $Qupdate->bindValue(':configuration_key', 'CLICSHOPPING_APP_WEBSERVICE_ODOO_WHAREHOUSE_CONFIG');
      $Qupdate->execute();
    }
  }