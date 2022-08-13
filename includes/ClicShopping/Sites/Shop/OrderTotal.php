<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Sites\Shop;

  use ClicShopping\OM\Apps;
  use ClicShopping\OM\Registry;

  class OrderTotal
  {
    public array $modules = [];

// class constructor
    public function __construct()
    {
      if (\defined('MODULE_ORDER_TOTAL_INSTALLED') && !\is_null(MODULE_ORDER_TOTAL_INSTALLED)) {
        $this->modules = explode(';', MODULE_ORDER_TOTAL_INSTALLED);

        foreach ($this->modules as $value) {
          if (str_contains($value, '\\')) {
            $class = Apps::getModuleClass($value, 'OrderTotal');

            Registry::set('OrderTotal_' . str_replace('\\', '_', $value), new $class);
          }
        }
      }
    }

    public function process()
    {
      $order_total_array = [];

      if (\is_array($this->modules)) {
        foreach ($this->modules as $value) {

          if (str_contains($value, '\\')) {
            $CLICSHOPPING_OTM = Registry::get('OrderTotal_' . str_replace('\\', '_', $value));
          }
          if ($CLICSHOPPING_OTM->enabled) {
            $CLICSHOPPING_OTM->output = [];
            $CLICSHOPPING_OTM->process();

            for ($i = 0, $n = \count($CLICSHOPPING_OTM->output); $i < $n; $i++) {
              if (!\is_null($CLICSHOPPING_OTM->output[$i]['title']) && !\is_null($CLICSHOPPING_OTM->output[$i]['text'])) {
                $order_total_array[] = [
                  'code' => $CLICSHOPPING_OTM->code,
                  'title' => $CLICSHOPPING_OTM->output[$i]['title'],
                  'text' => $CLICSHOPPING_OTM->output[$i]['text'],
                  'value' => $CLICSHOPPING_OTM->output[$i]['value'],
                  'sort_order' => $CLICSHOPPING_OTM->sort_order
                ];
              }
            }
          }
        }
      }

      return $order_total_array;
    }

    public function output()
    {
      $output_string = '';
      if (\is_array($this->modules)) {
        foreach ($this->modules as $value) {
          if (str_contains($value, '\\')) {
            $CLICSHOPPING_OTM = Registry::get('OrderTotal_' . str_replace('\\', '_', $value));
          }

          if ($CLICSHOPPING_OTM->enabled) {
            $size = \count($CLICSHOPPING_OTM->output);
            for ($i = 0; $i < $size; $i++) {
              $output_string .= '              <tr>' . "\n" .
                '                <td class="OrderTotalTitle">' . $CLICSHOPPING_OTM->output[$i]['title'] . '</td>' . "\n" .
                '                <td class="OrderTotalText">' . $CLICSHOPPING_OTM->output[$i]['text'] . '</td>' . "\n" .
                '              </tr>';
            }
          }
        }
      }

      return $output_string;
    }
  }