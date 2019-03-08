<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

  namespace ClicShopping\Apps\OrderTotal\TotalTax\Module\Total;

  use ClicShopping\OM\Registry;

  use ClicShopping\Sites\Common\B2BCommon;

  use ClicShopping\Apps\OrderTotal\TotalTax\TotalTax as TotalTaxApp;


  class TX implements \ClicShopping\OM\Modules\OrderTotalInterface  {

    public $code;
    public $title;
    public $description;
    public $enabled;
    public $output;
    public $sort_order = 0;
    public $app;
    public $surcharge;
    public $maximum;

    public function __construct() {

      if (!Registry::exists('TotalTax')) {
        Registry::set('TotalTax', new TotalTaxApp());
      }

      $this->app = Registry::get('TotalTax');
      $this->app->loadDefinitions('Module/Shop/TX/TX');

      $this->signature = 'Tax|' . $this->app->getVersion() . '|1.0';
      $this->api_version = $this->app->getApiVersion();

      $this->code = 'TX';
      $this->title = $this->app->getDef('modulemodule_tx_title');
      $this->public_title = $this->app->getDef('modulemodule_tx_public_title');


// Controle en B2B l'assujetti a la TVA (valeur true par defaut en mode B2C)
      if ( B2BCommon::getTaxUnallowed($this->code) ) {
        $this->enabled = defined('CLICSHOPPING_APP_ORDER_TOTAL_TAX_TX_STATUS') && (CLICSHOPPING_APP_ORDER_TOTAL_TAX_TX_STATUS == 'True') ? true : false;
      }

      $this->sort_order = defined('CLICSHOPPING_APP_ORDER_TOTAL_TAX_TX_SORT_ORDER') && ((int)CLICSHOPPING_APP_ORDER_TOTAL_TAX_TX_SORT_ORDER > 0) ? (int)CLICSHOPPING_APP_ORDER_TOTAL_TAX_TX_SORT_ORDER : 0;

      $this->output = [];
    }

    public function process() {
      $CLICSHOPPING_Order = Registry::get('Order');
      $CLICSHOPPING_Currencies = Registry::get('Currencies');
      $CLICSHOPPING_Db = Registry::get('Db');

// Txe Canada - Quebec
      if (DISPLAY_DOUBLE_TAXE == 'true') {
  //WARNING: This module does not consider tax_class!!! We assume everything is taxable.
  //We run SQL to get total number of taxes configured for our shipping zone
  //If we have many taxes rates configured, we will compare tax priorities.
  //If taxes have different priorities, ot_tax will apply 2nd priority tax_rate over 1rst (ex: for Quebec we have PST over GST), we assume GST has the lowest priority.
  //If taxes have the same priorities, ot_tax still show taxes on two line but dosen't apply compounded taxes (ie: Ontario)
  //If we get only one tax result, we assume we are handling only GST or HST (same scenario)
  // take also the shipping taxe

        $QtaxPriority = $CLICSHOPPING_Db->prepare('select tax_priority
                                           from :table_tax_rates tr left join :table_zones_to_geo_zones za on (tr.tax_zone_id = za.geo_zone_id)
                                                                    left join :table_geo_zones tz on (tz.geo_zone_id = tr.tax_zone_id)
                                           where za.zone_country_id = :zone_country_id
                                           and za.zone_id = :zone_id
                                           order by tr.tax_priority
                                          ');
        $QtaxPriority->bindInt(':zone_country_id', $CLICSHOPPING_Order->delivery['country']['id']);
        $QtaxPriority->bindInt(':zone_id', $CLICSHOPPING_Order->delivery['zone_id'] );
        $QtaxPriority->execute();

        $Qtax = $CLICSHOPPING_Db->prepare('select tax_rates_id,
                                          tax_priority,
                                          tax_rate,
                                          tax_description
                                   from :table_tax_rates tr left join :table_zones_to_geo_zones za on (tr.tax_zone_id = za.geo_zone_id)
                                                             left join :table_geo_zones tz on (tz.geo_zone_id = tr.tax_zone_id)
                                   where za.zone_country_id = :zone_country_id
                                   and za.zone_id = :zone_id
                                   order by tr.tax_priority
                                ');
        $Qtax->bindInt(':zone_country_id', $CLICSHOPPING_Order->delivery['country']['id']);
        $Qtax->bindInt(':zone_id', $CLICSHOPPING_Order->delivery['zone_id'] );
        $Qtax->execute();

        if ($QtaxPriority->fetch() !== false) {

          if ($QtaxPriority->rowCount() == 2) { //Show taxes on two lines
            $i=0;

            while ($QtaxPriority->fetch() ) { //compare tax_priotiries
              if ($i == 0) {
                $tax_priority = $QtaxPriority->valueInt('tax_priority');
              } else {
                if ($tax_priority != $QtaxPriority->valueInt('tax_priority')) {
                  $compound_tax=true;
                } else {
                  $compound_tax=false;
                }
              }
              $i++;
            }
//END Compare tax priorities

          if ($compound_tax) { //ie Quebec different de false et true

             $j=0;

             while ($Qtax->fetch() ) {

               if ($j == 0) {
                 $gst_description = $Qtax->value('tax_description');
                 $gst_rate = $Qtax->valueDecimal('tax_rate') / 100;
               } elseif ($j >= 1) {
                 $pst_description = $Qtax->value('tax_description');
                 $pst_rate = $Qtax->valueDecimal('tax_rate') / 100;
               }
               $j++;
             }

             $subtotal = $CLICSHOPPING_Order->info['subtotal'] + $CLICSHOPPING_Order->info['shipping_cost'];

// Si l'ordre d'affichage du shipping < sort order on additionne les frais d'envoi au sous total
             if (MODULE_ORDER_TOTAL_SHIPPING_SORT_ORDER < MODULE_ORDER_TOTAL_TAX_SORT_ORDER) $subtotal += $CLICSHOPPING_Order->info['shipping_cost'];

             $gst_total = round($subtotal * $gst_rate, $CLICSHOPPING_Currencies->currencies[DEFAULT_CURRENCY]['decimal_places']);
             $pst_total = ($subtotal+$gst_total) * $pst_rate;

             foreach ( $CLICSHOPPING_Order->info['tax_groups'] as $key => $value ) {

               if ($value > 0) {
                 $this->output[] = array('title' => $gst_description.':',
                                         'text' => $CLICSHOPPING_Currencies->format( $gst_total, true, $CLICSHOPPING_Order->info['currency'], $CLICSHOPPING_Order->info['currency_value']), 'value' => $gst_total);
                 $this->output[] = array('title' => $pst_description.':',
                                         'text' => $CLICSHOPPING_Currencies->format( $pst_total, true, $CLICSHOPPING_Order->info['currency'], $CLICSHOPPING_Order->info['currency_value']),
                                         'value' => $pst_total);
               }
             } // end while
          } else { //ie: Ontario
            $j=0;

            while ($Qtax->fetch() ) {

              if ($j == 0) {
                $gst_description = $Qtax->value('tax_description');
                $gst_rate = $Qtax->valueDecimal('tax_rate') / 100;
              } elseif ($j >= 1) {
                $pst_description = $Qtax->value('tax_description');
                $pst_rate = $Qtax->valueDecimal('tax_rate') / 100;
              }

              $j++;
          }
            $subtotal = $CLICSHOPPING_Order->info['subtotal'];

// Si l'ordre d'affichage du shipping < sort order on additionne les frais d'envoi au sous total
          if (MODULE_ORDER_TOTAL_SHIPPING_SORT_ORDER < MODULE_ORDER_TOTAL_TAX_SORT_ORDER) $subtotal += $CLICSHOPPING_Order->info['shipping_cost'];

          $gst_total = round($subtotal * $gst_rate, $CLICSHOPPING_Currencies->currencies[DEFAULT_CURRENCY]['decimal_places']);
          $pst_total = $subtotal * $pst_rate;

          foreach ( $CLICSHOPPING_Order->info['tax_groups'] as $key => $value ) {
            if ($value > 0) {
              $this->output[] = array('title' => $gst_description.':',
                                      'text' => $CLICSHOPPING_Currencies->format($gst_total, true, $CLICSHOPPING_Order->info['currency'], $CLICSHOPPING_Order->info['currency_value']),
                                      'value' => $gst_total);
              $this->output[] = array('title' => $pst_description.':',
                                      'text' => $CLICSHOPPING_Currencies->format($pst_total, true, $CLICSHOPPING_Order->info['currency'], $CLICSHOPPING_Order->info['currency_value']),
                                       'value' => $pst_total);
            }
          }
        }
// -----------------------------
// Only one taxe
// -----------------------------
//

      } elseif ($QtaxPriority->rowCount() == 1) { //Only GST or HST applies

        while ($Qtax->fetch() ) {

          $subtotal = $CLICSHOPPING_Order->info['subtotal'];

// Si l'ordre d'affichage du shipping < sort order on additionne les frais d'envoi au sous total
          if (MODULE_ORDER_TOTAL_SHIPPING_SORT_ORDER < MODULE_ORDER_TOTAL_TAX_SORT_ORDER) $subtotal += $CLICSHOPPING_Order->info['shipping_cost'];

          $hst_total = $subtotal * ($Qtax->valueDecimal('tax_rate') / 100);


          foreach ( $CLICSHOPPING_Order->info['tax_groups'] as $key => $value ) {
            if ($value > 0) {
              $this->output[] = array('title' => $Qtax->value('tax_description') . ' : ',
                                      'text' => $CLICSHOPPING_Currencies->format($hst_total, true, $CLICSHOPPING_Order->info['currency'], $CLICSHOPPING_Order->info['currency_value']),'value' => $hst_total
                                     );
            }
          }
        }
      } // end elseif
    }

//We calculate $CLICSHOPPING_Order->info with updated tax values. For this to work ot_tax has to be last ot module called, just before ot_total
    $CLICSHOPPING_Order->info['tax'] = round($gst_total, $CLICSHOPPING_Currencies->currencies[DEFAULT_CURRENCY]['decimal_places']) + round($pst_total,$CLICSHOPPING_Currencies->currencies[DEFAULT_CURRENCY]['decimal_places']) + round($hst_total,$CLICSHOPPING_Currencies->currencies[DEFAULT_CURRENCY]['decimal_places']);
    $CLICSHOPPING_Order->info['total'] = $CLICSHOPPING_Order->info['subtotal'] + $CLICSHOPPING_Order->info['tax'] + $CLICSHOPPING_Order->info['shipping_cost'];

    } else {
// **********************************
// normal tax
// ************************************

        foreach ( $CLICSHOPPING_Order->info['tax_groups'] as $key => $value ) {
          if ($value > 0) {
            $this->output[] = ['title' => $key,
                               'text' => $CLICSHOPPING_Currencies->format($value, true, $CLICSHOPPING_Order->info['currency'], $CLICSHOPPING_Order->info['currency_value']),
                               'value' => $value
                              ];
          }
      }
    }
  }

    public function check() {
      return defined('CLICSHOPPING_APP_ORDER_TOTAL_TAX_TX_STATUS') && (trim(CLICSHOPPING_APP_ORDER_TOTAL_TAX_TX_STATUS) != '');
    }

    public function install() {
      $this->app->redirect('Configure&Install&module=TX');
    }

    public function remove() {
      $this->app->redirect('Configure&Uninstall&module=TX');
    }

    public function keys() {
      return array('CLICSHOPPING_APP_ORDER_TOTAL_TAX_TX_SORT_ORDER');
    }
  }
