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

  namespace ClicShopping\Apps\Shipping\Item\Module\ClicShoppingAdmin\Config\IT\Params;

  use ClicShopping\OM\HTML;

  class zone extends \ClicShopping\Apps\Shipping\Item\Module\ClicShoppingAdmin\Config\ConfigParamAbstract
  {
    public $default = '0';
    public $sort_order = 500;

    protected function init()
    {
      $this->title = $this->app->getDef('cfg_item_zone_title');
      $this->description = $this->app->getDef('cfg_item_zone_desc');
    }

    public function getInputField()
    {
      $zone_class_array = [
        [
          'id' => '0',
          'text' => $this->app->getDef('cfg_item_zone_none')
        ]
      ];

      $Qclasses = $this->app->db->get('geo_zones', [
        'geo_zone_id',
        'geo_zone_name'
      ], null, 'geo_zone_name'
      );

      while ($Qclasses->fetch()) {
        $zone_class_array[] = [
          'id' => $Qclasses->valueInt('geo_zone_id'),
          'text' => $Qclasses->value('geo_zone_name')
        ];
      }

      $input = HTML::selectField($this->key, $zone_class_array, $this->getInputValue());

      return $input;
    }
  }
