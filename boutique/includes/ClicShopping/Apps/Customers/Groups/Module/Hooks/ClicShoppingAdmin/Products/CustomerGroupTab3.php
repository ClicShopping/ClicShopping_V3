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

  namespace ClicShopping\Apps\Customers\Groups\Module\Hooks\ClicShoppingAdmin\Products;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Customers\Groups\Groups as GroupsApp;

  use ClicShopping\Apps\Configuration\ProductsQuantityUnit\Classes\ClicShoppingAdmin\ProductsQuantityUnitAdmin;

  class CustomerGroupTab3 implements \ClicShopping\OM\Modules\HooksInterface {
    protected $app;
    protected $qteUnit;

    public function __construct() {
      if (!Registry::exists('Groups')) {
        Registry::set('Groups', new GroupsApp());
      }

      if (!Registry::exists('ProductsQuantityUnitAdmin')) {
        Registry::set('ProductsQuantityUnitAdmin', new ProductsQuantityUnitAdmin());
      }

      $this->qteUnit = Registry::get('ProductsQuantityUnitAdmin');
      $this->app = Registry::get('Groups');
    }

    public function display() {
      global $pInfo;

      $CLICSHOPPING_Template = Registry::get('TemplateAdmin');

      if (!defined('CLICSHOPPING_APP_CUSTOMERS_GROUPS_GR_STATUS') || CLICSHOPPING_APP_CUSTOMERS_GROUPS_GR_STATUS == 'False') {
        return false;
      }

      $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/CustomerGroup/customer_group');

      $output = '';

      if (CLICSHOPPING_APP_CUSTOMERS_GROUPS_GR_STATUS == 'True' &&  !empty(CLICSHOPPING_APP_CUSTOMERS_GROUPS_GR_STATUS)) {
        if (MODE_B2B_B2C == 'true') {

          if (!isset($pInfo->products_percentage)) $pInfo->products_percentage = '1';

          $products_quantity_unit_drop_down = $this->qteUnit->productsQuantityUnitDropDown();

          switch ($pInfo->products_percentage) {
            case '0':
              $in_percent = false;
              $out_percent = true;
              break;
            case '1':
            default:
              $in_percent = true;
              $out_percent = false;
              break;
          }

          $QcustomersGroup = $this->app->db->prepare('select distinct customers_group_id,
                                                                      customers_group_name,
                                                                      customers_group_discount
                                                     from :table_customers_groups
                                                     where customers_group_id != 0
                                                     order by customers_group_id
                                                    ');
          $QcustomersGroup->execute();

          $header = false;

          if (B2B == 'true'){
            $override_on = $this->app->getDef('text_override_on');
          } else {
            $override_on = $this->app->getDef('text_override_on1');
          }

          while ($QcustomersGroup->fetch() ) {
            if (!$header) {
              $header = true;

              $title = '<div class="separator"></div>';
              $title .= '<div class="col-md-12 mainTitle" style="height:30px;">';
              $title .= $this->app->getDef('text_cust_groups') . '&nbsp;&nbsp;&nbsp;&nbsp;' . HTML::radioField('products_percentage', '1', $in_percent) . '&nbsp;' . $override_on . '&nbsp;&nbsp;&nbsp;' . HTML::radioField('products_percentage', '0', $out_percent). '&nbsp;' . $this->app->getDef('text_override_off');
              $title .= '</div>';

              $content = '<table width="100%" cellpadding="5" cellspacing="0" border="0" class="adminformTitle">';
              $content .= '<tr valign="top">';
              $content .= '<td><table class="table table-sm table-hover">';
              $content .= '<thead>';
              $content .= '<tr>';
              $content .= '<td></td>';
              $content .= '<td></td>';
              $content .= '<td></td>';
              $content .= '<td>' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/euro.png', $this->app->getDef('tab_price_group_view')) . '</td>';
              $content .= '<td>' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/last.png', $this->app->getDef('tab_products_group_view')) . '</td>';
              $content .= '<td>' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/orders-up.gif', $this->app->getDef('tab_orders_group_view')) . '</td>';
              $content .= '<td>' . $this->app->getDef('text_products_model_group') . '</td>';
              $content .= '<td>' . $this->app->getDef('text_products_quantity_fixed_group') . '</td>';

              if (defined('CLICSHOPPING_APP_PRODUCTS_QUANTITY_UNIT_PQ_STATUS') &&  CLICSHOPPING_APP_PRODUCTS_QUANTITY_UNIT_PQ_STATUS == 'True' &&  !empty(CLICSHOPPING_APP_PRODUCTS_QUANTITY_UNIT_PQ_STATUS)) {
                $content .= '<td>' . $this->app->getDef('text_products_min_order_quantity_group') . '</td>';
              }

              $content .= '</tr>';
              $content .= '</thead>';
              $content .= '<tbody>';
            }

            if ($QcustomersGroup->rowCount() > 0) {

              $Qattributes = $this->app->db->prepare('select g.customers_group_id,
                                                               g.customers_group_price,
                                                               g.price_group_view,
                                                               g.products_group_view,
                                                               g.orders_group_view,
                                                               p.products_price,
                                                               p.products_id,
                                                               g.products_quantity_unit_id_group,
                                                               g.products_model_group,
                                                               g.products_quantity_fixed_group
                                                        from :table_products_groups g,
                                                             :table_products p
                                                        where p.products_id = :products_id
                                                        and p.products_id = g.products_id
                                                        and g.customers_group_id = :customers_group_id
                                                        order by g.customers_group_id
                                                       ');
              $Qattributes->bindInt(':products_id', $pInfo->products_id );
              $Qattributes->bindInt(':customers_group_id', $QcustomersGroup->valueInt('customers_group_id'));
              $Qattributes->execute();

            } else {
              $attributes = ['customers_group_id' => 'new'];
            }

            $content .= '<tr>';
            $content .= '<td>' . $QcustomersGroup->value('customers_group_name') . '&nbsp;:&nbsp;</td>';
            $content .= '<td class="dataTableContent">';

            if ($attributes = $Qattributes->fetch() ) {
              $content .=  HTML::inputField('price' . $QcustomersGroup->valueInt('customers_group_id'), $Qattributes->valueDecimal('customers_group_price'), 'onchange="updateGross()" size="7" placeholder="'. $this->app->getDef('tax_excluded') . '" class="form-control-sm"') .'<br /><strong>' . $this->app->getDef('tax_excluded') . '</strong>';
            } else {
              $content .=  HTML::inputField('price' . $QcustomersGroup->valueInt('customers_group_id'), '0', 'onchange="updateGross()" size="7" placeholder="'. $this->app->getDef('tax_excluded') . '"') .'<br /><strong>' . $this->app->getDef('tax_excluded') . '</strong>';
// Permet de cocher par defaut la case Afficher Prix Public, Afficher Produit et Autoriser commande
              $attributes['price_group_view'] = 1;
              $attributes['products_group_view'] = 1;
              $attributes['orders_group_view'] = 1;
              $attributes['products_quantity_unit_id_group'] = 0;
              $attributes['products_model_group'] = '';
              $attributes['products_quantity_fixed_group'] = 1;
            }

              $content .= '</td>';
              $content .= '<td class="dataTableContent">';

            if (DISPLAY_DOUBLE_TAXE == 'false') {
              $content .= HTML::inputField('price_gross' . $QcustomersGroup->valueInt('customers_group_id'), $attributes['customers_group_price'], 'onkeyUp="updateNet()" size="7"  placeholder="'. $this->app->getDef('tax_included') . '"') .'<br /><strong>' . $this->app->getDef('tax_included') . '</strong>';
            }

            $content .= '</td>';
  // Autorisation affichage prix public et produit + autorisation commande
            $content .= '<td class="dataTableContent">' . HTML::checkboxField('price_group_view' . $QcustomersGroup->valueInt('customers_group_id'), 1, $attributes['price_group_view']) . '</td>';
            $content .= '<td class="dataTableContent">' . HTML::checkboxField('products_group_view' . $QcustomersGroup->valueInt('customers_group_id'), '1', $attributes['products_group_view']) . '</td>';
            $content .= '<td class="dataTableContent">' . HTML::checkboxField('orders_group_view' . $QcustomersGroup->valueInt('customers_group_id'), '1', $attributes['orders_group_view']) . '</td>';
            $content .= '<td class="dataTableContent">' . HTML::inputField('products_model_group' . $QcustomersGroup->valueInt('customers_group_id'), $attributes['products_model_group']) . '</td>';
            $content .= '<td class="dataTableContent">' . HTML::inputField('products_quantity_fixed_group' . $QcustomersGroup->valueInt('customers_group_id'), $attributes['products_quantity_fixed_group']) . '</td>';

            if (defined('CLICSHOPPING_APP_PRODUCTS_QUANTITY_UNIT_PQ_STATUS') &&  CLICSHOPPING_APP_PRODUCTS_QUANTITY_UNIT_PQ_STATUS == 'True' &&  !empty(CLICSHOPPING_APP_PRODUCTS_QUANTITY_UNIT_PQ_STATUS)) {
              $content .= '<td class="dataTableContent">' . HTML::selectMenu('products_quantity_unit_id_group' . $QcustomersGroup->valueInt('customers_group_id'), $products_quantity_unit_drop_down, $attributes['products_quantity_unit_id_group']) . '</td>';
            }

            $content .= '</tr>';
            $content .= '</tbody>';
          }

          if ($header) {
            $content .= '</table></td>';
          }

          $content .= '</tr>';
          $content .= '</table>';

          $content .= '<div class="separator"></div>';
          $content .= '<div class="alert alert-info">';
          $content .= '<div class="row">';
          $content .= '<span class="col-sm-12">';
          $content .=  HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/help.gif', $this->app->getDef('title_help_price'));
          $content .= '<strong>' . '&nbsp;' . $this->app->getDef('title_help_price') . '</strong>';
          $content .= '</span>';
          $content .= '</div>';
          $content .= '<div class="separator"></div>';

          $content .= '<div class="row">';
          $content .= '<span class="col-sm-12">';
          $content .= HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/euro.png', $this->app->getDef('title_help_price'));
          $content .= '&nbsp;&nbsp;' . $this->app->getDef('help_price_group_view') . '<strong>*</strong>';
          $content .= '</span>';
          $content .= '<span class="col-sm-12">';
          $content .= HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/last.png', $this->app->getDef('tab_products_group_view'));
          $content .= '&nbsp;&nbsp;' . $this->app->getDef('help_products_view');
          $content .= '</span>';
          $content .= '<span class="col-sm-12">';
          $content .= HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/orders-up.gif', $this->app->getDef('tab_orders_group_view'));
          $content .= '&nbsp;&nbsp;' . $this->app->getDef('help_orders_view');
          $content .= '</span>';
          $content .= '</div>';
          $content .= '<div class="separator"></div>';
          $content .= '<div class="row">';
          $content .= '<span class="col-sm-12">&nbsp;<strong>' . '&nbsp;' . $this->app->getDef('help_others_group') . '</strong></span>';
          $content .= '</div>';
          $content .= '</div>';

          $output = '';
          $output .= <<<EOD
<!-- ######################## -->
<!--  Start CustomersGroup      -->
<!-- ######################## -->
       {$title}
       {$content}
<!-- ######################## -->
<!--  Start CustomersGroup      -->
<!-- ######################## -->
EOD;
          return $output;
        }
      }
    }
  }
