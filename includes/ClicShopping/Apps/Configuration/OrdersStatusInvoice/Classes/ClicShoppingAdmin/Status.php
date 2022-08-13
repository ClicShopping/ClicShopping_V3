<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Configuration\OrdersStatusInvoice\Classes\ClicShoppingAdmin;

  use ClicShopping\OM\Registry;

  class Status
  {

    protected $orders_status_tracking_id;
    protected $language_id;

    /**
     * the name of status invoice
     * @param int $orders_status_invoice_id
     * @param int $language_id
     * @return string
     */
    Public static function getOrdersStatusInvoiceName(int $orders_status_invoice_id, int  $language_id) :string
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      if (!$language_id) $language_id = $CLICSHOPPING_Language->getId();

      $Qinvoice = $CLICSHOPPING_Db->prepare('select orders_status_invoice_name
                                       from :table_orders_status_invoice
                                       where orders_status_invoice_id = :orders_status_invoice_id
                                       and language_id =:language_id
                                    ');
      $Qinvoice->bindInt(':orders_status_invoice_id', (int)$orders_status_invoice_id);
      $Qinvoice->bindInt(':language_id', (int)$language_id);

      $Qinvoice->execute();

      return $Qinvoice->value('orders_status_invoice_name');
    }

    /**
     * Array of the name of status invoice
     * @return array
     */
    Public static function getOrdersInvoiceStatus(): array
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');

      $orders_status_invoice_array = [];

      $Qinvoice = $CLICSHOPPING_Db->prepare('select orders_status_invoice_id,
                                                    orders_status_invoice_name
                                             from :table_orders_status_invoice
                                             where language_id = :language_id
                                             order by orders_status_invoice_id
                                            ');
      $Qinvoice->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId());

      $Qinvoice->execute();

      while ($orders_invoice_status = $Qinvoice->fetch()) {
        $orders_status_invoice_array[] = [
          'id' => $orders_invoice_status['orders_status_invoice_id'],
          'text' => $orders_invoice_status['orders_status_invoice_name']
        ];
      }

      return $orders_status_invoice_array;
    }
  }
