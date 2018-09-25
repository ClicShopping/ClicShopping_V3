<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *
 *
 */

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;

  use ClicShopping\Sites\Shop\Tax;
  use ClicShopping\Sites\Shop\Shipping as Delivery;

  class cs_checkout_shipping_listing {
    public $code;
    public $group;
    public $title;
    public $description;
    public $sort_order;
    public $enabled = false;

    public function __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_checkout_shipping_listing_title');
      $this->description = CLICSHOPPING::getDef('module_checkout_shipping_listing_description');

      if (defined('MODULE_CHECKOUT_SHIPPING_LISTING_STATUS')) {
        $this->sort_order = MODULE_CHECKOUT_SHIPPING_LISTING_SORT_ORDER;
        $this->enabled = (MODULE_CHECKOUT_SHIPPING_LISTING_STATUS == 'True');
      }
     }

    public function execute() {
      global $quotes, $free_shipping, $shipping;

      $CLICSHOPPING_Currencies = Registry::get('Currencies');
      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_ShoppingCart = Registry::get('ShoppingCart');

      if (!Registry::exists('Shipping')) {
        Registry::set('Shipping', new Delivery());
      }

      $CLICSHOPPING_Shipping = Registry::get('Shipping');

      if (isset($_GET['Checkout']) && isset($_GET['Shipping'])) {

        $content_width = (int)MODULE_CHECKOUT_SHIPPING_LISTING_CONTENT_WIDTH;

        $shipping_listing = '<!-- start checkout_shipping_listing -->'. "\n";

        if ($CLICSHOPPING_Shipping->geCountShippingModules() > 0) {

          $data = '<div class="separator"></div>';
          $data .= '<span class="page-header moduleCheckoutShippingListingPageHeader"><h3>' . CLICSHOPPING::getDef('module_checkout_shipping_table_heading_shipping_method') . '</h3></span>';

          if (count($quotes) > 1 && count($quotes[0]) > 1) {
            $data .= '<div>';
            $data .= '<span class="col-md-8 text-md-left moduleCheckoutShippingListingMethod">' . CLICSHOPPING::getDef('module_checkout_shipping_text_choose_shipping_method') . '</span>';
            $data .= '<span class="col-md-4 text-md-right float-md-right moduleCheckoutShippingListingSelect">' . CLICSHOPPING::getDef('module_checkout_shipping_title_please_select') . '</span>';
            $data .= '</div>';
            $data .= '<div class="separator"></div>';

          } elseif ($free_shipping === false) {
            $data .= '<div class="separator"></div>';
            $data .= '<div class="moduleCheckoutShippingListingInformation">' . CLICSHOPPING::getDef('module_checkout_shipping_text_enter_shipping_information') . '</div>';
          }

          $data .= '<div class="separator"></div>';
          $data .= '<table class="table table-striped table-sm table-hover">';
          $data .= '<tbody>';

          if ($free_shipping === true) {

            $data .= '<div class="moduleCheckoutShippingListingFreeTitle">' . CLICSHOPPING::getDef('module_checkout_shipping_free_shipping_title') . '&nbsp;' . $quotes[$i]['icon'] . '</div>';
            $data .= '<div style="padding-left: 15px;">';
            $data .= CLICSHOPPING::getDef('module_checkout_shipping_free_shipping_description', ['free_shipping_amount' => $CLICSHOPPING_Currencies->format(MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING_OVER)]) .  HTML::hiddenField('shipping', 'free_free');
            $data .= '</div>';

          } else {
// load the selected shipping module
            foreach ($quotes as $n => $quote) {
              if (is_array($quote['methods'])) {
                for ($j=0, $n2=count($quote['methods']); $j<$n2; $j++) {
                  $checked = (($quote['id'] . '_' . $quote['methods'][$j]['id'] == $_SESSION['shipping']['id']) ? true : false);

                  $data .= '<tr>' . "\n";
                  $data .= '<tr>';
                  $data .= '<td>';

                  $data .= '<strong>'. $quote['module'] . '</strong>';

                  if (isset($quote['icon']) && !empty($quote['icon'])) {
                    $data .=  '&nbsp;' . $quote['icon'];
                  }

                  if (isset($quote['error'])) {
                    $data .= '<div class="form-text">' . $quote['error'] . '</div>';
                  }

                  $data .= '</td>';



                  if ( ($n >= 1) || ($n2 >= 1) ) {
                    $data .= '<td class="text-md-right">';

                    if (isset($quotes['error'])) {
                      $data .= '&nbsp;';
                    } else {
                      $data .=  '<span class="moduleCheckoutShippingListingCurrencies">'. $CLICSHOPPING_Currencies->format(Tax::addTax($quote['methods'][$j]['cost'], (isset($quote['tax']) ? $quote['tax'] : 0))) . '</span>&nbsp;&nbsp';
                      $data .=  '<span class="moduleCheckoutShippingListingRadio">'  . HTML::radioField('shipping', $quote['id'] . '_' . $quote['methods'][$j]['id'], $checked, 'required aria-required="true"') .'</span>';
                    }

                    $data .= '</td>';
                    $data .= '</tr>';

                  } else {
                    $data .= '<td class="text-md-right">';
                    $data .=  $CLICSHOPPING_Currencies->format(Tax::addTax($quote['methods'][$j]['cost'], (isset($quote['tax']) ? $quote['tax'] : 0))) .  HTML::hiddenField('shipping', $quote['id'] . '_' . $quote['methods'][$j]['id']);
                    $data .= '</td>';
                  }

                  $data .= '</tr>';
                }
              }
            }
          }

          $data .= '</tbody>';
          $data .= '</table>';
        }

        ob_start();
        require($CLICSHOPPING_Template->getTemplateModules($this->group . '/content/checkout_shipping_listing'));

        $shipping_listing .= ob_get_clean();

        $shipping_listing .= '<!--  end checkout_shipping_listing -->' . "\n";

        $CLICSHOPPING_Template->addBlock($shipping_listing, $this->group);
      }
    } // public function execute

    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return defined('MODULE_CHECKOUT_SHIPPING_LISTING_STATUS');
    }

    public function install() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want activate this module ?',
          'configuration_key' => 'MODULE_CHECKOUT_SHIPPING_LISTING_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Do you want activate this module in your shop ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please select the width of the module',
          'configuration_key' => 'MODULE_CHECKOUT_SHIPPING_LISTING_CONTENT_WIDTH',
          'configuration_value' => '12',
          'configuration_description' => 'Select a number between 1 and 12',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_content_module_width_pull_down',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Sort order',
          'configuration_key' => 'MODULE_CHECKOUT_SHIPPING_LISTING_SORT_ORDER',
          'configuration_value' => '30',
          'configuration_description' => 'Sort order of display. Lowest is displayed first',
          'configuration_group_id' => '6',
          'sort_order' => '4',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      return $CLICSHOPPING_Db->save('configuration', ['configuration_value' => '1'],
                                               ['configuration_key' => 'WEBSITE_MODULE_INSTALLED']
      );

    }

    public function remove() {
      return Registry::get('Db')->exec('delete from :table_configuration where configuration_key in ("' . implode('", "', $this->keys()) . '")');
    }

    public function keys() {
      return array (
        'MODULE_CHECKOUT_SHIPPING_LISTING_STATUS',
        'MODULE_CHECKOUT_SHIPPING_LISTING_CONTENT_WIDTH',
        'MODULE_CHECKOUT_SHIPPING_LISTING_SORT_ORDER'
      );
    }
  }
