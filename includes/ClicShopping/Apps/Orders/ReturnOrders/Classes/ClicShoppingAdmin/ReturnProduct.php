<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Orders\ReturnOrders\Classes\ClicShoppingAdmin;

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

class ReturnProduct
{
  /**
   * @param int|null $id
   * @return string
   */
  public static function getDropDownReasonOpened(?int $id): string
  {
    $CLICSHOPPING_ReturnOrders = Registry::get('ReturnOrders');

    $return_opened_array = [
      ['id' => '0', 'text' => $CLICSHOPPING_ReturnOrders->getDef('text_opened')],
      ['id' => '1', 'text' => $CLICSHOPPING_ReturnOrders->getDef('text_unopened')]
    ];

    $dropdown = HTML::selectField('return_reason_opened', $return_opened_array, $id);

    return $dropdown;
  }

  /**
   * @param int|null $id
   * @return string
   */
  public static function getDropDownAction(?int $id): string
  {
    $CLICSHOPPING_ReturnOrders = Registry::get('ReturnOrders');
    $CLICSHOPPING_language = Registry::get('Language');

    $Qaction = $CLICSHOPPING_ReturnOrders->db->prepare('select return_action_id,
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

    $dropdown = HTML::selectField('return_action', $return_action_array, $id);

    return $dropdown;
  }

  /**
   * @param int|null $id
   * @return string
   */
  public static function getDropDownStatus(?int $id): string
  {
    $CLICSHOPPING_ReturnOrders = Registry::get('ReturnOrders');
    $CLICSHOPPING_Language = Registry::get('Language');

    $Qstatus = $CLICSHOPPING_ReturnOrders->db->prepare('select return_status_id,
                                                                 language_id,
                                                                 name
                                                          from :table_return_orders_status
                                                          where language_id = :language_id
                                                          ');
    $Qstatus->bindInt(':language_id', $CLICSHOPPING_Language->getId());
    $Qstatus->execute();

    $return_action_array = [];

    while ($Qstatus->fetch()) {
      $return_action_array[] = [
        'id' => $Qstatus->valueInt('return_status_id'),
        'text' => $Qstatus->value('name')
      ];
    }

    $dropdown = HTML::selectField('return_status', $return_action_array, $id);

    return $dropdown;
  }
}