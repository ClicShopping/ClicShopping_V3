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

  namespace ClicShopping\Apps\Customers\Groups\Module\Hooks\ClicShoppingAdmin\Customers;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\ObjectInfo;
  use ClicShopping\OM\Apps;

  use ClicShopping\Apps\Customers\Groups\Classes\ClicShoppingAdmin\GroupsB2BAdmin;

  use ClicShopping\Apps\Customers\Groups\Groups as GroupsApp;

  class pageTab5 implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected $app;

    public function __construct()
    {
      if (!Registry::exists('Groups')) {
        Registry::set('Groups', new GroupsApp());
      }

      $this->app = Registry::get('Groups');
    }

    public function display()
    {
      $CLICSHOPPING_Template = Registry::get('TemplateAdmin');
      $CLICSHOPPING_Language = Registry::get('Language');

      $this->app = Registry::get('Customers');

      if (!defined('CLICSHOPPING_APP_CUSTOMERS_GROUPS_GR_STATUS') || CLICSHOPPING_APP_CUSTOMERS_GROUPS_GR_STATUS == 'False') {
        return false;
      }

      $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/Customers/page_tab_5');

      $Qcustomers = $this->app->db->prepare('select customers_group_id
                                                from :table_customers
                                                where customers_id = :customers_id
                                              ');
      $Qcustomers->bindInt(':customers_id', $_GET['cID']);
      $Qcustomers->execute();

      $cInfo = new ObjectInfo($Qcustomers->toArray());

// Lecture sur la base de données des informations facturations et livraison du groupe client
      if ($cInfo->customers_group_id != 0) {
        $QcustomersGroup = $this->app->db->prepare('select customers_group_name,
                                                                        group_order_taxe,
                                                                        group_payment_unallowed,
                                                                        group_shipping_unallowed
                                                                 from :table_customers_groups
                                                                 where customers_group_id = :customers_group_id
                                                                ');
        $QcustomersGroup->bindInt(':customers_group_id', $cInfo->customers_group_id);
        $QcustomersGroup->execute();

        $cInfo_group = new ObjectInfo($QcustomersGroup->toArray());
      }

      $content = '';

      if (CLICSHOPPING_APP_CUSTOMERS_GROUPS_GR_STATUS == 'True') {
// Activation du module B2B
        if ($cInfo->customers_group_id != 0) {
          $title = $this->app->getDef('category_shipping_customer_group') . '&nbsp;' . $cInfo_group->customers_group_name;
// Activation du module B2B
        } else {
          $title = $this->app->getDef('category_shipping_customer');
        }

        $content .= '<div class="adminformTitle">';

// Search Shipping Module
        if ($cInfo->customers_group_id != 0) {
          $shipping_unallowed = explode(',', $cInfo_group->group_shipping_unallowed);
        }

        $module_key = 'MODULE_SHIPPING_INSTALLED';

        $Qconfiguration_shipping = $this->app->db->prepare('select configuration_value
                                                                  from :table_configuration
                                                                  where configuration_key = :configuration_key
                                                                ');
        $Qconfiguration_shipping->bindValue(':configuration_key', $module_key);
        $Qconfiguration_shipping->execute();

        $modules_shipping = explode(';', $Qconfiguration_shipping->value('configuration_value'));

        $include_modules = [];


        foreach ($modules_shipping as $value) {
          if (strpos($value, '\\') !== false) {
            $class = Apps::getModuleClass($value, 'Shipping');

            $include_modules[] = ['class' => $value,
              'file' => $class
            ];
          }
        }

        for ($i = 0, $n = count($include_modules); $i < $n; $i++) {
          if (strpos($include_modules[$i]['class'], '\\') !== false) {
            Registry::set('Shipping_' . str_replace('\\', '_', $include_modules[$i]['class']), new $include_modules[$i]['file']);
            $module = Registry::get('Shipping_' . str_replace('\\', '_', $include_modules[$i]['class']));

            if (($cInfo->customers_group_id != 0) && (in_array($module->code, $shipping_unallowed))) {
              $content .= '<div class="col-md-12">';
              $content .= '<span class="col-md-1"><i class="fas fa-check fa-lg" aria-hidden="true"></i></span>';
              $content .= '<span class="col-md-3">' . $module->title . '</span>';
              $content .= '</div>';
            } elseif ($cInfo->customers_group_id != 0 && !in_array($module->code, $shipping_unallowed)) {
              $content .= '<div class="col-md-12">';
              $content .= '<span class="col-md-1"><i class="fas fa-check fa-lg text-danger" aria-hidden="true"></i></span>';
              $content .= '<span class="col-md-3">' . $module->title . '</span>';
              $content .= '</div>';
            } elseif ($cInfo->customers_group_id == 0) {
              $content .= '<div class="col-md-12">';
              $content .= '<span class="col-md-1"><i class="fas fa-check fa-lg" aria-hidden="true"></i></span>';
              $content .= '<span class="col-md-3">' . $module->title . '</span>';
              $content .= '</div>';
            } // end customers_group_id
          }
        } // end for

        $content .= '</div>';

        $tab_title = $this->app->getDef('Shipping');

        $output = <<<EOD
<!-- ######################## -->
<!--  Start Customers Shiping Group App      -->
<!-- ######################## -->
<div class="tab-pane" id="section_ShippingCustomerApp_content">
  <div class="mainTitle">
    <span class="col-md-12">{$title}</span>
  </div>
  {$content}
</div>

<script>
$('#section_ShippingCustomerApp_content').appendTo('#customersTabs .tab-content');
$('#customersTabs .nav-tabs').append('    <li class="nav-item"><a data-target="#section_ShippingCustomerApp_content" role="tab" data-toggle="tab" class="nav-link">{$tab_title}</a></li>');
</script>
<!-- ######################## -->
<!--  End Customers Shiping Group App      -->
<!-- ######################## -->

EOD;
        return $output;

      }
    }
  }