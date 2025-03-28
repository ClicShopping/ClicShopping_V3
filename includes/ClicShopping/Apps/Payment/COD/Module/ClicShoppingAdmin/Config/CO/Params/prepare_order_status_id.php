<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Payment\COD\Module\ClicShoppingAdmin\Config\CO\Params;

use ClicShopping\OM\HTML;

class prepare_order_status_id extends \ClicShopping\Apps\Payment\COD\Module\ClicShoppingAdmin\Config\ConfigParamAbstract
{
  public $default = '0';
  public int|null $sort_order = 400;

  protected function init()
  {
    $this->title = $this->app->getDef('cfg_cod_prepare_order_status_id_title');
    $this->description = $this->app->getDef('cfg_cod_prepare_order_status_id_desc');

    $status_id = 1;

    $this->default = $status_id;
  }

  public function getInputField()
  {
    $statuses_array = [
      [
        'id' => '0',
        'text' => $this->app->getDef('cfg_cod_prepare_order_status_id_default')
      ]
    ];

    $Qstatuses = $this->app->db->get('orders_status', [
      'orders_status_id',
      'orders_status_name'
    ], [
      'language_id' => $this->app->lang->getId()
    ],
      'orders_status_name'
    );

    while ($Qstatuses->fetch()) {
      $statuses_array[] = [
        'id' => $Qstatuses->valueInt('orders_status_id'),
        'text' => $Qstatuses->value('orders_status_name')
      ];
    }

    $input = HTML::selectField($this->key, $statuses_array, $this->getInputValue());

    return $input;
  }
}
