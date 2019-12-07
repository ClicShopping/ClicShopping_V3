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


  namespace ClicShopping\Apps\Marketing\Specials\Sites\ClicShoppingAdmin\Pages\Home\Actions\Specials;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  class Insert extends \ClicShopping\OM\PagesActionsAbstract
  {
    public function execute()
    {

      $CLICSHOPPING_Specials = Registry::get('Specials');
      $CLICSHOPPING_Hooks = Registry::get('Hooks');

      $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? HTML::sanitize($_GET['page']) : 1;

      $products_id = HTML::sanitize($_POST['products_id']);
      $specials_price = HTML::sanitize($_POST['specials_price']);
      $status = 1;

      if (!empty($_POST['expdate'])) {
        $expdate = HTML::sanitize($_POST['expdate']);
      } else {
        $expdate = null;
      }

      if (!empty($_POST['schdate'])) {
        $schdate = HTML::sanitize($_POST['schdate']);

        $date1 = new \DateTime(date('Y-m-d'));
        $date2 = new \DateTime($schdate);

        if ($date1 < $date2) {
          $status = 0;
        }
      } else {
        $schdate = null;
      }

      if (isset($_POST['flash_discount'])) {
        $flash_discount = 1;
      } else {
        $flash_discount = 0;
      }

      if (substr($specials_price, -1) == '%') {
        $specials_price = str_replace('%', '', $specials_price);

        $Qproduct = $CLICSHOPPING_Specials->db->get('products', 'products_price', ['products_id' => (int)$products_id]);

        $products_price = $Qproduct->valueDecimal('products_price');
        $discount = ($specials_price / 100) * $products_price;
        $specials_price = $products_price - $discount;
      }

       $CLICSHOPPING_Specials->db->save('specials', ['products_id' => (int)$products_id,
          'specials_new_products_price' => (float)$specials_price,
          'specials_date_added' => 'now()',
          'scheduled_date' => $schdate,
          'expires_date' => $expdate,
          'status' => $status,
          'flash_discount' => (int)$flash_discount
        ]
      );

      $CLICSHOPPING_Hooks->call('Specials', 'Insert');

      $CLICSHOPPING_Specials->redirect('Specials', 'page=' . $page);
    }
  }