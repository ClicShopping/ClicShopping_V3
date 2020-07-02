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

  class pageTab4 implements \ClicShopping\OM\Modules\HooksInterface
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
      $CLICSHOPPING_Customers = Registry::get('Customers');

      if (!defined('CLICSHOPPING_APP_CUSTOMERS_GROUPS_GR_STATUS') || CLICSHOPPING_APP_CUSTOMERS_GROUPS_GR_STATUS == 'False') {
        return false;
      }

      $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/Customers/page_tab_4');

      $Qcustomers = $CLICSHOPPING_Customers->db->prepare('select customers_group_id
                                                          from :table_customers
                                                          where customers_id = :customers_id
                                                        ');
      $Qcustomers->bindInt(':customers_id', $_GET['cID']);
      $Qcustomers->execute();
      $cInfo = new ObjectInfo($Qcustomers->toArray());

// Lecture sur la base de données des informations facturations et livraison du groupe client
     if ($cInfo->customers_group_id != 0) {
        $QcustomersGroup = $CLICSHOPPING_Customers->db->prepare('select customers_group_name,
                                                                        group_order_taxe,
                                                                        group_payment_unallowed,
                                                                        group_shipping_unallowed
                                                                 from :table_customers_groups
                                                                 where customers_group_id = :customers_group_id
                                                                ');
        $QcustomersGroup->bindInt(':customers_group_id', $cInfo->customers_group_id);
        $QcustomersGroup->execute();

        $customer_group = $QcustomersGroup->toArray();
        $group_order_taxe = $QcustomersGroup->value('group_order_taxe');
     } else {
       $group_order_taxe = 0;
     }

      if (CLICSHOPPING_APP_CUSTOMERS_GROUPS_GR_STATUS == 'True') {
        switch ($group_order_taxe) {
          case '0':
            $status_order_taxe = true;
            $status_order_no_taxe = false;
            break;
          case '1':
            $status_order_taxe = false;
            $status_order_no_taxe = true;
            break;
          default:
            $status_order_taxe = true;
            $status_order_no_taxe = false;
        }

        if (MODE_B2B_B2C == 'true') {
          $content = '<div class="adminformTitle">';
          $content .= '<div class="row">';
          $content .= '<div class="col-md-12">';

          $content .= '<div class="row">';
          $content .= '<div class="col-md-5">';
          $content .= '<div class="form-group row">';
          $content .= '<label for="' . $this->app->getDef('entry_customers_group_name') . '" class="col-5 col-form-label">' . $this->app->getDef('entry_customers_group_name') . '</label>';
          $content .= '<div class="col-md-5">';
          $content .= HTML::selectMenu('customers_group_id', GroupsB2BAdmin::getCustomersGroup($this->app->getDef('visitor_name')), $cInfo->customers_group_id);
          $content .= '</div>';
          $content .= '</div>';
          $content .= '</div>';
          $content .= '</div>';

          $content .= '</div>';
          $content .= '</div>';
          $content .= '</div>';

          if ($cInfo->customers_group_id != 0) {
            $content .= '<div class="separator"></div>';
            $content .= '<div class="mainTitle">' . $this->app->getDef('category_order_taxe_group') . '&nbsp;' . $customer_group['customers_group_name'] . '</div>';
            $content .= '<div class="adminformTitle">';
            $content .= '<div class="row">';

            if ($status_order_taxe === false) {
              $class_option_order_taxe = 'fas fa-times fa-lg';
            }  else {
              $class_option_order_taxe ='fas fa-check fa-lg';
            }

            if ($status_order_no_taxe === false) {
              $class_option_no_order_taxe = 'fas fa-times fa-lg';
              } else {
              $class_option_no_order_taxe ='fas fa-check fa-lg';
            }

            if ($group_order_taxe == 0) {
              $content .= '<div class="col-md-12">';
              $content .= '<span class="col-md-1"><i class="' .  $class_option_order_taxe . '" aria-hidden="true"></i></span>';
              $content .= '<span class="col-md-3">' . $this->app->getDef('options_order_taxe') . '</span>';
              $content .= '</div>';
              $content .= '<div class="col-md-12">';
              $content .= '<span class="col-md-1"><i class="' . $class_option_no_order_taxe . '" aria-hidden="true"></i></span>';
              $content .= '<span class="col-md-3">' . $this->app->getDef('options_order_no_taxe') . '</span>';
              $content .= '</div>';
            } else {
              $content .= '<div class="col-md-12">';
              $content .= '<span class="col-md-1"><i class="' .  $class_option_order_taxe . '" aria-hidden="true"></i></span>';
              $content .= '<span class="col-md-3">' . $this->app->getDef('options_order_taxe') . '</span>';
              $content .= '</div>';
              $content .= '<div class="col-md-12">';
              $content .= '<span class="col-md-1"><i class="' . $class_option_no_order_taxe . '" aria-hidden="true"></i></span>';
              $content .= '<span class="col-md-3">' . $this->app->getDef('options_order_no_taxe') . '</span>';
              $content .= '</div>';
            } //end group_order_taxe

            $content .= '</div>';
            $content .= '</div>';

          } // end customers_group_id

          $content .= '<div class="separator"></div>';
          $content .= '<div class="mainTitle">';

          if ($cInfo->customers_group_id != 0) {
            $content .= '<span class="col-md-3">' . $this->app->getDef('category_order_customer_group') . '&nbsp;' . $customer_group['customers_group_name'] . '</span>';
          } else {
            $content .= '<span class="col-md-3">' . $this->app->getDef('category_order_customer') . '</span>';
          } // end customers_group_id


          $content .= '</div>';

          $content .= '<div class="adminformTitle">';
          $content .= '<div class="row">';
          $content .= '<div class="col-md-12">';

// Search payment module
          if ($cInfo->customers_group_id != 0 && $customer_group['group_payment_unallowed']) {
            $payments_unallowed = explode(',', $customer_group['group_payment_unallowed']);
          }

          $module_key = 'MODULE_PAYMENT_INSTALLED';

          $Qconfiguration_payment = $CLICSHOPPING_Customers->db->prepare('select configuration_value
                                                                          from :table_configuration
                                                                          where configuration_key = :configuration_key
                                                                        ');
          $Qconfiguration_payment->bindValue(':configuration_key', $module_key);
          $Qconfiguration_payment->execute();

          $modules_payment = explode(';', $Qconfiguration_payment->value('configuration_value'));

          $include_modules = [];

          foreach ($modules_payment as $value) {
            if (strpos($value, '\\') !== false) {
              $class = Apps::getModuleClass($value, 'Payment');

              $include_modules[] = ['class' => $value,
                'file' => $class
              ];
            }
          }

          for ($i = 0, $n = count($include_modules); $i < $n; $i++) {
            if (strpos($include_modules[$i]['class'], '\\') !== false) {
              Registry::set('Payment_' . str_replace('\\', '_', $include_modules[$i]['class']), new $include_modules[$i]['file']);
              $module = Registry::get('Payment_' . str_replace('\\', '_', $include_modules[$i]['class']));

              if (($cInfo->customers_group_id != 0) && (in_array($module->code, $payments_unallowed))) {
                $content .= '<div class="row">';
                $content .= '<div class="col-md-5">';
                $content .= '<div class="form-group row">';
                $content .= '<div class="col-md-12">';
                $content .= '<span class="col-md-1"><i class="fas fa-check fa-lg" aria-hidden="true"></i></span>';
                $content .= '<span class="col-md-3">' . $module->title . '</span>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
              } elseif (($cInfo->customers_group_id != 0) && (!in_array($module->code, $payments_unallowed))) {
                $content .= '<div class="row">';
                $content .= '<div class="col-md-5">';
                $content .= '<div class="form-group row">';
                $content .= '<div class="col-md-12">';
                $content .= '<span class="col-md-1"><i class="fas fa-times fa-lg" aria-hidden="true"></i></span>';
                $content .= '<span class="col-md-3">' . $module->title . '</span>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
              } elseif ($cInfo->customers_group_id == 0) {
                $content .= '<div class="row">';
                $content .= '<div class="col-md-5">';
                $content .= '<div class="form-group row">';
                $content .= '<div class="col-md-12">';
                $content .= '<span class="col-md-1"><i class="fas fa-check fa-lg" aria-hidden="true"></i></span>';
                $content .= '<span class="col-md-3">' . $module->title . '</span>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
                $content .= '</div>';
              } // end customers_group_id
            }
          } // end for

          $content .= '</div>';
          $content .= '</div>';
          $content .= '</div>';

          $title = $this->app->getDef('category_group_customer');
          $tab_title = $this->app->getDef('Payment');

          $output = <<<EOD
<!-- ######################## -->
<!--  Start Customers Group App      -->
<!-- ######################## -->
<div class="tab-pane" id="section_PaymentCustomerApp_content">
  <div class="mainTitle">
    <span class="col-md-12">{$title}</span>
  </div>
  {$content}
</div>

<script>
$('#section_PaymentCustomerApp_content').appendTo('#customersTabs .tab-content');
$('#customersTabs .nav-tabs').append('    <li class="nav-item"><a data-target="#section_PaymentCustomerApp_content" role="tab" data-toggle="tab" class="nav-link">{$tab_title}</a></li>');
</script>

<!-- ######################## -->
<!--  End Customers Group App      -->
<!-- ######################## -->

EOD;
          return $output;
        }
      }
    }
  }
