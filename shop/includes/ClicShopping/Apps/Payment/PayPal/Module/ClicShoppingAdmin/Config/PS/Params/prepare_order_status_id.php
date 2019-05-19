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
  use ClicShopping\OM\Registry;

  class prepare_order_status_id extends \ClicShopping\Apps\Payment\PayPal\Module\ClicShoppingAdmin\Config\ConfigParamAbstract
  {
    public $default = '0';
    public $sort_order = 400;

    protected function init()
    {
      $CLICSHOPPING_Language = Registry::get('Language');

      $this->title = $this->app->getDef('cfg_ps_prepare_order_status_id_title');
      $this->description = $this->app->getDef('cfg_ps_prepare_order_status_id_desc');

      if (!defined('CLICSHOPPING_APP_PAYPAL_PS_PREPARE_ORDER_STATUS_ID') || (strlen(CLICSHOPPING_APP_PAYPAL_PS_PREPARE_ORDER_STATUS_ID) < 1)) {
        $Qcheck = $this->app->db->get('orders_status', 'orders_status_id', ['orders_status_name' => 'Preparing [PayPal Standard]'], null, 1);

        if ($Qcheck->fetch() === false) {
          $Qstatus = $this->app->db->get('orders_status', 'max(orders_status_id) as status_id');

          $status_id = $Qstatus->valueInt('status_id') + 1;

          $languages = $CLICSHOPPING_Language->getLanguages();

          foreach ($languages as $lang) {
            $this->app->db->save('orders_status', [
                'orders_status_id' => $status_id,
                'language_id' => $lang['id'],
                'orders_status_name' => 'Preparing [PayPal Standard]',
                'public_flag' => '0',
                'downloads_flag' => '0'
              ]
            );
          }
        } else {
          $status_id = $Qcheck->valueInt('orders_status_id');
        }
      } else {
        $status_id = CLICSHOPPING_APP_PAYPAL_PS_PREPARE_ORDER_STATUS_ID;
      }

      $this->default = $status_id;
    }

    public function getInputField()
    {
      $statuses_array = [
        [
          'id' => '0',
          'text' => $this->app->getDef('cfg_ps_prepare_order_status_id_default')
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
