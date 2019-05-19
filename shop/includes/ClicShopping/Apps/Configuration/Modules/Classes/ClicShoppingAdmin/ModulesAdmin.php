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


  namespace ClicShopping\Apps\Configuration\Modules\Classes\ClicShoppingAdmin;


  class ModulesAdmin
  {


    public function getSwitchModules($module_type)
    {

      $appModuleType = null;

      switch ($module_type) {
        case 'dashboard':
          $appModuleType = 'AdminDashboard';
          break;
        case 'header_tags':
          $appModuleType = 'HeaderTags';
          break;
        case 'payment':
          $appModuleType = 'Payment';
          break;

        case 'shipping':
          $appModuleType = 'Shipping';
          break;

        case 'order_total':
          $appModuleType = 'OrderTotal';
          break;
      }

      return $appModuleType;
    }


  }