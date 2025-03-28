<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Payment\MoneyOrder\Module\ClicShoppingAdmin\Config\MO\Params;

use ClicShopping\OM\HTML;

class zone extends \ClicShopping\Apps\Payment\MoneyOrder\Module\ClicShoppingAdmin\Config\ConfigParamAbstract
{
  public $default = '0';
  public int|null $sort_order = 500;

  protected function init()
  {
    $this->title = $this->app->getDef('cfg_moneyorder_zone_title');
    $this->description = $this->app->getDef('cfg_moneyorder_zone_desc');
  }

  public function getInputField()
  {
    $zone_class_array = [
      [
        'id' => '0',
        'text' => $this->app->getDef('cfg_moneyorder_zone_global')
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
