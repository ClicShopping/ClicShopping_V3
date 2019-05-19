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

  namespace ClicShopping\Apps\Payment\PayPal\Module\ClicShoppingAdmin\Config\PS\Params;

  use ClicShopping\OM\HTML;

  class order_status_id extends \ClicShopping\Apps\Payment\PayPal\Module\ClicShoppingAdmin\Config\ConfigParamAbstract
  {
    public $default = '0';
    public $sort_order = 500;

    protected function init()
    {
      $this->title = $this->app->getDef('cfg_ps_order_status_id_title');
      $this->description = $this->app->getDef('cfg_ps_order_status_id_desc');
    }

    public function getInputField()
    {
      $statuses_array = [
        [
          'id' => '0',
          'text' => $this->app->getDef('cfg_ps_order_status_id_default')
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
