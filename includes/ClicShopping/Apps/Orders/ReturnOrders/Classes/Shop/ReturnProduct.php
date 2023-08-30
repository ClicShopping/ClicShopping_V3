<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Orders\ReturnOrders\Classes\Shop;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

class ReturnProduct
{
  /**
   * @return string
   */
  public static function getDropDownReasonOpened(): string
  {
    $return_opened_array = [
      ['id' => '0', 'text' => CLICSHOPPING::getDef('text_opened')],
      ['id' => '1', 'text' => CLICSHOPPING::getDef('text_unopened')]
    ];

    $dropdown = HTML::selectField('return_reason_opened', $return_opened_array);

    return $dropdown;
  }

  /**
   * @return string
   */
  public static function getDropDownReason(): string
  {
    $CLICSHOPPING_Db = Registry::get('Db');
    $CLICSHOPPING_language = Registry::get('Language');

    $Qreason = $CLICSHOPPING_Db->prepare('select return_reason_id,
                                                   language_id,
                                                   name
                                              from :table_return_orders_reason
                                              where language_id = :language_id
                                              ');
    $Qreason->bindInt(':language_id', $CLICSHOPPING_language->getId());
    $Qreason->execute();

    $return_reason_array = [];

    while ($Qreason->fetch()) {
      $return_reason_array[] = [
        'id' => $Qreason->valueInt('return_reason_id'),
        'text' => $Qreason->value('name')
      ];
    }

    $dropdown = HTML::selectField('return_reason', $return_reason_array);


    return $dropdown;
  }

  /**
   * @return string
   */
  public static function getDropDownAction(): string
  {
    $CLICSHOPPING_Db = Registry::get('Db');
    $CLICSHOPPING_language = Registry::get('Language');

    $Qaction = $CLICSHOPPING_Db->prepare('select return_action_id,
                                                     language_id,
                                                     name
                                              from :table_return_orders_action
                                              where language_id = :language_id
                                              ');
    $Qaction->bindInt(':language_id', $CLICSHOPPING_language->getId());
    $Qaction->execute();

    $return_action_array = [];

    while ($Qaction->fetch()) {
      $return_action_array[] = [
        'id' => $Qaction->valueInt('return_action_id'),
        'text' => $Qaction->value('name')
      ];
    }

    $dropdown = HTML::selectField('return_reason_action', $return_action_array);

    return $dropdown;
  }

  /**
   * @param int $order_id
   * @return array
   */
  public static function getInfoCustomer(int $order_id): array
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    $QordersInfo = $CLICSHOPPING_Db->prepare('select orders_id,
                                                         customers_id,
                                                         customers_name,
                                                         customers_company,
                                                         customers_street_address,
                                                         customers_suburb,
                                                         customers_city,
                                                         customers_postcode,
                                                         customers_state,
                                                         customers_country,
                                                         customers_telephone,
                                                         customers_email_address,
                                                         delivery_name,
                                                         delivery_street_address,
                                                         delivery_suburb,
                                                         delivery_city,
                                                         delivery_postcode,
                                                         delivery_state,
                                                         delivery_country,
                                                         date_purchased
                                          from :table_orders
                                          where orders_id = :orders_id
                                         ');
    $QordersInfo->bindInt(':orders_id', (int)$order_id);
    $QordersInfo->execute();

    $result = $QordersInfo->fetch();

    return $result;
  }

  /**
   * @return array
   */
  public static function getListing(): array
  {
    $CLICSHOPPING_Customer = Registry::get('CustomerShop');
    $CLICSHOPPING_Db = Registry::get('Db');

    $QreturnInfo = $CLICSHOPPING_Db->prepare('select return_id,
                                                       return_ref,
                                                       comment,
                                                       date_added,
                                                       product_id,
                                                       order_id,
                                                       return_status_id,
                                                       product_name,
                                                       product_model
                                                from :table_return_orders
                                                where customer_id = :customer_id
                                               ');
    $QreturnInfo->bindInt(':customer_id', $CLICSHOPPING_Customer->getId());
    $QreturnInfo->execute();

    $result = $QreturnInfo->fetch();

    return $result;
  }

  /**
   * @param int $order_id
   * @param int $product_id
   * @return array
   */
  public static function removeButtonHistoryInfo(int $order_id, int $product_id): array
  {
    $CLICSHOPPING_Db = Registry::get('Db');
    $order_id = HTML::sanitize($_GET['order_id']);

    $Qremove = $CLICSHOPPING_Db->prepare('select return_id,
                                                  opened,
                                                  return_status_id  
                                           from :table_return_orders
                                           where order_id = :order_id
                                           and product_id = :product_id
                                          ');
    $Qremove->bindInt(':order_id', $order_id);
    $Qremove->bindInt(':product_id', $product_id);

    $Qremove->execute();

    $value = [
      'return_id' => $Qremove->valueInt('return_id'),
      'opened' => $Qremove->valueInt('opened'),
      'return_status_id' => $Qremove->valueInt('return_status_id'),
    ];

    return $value;
  }
}