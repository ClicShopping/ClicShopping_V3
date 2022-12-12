<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */


  namespace ClicShopping\Apps\Configuration\Api\Sites\ClicShoppingAdmin\Pages\Home\Actions\Api;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  
  class Insert extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected mixed $app;

    public function __construct()
    {
      $this->app = Registry::get('Api');
    }

    public function execute()
    {
      $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;

      $username = HTML::sanitize($_POST['username']);
      $api_key = HTML::sanitize($_POST['api_key']);

// products
      if (isset($_POST['get_product_status'])) {
        $get_product_status = HTML::sanitize($_POST['get_product_status']);
      } else {
        $get_product_status = 0;
      }

      if (isset($_POST['update_product_status'])) {
        $update_product_status = HTML::sanitize($_POST['update_product_status']);
      } else {
        $update_product_status = 0;
      }

      if (isset($_POST['insert_product_status'])) {
        $insert_product_status = HTML::sanitize($_POST['insert_product_status']);
      } else {
        $insert_product_status = 0;
      }


      if (isset($_POST['delete_product_status'])) {
        $delete_product_status = HTML::sanitize($_POST['delete_product_status']);
      } else {
        $delete_product_status = 0;
      }

// categories
      if (isset($_POST['get_categories_status'])) {
        $get_categories_status = HTML::sanitize($_POST['get_categories_status']);
      } else {
        $get_categories_status = 0;
      }

      if (isset($_POST['update_categories_status'])) {
        $update_categories_status = HTML::sanitize($_POST['update_categories_status']);
      } else {
        $update_categories_status = 0;
      }

      if (isset($_POST['insert_categories_status'])) {
        $insert_categories_status = HTML::sanitize($_POST['insert_categories_status']);
      } else {
        $insert_categories_status = 0;
      }

      if (isset($_POST['delete_categories_status'])) {
        $delete_categories_status = HTML::sanitize($_POST['delete_categories_status']);
      } else {
        $delete_categories_status = 0;
      }

// customer
      if (isset($_POST['get_customer_status'])) {
        $get_customer_status = HTML::sanitize($_POST['get_customer_status']);
      } else {
        $get_customer_status = 0;
      }

      if (isset($_POST['update_customer_status'])) {
        $update_customer_status = HTML::sanitize($_POST['update_customer_status']);
      } else {
        $update_customer_status = 0;
      }

      if (isset($_POST['insert_customer_status'])) {
        $insert_customer_status = HTML::sanitize($_POST['insert_customer_status']);
      } else {
        $insert_customer_status = 0;
      }

      if (isset($_POST['delete_customer_status'])) {
        $delete_customer_status = HTML::sanitize($_POST['delete_customer_status']);
      } else {
        $delete_customer_status = 0;
      }

      $sql_data_array = [
        'username' => $username,
        'api_key' => $api_key,
        'status' => 0,
        'date_added' => 'now()',
        'date_modified' => 'now()',
        'get_product_status' => $get_product_status,
        'update_product_status' => $update_product_status,
        'insert_product_status' => $insert_product_status,
        'delete_product_status' => $delete_product_status,
        'get_categories_status' => $get_categories_status,
        'update_categories_status' => $update_categories_status,
        'insert_categories_status' => $insert_categories_status,
        'delete_categories_status' => $delete_categories_status,
        'get_customer_status' => $get_customer_status,
        'update_customer_status' => $update_customer_status,
        'insert_customer_status' => $insert_customer_status,
        'delete_customer_status' => $delete_customer_status,
      ];

      $this->app->db->save('api', $sql_data_array);

      $this->app->redirect('Api&page=' . $page);
    }
  }